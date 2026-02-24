<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\WaterReading;
use Illuminate\Http\Request;
use App\Models\WaterAnalysis;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Ably\AblyRest;

class WaterReadingController extends Controller
{
    public function store(Request $request)
    {
        $token = trim((string) $request->header('X-Device-Token'));
        $tokens = config('services.device.tokens', []);
        $tokens = array_values(array_filter(array_map('trim', $tokens)));

        if (empty($tokens) || !in_array($token, $tokens, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $validated = $request->validate([
            'device_id' => ['required', 'string'],
            'turbidity' => ['required', 'numeric'],
            'tds' => ['required', 'numeric'],
            'ph' => ['required', 'numeric'],
            'temperature' => ['nullable', 'numeric'],
            'no_water_detected' => ['nullable', 'boolean'],
        ]);

        Device::firstOrCreate(
            ['device_id' => $validated['device_id']],
            ['species' => config('aquaculture.default_species', 'tilapia')]
        );

        // Check last reading time to throttle DB saves (History/Alerts = 30s)
        $lastReading = WaterReading::latest()->first();
        $shouldSaveToDb = !$lastReading || $lastReading->created_at->diffInSeconds(now()) >= 30;

        $reading = null;
        
        // Prepare data array including nullable fields defaults
        $data = [
            'device_id' => $validated['device_id'],
            'turbidity' => $validated['turbidity'],
            'tds' => $validated['tds'],
            'ph' => $validated['ph'],
            'temperature' => $validated['temperature'] ?? null,
            'no_water_detected' => $validated['no_water_detected'] ?? false,
        ];

        if ($shouldSaveToDb) {
            // Save to DB (History & Alerts will see this)
            $reading = WaterReading::create($data);
        } else {
            // Transient object for Live Chart only (Not saved to DB)
            $reading = new WaterReading($data);
            $reading->created_at = now();
            // ID will be null, but chart doesn't need it
        }

        // 2. Perform AI Analysis (Every saved reading)
        // Only analyze if we SAVED the reading (requires ID)
        $analysis = null;
        if ($shouldSaveToDb) {
            $analysis = $this->performAnalysis($reading);
        }

        // Broadcast to Ably for live updates (Every 5 seconds)
        $ablyKey = config('services.ably.key');
        $ablyChannel = config('services.ably.channel', 'water-readings');
        
        if ($ablyKey) {
            try {
                $ably = new AblyRest($ablyKey);
                $channel = $ably->channels->get($ablyChannel);
                $channel->publish('reading', [
                    'id' => $reading->id, // Null if transient
                    'device_id' => $reading->device_id,
                    'turbidity' => (float) $reading->turbidity,
                    'tds' => (float) $reading->tds,
                    'ph' => (float) $reading->ph,
                    'temperature' => (float) $reading->temperature,
                    'created_at' => $reading->created_at?->toIso8601String(),
                ]);

                if ($analysis) {
                    $channel->publish('analysis', [
                        'id' => $analysis->id,
                        'water_reading_id' => $analysis->water_reading_id,
                        'risk_level' => $analysis->risk_level,
                        'ai_insight' => $analysis->ai_insight,
                        'recommendations' => $analysis->recommendations,
                        'analyzed_at' => $analysis->analyzed_at->toIso8601String(),
                        'confidence_score' => $analysis->confidence_score,
                    ]);
                }
            } catch (\Throwable $e) {
                // Fail silently to avoid breaking the API response
            }
        }

        return response()->json([
            'success' => true,
            'reading_id' => $reading->id, // Null if not saved
            'saved' => $shouldSaveToDb
        ]);
    }

    private function performAnalysis(WaterReading $reading)
    {
        // --- PERFORM ANALYSIS (AQUACULTURE FOCUS) ---
        $risk = 'safe';
        $insight = '';
        $recommendations = [];
        $riskScore = 0;
        $details = [];

        $turbidity = $reading->turbidity; // Clarity % (High is Good)
        $tds = $reading->tds;
        $ph = $reading->ph;
        $temp = $reading->temperature;

        // 1. Analyze Turbidity (Clarity)
        if ($turbidity < 50) {
            $riskScore += 40;
            $details[] = "water clarity is critically low at {$turbidity}%";
            $recommendations[] = "Replace 25-30% of pond water with fresh, dechlorinated water to reduce suspended solids.";
            $recommendations[] = "Inspect and clean mechanical filters, settling tanks, or biofilters.";
            $recommendations[] = "Reduce feeding rate temporarily — uneaten feed contributes to turbidity buildup.";
        } elseif ($turbidity < 75) {
            $riskScore += 20;
            $details[] = "water clarity is slightly reduced at {$turbidity}%";
            $recommendations[] = "Perform a 10-15% water exchange to improve clarity.";
            $recommendations[] = "Monitor algae bloom indicators and consider adding pond aeration.";
        }

        // 2. Analyze pH
        if ($ph < 5.0 || $ph > 9.0) {
            $riskScore += 40;
            $details[] = "pH level is at a dangerous range ({$ph})";
            if ($ph < 5.0) {
                $recommendations[] = "Apply agricultural lime (CaCO3) at 50-100 kg/ha to raise pH gradually.";
                $recommendations[] = "Check for acid runoff or decaying organic matter as possible acidification sources.";
            } else {
                $recommendations[] = "Increase freshwater inflow to dilute alkalinity and bring pH down.";
                $recommendations[] = "Add organic acids or reduce exposure to photosynthetic algae blooms.";
            }
        } elseif ($ph < 5.5 || $ph > 8.5) {
            $riskScore += 10;
            $details[] = "pH is approaching borderline levels ({$ph})";
            if ($ph < 5.5) {
                $recommendations[] = "Consider light liming to buffer pH and prevent further decline.";
            } else {
                $recommendations[] = "Increase water exchange rate to stabilize pH within optimal range.";
            }
        }

        // 3. Analyze TDS
        if ($tds > 1000) {
            $riskScore += 30;
            $details[] = "total dissolved solids (TDS) is elevated at {$tds} ppm";
            $recommendations[] = "Perform a 20-30% water change with low-TDS source water.";
            $recommendations[] = "Inspect for mineral or salt intrusion from soil runoff.";
            $recommendations[] = "Reduce supplemental feed inputs which raise dissolved organic matter.";
        } elseif ($tds > 500) {
            $riskScore += 10;
            $details[] = "TDS is moderately high at {$tds} ppm";
            $recommendations[] = "Schedule routine water exchange to prevent further TDS accumulation.";
        }

        // 4. Analyze Temperature
        if ($temp && ($temp < 20 || $temp > 34)) {
            $riskScore += 20;
            if ($temp < 20) {
                $details[] = "water temperature is below optimal range ({$temp}°C)";
                $recommendations[] = "Increase pond water depth to retain thermal mass during cold periods.";
                $recommendations[] = "Reduce feeding frequency — fish metabolism slows significantly below 20°C.";
            } else {
                $details[] = "water temperature exceeds safe threshold ({$temp}°C)";
                $recommendations[] = "Increase aeration to compensate for reduced dissolved oxygen at high temperatures.";
                $recommendations[] = "Provide shade cover or increase water flow to lower pond temperature.";
            }
        } elseif ($temp && ($temp < 24 || $temp > 32)) {
            $riskScore += 5;
            $details[] = "water temperature is outside the ideal range ({$temp}°C)";
            $recommendations[] = "Monitor temperature trends — sustained readings outside 24-32°C may affect growth rate.";
        }

        // --- Determine Risk Level & Insight ---
        if ($riskScore >= 40) {
            $risk = 'critical';
            $insight = "Critical water quality conditions detected: " . implode('; ', $details) . ". ";
            $insight .= "Immediate corrective action is required to prevent fish mortality and significant growth loss.";
        } elseif ($riskScore >= 20) {
            $risk = 'high';
            $insight = "Suboptimal water conditions identified: " . implode('; ', $details) . ". ";
            $insight .= "Fish growth rate may be reduced. Corrective measures are recommended to restore optimal conditions.";
        } else {
            $risk = 'safe';
            $insight = "All water quality parameters are within the optimal range for aquaculture. ";
            $insight .= "Turbidity at {$turbidity}%, TDS at {$tds} ppm, pH at {$ph}, and water temperature at {$temp}°C — ";
            $insight .= "conditions are favorable for healthy fish growth and development.";

            // Real-world safe-condition recommendations
            $recommendations[] = "Continue regular water quality monitoring at consistent intervals.";
            $recommendations[] = "Maintain current feeding schedule based on fish biomass and growth stage.";
            $recommendations[] = "Inspect pond infrastructure (dikes, inlet/outlet, screens) during routine rounds.";
        }

        // Create Analysis Record
        $analysis = WaterAnalysis::create([
            'water_reading_id' => $reading->id,
            'analysis_type' => 'automated',
            'ai_insight' => $insight,
            'risk_level' => $risk,
            'recommendations' => array_values(array_unique($recommendations)),
            'confidence_score' => max(60, 100 - $riskScore),
            'analyzed_at' => now(),
        ]);

        return $analysis;
    }
}

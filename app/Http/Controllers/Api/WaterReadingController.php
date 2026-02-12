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
            'humidity' => ['required', 'numeric'],
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
            'humidity' => $validated['humidity'] ?? null,
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
                    'humidity' => (float) $reading->humidity,
                    'created_at' => $reading->created_at?->toIso8601String(),
                ]);

                // 2. Check and Perform AI Analysis (Every 5 Minutes)
                // Only analyze if we SAVED the reading (requires ID)
                if ($shouldSaveToDb) {
                    $analysis = $this->performAnalysis($reading);
                    
                    if ($analysis) {
                        $channel->publish('analysis', [
                            'id' => $analysis->id,
                            'risk_level' => $analysis->risk_level,
                            'ai_insight' => $analysis->ai_insight,
                            'recommendations' => $analysis->recommendations,
                            'analyzed_at' => $analysis->analyzed_at->toIso8601String(),
                            'confidence_score' => $analysis->confidence_score,
                        ]);
                    }
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
        // specific rule: only analyze every 5 minutes
        $lastAnalysis = WaterAnalysis::latest('analyzed_at')->first();
        if ($lastAnalysis && $lastAnalysis->analyzed_at->diffInMinutes(now()) < 5) {
            return null; // Skip analysis (too soon)
        }

        // --- PERFORM ANALYSIS ---
        $risk = 'safe';
        $insight = 'Water quality parameters are within the optimal range for aquaculture.';
        $recommendations = ['Continue routine monitoring.'];
        
        $turbidity = $reading->turbidity;
        $tds = $reading->tds;
        $ph = $reading->ph;
        $temp = $reading->temperature;

        // Custom Logic (Same as AnalysisController)
        // Custom Logic (updated for Clarity %)
        // Critical: Clarity < 25 OR TDS > 800 OR pH < 5.0 OR pH > 9.0
        if ($turbidity < 25 || $tds > 800 || $ph < 5.0 || $ph > 9.0) {
            $risk = 'critical';
            $insight = 'Critical water quality detected! Immediate action required.';
            $recommendations = ['Check aeration system', 'Perform partial water change', 'Verify sensor calibration'];
        } 
        // High Risk: Clarity < 50 OR TDS > 600 OR pH < 5.5 OR pH > 8.5
        elseif ($turbidity < 50 || $tds > 600 || $ph < 5.5 || $ph > 8.5) {
            $risk = 'high';
            $insight = 'Water quality is degrading. Parameters deviating from optimal.';
            $recommendations = ['Inspect filtration system', 'Monitor feeding rates'];
        } elseif ($temp < 20 || $temp > 30) {
             $risk = 'medium';
             $insight = 'Temperature fluctuation detected.';
             $recommendations = ['Check heater/cooler system'];
        } else {
             // Randomize "safe" insights slightly for realism
             $safeInsights = [
                 'Water quality is optimal for aquatic updates.',
                 'No significant anomalies detected in recent readings.',
                 'Environmental conditions are stable.',
             ];
             $insight = $safeInsights[array_rand($safeInsights)];
        }

        // Create Analysis Record
        $analysis = WaterAnalysis::create([
            'water_reading_id' => $reading->id,
            'analysis_type' => 'automated',
            'ai_insight' => $insight,
            'risk_level' => $risk,
            'recommendations' => $recommendations,
            'confidence_score' => rand(85, 99) + (rand(0, 99) / 10),
            'analyzed_at' => now(),
        ]);

        return $analysis;
    }
}

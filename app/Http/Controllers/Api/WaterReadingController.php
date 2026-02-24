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
        // NO MORE TIMEOUT: Analyze every saved reading as requested.

        // --- PERFORM ANALYSIS (FISH GROWTH FOCUS) ---
        $risk = 'safe';
        $insight = '';
        $recommendations = [];
        $riskScore = 0;
        $details = [];

        $turbidity = $reading->turbidity; // Clarity % (High is Good)
        $tds = $reading->tds;
        $ph = $reading->ph; // Safe: 5.0 - 9.0
        $temp = $reading->temperature;

        // 1. Analyze Turbidity (Clarity)
        // High % is Clear (Good), Low % is Muddy (Bad)
        if ($turbidity < 50) { 
            $riskScore += 40; 
            $details[] = "Water is too muddy (" . $turbidity . "%)";
            $recommendations[] = "Perform a partial water change based on Clarity."; 
        } elseif ($turbidity < 75) { 
            $riskScore += 20; 
            $details[] = "Water is a bit cloudy (" . $turbidity . "%)";
            $recommendations[] = "Check filtration system.";
        }

        // 2. Analyze pH (Safe Range: 5.0 - 9.0)
        if ($ph < 5.0 || $ph > 9.0) {
            $riskScore += 40;
            $details[] = "pH is dangerous ({$ph})";
            $recommendations[] = "Adjust pH level immediately.";
        } elseif ($ph < 5.5 || $ph > 8.5) {
            $riskScore += 10;
        }

        // 3. Analyze TDS
        if ($tds > 1000) {
            $riskScore += 30;
            $details[] = "TDS is too high";
            $recommendations[] = "Reduce dissolved solids by changing water.";
        }

        // 4. Analyze Temperature (If available)
        if ($temp && ($temp < 20 || $temp > 34)) {
            $riskScore += 20;
            $details[] = "Temperature is extreme";
            $recommendations[] = "Check water depth or shading.";
        }

        // --- Determine Status & Insight ---
        if ($riskScore >= 40) {
            $risk = 'critical';
            $insight = "🚨 Poor Conditions for Growth! ";
            $insight .= "Your fish are stressed because " . implode(' and ', $details) . ". ";
            $insight .= "Growth will stop or fish may die if not fixed.";
        } elseif ($riskScore >= 20) {
            $risk = 'high';
            $insight = "⚠️ Slower Growth Detected. ";
            if (count($details) > 0) {
                $insight .= "The water is " . implode(', ', $details) . ". ";
            }
            $insight .= "Fish are surviving but not growing fast. Improve conditions for better harvest.";
        } else {
            $risk = 'safe';
            // Random Positive Insights
            $positiveMsg = [
                "Conditions are perfect for maximum fish growth! 🐟📈",
                "Water quality is excellent. Your fish are happy and growing fast!",
                "Great job! The environment is ideal for your aquaculture.",
            ];
            $insight = $positiveMsg[array_rand($positiveMsg)];
            $recommendations[] = "Keep up the good work!";
            $recommendations[] = "Maintain current feeding schedule.";
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

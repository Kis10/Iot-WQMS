<?php

namespace App\Jobs;

use App\Models\WaterReading;
use App\Models\WaterAnalysis;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AnalyzeWaterQuality implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [30, 60, 120]; // Retry delays in seconds

    protected $waterReading;

    /**
     * Create a new job instance.
     */
    public function __construct(WaterReading $waterReading)
    {
        $this->waterReading = $waterReading;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $windowReadings = WaterReading::where('created_at', '>=', now()->subMinutes(5))
                ->where('no_water_detected', false)
                ->orderBy('created_at')
                ->get();

            if ($windowReadings->isEmpty()) {
                if ($this->waterReading->no_water_detected) {
                    Log::info('Skipping analysis: no water detected for reading ID: ' . $this->waterReading->id);
                    return;
                }

                $windowReadings = collect([$this->waterReading]);
            }

            $latestReading = $windowReadings->last();

            if (!$latestReading) {
                Log::warning('No water readings available for analysis.');
                return;
            }

            $recentAnalysis = WaterAnalysis::where('analyzed_at', '>', now()->subMinutes(5))
                ->latest('analyzed_at')
                ->first();

            if ($recentAnalysis) {
                Log::info('Skipping analysis: already analyzed within the last 5 minutes.');
                return;
            }

            $latestReading->loadMissing('device');

            $summaryReading = (object) [
                'turbidity' => round((float) $windowReadings->avg('turbidity'), 2),
                'tds' => round((float) $windowReadings->avg('tds'), 2),
                'ph' => round((float) $windowReadings->avg('ph'), 2),
                'temperature' => round((float) $windowReadings->avg('temperature'), 2),
                'sample_count' => $windowReadings->count()
            ];

            // Simulate AI analysis (replace with actual AI API call)
            $trendData = $this->calculateTrends($windowReadings);
            $speciesConfig = $this->getSpeciesConfig($latestReading);
            $analysis = $this->performAIAnalysis($summaryReading, $trendData, $speciesConfig);

            // Store the analysis results
            WaterAnalysis::create([
                'water_reading_id' => $latestReading->id,
                'analysis_type' => $analysis['type'],
                'ai_insight' => $analysis['insight'],
                'risk_level' => $analysis['risk_level'],
                'recommendations' => $analysis['recommendations'],
                'confidence_score' => $analysis['confidence_score'],
                'analyzed_at' => now(),
            ]);

            Log::info('Water quality analysis completed for reading ID: ' . $latestReading->id);

        } catch (\Exception $e) {
            Log::error('Failed to analyze water quality: ' . $e->getMessage());
            throw $e; // This will trigger retry mechanism
        }
    }

    /**
     * Perform AI analysis on water quality data
     */
    private function performAIAnalysis(object $reading, array $trendData, array $speciesConfig): array
    {
        // For now, implement rule-based analysis
        // In production, you would integrate with OpenAI, Claude, or other AI services

        $riskFactors = [];
        $recommendations = [];
        $riskScore = 0;
        $positiveNotes = [];

        [$speciesKey, $species] = $speciesConfig;
        $speciesLabel = $species['label'] ?? ucfirst($speciesKey);

        // Analyze turbidity
        [$turbidityOptimalMin, $turbidityOptimalMax] = $species['turbidity']['optimal'];
        [$turbiditySafeMin, $turbiditySafeMax] = $species['turbidity']['safe'];
        if ($reading->turbidity < $turbiditySafeMin || $reading->turbidity > $turbiditySafeMax) {
            $riskFactors[] = "The water is too cloudy! ({$reading->turbidity} NTU)";
            $recommendations[] = "Clean the filter or remove dirty poop from the bottom.";
            $recommendations[] = "Stop feeding for a bit so the water clears up.";
            $riskScore += 30;
        } elseif ($reading->turbidity < $turbidityOptimalMin || $reading->turbidity > $turbidityOptimalMax) {
            $riskFactors[] = "Water is a bit murky ({$reading->turbidity} NTU)";
            $recommendations[] = "Check if the pump is running well to move the water.";
            $riskScore += 15;
        } else {
            $positiveNotes[] = "Water is crystal clear! ✨";
        }

        // Analyze TDS
        [$tdsOptimalMin, $tdsOptimalMax] = $species['tds']['optimal'];
        [$tdsSafeMin, $tdsSafeMax] = $species['tds']['safe'];
        if ($reading->tds < $tdsSafeMin || $reading->tds > $tdsSafeMax) {
            $riskFactors[] = "Too much stuff dissolved in water! ({$reading->tds})";
            $recommendations[] = "Change some of the water (about 20%) with fresh water.";
            $riskScore += 25;
        } elseif ($reading->tds < $tdsOptimalMin || $reading->tds > $tdsOptimalMax) {
            $riskFactors[] = "Water has a bit too much minerals ({$reading->tds})";
            $recommendations[] = "Just add a little fresh water slowly.";
            $riskScore += 10;
        } else {
            $positiveNotes[] = "Water purity is perfect! 💧";
        }

        // Analyze pH
        [$phOptimalMin, $phOptimalMax] = $species['ph']['optimal'];
        [$phSafeMin, $phSafeMax] = $species['ph']['safe'];
        if ($reading->ph < $phSafeMin || $reading->ph > $phSafeMax) {
            $riskFactors[] = "pH is dangerous! ({$reading->ph})";
            $recommendations[] = "The water is too acidic or basic! Ask an adult to help adjust it.";
            $riskScore += 35;
        } elseif ($reading->ph < $phOptimalMin || $reading->ph > $phOptimalMax) {
            $riskFactors[] = "pH is slightly off ({$reading->ph})";
            $recommendations[] = "Keep an eye on the pH level today.";
            $riskScore += 15;
        } else {
            $positiveNotes[] = "pH level is just right! 👍";
        }

        // Analyze temperature
        [$tempOptimalMin, $tempOptimalMax] = $species['temperature']['optimal'];
        [$tempSafeMin, $tempSafeMax] = $species['temperature']['safe'];
        if ($reading->temperature < $tempSafeMin || $reading->temperature > $tempSafeMax) {
            $riskFactors[] = "Water is too hot or cold! ({$reading->temperature} C)";
            $recommendations[] = "Check the heater or add some shade/ice to help the fish.";
            $riskScore += 20;
        } elseif ($reading->temperature < $tempOptimalMin || $reading->temperature > $tempOptimalMax) {
            $riskFactors[] = "Temp is not quite right ({$reading->temperature} C)";
            $recommendations[] = "Make sure the water stays steady, fish hate sudden changes!";
            $riskScore += 10;
        } else {
            $positiveNotes[] = "Temperature is comfy! 🌡️";
        }

        $this->applyTrendSignals($trendData, $riskFactors, $recommendations, $positiveNotes, $riskScore);

        // Determine risk level
        $riskLevel = 'safe';
        if ($riskScore >= 70) {
            $riskLevel = 'critical';
        } elseif ($riskScore >= 50) {
            $riskLevel = 'high';
        } elseif ($riskScore >= 25) {
            $riskLevel = 'medium';
        }

        // Generate AI insight
        $insight = $this->generateInsight($reading, $riskFactors, $positiveNotes, $riskLevel, $speciesLabel);

        if ($riskLevel === 'safe') {
            $recommendations[] = "Maintain regular aeration and water circulation to support growth";
            $recommendations[] = "Keep feeding consistent and avoid overfeeding to prevent waste buildup";
            $recommendations[] = "Continue routine filter cleaning and sensor checks to keep conditions stable";
        }

        $recommendations = array_values(array_unique($recommendations));

        $sampleCount = $reading->sample_count ?? 1;
        $baseConfidence = max(70, 95 - $riskScore);
        if ($sampleCount < 3) {
            $baseConfidence -= 5;
        }

        return [
            'type' => 'ai-5min-window',
            'insight' => $insight,
            'risk_level' => $riskLevel,
            'recommendations' => $recommendations,
            'confidence_score' => max(60, $baseConfidence), // Higher confidence for lower risk
        ];
    }

    /**
     * Generate AI insight based on analysis
     */
    private function generateInsight(object $reading, array $riskFactors, array $positiveNotes, string $riskLevel, string $speciesLabel): string
    {
        $sampleCount = $reading->sample_count ?? 1;
        
        // Friendly Start
        $insight = "";
        
        if ($riskLevel === 'safe') {
            $insight = "Everything looks great! The water is perfect for your {$speciesLabel}. ";
            $insight .= "Your fish are happy and healthy! 🐟✨";
        } elseif ($riskLevel === 'medium') {
            $insight = "Heads up! The water is okay, but could be better. ";
            $insight .= "Your {$speciesLabel} might be a little uncomfy. Let's fix a few things.";
        } else {
            $insight = "Oh no! The water needs help right now! 🚨 ";
            $insight .= "Your {$speciesLabel} are in danger. Please check the pond immediately.";
        }

        // Add details simply
        if (!empty($riskFactors)) {
            $insight .= " Problem: " . implode(', ', $riskFactors) . ".";
        }

        // Add positive notes if any
        if (!empty($positiveNotes) && $riskLevel !== 'critical') {
            $insight .= " On the bright side: " . implode(', ', $positiveNotes) . ".";
        }

        return $insight;
    }

    private function getSpeciesConfig(?WaterReading $reading = null): array
    {
        $speciesKey = $reading?->device?->species ?? config('aquaculture.default_species', 'tilapia');
        $species = config("aquaculture.species.{$speciesKey}");

        if (!$species) {
            $speciesKey = 'tilapia';
            $species = config('aquaculture.species.tilapia');
        }

        return [$speciesKey, $species];
    }

    private function calculateTrends($readings): array
    {
        if ($readings->count() < 2) {
            return [];
        }

        $first = $readings->first();
        $last = $readings->last();
        $minutes = max(1, $first->created_at->diffInMinutes($last->created_at));

        return [
            'minutes' => $minutes,
            'deltas' => [
                'turbidity' => (float) $last->turbidity - (float) $first->turbidity,
                'tds' => (float) $last->tds - (float) $first->tds,
                'ph' => (float) $last->ph - (float) $first->ph,
                'temperature' => (float) $last->temperature - (float) $first->temperature,
            ]
        ];
    }

    private function applyTrendSignals(array $trendData, array &$riskFactors, array &$recommendations, array &$positiveNotes, int &$riskScore): void
    {
        if (empty($trendData['deltas'])) {
            return;
        }

        $thresholds = config('aquaculture.trend', []);
        $minutes = $trendData['minutes'] ?? 5;

        $turbidityDelta = $trendData['deltas']['turbidity'] ?? 0;
        if (isset($thresholds['turbidity']) && abs($turbidityDelta) >= $thresholds['turbidity']) {
            if ($turbidityDelta > 0) {
                $riskFactors[] = "Turbidity rising (+{$turbidityDelta} NTU in {$minutes} min)";
                $recommendations[] = "Check solids removal and avoid overfeeding to prevent oxygen drops";
                $riskScore += 10;
            } else {
                $positiveNotes[] = "Turbidity trend is improving";
            }
        }

        $tdsDelta = $trendData['deltas']['tds'] ?? 0;
        if (isset($thresholds['tds']) && abs($tdsDelta) >= $thresholds['tds']) {
            if ($tdsDelta > 0) {
                $riskFactors[] = "TDS climbing (+{$tdsDelta} mg/L in {$minutes} min)";
                $recommendations[] = "Monitor source water and consider dilution to limit chronic stress";
                $riskScore += 8;
            } else {
                $positiveNotes[] = "TDS trend is improving";
            }
        }

        $phDelta = $trendData['deltas']['ph'] ?? 0;
        if (isset($thresholds['ph']) && abs($phDelta) >= $thresholds['ph']) {
            $riskFactors[] = "pH shifting quickly ({$phDelta} in {$minutes} min)";
            $recommendations[] = "Stabilize pH gradually to avoid sudden stress";
            $riskScore += 10;
        }

        $tempDelta = $trendData['deltas']['temperature'] ?? 0;
        if (isset($thresholds['temperature']) && abs($tempDelta) >= $thresholds['temperature']) {
            $riskFactors[] = "Temperature drifting ({$tempDelta} C in {$minutes} min)";
            $recommendations[] = "Reduce temperature swings for steady growth and appetite";
            $riskScore += 8;
        }
    }
}

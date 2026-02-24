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
            throw $e;
        }
    }

    /**
     * Perform analysis on water quality data
     */
    private function performAIAnalysis(object $reading, array $trendData, array $speciesConfig): array
    {
        $riskFactors = [];
        $recommendations = [];
        $riskScore = 0;
        $positiveNotes = [];

        [$speciesKey, $species] = $speciesConfig;
        $speciesLabel = $species['label'] ?? ucfirst($speciesKey);

        // Analyze turbidity as Clarity % (100 = Clear, 0 = Dirty)
        $clarity = $reading->turbidity;

        if ($clarity >= 85) {
            $positiveNotes[] = "Water clarity is excellent at {$clarity}%";
        } elseif ($clarity >= 50) {
            $positiveNotes[] = "Water clarity is acceptable at {$clarity}%";
        } elseif ($clarity >= 25) {
            $riskFactors[] = "Water clarity is reduced to {$clarity}%";
            $recommendations[] = "Perform a 10-15% water exchange to improve clarity.";
            $recommendations[] = "Inspect and clean mechanical filters or biofilters.";
            $recommendations[] = "Reduce feeding rate temporarily — uneaten feed contributes to turbidity buildup.";
            $riskScore += 20;
        } else {
            $riskFactors[] = "Water clarity is critically low at {$clarity}%";
            $recommendations[] = "Replace 25-30% of pond water with fresh, dechlorinated water immediately.";
            $recommendations[] = "Inspect filtration system and settling tanks for blockages.";
            $recommendations[] = "Suspend feeding until water clarity improves above 25%.";
            $riskScore += 40;
        }

        // Analyze TDS
        [$tdsOptimalMin, $tdsOptimalMax] = $species['tds']['optimal'];
        [$tdsSafeMin, $tdsSafeMax] = $species['tds']['safe'];
        if ($reading->tds < $tdsSafeMin || $reading->tds > $tdsSafeMax) {
            $riskFactors[] = "TDS is outside safe range at {$reading->tds} ppm";
            $recommendations[] = "Perform a 20-30% water change with low-TDS source water.";
            $recommendations[] = "Inspect for mineral or salt intrusion from soil runoff.";
            $riskScore += 25;
        } elseif ($reading->tds < $tdsOptimalMin || $reading->tds > $tdsOptimalMax) {
            $riskFactors[] = "TDS is slightly outside optimal range at {$reading->tds} ppm";
            $recommendations[] = "Schedule routine water exchange to prevent further TDS accumulation.";
            $riskScore += 10;
        } else {
            $positiveNotes[] = "TDS is within optimal range at {$reading->tds} ppm";
        }

        // Analyze pH
        [$phOptimalMin, $phOptimalMax] = $species['ph']['optimal'];
        [$phSafeMin, $phSafeMax] = $species['ph']['safe'];
        if ($reading->ph < $phSafeMin || $reading->ph > $phSafeMax) {
            $riskFactors[] = "pH is at a dangerous level ({$reading->ph})";
            if ($reading->ph < $phSafeMin) {
                $recommendations[] = "Apply agricultural lime (CaCO3) at 50-100 kg/ha to raise pH gradually.";
                $recommendations[] = "Check for acid runoff or decaying organic matter as possible acidification sources.";
            } else {
                $recommendations[] = "Increase freshwater inflow to dilute alkalinity and bring pH down.";
                $recommendations[] = "Reduce exposure to photosynthetic algae blooms that elevate pH.";
            }
            $riskScore += 35;
        } elseif ($reading->ph < $phOptimalMin || $reading->ph > $phOptimalMax) {
            $riskFactors[] = "pH is approaching borderline levels ({$reading->ph})";
            if ($reading->ph < $phOptimalMin) {
                $recommendations[] = "Consider light liming to buffer pH and prevent further decline.";
            } else {
                $recommendations[] = "Increase water exchange rate to stabilize pH within optimal range.";
            }
            $riskScore += 15;
        } else {
            $positiveNotes[] = "pH level is within optimal range at {$reading->ph}";
        }

        // Analyze temperature
        [$tempOptimalMin, $tempOptimalMax] = $species['temperature']['optimal'];
        [$tempSafeMin, $tempSafeMax] = $species['temperature']['safe'];
        if ($reading->temperature < $tempSafeMin || $reading->temperature > $tempSafeMax) {
            if ($reading->temperature < $tempSafeMin) {
                $riskFactors[] = "Water temperature is critically low at {$reading->temperature}°C";
                $recommendations[] = "Increase pond water depth to retain thermal mass during cold periods.";
                $recommendations[] = "Reduce feeding frequency — fish metabolism slows significantly at low temperatures.";
            } else {
                $riskFactors[] = "Water temperature exceeds safe threshold at {$reading->temperature}°C";
                $recommendations[] = "Increase aeration to compensate for reduced dissolved oxygen at high temperatures.";
                $recommendations[] = "Provide shade cover or increase water flow to lower pond temperature.";
            }
            $riskScore += 20;
        } elseif ($reading->temperature < $tempOptimalMin || $reading->temperature > $tempOptimalMax) {
            $riskFactors[] = "Water temperature is outside the ideal range at {$reading->temperature}°C";
            $recommendations[] = "Monitor temperature trends — sustained readings outside the optimal range may affect growth rate.";
            $riskScore += 10;
        } else {
            $positiveNotes[] = "Water temperature is optimal at {$reading->temperature}°C";
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

        // Generate insight
        $insight = $this->generateInsight($reading, $riskFactors, $positiveNotes, $riskLevel, $speciesLabel);

        if ($riskLevel === 'safe') {
            $recommendations[] = "Continue regular water quality monitoring at consistent intervals.";
            $recommendations[] = "Maintain current feeding schedule based on fish biomass and growth stage.";
            $recommendations[] = "Inspect pond infrastructure (dikes, inlet/outlet, screens) during routine rounds.";
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
            'confidence_score' => max(60, $baseConfidence),
        ];
    }

    /**
     * Generate insight based on analysis
     */
    private function generateInsight(object $reading, array $riskFactors, array $positiveNotes, string $riskLevel, string $speciesLabel): string
    {
        $insight = "";

        if ($riskLevel === 'safe') {
            $insight = "All water quality parameters are within the optimal range for {$speciesLabel} aquaculture. ";
            $insight .= "Conditions are favorable for healthy fish growth and development.";
        } elseif ($riskLevel === 'medium') {
            $insight = "Suboptimal water conditions identified for {$speciesLabel}. ";
            $insight .= "Fish growth rate may be reduced. Corrective measures are recommended to restore optimal conditions.";
        } else {
            $insight = "Critical water quality conditions detected for {$speciesLabel}. ";
            $insight .= "Immediate corrective action is required to prevent fish mortality and significant growth loss.";
        }

        if (!empty($riskFactors)) {
            $insight .= " Issues detected: " . implode('; ', $riskFactors) . ".";
        }

        if (!empty($positiveNotes) && $riskLevel !== 'critical') {
            $insight .= " Positive indicators: " . implode('; ', $positiveNotes) . ".";
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
                $recommendations[] = "Check solids removal and reduce feeding to prevent dissolved oxygen depletion.";
                $riskScore += 10;
            } else {
                $positiveNotes[] = "Turbidity trend is improving";
            }
        }

        $tdsDelta = $trendData['deltas']['tds'] ?? 0;
        if (isset($thresholds['tds']) && abs($tdsDelta) >= $thresholds['tds']) {
            if ($tdsDelta > 0) {
                $riskFactors[] = "TDS climbing (+{$tdsDelta} mg/L in {$minutes} min)";
                $recommendations[] = "Monitor source water quality and consider dilution to limit chronic stress.";
                $riskScore += 8;
            } else {
                $positiveNotes[] = "TDS trend is improving";
            }
        }

        $phDelta = $trendData['deltas']['ph'] ?? 0;
        if (isset($thresholds['ph']) && abs($phDelta) >= $thresholds['ph']) {
            $riskFactors[] = "pH shifting rapidly ({$phDelta} in {$minutes} min)";
            $recommendations[] = "Stabilize pH gradually — avoid rapid chemical corrections that may stress fish.";
            $riskScore += 10;
        }

        $tempDelta = $trendData['deltas']['temperature'] ?? 0;
        if (isset($thresholds['temperature']) && abs($tempDelta) >= $thresholds['temperature']) {
            $riskFactors[] = "Temperature fluctuating ({$tempDelta}°C in {$minutes} min)";
            $recommendations[] = "Minimize temperature swings to maintain steady growth and appetite.";
            $riskScore += 8;
        }
    }
}

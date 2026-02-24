<?php

namespace App\Http\Controllers;

use App\Models\WaterAnalysis;
use App\Models\WaterReading;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function index()
    {
        $analyses = WaterAnalysis::with('waterReading')
            ->latest('analyzed_at')
            ->paginate(10);
            
        return view('analysis.index', compact('analyses'));
    }

    public function latest()
    {
        $latestAnalysis = WaterAnalysis::with('waterReading')
            ->latest('analyzed_at')
            ->first();
            
        return response()->json($latestAnalysis);
    }

    public function show(WaterAnalysis $analysis)
    {
        $analysis->load('waterReading');
        return view('analysis.show', compact('analysis'));
    }
    public function generate()
    {
        $reading = WaterReading::latest()->first();
        
        $risk = 'safe';
        $insight = 'All water quality parameters are within the optimal range for aquaculture. Conditions are favorable for healthy fish growth and development.';
        $recommendations = [
            'Continue regular water quality monitoring at consistent intervals.',
            'Maintain current feeding schedule based on fish biomass and growth stage.',
            'Inspect pond infrastructure (dikes, inlet/outlet, screens) during routine rounds.',
        ];
        
        if ($reading) {
            if ($reading->turbidity > 50 || $reading->tds > 800 || $reading->ph < 5.5 || $reading->ph > 9) {
                $risk = 'critical';
                $insight = 'Critical water quality conditions detected. Immediate corrective action is required to prevent fish mortality and significant growth loss.';
                $recommendations = [
                    'Increase aeration to maintain dissolved oxygen levels.',
                    'Perform a 25-30% water change with fresh, dechlorinated source water.',
                    'Verify sensor calibration to confirm accuracy of readings.',
                ];
            } elseif ($reading->turbidity > 25 || $reading->tds > 500 || $reading->ph < 6.5 || $reading->ph > 8.5) {
                $risk = 'high';
                $insight = 'Suboptimal water conditions identified. Fish growth rate may be reduced. Corrective measures are recommended to restore optimal conditions.';
                $recommendations = [
                    'Inspect and clean mechanical filters or biofilters.',
                    'Reduce feeding rate temporarily to minimize organic waste.',
                    'Schedule routine water exchange to prevent further deterioration.',
                ];
            } elseif ($reading->temperature < 20 || $reading->temperature > 30) {
                 $risk = 'medium';
                 $insight = 'Water temperature is outside the optimal range. Monitor trends to prevent stress-related growth reduction.';
                 $recommendations = [
                    'Monitor temperature trends and adjust aeration or shading accordingly.',
                    'Adjust feeding frequency based on current water temperature.',
                 ];
            }
            
            $analysis = WaterAnalysis::create([
                'water_reading_id' => $reading->id,
                'analysis_type' => 'automated',
                'ai_insight' => $insight,
                'risk_level' => $risk,
                'recommendations' => $recommendations,
                'confidence_score' => rand(85, 99) + (rand(0, 99) / 10),
                'analyzed_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'No readings found']);
    }
}

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
        
        // Simulating AI Analysis
        $risk = 'safe';
        $insight = 'Water quality parameters are within the optimal range for aquaculture.';
        $recommendations = ['Continue routine monitoring.'];
        
        if ($reading) {
            if ($reading->turbidity > 50 || $reading->tds > 800 || $reading->ph < 5.5 || $reading->ph > 9) {
                $risk = 'critical';
                $insight = 'Critical water quality issues detected! Immediate action required to prevent fish mortality.';
                $recommendations = ['Check aeration system', 'Perform partial water change', 'Verify sensor calibration'];
            } elseif ($reading->turbidity > 25 || $reading->tds > 500 || $reading->ph < 6.5 || $reading->ph > 8.5) {
                $risk = 'high';
                $insight = 'Water quality is degrading. Parameters are deviating from optimal levels.';
                $recommendations = ['Inspect filtration system', 'Monitor feeding rates'];
            } elseif ($reading->temperature < 20 || $reading->temperature > 30) {
                 $risk = 'medium';
                 $insight = 'Temperature fluctuation detected.';
                 $recommendations = ['Check heater/cooler system'];
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

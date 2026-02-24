<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LatestReading;
use App\Models\WaterReading;
use App\Models\WaterAnalysis;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $latest = LatestReading::latest()->first();
        $latestAnalysis = WaterAnalysis::latest()->first();
        
        // Check if dashboard should be reset (refreshed)
        $resetDashboard = $request->session()->get('reset_dashboard', false);
        
        if ($resetDashboard) {
            // Clear the session flag
            $request->session()->forget('reset_dashboard');
            
            // Return empty data for fresh start
            return view('dashboard', [
                'latest' => null,
                'chartData' => [],
                'latestAnalysis' => $latestAnalysis
            ]);
        }
        
        // Get recent data for chart (last 20 readings)
        $chartData = WaterReading::latest()->take(20)->get()->reverse()->values();

        return view('dashboard', compact('latest', 'chartData', 'latestAnalysis'));
    }
    
    public function refresh(Request $request)
    {
        // Set session flag to reset dashboard on next page load
        $request->session()->put('reset_dashboard', true);
        
        return response()->json(['success' => true]);
    }
}

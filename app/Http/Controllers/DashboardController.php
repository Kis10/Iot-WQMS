<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LatestReading;
use App\Models\WaterQualityReading;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $latest = LatestReading::latest()->first();
        
        // Check if dashboard should be reset (refreshed)
        $resetDashboard = $request->session()->get('reset_dashboard', false);
        
        if ($resetDashboard) {
            // Clear the session flag
            $request->session()->forget('reset_dashboard');
            
            // Return empty data for fresh start
            return view('dashboard', [
                'latest' => null,
                'chartData' => []
            ]);
        }
        
        // Get recent data for chart (last 20 readings)
        $chartData = WaterQualityReading::latest()->take(20)->get()->reverse();

        return view('dashboard', compact('latest', 'chartData'));
    }
    
    public function refresh(Request $request)
    {
        // Set session flag to reset dashboard on next page load
        $request->session()->put('reset_dashboard', true);
        
        return response()->json(['success' => true]);
    }
}

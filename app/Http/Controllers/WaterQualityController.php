<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterReading;
use App\Models\WaterAnalysis;
use App\Models\Device;
use Ably\AblyRest;

class WaterQualityController extends Controller
{
    // Dashboard
    public function dashboard(Request $request)
    {
        $resetDashboard = (bool) $request->session()->pull('reset_dashboard', false);

        if ($resetDashboard) {
            $latest = null;
            $chartData = collect();
            $devices = Device::orderBy('device_id')->get();
            $latestAnalysis = null;

            return view('dashboard', compact('latest', 'chartData', 'latestAnalysis', 'devices', 'resetDashboard'));
        }

        $latest = WaterReading::with('device')->latest()->first();
        // Fetch last 20 readings for chart
        $chartData = WaterReading::orderBy('created_at', 'asc')->limit(20)->get();
        $devices = Device::orderBy('device_id')->get();
        
        // Get latest AI analysis
        $latestAnalysis = WaterAnalysis::with('waterReading')
            ->latest('analyzed_at')
            ->first();

        $resetDashboard = false;
        
        return view('dashboard', compact('latest', 'chartData', 'latestAnalysis', 'devices', 'resetDashboard'));
    }

    // History
    public function history()
    {
        $readings = WaterReading::orderBy('created_at', 'desc')->paginate(10);
        return view('history', compact('readings'));
    }

    // Alerts
    public function alerts()
    {
        // Get all abnormal readings
        $alerts = WaterReading::where(function($query) {
            // Turbidity: anything > 25 is concerning
            $query->where('turbidity', '>', 25)
            // TDS: anything > 500 is concerning
            ->orWhere('tds', '>', 500)
            // pH: anything < 6.0 or > 8.0 is concerning
            ->orWhere('ph', '<', 6.0)
            ->orWhere('ph', '>', 8.0)
            // Temperature: < 15 or > 32 is concerning
            ->orWhere('temperature', '<', 15)
            ->orWhere('temperature', '>', 32);
        })->orderBy('created_at', 'desc')->paginate(10);

        return view('alerts', compact('alerts'));
    }

    // Store sensor reading (from ESP32)
    public function storeReading(Request $request)
    {
        $validated = $request->validate([
            'device_id'   => 'required|string',
            'turbidity'   => 'required|numeric',
            'tds'         => 'required|numeric',
            'ph'          => 'required|numeric',
            'temperature' => 'required|numeric',
            'humidity'    => 'required|numeric',
        ]);

        Device::firstOrCreate(
            ['device_id' => $validated['device_id']],
            ['species' => config('aquaculture.default_species', 'tilapia')]
        );

        $reading = WaterReading::create($validated);

        $ablyKey = config('services.ably.key');
        $ablyChannel = config('services.ably.channel', 'water-readings');
        if ($ablyKey) {
            try {
                $ably = new AblyRest($ablyKey);
                $channel = $ably->channels->get($ablyChannel);
                $channel->publish('reading', [
                    'id' => $reading->id,
                    'device_id' => $reading->device_id,
                    'turbidity' => (float) $reading->turbidity,
                    'tds' => (float) $reading->tds,
                    'ph' => (float) $reading->ph,
                    'temperature' => (float) $reading->temperature,
                    'humidity' => (float) $reading->humidity,
                    'created_at' => $reading->created_at?->toIso8601String(),
                ]);
            } catch (\Throwable $e) {
                // Fail silently to avoid breaking ingestion
            }
        }

        return response()->json([
            'success' => true,
            'reading' => $reading
        ]);
    }

    public function refresh(Request $request)
    {
        $request->session()->put('reset_dashboard', true);

        return response()->json(['success' => true]);
    }

    public function chartData()
    {
        $chartData = WaterReading::orderBy('created_at', 'asc')->limit(20)->get();

        return response()->json($chartData);
    }

    public function destroyReading(WaterReading $reading)
    {
        $reading->delete();

        return redirect()
            ->route('history')
            ->with('status', 'Reading deleted.');
    }

    public function destroyReadings(Request $request)
    {
        $validated = $request->validate([
            'reading_ids' => ['required', 'array'],
            'reading_ids.*' => ['integer'],
        ]);

        WaterReading::whereIn('id', $validated['reading_ids'])->delete();

        return redirect()
            ->route('history')
            ->with('status', 'Selected readings deleted.');
    }
}

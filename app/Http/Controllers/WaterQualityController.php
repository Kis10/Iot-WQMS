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
    public function history(Request $request)
    {
        $query = WaterReading::query();

        // sort_by (sensor) and order
        if ($request->has('sort_by') && in_array($request->sort_by, ['ph', 'turbidity', 'tds', 'temperature', 'humidity', 'created_at'])) {
            $order = $request->get('order', 'desc');
            $query->orderBy($request->sort_by, $order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $readings = $query->paginate(10);
        return view('history', compact('readings'));
    }

    // Show single history reading details
    public function show(WaterReading $reading)
    {
        $location = \App\Models\LandingContent::where('key', 'contact_location')->value('value') ?? 'Po-Ok, Hinoba-an, Negros Occidental';
        return view('history.show', compact('reading', 'location'));
    }

    // Alerts
    public function alerts(Request $request)
    {
        // Get all abnormal readings
        $query = WaterReading::where(function($q) {
            $q->where('turbidity', '>', 25)
            ->orWhere('tds', '>', 500)
            ->orWhere('ph', '<', 6.0)
            ->orWhere('ph', '>', 8.0)
            ->orWhere('temperature', '<', 15)
            ->orWhere('temperature', '>', 32);
        });

        // Sorting
        if ($request->has('sort_by') && in_array($request->sort_by, ['ph', 'turbidity', 'tds', 'temperature', 'humidity', 'created_at'])) {
            $order = $request->get('order', 'desc');
            $query->orderBy($request->sort_by, $order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $alerts = $query->paginate(10);

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

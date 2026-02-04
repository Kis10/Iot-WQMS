<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\WaterReading;
use Illuminate\Http\Request;

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

        $reading = WaterReading::create([
            'device_id' => $validated['device_id'],
            'turbidity' => $validated['turbidity'],
            'tds' => $validated['tds'],
            'ph' => $validated['ph'],
            'temperature' => $validated['temperature'] ?? null,
            'humidity' => $validated['humidity'] ?? null,
            'no_water_detected' => $validated['no_water_detected'] ?? false,
        ]);

        return response()->json([
            'success' => true,
            'reading_id' => $reading->id,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    public function updateSpecies(Request $request)
    {
        $speciesKeys = array_keys(config('aquaculture.species', []));

        $validated = $request->validate([
            'device_id' => ['required', 'string'],
            'species' => ['required', Rule::in($speciesKeys)],
        ]);

        $device = Device::firstOrCreate(['device_id' => $validated['device_id']]);
        $device->species = $validated['species'];
        $device->save();

        return back()->with('status', 'Species profile updated.');
    }
}

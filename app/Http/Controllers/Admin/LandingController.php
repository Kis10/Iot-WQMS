<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandingContent;
use Illuminate\Support\Facades\Storage;

class LandingController extends Controller
{
    public function index()
    {
        $contents = LandingContent::all()->keyBy('key');
        return view('admin.landing.index', compact('contents'));
    }

    public function update(Request $request)
    {
        // Handle Text Inputs
        $inputs = $request->except(['_token', 'hero_bg_file']);
        
        foreach ($inputs as $key => $value) {
            LandingContent::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Handle File Upload (Hero BG)
        if ($request->hasFile('hero_bg_file')) {
            $file = $request->file('hero_bg_file');
            $path = $file->store('landing', 'public');
            
            LandingContent::updateOrCreate(
                ['key' => 'hero_bg'],
                ['image' => 'storage/' . $path] // Store relative path
            );
        } 
        // Handle URL Input for Image (if provided and no file)
        elseif ($request->input('hero_bg_url')) {
             LandingContent::updateOrCreate(
                ['key' => 'hero_bg'],
                ['image' => $request->input('hero_bg_url')]
            );
        }

        return redirect()->route('admin.landing.index')->with('status', 'Landing Page Updated Successfully!');
    }
}

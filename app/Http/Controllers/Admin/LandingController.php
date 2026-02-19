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
        // 1. Define Image Keys
        $imageKeys = [
            'hero_bg', 
            'team1_img', 'team1_img_hover', 
            'team2_img', 'team2_img_hover', 
            'team3_img', 'team3_img_hover', 
            'team4_img', 'team4_img_hover'
        ];

        // 2. Separate Inputs
        // $inputs will contain all text fields sent by the form.
        // We exclude _token, _method, and any _file/_url parameters related to images.
        $exclude = ['_token', '_method'];
        foreach ($imageKeys as $key) {
            $exclude[] = $key . '_file';
            $exclude[] = $key . '_url';
        }
        
        $textInputs = $request->except($exclude);

        // 3. Process Text Updates
        foreach ($textInputs as $key => $value) {
            // Skip if this is actually an image key that slipped through (shouldn't happen due to JS logic, but safety first)
            if (in_array($key, $imageKeys)) continue;

            LandingContent::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // 4. Process Image Updates
        foreach ($imageKeys as $key) {
            $fileInput = $key . '_file';
            $urlInput = $key . '_url';
            $updateData = [];

            // A. Handle File Upload
            if ($request->hasFile($fileInput)) {
                $file = $request->file($fileInput);
                if ($file->isValid()) {
                    $path = $file->store('landing', 'public');
                    $updateData['image'] = 'storage/' . $path;
                }
            } 
            // B. Handle URL String (only if no file uploaded)
            elseif ($request->filled($urlInput)) {
                $updateData['image'] = $request->input($urlInput);
            }

            // Only perform DB update if we have new image data
            if (!empty($updateData)) {
                LandingContent::updateOrCreate(
                    ['key' => $key],
                    $updateData
                );
            }
        }

        // Return Success
        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['status' => 'success']);
        }

        return redirect()->route('admin.landing.index')->with('status', 'Landing Page Updated Successfully!');
    }
}

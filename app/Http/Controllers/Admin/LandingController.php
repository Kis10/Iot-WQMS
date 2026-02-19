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
        // Gather all text inputs (exclude special fields)
        // Gather all text inputs (exclude special fields)
        $exclude = ['_token', '_method'];
        $imageKeys = ['hero_bg', 'team1_img', 'team2_img', 'team3_img', 'team4_img'];
        foreach ($imageKeys as $key) {
            $exclude[] = $key . '_file';
            $exclude[] = $key . '_url';
        }
        $inputs = $request->except($exclude);

        foreach ($inputs as $key => $value) {
            LandingContent::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Handle Multiple File Uploads
        $imageKeys = ['hero_bg', 'team1_img', 'team2_img', 'team3_img', 'team4_img'];

        foreach ($imageKeys as $key) {
            $fileInput = $key . '_file';
            $urlInput = $key . '_url';

            if ($request->hasFile($fileInput)) {
                $file = $request->file($fileInput);
                $path = $file->store('landing', 'public');
                LandingContent::updateOrCreate(['key' => $key], ['image' => 'storage/' . $path]);
            } elseif ($request->input($urlInput)) {
                LandingContent::updateOrCreate(['key' => $key], ['image' => $request->input($urlInput)]);
            }
        }

        // For AJAX requests, return JSON
        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['status' => 'success']);
        }

        return redirect()->route('admin.landing.index')->with('status', 'Landing Page Updated Successfully!');
    }
}

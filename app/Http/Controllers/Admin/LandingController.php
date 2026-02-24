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
                \Log::info("Processing file upload for key: " . $fileInput);
                $file = $request->file($fileInput);
                if ($file->isValid()) {
                    try {
                        // 1. Determine Folder
                        $folder = 'landing';
                        if ($key === 'hero_bg') {
                            $folder = 'landing/hero';
                        } elseif (str_contains($key, 'hover')) {
                            $folder = 'landing/hover';
                        } elseif (str_contains($key, 'img')) {
                            $folder = 'landing/main';
                        }

                        // 2. Store Locally (Backup)
                        $path = $file->store($folder, 'public');
                        \Log::info("Stored local backup at: " . $path);

                        // 3. Upload to Cloudinary (Primary for production)
                        $result = $file->storeOnCloudinary($folder);
                        $updateData['image'] = $result->getSecurePath();
                        \Log::info("Uploaded to Cloudinary: " . $updateData['image'] . " in folder: " . $folder);
                    } catch (\Exception $e) {
                        \Log::error("Cloudinary Upload Error: " . $e->getMessage());
                    }
                } else {
                    \Log::error("File is not valid for key: " . $fileInput);
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

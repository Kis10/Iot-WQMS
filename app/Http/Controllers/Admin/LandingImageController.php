<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Models\LandingContent;

class LandingImageController extends Controller
{
    /**
     * Supported image keys for the landing page.
     */
    public static $imageKeys = [
        'hero_bg', 
        'team1_img', 'team1_img_hover', 
        'team2_img', 'team2_img_hover', 
        'team3_img', 'team3_img_hover', 
        'team4_img', 'team4_img_hover'
    ];

    /**
     * Handle dedicated image uploads via individual routes.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'key' => 'required|string',
        ]);

        $key = $request->input('key');
        $file = $request->file('image');

        $path = $this->saveLocalFile($file, $key);

        if ($path) {
            LandingContent::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $path,
                    'image' => $path,
                    'type' => 'image'
                ]
            );

            return response()->json([
                'status' => 'success',
                'path' => $path,
                'url' => asset($path),
                'key' => $key
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Upload failed'], 500);
    }

    /**
     * Helper to process bulk image updates from the main LandingController.
     */
    public function processBulkImages(Request $request)
    {
        $updated = [];
        
        foreach (self::$imageKeys as $key) {
            $fileInput = $key . '_file';
            $urlInput = $key . '_url';
            $updateData = [];

            if ($request->hasFile($fileInput)) {
                $path = $this->saveLocalFile($request->file($fileInput), $key);
                if ($path) {
                    $updateData = ['image' => $path, 'value' => $path, 'type' => 'image'];
                }
            } elseif ($request->filled($urlInput)) {
                $url = $request->input($urlInput);
                // Only download if it looks like a remote URL
                if (filter_var($url, FILTER_VALIDATE_URL) && !str_contains($url, request()->getHost())) {
                    $path = $this->saveFromUrl($url, $key);
                    if ($path) {
                        $updateData = ['image' => $path, 'value' => $path, 'type' => 'image'];
                    }
                }
            }

            if (!empty($updateData)) {
                LandingContent::updateOrCreate(['key' => $key], $updateData);
                $updated[] = $key;
            }
        }

        return $updated;
    }

    /**
     * Save a file to the local public directory.
     */
    private function saveLocalFile($file, $key)
    {
        try {
            $ext = $file->getClientOriginalExtension() ?: 'png';
            $filename = $key . '_' . time() . '.' . $ext;
            $subfolder = $this->getSubfolder($key);
            $directory = public_path('img/' . $subfolder);

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true);
            }

            $file->move($directory, $filename);
            return 'img/' . $subfolder . '/' . $filename;
        } catch (\Exception $e) {
            Log::error("Error saving local file for {$key}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch and save an image from a URL.
     */
    private function saveFromUrl($url, $key)
    {
        try {
            $imageContents = @file_get_contents($url);
            if ($imageContents === false) return null;

            $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $key . '_' . time() . '.' . $ext;
            $subfolder = $this->getSubfolder($key);
            $directory = public_path('img/' . $subfolder);

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true);
            }

            $localPath = 'img/' . $subfolder . '/' . $filename;
            file_put_contents(public_path($localPath), $imageContents);
            return $localPath;
        } catch (\Exception $e) {
            Log::error("Error saving URL image for {$key}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Mapping of keys to folders.
     */
    private function getSubfolder(string $key): string
    {
        if (str_contains($key, 'team')) {
            return str_contains($key, 'hover') ? 'members/hover' : 'members/display';
        }
        return 'uploads';
    }
}

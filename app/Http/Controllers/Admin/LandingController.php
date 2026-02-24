<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandingContent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $exclude = ['_token', '_method'];
        foreach ($imageKeys as $key) {
            $exclude[] = $key . '_file';
            $exclude[] = $key . '_url';
        }
        
        $textInputs = $request->except($exclude);

        // 3. Process Text Updates
        foreach ($textInputs as $key => $value) {
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

            // Determine Cloudinary folder
            $folder = 'Landing';
            if ($key === 'hero_bg') {
                $folder = 'Landing/Hero';
            } elseif (str_contains($key, 'hover')) {
                $folder = 'Landing/Hover';
            } elseif (str_contains($key, 'img')) {
                $folder = 'Landing/Main';
            }

            // A. Handle File Upload
            if ($request->hasFile($fileInput)) {
                Log::info("Processing file upload for key: " . $fileInput);
                $file = $request->file($fileInput);
                if ($file->isValid()) {
                    try {
                        // Store Locally (Backup)
                        $path = $file->store($folder, 'public');
                        Log::info("Stored local backup at: " . $path);

                        // Upload to Cloudinary (Primary)
                        $result = $file->storeOnCloudinary($folder);
                        $updateData['image'] = $result->getSecurePath();
                        Log::info("Uploaded to Cloudinary: " . $updateData['image'] . " in folder: " . $folder);
                    } catch (\Exception $e) {
                        Log::error("Cloudinary Upload Error: " . $e->getMessage());
                        // Fallback: use local path if Cloudinary fails
                        if (isset($path)) {
                            $updateData['image'] = asset('storage/' . $path);
                            Log::info("Fallback to local: " . $updateData['image']);
                        }
                    }
                } else {
                    Log::error("File is not valid for key: " . $fileInput);
                }
            } 
            // B. Handle URL String — also upload to Cloudinary for permanent backup
            elseif ($request->filled($urlInput)) {
                $url = $request->input($urlInput);
                
                try {
                    // Download the URL image to a temp file, then upload to Cloudinary
                    $tempFile = tempnam(sys_get_temp_dir(), 'landing_');
                    $imageContents = file_get_contents($url);
                    
                    if ($imageContents !== false) {
                        file_put_contents($tempFile, $imageContents);
                        
                        // Upload to Cloudinary
                        $result = cloudinary()->upload($tempFile, [
                            'folder' => $folder,
                        ]);
                        $updateData['image'] = $result->getSecurePath();
                        Log::info("URL image uploaded to Cloudinary: " . $updateData['image'] . " (from: {$url})");
                        
                        // Also save locally
                        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                        $localName = $folder . '/' . $key . '_' . time() . '.' . $ext;
                        Storage::disk('public')->put($localName, $imageContents);
                        Log::info("URL image saved locally at: " . $localName);
                        
                        @unlink($tempFile);
                    } else {
                        // If download fails, just store the URL directly
                        $updateData['image'] = $url;
                        Log::warning("Could not download URL, storing raw URL: " . $url);
                    }
                } catch (\Exception $e) {
                    // Fallback: store the URL directly if Cloudinary upload fails
                    $updateData['image'] = $url;
                    Log::error("URL-to-Cloudinary failed: " . $e->getMessage() . " — storing raw URL");
                }
            }

            // Only perform DB update if we have new image data
            if (!empty($updateData)) {
                LandingContent::updateOrCreate(
                    ['key' => $key],
                    $updateData
                );
            }
        }

        // 5. Auto-update the Seeder file so photos survive database resets
        $this->syncSeeder();

        // Return Success
        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['status' => 'success']);
        }

        return redirect()->route('admin.landing.index')->with('status', 'Landing Page Updated Successfully!');
    }

    /**
     * Auto-sync the LandingContentSeeder with the current database state.
     * This ensures that all uploaded photos (URLs) are preserved in Git,
     * so they can be restored after a database reset.
     */
    private function syncSeeder(): void
    {
        try {
            $allContent = LandingContent::all();
            
            $entries = [];
            foreach ($allContent as $item) {
                $entry = [
                    'key' => $item->key,
                    'value' => $item->value,
                ];
                
                if ($item->image) {
                    $entry['image'] = $item->image;
                }
                
                if ($item->type) {
                    $entry['type'] = $item->type;
                }
                
                $entries[] = $entry;
            }

            // Build PHP code for the seeder
            $php = "<?php\n\n";
            $php .= "namespace Database\\Seeders;\n\n";
            $php .= "use Illuminate\\Database\\Seeder;\n";
            $php .= "use App\\Models\\LandingContent;\n\n";
            $php .= "class LandingContentSeeder extends Seeder\n";
            $php .= "{\n";
            $php .= "    /**\n";
            $php .= "     * Seed the landing page content.\n";
            $php .= "     * AUTO-GENERATED — This file is automatically updated when you save changes\n";
            $php .= "     * in the admin landing page editor. Do not edit manually.\n";
            $php .= "     *\n";
            $php .= "     * Last updated: " . now()->setTimezone('Asia/Manila')->format('M j, Y g:i A') . "\n";
            $php .= "     */\n";
            $php .= "    public function run(): void\n";
            $php .= "    {\n";
            $php .= "        \$data = [\n";

            foreach ($entries as $entry) {
                $php .= "            [\n";
                foreach ($entry as $k => $v) {
                    if ($v === null) {
                        $php .= "                '{$k}' => null,\n";
                    } else {
                        $escaped = str_replace("'", "\\'", $v);
                        $php .= "                '{$k}' => '{$escaped}',\n";
                    }
                }
                $php .= "            ],\n";
            }

            $php .= "        ];\n\n";
            $php .= "        foreach (\$data as \$item) {\n";
            $php .= "            LandingContent::updateOrCreate(\n";
            $php .= "                ['key' => \$item['key']],\n";
            $php .= "                \$item\n";
            $php .= "            );\n";
            $php .= "        }\n";
            $php .= "    }\n";
            $php .= "}\n";

            $seederPath = database_path('seeders/LandingContentSeeder.php');
            file_put_contents($seederPath, $php);
            Log::info("LandingContentSeeder auto-synced with " . count($entries) . " entries.");
        } catch (\Exception $e) {
            Log::error("Failed to sync seeder: " . $e->getMessage());
        }
    }
}

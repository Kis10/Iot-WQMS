<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandingContent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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

            // A. Handle File Upload (Save to S3 Display Folder)
            if ($request->hasFile($fileInput)) {
                $file = $request->file($fileInput);
                if ($file->isValid()) {
                    try {
                        $ext = $file->getClientOriginalExtension() ?: 'png';
                        $filename = $key . '_' . time() . '.' . $ext;
                        $bucketPath = 'members/display/' . $filename;

                        // Upload directly to S3 Bucket
                        Storage::disk('t3_storage')->put($bucketPath, file_get_contents($file->getPathname()));
                        
                        // Generate full, absolute URL (Primary)
                        $url = config('filesystems.disks.t3_storage.url') . '/' . $bucketPath;
                        $updateData['image'] = $url;
                        
                        // B. Backup to Cloudinary
                        try {
                            Log::info("Attempting Cloudinary backup for {$key}...");
                            $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), [
                                'folder' => $folder,
                                'public_id' => $key . '_' . time()
                            ]);
                            $updateData['value'] = $cloudinaryUpload->getSecurePath(); // Backup URL
                            Log::info("Cloudinary Backup Success: " . $updateData['value']);
                        } catch (\Exception $ce) {
                            Log::error("Cloudinary Backup Failed for {$key}: " . $ce->getMessage());
                            $updateData['value'] = $url; // Fallback to T3 URL if backup fails
                        }

                        Log::info("Saved Cloud URL: " . $url);
                    } catch (\Exception $e) {
                        Log::error("S3 File Upload Error for {$key}: " . $e->getMessage());
                        return response()->json(['status' => 'error', 'message' => 'S3 Upload Failed: ' . $e->getMessage()], 500);
                    }
                }
            } 
            // B. Handle URL String (Save to S3 Hover Folder)
            elseif ($request->filled($urlInput)) {
                $url = $request->input($urlInput);
                try {
                    $imageContents = @file_get_contents($url);
                    if ($imageContents !== false) {
                        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                        $filename = $key . '_' . time() . '.' . $ext;
                        $bucketPath = 'members/hover/' . $filename;

                        // Upload to Cloud
                        Storage::disk('t3_storage')->put($bucketPath, $imageContents);
                        
                        // Generate full, absolute URL (Primary)
                        $url = config('filesystems.disks.t3_storage.url') . '/' . $bucketPath;
                        $updateData['image'] = $url;
                        
                        // B. Backup to Cloudinary
                        try {
                            Log::info("Attempting Cloudinary backup via URL for {$key}...");
                            $cloudinaryUpload = Cloudinary::upload($url, [ // Cloudinary can upload from a URL
                                'folder' => $folder,
                                'public_id' => $key . '_' . time()
                            ]);
                            $updateData['value'] = $cloudinaryUpload->getSecurePath(); // Backup URL
                            Log::info("Cloudinary Backup Success: " . $updateData['value']);
                        } catch (\Exception $ce) {
                            Log::error("Cloudinary Backup Failed for URL {$key}: " . $ce->getMessage());
                            $updateData['value'] = $url;
                        }

                        Log::info("Saved Cloud URL from link: " . $url);
                    } else {
                        $updateData['image'] = $url;
                    }
                } catch (\Exception $e) {
                    Log::error("S3 URL Processing Error for {$key}: " . $e->getMessage());
                    // Don't fail the whole request for a URL error, just fallback
                    $updateData['image'] = $url;
                }
            }

            // Only perform DB update if we have new image data
            if (!empty($updateData)) {
                $updateData['type'] = 'image'; // Ensure type is set
                LandingContent::updateOrCreate(
                    ['key' => $key],
                    $updateData
                );
            }
        }

        // 5. Skip syncSeeder on Railway to avoid ephemeral disk issues
        // $this->syncSeeder();

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

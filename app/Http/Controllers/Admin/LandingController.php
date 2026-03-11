<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LandingContent;
use Illuminate\Support\Facades\Log;
use Cloudinary\Cloudinary;

class LandingController extends Controller
{
    public function index()
    {
        $contents = LandingContent::all()->keyBy('key');
        // Ensure it's an object even if empty for Alpine.js
        if ($contents->isEmpty()) {
            $contents = (object)[];
        }
        return view('admin.landing.index', compact('contents'));
    }

    public function update(Request $request)
    {
        Log::info("LandingController: update method hit.");
        
        // 1. Define Image/Media Keys
        $imageKeys = [
            'hero_bg', 'project_video',
            'team1_img', 'team1_img_hover', 
            'team2_img', 'team2_img_hover', 
            'team3_img', 'team3_img_hover', 
            'team4_img', 'team4_img_hover',
            'slider1_img', 'slider2_img', 'slider3_img', 'slider4_img', 'slider5_img'
        ];

        // 2. Separate Inputs
        $exclude = ['_token', '_method', 'deleted_media'];
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

        // 3.5. Process explicit media deletions
        if ($request->has('deleted_media')) {
            $deletedKeys = $request->input('deleted_media');
            if (is_array($deletedKeys)) {
                foreach ($deletedKeys as $delKey) {
                    if (in_array($delKey, $imageKeys)) {
                        Log::info("LandingController: Clearing media for key: {$delKey}");
                        LandingContent::updateOrCreate(
                            ['key' => $delKey],
                            ['image' => null, 'value' => null, 'type' => 'image']
                        );
                    }
                }
            }
        }

        // 4. Process Image Updates — Upload to Local AND Cloudinary
        Log::info("LandingController: Processing images...");
        foreach ($imageKeys as $key) {
            $fileInput = $key . '_file';
            $urlInput = $key . '_url';
            $updateData = [];

            if ($request->hasFile($fileInput)) {
                Log::info("LandingController: Found file for key: {$fileInput}");
                $file = $request->file($fileInput);
                
                // 1. Save locally
                $folder = $this->resolveImageSubfolder($key);
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('img/' . $folder), $filename);
                $localPath = 'img/' . $folder . '/' . $filename;
                
                // 2. Upload to Cloudinary (using the moved local file)
                $cloudUrl = null;
                if ($this->isCloudinaryActive()) {
                    $cloudUrl = $this->uploadToCloudinary(public_path($localPath), $key);
                }
                
                $updateData = [
                    'image' => $cloudUrl ?? $localPath, // Cloud fallback to local
                    'value' => $localPath,              // value is always local
                    'type' => 'image',
                ];

            } elseif ($request->filled($urlInput)) {
                Log::info("LandingController: Found URL for key: {$urlInput}");
                $url = $request->input($urlInput);
                if (filter_var($url, FILTER_VALIDATE_URL) && !str_contains($url, request()->getHost())) {
                    $cloudUrl = null;
                    if ($this->isCloudinaryActive()) {
                        $cloudUrl = $this->uploadUrlToCloudinary($url, $key);
                    }
                    
                    $updateData = [
                        'image' => $cloudUrl ?? $url,
                        'value' => $url, // local URL is just the source URL
                        'type' => 'image',
                    ];
                }
            }

            if (!empty($updateData)) {
                Log::info("LandingController: Saved image for key: {$key} - URL: " . $updateData['image']);
                LandingContent::updateOrCreate(['key' => $key], $updateData);
            }
        }

        // 5. Skip syncSeeder on Railway
        // $this->syncSeeder();

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'cloudinary_active' => (bool)(config('cloudinary.cloud_url') || env('CLOUDINARY_URL'))
            ]);
        }

        return redirect()->route('admin.landing.index')->with('status', 'Landing Page Updated Successfully!');
    }

    /**
     * Upload a file directly to Cloudinary.
     */
    private function uploadToCloudinary($file, string $key): ?string
    {
        try {
            $folder = $this->resolveImageSubfolder($key);
            $publicId = $key . '_' . time();

            $cloudinary = $this->getCloudinaryInstance();
            $path = is_string($file) ? $file : $file->getRealPath();

            $resourceType = 'auto';
            if ($key === 'project_video' || (!is_string($file) && str_starts_with($file->getMimeType(), 'video/'))) {
                $resourceType = 'video';
            }

            $result = $cloudinary->uploadApi()->upload($path, [
                'folder' => $folder,
                'public_id' => $publicId,
                'overwrite' => true,
                'resource_type' => $resourceType,
            ]);

            $url = $result['secure_url'] ?? $result['url'] ?? null;
            Log::info("LandingController: Uploaded to Cloudinary for {$key}: {$url}");
            return $url;
        } catch (\Exception $e) {
            Log::error("LandingController: Cloudinary upload failed for {$key}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Upload an image from a URL directly to Cloudinary.
     * Cloudinary can fetch remote URLs natively — no need to download first.
     */
    private function uploadUrlToCloudinary(string $url, string $key): ?string
    {
        try {
            $folder = $this->resolveImageSubfolder($key);
            $publicId = $key . '_' . time();

            $cloudinary = $this->getCloudinaryInstance();
            // Cloudinary can accept a remote URL directly!
            $result = $cloudinary->uploadApi()->upload($url, [
                'folder' => $folder,
                'public_id' => $publicId,
                'overwrite' => true,
                'resource_type' => 'auto',
            ]);

            $cloudUrl = $result['secure_url'] ?? $result['url'] ?? null;
            Log::info("LandingController: Uploaded URL to Cloudinary for {$key}: {$cloudUrl}");
            return $cloudUrl;
        } catch (\Exception $e) {
            Log::error("LandingController: Cloudinary URL upload failed for {$key}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get a configured Cloudinary instance.
     * We use the direct SDK because the Laravel Facade had initialization issues.
     */
    private function getCloudinaryInstance(): Cloudinary
    {
        return new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    private function isCloudinaryActive(): bool
    {
        return (bool)(config('cloudinary.cloud_url') || env('CLOUDINARY_URL'));
    }

    private function resolveImageSubfolder(string $key): string
    {
        if ($key === 'project_video') return 'videos';
        
        if (str_starts_with($key, 'team')) {
            if (str_ends_with($key, '_img_hover')) return 'members/hover';
            return 'members/display';
        }
        if (str_starts_with($key, 'slider')) {
            return 'sliders';
        }

        return 'uploads';
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

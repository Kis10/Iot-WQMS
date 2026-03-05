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

        // 4. Process Image Updates — Upload directly to Cloudinary
        Log::info("LandingController: Processing images...");
        foreach ($imageKeys as $key) {
            $fileInput = $key . '_file';
            $urlInput = $key . '_url';
            $updateData = [];

            if ($request->hasFile($fileInput)) {
                Log::info("LandingController: Found file for key: {$fileInput}");
                $cloudUrl = $this->uploadToCloudinary($request->file($fileInput), $key);
                if ($cloudUrl) {
                    $updateData = [
                        'image' => $cloudUrl,
                        'value' => $cloudUrl,
                        'type' => 'image',
                    ];
                }
            } elseif ($request->filled($urlInput)) {
                Log::info("LandingController: Found URL for key: {$urlInput}");
                $url = $request->input($urlInput);
                if (filter_var($url, FILTER_VALIDATE_URL) && !str_contains($url, request()->getHost())) {
                    $cloudUrl = $this->uploadUrlToCloudinary($url, $key);
                    if ($cloudUrl) {
                        $updateData = [
                            'image' => $cloudUrl,
                            'value' => $cloudUrl,
                            'type' => 'image',
                        ];
                    }
                }
            }

            if (!empty($updateData)) {
                Log::info("LandingController: Saved to Cloudinary for key: {$key} - URL: " . $updateData['image']);
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
            $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => $folder,
                'public_id' => $publicId,
                'overwrite' => true,
                'resource_type' => 'image',
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
                'resource_type' => 'image',
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
        if (str_starts_with($key, 'team')) {
            if (str_ends_with($key, '_img_hover')) return 'members/hover';
            return 'members/display';
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

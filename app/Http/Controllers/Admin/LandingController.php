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

        // 4. Process Image Updates (Delegated to LandingImageController)
        $imageController = new LandingImageController();
        $imageController->processBulkImages($request);

        // 5. Skip syncSeeder on Railway to avoid ephemeral disk issues
        // $this->syncSeeder();

        // Return Success with Status
        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'status' => 'success',
                'cloudinary_active' => (bool)(config('cloudinary.cloud_url') || env('CLOUDINARY_URL'))
            ]);
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

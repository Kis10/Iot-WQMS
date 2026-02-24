<?php

namespace App\Console\Commands;

use App\Models\LandingContent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupPhotosToCloudinary extends Command
{
    protected $signature = 'landing:backup-photos';
    protected $description = 'Backup all existing landing page photos to Cloudinary and sync the seeder file';

    public function handle()
    {
        $imageKeys = [
            'hero_bg',
            'team1_img', 'team1_img_hover',
            'team2_img', 'team2_img_hover',
            'team3_img', 'team3_img_hover',
            'team4_img', 'team4_img_hover',
        ];

        $this->info('🔍 Scanning database for existing photos...');
        $processed = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($imageKeys as $key) {
            $content = LandingContent::where('key', $key)->first();

            if (!$content || !$content->image) {
                $this->line("  ⏭  {$key}: No image found, skipping.");
                $skipped++;
                continue;
            }

            $imageUrl = $content->image;

            // Already on Cloudinary? Skip.
            if (str_contains($imageUrl, 'cloudinary.com') || str_contains($imageUrl, 'res.cloudinary')) {
                $this->info("  ✅ {$key}: Already on Cloudinary.");
                $processed++;
                continue;
            }

            // Determine folder
            $folder = 'Landing';
            if ($key === 'hero_bg') {
                $folder = 'Landing/Hero';
            } elseif (str_contains($key, 'hover')) {
                $folder = 'Landing/Hover';
            } elseif (str_contains($key, 'img')) {
                $folder = 'Landing/Main';
            }

            $this->line("  ⬆  {$key}: Uploading to Cloudinary ({$folder})...");

            try {
                // Download the image
                $imageContents = @file_get_contents($imageUrl);

                if ($imageContents === false) {
                    // Try as a local/relative path
                    $localPath = public_path($imageUrl);
                    if (file_exists($localPath)) {
                        $imageContents = file_get_contents($localPath);
                    } else {
                        // Try storage path
                        $storagePath = str_replace(asset('storage/'), '', $imageUrl);
                        if (Storage::disk('public')->exists($storagePath)) {
                            $imageContents = Storage::disk('public')->get($storagePath);
                        }
                    }
                }

                if ($imageContents === false || empty($imageContents)) {
                    $this->error("  ❌ {$key}: Could not download image from: {$imageUrl}");
                    $failed++;
                    continue;
                }

                // Save to temp file
                $tempFile = tempnam(sys_get_temp_dir(), 'landing_backup_');
                file_put_contents($tempFile, $imageContents);

                // Upload to Cloudinary
                $result = cloudinary()->upload($tempFile, [
                    'folder' => $folder,
                    'public_id' => $key . '_' . time(),
                ]);

                $cloudinaryUrl = $result->getSecurePath();

                // Update database
                $content->update(['image' => $cloudinaryUrl]);

                // Save local backup too
                $ext = 'jpg';
                $localName = $folder . '/' . $key . '_backup.' . $ext;
                Storage::disk('public')->put($localName, $imageContents);

                @unlink($tempFile);

                $this->info("  ✅ {$key}: Uploaded → {$cloudinaryUrl}");
                $processed++;

            } catch (\Exception $e) {
                $this->error("  ❌ {$key}: Upload failed — " . $e->getMessage());
                Log::error("Cloudinary backup failed for {$key}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("📊 Results: {$processed} uploaded, {$skipped} skipped, {$failed} failed");

        // Sync seeder
        $this->line('📝 Syncing seeder file...');
        $this->syncSeeder();
        $this->info('✅ LandingContentSeeder.php updated!');

        $this->newLine();
        $this->info('🎉 Done! All photos are now backed up to Cloudinary.');
        $this->line('   Run "php artisan db:seed --class=LandingContentSeeder" to restore anytime.');

        return Command::SUCCESS;
    }

    private function syncSeeder(): void
    {
        $allContent = LandingContent::all();

        $entries = [];
        foreach ($allContent as $item) {
            $entry = ['key' => $item->key, 'value' => $item->value];
            if ($item->image) $entry['image'] = $item->image;
            if ($item->type) $entry['type'] = $item->type;
            $entries[] = $entry;
        }

        $php = "<?php\n\nnamespace Database\\Seeders;\n\nuse Illuminate\\Database\\Seeder;\nuse App\\Models\\LandingContent;\n\nclass LandingContentSeeder extends Seeder\n{\n";
        $php .= "    /**\n     * AUTO-GENERATED by landing:backup-photos command.\n";
        $php .= "     * Last synced: " . now()->setTimezone('Asia/Manila')->format('M j, Y g:i A') . "\n     */\n";
        $php .= "    public function run(): void\n    {\n        \$data = [\n";

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

        $php .= "        ];\n\n        foreach (\$data as \$item) {\n";
        $php .= "            LandingContent::updateOrCreate(\n                ['key' => \$item['key']],\n                \$item\n            );\n        }\n    }\n}\n";

        file_put_contents(database_path('seeders/LandingContentSeeder.php'), $php);
    }
}

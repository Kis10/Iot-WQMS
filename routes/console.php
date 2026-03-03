<?php

use App\Jobs\AnalyzeWaterQuality;
use App\Models\LandingContent;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\WaterReading;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('analyze:water-quality', function () {
    $latest = WaterReading::latest()->first();

    if (!$latest) {
        $this->comment('No water readings found.');
        return;
    }

    AnalyzeWaterQuality::dispatchSync($latest);

    $this->info('Water quality analysis completed.');
})->purpose('Analyze the last 5 minutes of water readings.');

Schedule::command('analyze:water-quality')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Artisan::command('landing:backfill-cloudinary {--dry-run}', function () {
    $cloudinaryActive = (bool)(config('cloudinary.cloud_url') || env('CLOUDINARY_URL'));
    if (! $cloudinaryActive) {
        $this->error('Cloudinary is not configured. Set CLOUDINARY_URL in .env.');
        return 1;
    }

    $keys = [
        'hero_bg',
        'team1_img', 'team1_img_hover',
        'team2_img', 'team2_img_hover',
        'team3_img', 'team3_img_hover',
        'team4_img', 'team4_img_hover',
    ];

    $items = LandingContent::whereIn('key', $keys)->get();
    if ($items->isEmpty()) {
        $this->info('No team images found to backfill.');
        return 0;
    }

    $uploaded = 0;
    $skipped = 0;
    $errors = 0;
    $dryRun = (bool)$this->option('dry-run');

    foreach ($items as $item) {
        $localPath = $item->value ?: $item->image;
        if (! $localPath) {
            $this->line("skip {$item->key}: no local path");
            $skipped++;
            continue;
        }

        if (str_starts_with($localPath, 'http')) {
            $this->line("skip {$item->key}: local path is already a URL");
            $skipped++;
            continue;
        }

        $absolutePath = public_path($localPath);
        if (! file_exists($absolutePath)) {
            $this->warn("missing {$item->key}: {$absolutePath}");
            $errors++;
            continue;
        }

        if (str_starts_with($item->key, 'team')) {
            $subfolder = str_contains($item->key, 'hover') ? 'members/hover' : 'members/display';
        } else {
            $subfolder = 'uploads';
        }
        $folder = 'aquasense/' . $subfolder;
        $publicId = pathinfo(basename($localPath), PATHINFO_FILENAME);

        if ($dryRun) {
            $this->line("dry-run upload {$item->key} -> {$folder}/{$publicId}");
            $skipped++;
            continue;
        }

        try {
            $result = Cloudinary::uploadApi()->upload($absolutePath, [
                'folder' => $folder,
                'public_id' => $publicId,
                'overwrite' => true,
                'resource_type' => 'image',
            ]);

            $cloudUrl = $result['secure_url'] ?? $result['url'] ?? null;
            if (! $cloudUrl) {
                $this->error("upload failed {$item->key}: missing URL in response");
                $errors++;
                continue;
            }

            $item->image = $cloudUrl;
            if (! $item->value) {
                $item->value = $localPath;
            }
            if (! $item->type) {
                $item->type = 'image';
            }
            $item->save();

            $this->info("uploaded {$item->key} -> {$cloudUrl}");
            $uploaded++;
        } catch (\Exception $e) {
            $this->error("upload failed {$item->key}: {$e->getMessage()}");
            $errors++;
        }
    }

    $this->info("Backfill complete. uploaded={$uploaded} skipped={$skipped} errors={$errors}");
    return $errors > 0 ? 1 : 0;
})->purpose('Backfill team photos to Cloudinary and store local path for fallback.');

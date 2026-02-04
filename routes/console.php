<?php

use App\Jobs\AnalyzeWaterQuality;
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

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ALL LandingContent rows ===\n";
$items = App\Models\LandingContent::all();
echo "Total rows: " . $items->count() . "\n\n";
foreach ($items as $item) {
    echo "KEY: " . $item->key . "\n";
    echo "  value: " . mb_substr($item->value ?? 'NULL', 0, 100) . "\n";
    echo "  image: " . ($item->image ?? 'NULL') . "\n\n";
}

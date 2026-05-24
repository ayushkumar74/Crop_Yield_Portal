<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$crops = App\Models\Crop::all()->groupBy('name');
foreach ($crops as $name => $group) {
    echo $name . ': ' . count($group) . PHP_EOL;
}

<?php

use App\Models\Crop;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$crops = Crop::all()->groupBy('name');
foreach ($crops as $name => $group) {
    echo $name.': '.count($group).PHP_EOL;
}

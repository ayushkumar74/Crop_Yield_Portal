<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Seeder;

class CropSeeder extends Seeder
{
    public function run(): void
    {
        $path = resource_path('data/indian_crops.json');
        if (! file_exists($path)) {
            return;
        }

        $json = json_decode(file_get_contents($path), true);
        if (! is_array($json)) {
            return;
        }

        foreach ($json as $entry) {
            // Map JSON fields to DB columns. Use sensible defaults where values are missing.
            Crop::create([
                'name' => $entry['name'] ?? 'Unknown',
                'min_temp' => $entry['ideal_temperature']['min'] ?? 15,
                'max_temp' => $entry['ideal_temperature']['max'] ?? 35,
                'min_rainfall' => $entry['rainfall']['min'] ?? 400,
                'max_rainfall' => $entry['rainfall']['max'] ?? 1500,
                'min_humidity' => $entry['humidity']['min'] ?? 30,
                'max_humidity' => $entry['humidity']['max'] ?? 90,
                'base_yield' => $entry['base_yield'] ?? ($entry['expected_yield'] ?? 5.0),
            ]);
        }
    }
}

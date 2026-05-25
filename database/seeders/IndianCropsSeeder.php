<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Seeder;

class IndianCropsSeeder extends Seeder
{
    public function run()
    {
        $path = resource_path('data/indian_crops.json');
        if (! file_exists($path)) {
            $this->command->warn('indian_crops.json not found at '.$path);

            return;
        }

        $raw = file_get_contents($path);
        $list = json_decode($raw, true) ?: [];

        // Avoid duplicates: match by name (case-insensitive)
        foreach ($list as $entry) {
            $name = trim($entry['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $existing = Crop::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
            $aliases = array_map(fn ($v) => strtolower(trim($v)), (array) ($entry['name_variants'] ?? []));

            $data = [
                'name' => $name,
                'aliases' => implode(',', array_unique(array_filter($aliases))),
                'season' => $entry['suitable_season'] ?? null,
                'min_temp' => $entry['ideal_temperature']['min'] ?? null,
                'max_temp' => $entry['ideal_temperature']['max'] ?? null,
                'min_rainfall' => $entry['rainfall']['min'] ?? 0,
                'max_rainfall' => $entry['rainfall']['max'] ?? 0,
                'min_humidity' => $entry['humidity']['min'] ?? 0,
                'max_humidity' => $entry['humidity']['max'] ?? 0,
                'base_yield' => $entry['base_yield'] ?? 5.0,
            ];

            if ($existing) {
                $existing->update($data);
            } else {
                Crop::create($data);
            }
        }

        // Add common alias mappings
        $this->addAlias('Rice', ['paddy']);
        $this->addAlias('Maize', ['corn']);
        $this->addAlias('Okra', ['bhindi']);
    }

    private function addAlias(string $cropName, array $newAliases)
    {
        $crop = Crop::whereRaw('LOWER(name) = ?', [strtolower($cropName)])->first();
        if (! $crop) {
            return;
        }
        $existing = array_filter(array_map('trim', explode(',', (string) $crop->aliases ?? '')));
        $merged = array_unique(array_merge($existing, array_map('strtolower', $newAliases)));
        $crop->update(['aliases' => implode(',', $merged)]);
    }
}

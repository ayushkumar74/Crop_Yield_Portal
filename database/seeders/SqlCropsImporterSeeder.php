<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Seeder;

class SqlCropsImporterSeeder extends Seeder
{
    public function run()
    {
        $path = base_path('full_backup.sql');
        if (! file_exists($path)) {
            $this->command->warn('full_backup.sql not found at '.$path);

            return;
        }

        $raw = file_get_contents($path);

        // Detect and convert UTF-16LE BOM/encoding if present
        if (strpos($raw, "\x00\x00") !== false || preg_match('/\x00[\x00-\x7F]/', $raw)) {
            // Likely UTF-16LE — convert to UTF-8
            $raw = @mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
        }

        // Find the INSERT INTO `crops` VALUES (...) statement
        $pos = stripos($raw, 'INSERT INTO `crops` VALUES');
        if ($pos === false) {
            $this->command->warn('No INSERT INTO `crops` VALUES found in full_backup.sql');

            return;
        }

        // Find the end of the INSERT statement by locating the next semicolon after the INSERT
        $endPos = strpos($raw, ';', $pos);
        if ($endPos === false) {
            $this->command->warn('Could not find end of INSERT statement for crops.');

            return;
        }

        $insertStmt = substr($raw, $pos, $endPos - $pos + 1);

        // Extract content inside VALUES(...);
        $m = [];
        if (! preg_match('/VALUES\s*(\(.+\))\s*;/si', $insertStmt, $m)) {
            $this->command->warn('Failed to parse VALUES(...) from INSERT statement.');

            return;
        }

        $valuesBlock = $m[1];
        // Remove starting and ending parentheses if the whole block is wrapped
        if (substr($valuesBlock, 0, 1) === '(' && substr($valuesBlock, -1) === ')') {
            // We'll split on '),(' but first strip the outermost parens if present
            // ensure we don't accidentally remove inner content
        }

        // Normalize tuples by replacing '),(' with a marker and trimming ends
        $inner = preg_replace('/^\(+|\)+$/', '', $valuesBlock);
        // Split tuples — this assumes no '),(' inside string literals
        $tuples = preg_split('/\),\s*\(/', $inner);

        $count = 0;
        foreach ($tuples as $tuple) {
            $tuple = trim($tuple);
            // Use str_getcsv with single quote enclosure
            $fields = str_getcsv($tuple, ',', "'");
            if (! is_array($fields) || count($fields) < 2) {
                continue;
            }

            // Typical fields: id, name, min_temp, max_temp, min_rainfall, max_rainfall, min_humidity, max_humidity, base_yield, created_at, updated_at
            $name = trim($fields[1]);
            if ($name === '') {
                continue;
            }

            // Prepare data mapping; fields may contain NULL as literal
            $get = function ($idx) use ($fields) {
                if (! isset($fields[$idx])) {
                    return null;
                }
                $v = $fields[$idx];
                $v = trim($v);
                if (strtoupper($v) === 'NULL') {
                    return null;
                }

                return $v;
            };

            $data = [
                'min_temp' => $get(2) !== null ? (float) $get(2) : null,
                'max_temp' => $get(3) !== null ? (float) $get(3) : null,
                'min_rainfall' => $get(4) !== null ? (float) $get(4) : null,
                'max_rainfall' => $get(5) !== null ? (float) $get(5) : null,
                'min_humidity' => $get(6) !== null ? (float) $get(6) : null,
                'max_humidity' => $get(7) !== null ? (float) $get(7) : null,
                'base_yield' => $get(8) !== null ? (float) $get(8) : null,
            ];

            // Upsert by name (case-insensitive)
            $existing = Crop::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
            if ($existing) {
                $existing->update($data);
            } else {
                Crop::create(array_merge(['name' => $name, 'aliases' => ''], $data));
            }
            $count++;
        }

        $this->command->info("Imported/updated {$count} crop records from full_backup.sql");
    }
}

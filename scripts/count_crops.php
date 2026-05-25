<?php

$root = __DIR__.'/../';
$jsonPath = $root.'resources/data/indian_crops.json';
$sqlPath = $root.'full_backup.sql';

$countJson = 0;
if (file_exists($jsonPath)) {
    $data = @file_get_contents($jsonPath);
    $arr = json_decode($data, true);
    if (is_array($arr)) {
        $countJson = count($arr);
    }
}

$countSql = 0;
if (file_exists($sqlPath)) {
    $raw = file_get_contents($sqlPath);
    // Try convert if looks like UTF-16LE
    if (strpos($raw, "\x00") !== false) {
        $raw = @mb_convert_encoding($raw, 'UTF-8', 'UTF-16LE');
    }
    $pos = stripos($raw, 'INSERT INTO `crops` VALUES');
    if ($pos !== false) {
        $end = strpos($raw, ';', $pos);
        if ($end !== false) {
            $insert = substr($raw, $pos, $end - $pos + 1);
            // Extract content between VALUES and ending semicolon
            $m = [];
            if (preg_match('/VALUES\s*(\(.+\))\s*;/si', $insert, $m)) {
                $vals = $m[1];
                $inner = preg_replace('/^\(+|\)+$/', '', $vals);
                $tuples = preg_split('/\),\s*\(/', $inner);
                $countSql = count($tuples);
            }
        }
    }
}

echo "indian_crops.json entries: $countJson\n";
echo "full_backup.sql crop tuples: $countSql\n";
echo "Note: final DB crop count depends on seeders run (upserts may merge duplicates).\n";

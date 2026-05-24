<?php

namespace Database\Seeders;

use App\Models\WeatherLog;
use Illuminate\Database\Seeder;

class WeatherLogSeeder extends Seeder
{
    public function run(): void
    {
        $logs = [
            ['city' => 'New Delhi', 'temperature' => 32.5, 'humidity' => 65, 'rainfall' => 12.0, 'wind_speed' => 18.0, 'weather_condition' => 'Partly Cloudy', 'latitude' => 28.6139, 'longitude' => 77.2090],
            ['city' => 'Mumbai', 'temperature' => 28.0, 'humidity' => 82, 'rainfall' => 45.0, 'wind_speed' => 22.0, 'weather_condition' => 'Rainy', 'latitude' => 19.0760, 'longitude' => 72.8777],
            ['city' => 'Bengaluru', 'temperature' => 24.0, 'humidity' => 70, 'rainfall' => 8.0, 'wind_speed' => 15.0, 'weather_condition' => 'Cloudy', 'latitude' => 12.9716, 'longitude' => 77.5946],
            ['city' => 'Kolkata', 'temperature' => 30.0, 'humidity' => 78, 'rainfall' => 20.0, 'wind_speed' => 12.0, 'weather_condition' => 'Humid', 'latitude' => 22.5726, 'longitude' => 88.3639],
            ['city' => 'Chennai', 'temperature' => 34.0, 'humidity' => 75, 'rainfall' => 5.0, 'wind_speed' => 20.0, 'weather_condition' => 'Hot & Sunny', 'latitude' => 13.0827, 'longitude' => 80.2707],
            ['city' => 'Hyderabad', 'temperature' => 29.5, 'humidity' => 60, 'rainfall' => 0.0, 'wind_speed' => 14.0, 'weather_condition' => 'Clear', 'latitude' => 17.3850, 'longitude' => 78.4867],
            ['city' => 'Pune', 'temperature' => 26.0, 'humidity' => 72, 'rainfall' => 15.0, 'wind_speed' => 16.0, 'weather_condition' => 'Partly Rainy', 'latitude' => 18.5204, 'longitude' => 73.8567],
            ['city' => 'Lucknow', 'temperature' => 33.0, 'humidity' => 58, 'rainfall' => 2.0, 'wind_speed' => 10.0, 'weather_condition' => 'Mostly Sunny', 'latitude' => 26.8467, 'longitude' => 80.9462],
        ];

        foreach ($logs as $log) {
            WeatherLog::create($log);
        }
    }
}

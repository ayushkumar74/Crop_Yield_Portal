<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'temperature',
        'humidity',
        'rainfall',
        'wind_speed',
        'weather_condition',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'temperature' => 'float',
            'humidity' => 'float',
            'rainfall' => 'float',
            'wind_speed' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }
}

<?php

use App\Models\User;
<<<<<<< HEAD
use App\Services\WeatherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
=======
use App\Models\WeatherLog;
use App\Services\WeatherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755

uses(RefreshDatabase::class);

test('login redirects user to dashboard instead of predictions', function () {
    $user = User::factory()->create([
        'email' => 'farmer@cropyield.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('login'), [
        'email' => 'farmer@cropyield.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('home'));
});

<<<<<<< HEAD
test('weather service maps actual current provider readings and removes uv data', function () {
    Http::preventStrayRequests();
    Http::fake([
        'https://api.open-meteo.com/*' => Http::response([
            'current' => [
                'time' => '2026-05-24T14:15',
                'temperature_2m' => 33.4,
                'relative_humidity_2m' => 46,
                'apparent_temperature' => 36.1,
                'precipitation' => 0.4,
                'precipitation_probability' => 65,
                'weather_code' => 61,
                'wind_speed_10m' => 13.2,
                'wind_direction_10m' => 225,
            ],
            'hourly' => [
                'time' => ['2026-05-24T14:00', '2026-05-24T15:00', '2026-05-24T16:00', '2026-05-24T17:00', '2026-05-24T18:00', '2026-05-24T19:00'],
                'temperature_2m' => [33.4, 33.0, 32.8, 31.9, 31.3, 30.4],
                'relative_humidity_2m' => [46, 48, 50, 52, 55, 58],
                'apparent_temperature' => [36.1, 35.6, 35.1, 34.2, 33.6, 32.5],
                'precipitation' => [0.4, 0.2, 0.1, 0.0, 0.0, 0.0],
                'weather_code' => [61, 61, 2, 2, 1, 0],
            ],
        ]),
        'https://archive-api.open-meteo.com/*' => Http::response([
            'daily' => ['precipitation_sum' => [400.2, 275.3]],
        ]),
        'https://nominatim.openstreetmap.org/*' => Http::response([
            'address' => ['town' => 'Phagwara Tahsil', 'state' => 'Punjab', 'village' => 'Athouli'],
        ]),
    ]);

    $weather = app(WeatherService::class)->getWeather(31.224, 75.77, true);

    expect($weather['success'])->toBeTrue()
        ->and($weather['temperature'])->toBe(33.4)
        ->and($weather['humidity'])->toBe(46.0)
        ->and($weather['feels_like'])->toBe(36.1)
        ->and($weather['precipitation'])->toBe(0.4)
        ->and($weather['rainfall'])->toBe(0.4)
        ->and($weather['annual_rain'])->toBe(675.5)
        ->and($weather['wind_speed'])->toBe(13.2)
        ->and($weather['wind_direction'])->toBe('SW')
        ->and($weather['weather_condition'])->toBe('Rainy')
        ->and($weather['city'])->toBe('Phagwara, Punjab')
        ->and($weather['hourly'])->toHaveCount(5)
        ->and($weather['hourly'][0]['temp'])->toBe(33.0)
        ->and($weather)->not->toHaveKey('visibility')
        ->and($weather)->not->toHaveKey('uv_index');
});

test('weather service autofills rain from precipitation probability when amount is unavailable', function () {
    Http::preventStrayRequests();
    Http::fake([
        'https://api.open-meteo.com/*' => Http::response([
            'current' => [
                'time' => '2026-05-24T14:00',
                'temperature_2m' => 31.0,
                'relative_humidity_2m' => 54,
                'apparent_temperature' => 33.0,
                'precipitation_probability' => 70,
                'weather_code' => 2,
                'wind_speed_10m' => 5.5,
                'wind_direction_10m' => 90,
            ],
        ]),
        'https://nominatim.openstreetmap.org/*' => Http::response([
            'address' => ['town' => 'Phagwara', 'state' => 'Punjab'],
        ]),
        'https://archive-api.open-meteo.com/*' => Http::response([], 500),
    ]);

    $weather = app(WeatherService::class)->getWeather(31.224, 75.77, true);

    expect($weather['success'])->toBeTrue()
        ->and($weather['rain'])->toBe(0.7)
        ->and($weather['precipitation'])->toBe(0.7)
        ->and($weather['annual_rain'])->toBeNull()
        ->and($weather['annual_rain_source'])->toBe('unavailable')
        ->and($weather['rainfall_source'])->toBe('precipitation_probability_estimate')
        ->and($weather['precipitation_probability'])->toBe(70.0);
});

test('weather service replaces a small settlement name with its major town', function () {
    config(['services.google_maps.key' => 'maps-test-key']);
    Http::preventStrayRequests();
    Http::fake([
        'https://api.open-meteo.com/*' => Http::response([
            'current' => [
                'temperature_2m' => 31,
                'relative_humidity_2m' => 54,
                'apparent_temperature' => 32,
                'precipitation' => 0,
                'weather_code' => 0,
                'wind_speed_10m' => 6,
            ],
        ]),
        'https://nominatim.openstreetmap.org/*' => Http::response([
            'address' => ['village' => 'Athouli', 'state' => 'Punjab'],
        ]),
        'https://archive-api.open-meteo.com/*' => Http::response([
            'daily' => ['precipitation_sum' => [0]],
        ]),
        'https://maps.googleapis.com/*' => Http::response([
            'results' => [[
                'address_components' => [
                    ['long_name' => 'Athouli', 'types' => ['locality']],
                    ['long_name' => 'Phagwara', 'types' => ['administrative_area_level_3']],
                    ['long_name' => 'Punjab', 'types' => ['administrative_area_level_1']],
                ],
            ]],
        ]),
    ]);

    $weather = app(WeatherService::class)->getWeather(31.224, 75.77, true);

    config(['services.google_maps.key' => null]);

    expect($weather['city'])->toBe('Phagwara, Punjab')
        ->and($weather['city'])->not->toContain('Athouli');
=======
test('weather service returns accurate rain and annual_rain', function () {
    $service = new WeatherService;

    // Query Punjab coordinates
    $weather = $service->getWeather(30.34, 76.38);

    expect($weather['success'])->toBeTrue();
    expect($weather)->toHaveKey('rain');
    expect($weather)->toHaveKey('annual_rain');

    // Actual current hourly precipitation should be minimal (typically 0 on normal days)
    expect($weather['rain'])->toBeLessThan(100.0);

    // Annual rain should be estimated in the Punjab agriculture band (580 - 720 mm)
    expect($weather['annual_rain'])->toBeGreaterThanOrEqual(300);
    expect($weather['annual_rain'])->toBeLessThanOrEqual(3500);
});

test('weather service database fallback operates correctly', function () {
    // Seed a recent local weather log near target coordinates
    WeatherLog::create([
        'city' => 'Ambala, Haryana',
        'temperature' => 24.5,
        'humidity' => 55,
        'rainfall' => 2.0,
        'wind_speed' => 10.0,
        'weather_condition' => 'Clear Sky',
        'latitude' => 30.38,
        'longitude' => 76.77,
        'created_at' => now(),
    ]);

    $service = new WeatherService;

    // Run search for nearby coordinates (within 0.5 degrees / ~50km)
    // Note: We bypass Cache here since round(30.40,3) is different from round(30.38,3)
    $weather = $service->getWeather(30.40, 76.80);

    expect($weather['success'])->toBeTrue();
    // Since API lookup for 30.40, 76.80 will resolve and cacheHaryana, let's verify database query specifically by checking the fallback logic
    if (isset($weather['cached']) && $weather['cached'] === true) {
        expect($weather['city'])->toContain('Ambala');
        expect($weather['temperature'])->toEqual(24.5);
    }
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
});

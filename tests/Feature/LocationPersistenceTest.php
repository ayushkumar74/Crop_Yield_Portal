<?php

use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
<<<<<<< HEAD
use Illuminate\Support\Facades\Http;
=======
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755

uses(RefreshDatabase::class);

test('guest users are redirected from user location update endpoint', function () {
    $response = $this->postJson(route('api.user_location'), [
        'latitude' => 30.7333,
        'longitude' => 76.7794,
        'last_detected_location' => 'Patiala, Punjab',
        'location_permission_granted' => true,
    ]);

    $response->assertStatus(401);
});

test('authenticated users can update and save their exact location coordinates and status', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('api.user_location'), [
        'latitude' => 30.3398,
        'longitude' => 76.3869,
        'last_detected_location' => 'Hardaspur, Punjab',
        'location_permission_granted' => true,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $user->refresh();

    expect($user->latitude)->toBe(30.3398)
        ->and($user->longitude)->toBe(76.3869)
        ->and($user->last_detected_location)->toBe('Hardaspur, Punjab')
        ->and($user->location_permission_granted)->toBeTrue();
});

test('weather service falls back to bigdatacloud when openstreetmap fails', function () {
    $weatherService = app(WeatherService::class);

<<<<<<< HEAD
    Http::preventStrayRequests();
=======
    // Mock HTTP requests to force Nominatim fail, and BigDataCloud to succeed
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
    Http::fake([
        'https://nominatim.openstreetmap.org/*' => Http::response([], 500),
        'https://api.bigdatacloud.net/*' => Http::response([
            'locality' => 'Ludhiana',
            'principalSubdivision' => 'Punjab',
        ], 200),
<<<<<<< HEAD
        'https://archive-api.open-meteo.com/*' => Http::response([
            'daily' => ['precipitation_sum' => [540]],
        ]),
=======
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
        'https://api.open-meteo.com/*' => Http::response([
            'current' => [
                'temperature_2m' => 30.8,
                'relative_humidity_2m' => 43,
<<<<<<< HEAD
                'precipitation' => 0,
=======
                'rain' => 0,
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                'weather_code' => 0,
                'wind_speed_10m' => 7.2,
            ],
        ], 200),
    ]);

    $weather = $weatherService->getWeather(30.3398, 76.3869, true);

    expect($weather['city'])->toBe('Ludhiana, Punjab')
        ->and($weather['city_name'])->toBe('Ludhiana')
        ->and($weather['state_name'])->toBe('Punjab')
        ->and($weather['formatted_location'])->toBe('Ludhiana, Punjab');
});

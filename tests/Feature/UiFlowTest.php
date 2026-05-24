<?php

use App\Models\Crop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('prediction page shows only default crop cards', function () {
    $user = User::factory()->create();

    // Create default and extra crops
    $defaultNames = ['Rice', 'Wheat', 'Maize (Corn)', 'Sugarcane', 'Cotton', 'Potato', 'Tomato'];
    foreach ($defaultNames as $name) {
        Crop::create(['name' => $name, 'min_temp' => 15, 'max_temp' => 35, 'min_rainfall' => 400, 'max_rainfall' => 1200, 'min_humidity' => 30, 'max_humidity' => 90, 'base_yield' => 5.0]);
    }

    // Add some other crops that should not appear in the default grid
    Crop::create(['name' => 'UncommonCrop', 'min_temp' => 15, 'max_temp' => 35, 'min_rainfall' => 400, 'max_rainfall' => 1200, 'min_humidity' => 30, 'max_humidity' => 90, 'base_yield' => 5.0]);

    $response = $this->actingAs($user)->get(route('predictions.create'));
    $response->assertStatus(200);

    // Count rendered crop card labels in the HTML
    $html = $response->getContent();
    preg_match_all('/class="[^"]*crop-card-label[^"]*"/', $html, $matches);
    // Expect exactly the number of default crops present
    expect(count($matches[0]))->toBe(count($defaultNames));
});

test('discover endpoint does not persist crops and returns attributes', function () {
    $user = User::factory()->create();

    $payload = ['name' => 'TempDiscoverCrop'];

    $response = $this->actingAs($user)->postJson(route('api.crops.find_or_create'), $payload);
    $response->assertStatus(200);
    $data = $response->json();

    expect($data['success'])->toBeTrue();
    // ID should be null because discover is non-persistent
    expect($data['id'])->toBeNull();

    // Ensure DB has no persisted crop with that name
    $this->assertDatabaseMissing('crops', ['name' => 'TempDiscoverCrop']);
});

test('submitting prediction with crop_name persists crop and creates prediction', function () {
    $user = User::factory()->create();

    $payload = [
        'crop_name' => 'SubmittedCrop',
        'temperature' => 28,
        'rainfall' => 800,
        'humidity' => 60,
        'soil_ph' => 6.5,
    ];

    $response = $this->actingAs($user)->post(route('predictions.store'), $payload);
    $response->assertRedirect();

    // Crop should now exist in DB
    $this->assertDatabaseHas('crops', ['name' => 'SubmittedCrop']);
    // Prediction should be created
    $this->assertDatabaseHas('predictions', ['user_id' => $user->id]);
});

test('profile edit contains visible input-field elements', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.edit'));
    $response->assertStatus(200);
    $html = $response->getContent();

    // Ensure input-field class is present for profile/name and email and password fields
    $this->assertStringContainsString('class="input-field', $html);
});

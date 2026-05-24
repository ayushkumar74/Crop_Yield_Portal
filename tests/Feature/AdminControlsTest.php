<?php

use App\Models\Crop;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest is redirected to login from admin dashboard and user management', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    $this->get(route('admin.users'))->assertRedirect(route('login'));
});

test('normal user is forbidden from accessing admin dashboard and user management', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->get(route('admin.dashboard'))->assertStatus(403);
    $this->actingAs($user)->get(route('admin.users'))->assertStatus(403);
});

test('admin can access admin dashboard and view all users list with activity', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user']);
    $crop = Crop::create([
        'name' => 'Wheat',
        'min_temp' => 10,
        'max_temp' => 30,
        'min_rainfall' => 200,
        'max_rainfall' => 800,
        'min_humidity' => 30,
        'max_humidity' => 70,
        'base_yield' => 3.5,
    ]);

    // Create prediction for user to test activity eager loading
    Prediction::create([
        'user_id' => $user->id,
        'crop_id' => $crop->id,
        'temperature' => 22,
        'rainfall' => 450,
        'humidity' => 55,
        'soil_ph' => 6.5,
        'predicted_yield' => 3.2,
        'suitability_score' => 85,
        'risk_level' => 'Low',
        'recommendation' => 'Good conditions.',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users'));
    $response->assertStatus(200);
    $response->assertSee($user->name);
    $response->assertSee('Wheat');
});

test('admin can view user detail profile with predictions and crop-wise stats', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user']);
    $crop = Crop::create([
        'name' => 'Rice',
        'min_temp' => 20,
        'max_temp' => 38,
        'min_rainfall' => 800,
        'max_rainfall' => 2000,
        'min_humidity' => 50,
        'max_humidity' => 90,
        'base_yield' => 4.2,
    ]);

    $prediction = Prediction::create([
        'user_id' => $user->id,
        'crop_id' => $crop->id,
        'temperature' => 28,
        'rainfall' => 1200,
        'humidity' => 75,
        'soil_ph' => 6.2,
        'predicted_yield' => 4.0,
        'suitability_score' => 90,
        'risk_level' => 'Low',
        'recommendation' => 'Perfect conditions.',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.show', $user));
    $response->assertStatus(200);
    $response->assertSee($user->name);
    $response->assertSee($user->email);
    $response->assertSee('Rice');
    $response->assertSee('1200mm');
    $response->assertSee('4 t/ha');
});

test('admin can delete prediction history and referer dynamic redirection works', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user']);
    $crop = Crop::create([
        'name' => 'Maize',
        'min_temp' => 15,
        'max_temp' => 35,
        'min_rainfall' => 500,
        'max_rainfall' => 1200,
        'min_humidity' => 40,
        'max_humidity' => 80,
        'base_yield' => 5.0,
    ]);

    $prediction = Prediction::create([
        'user_id' => $user->id,
        'crop_id' => $crop->id,
        'temperature' => 25,
        'rainfall' => 800,
        'humidity' => 60,
        'soil_ph' => 6.8,
        'predicted_yield' => 4.8,
        'suitability_score' => 92,
        'risk_level' => 'Low',
        'recommendation' => 'Excellent.',
    ]);

    // Ensure prediction exists
    $this->assertDatabaseHas('predictions', ['id' => $prediction->id]);

    // Admin destroys it from the show page referer (simulated)
    $response = $this->actingAs($admin)
        ->from(route('predictions.show', $prediction))
        ->delete(route('admin.predictions.destroy', $prediction));

    // Should redirect to prediction history index instead of back (to avoid 404)
    $response->assertRedirect(route('predictions.index'));

    // Check database
    $this->assertDatabaseMissing('predictions', ['id' => $prediction->id]);
});

test('normal user is forbidden from deleting prediction history', function () {
    $user = User::factory()->create(['role' => 'user']);
    $crop = Crop::create([
        'name' => 'Maize',
        'min_temp' => 15,
        'max_temp' => 35,
        'min_rainfall' => 500,
        'max_rainfall' => 1200,
        'min_humidity' => 40,
        'max_humidity' => 80,
        'base_yield' => 5.0,
    ]);

    $prediction = Prediction::create([
        'user_id' => $user->id,
        'crop_id' => $crop->id,
        'temperature' => 25,
        'rainfall' => 800,
        'humidity' => 60,
        'soil_ph' => 6.8,
        'predicted_yield' => 4.8,
        'suitability_score' => 92,
        'risk_level' => 'Low',
        'recommendation' => 'Excellent.',
    ]);

    $response = $this->actingAs($user)->delete(route('admin.predictions.destroy', $prediction));
    $response->assertStatus(403);

    $this->assertDatabaseHas('predictions', ['id' => $prediction->id]);
});

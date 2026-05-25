<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('guest is redirected to login from profile page', function () {
    $response = $this->get(route('profile.edit'));
    $response->assertRedirect(route('login'));
});

test('authenticated user can view profile edit page', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('profile.edit'));
    $response->assertStatus(200);
    $response->assertSee($user->name);
});

test('authenticated user can update profile details', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post(route('profile.update'), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
    $response->assertRedirect();
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

test('authenticated user uploaded avatar is stored and rendered after a later request', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => UploadedFile::fake()->createWithContent(
            'profile.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')
        ),
    ]);

    $response->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->avatar)->toStartWith('avatars/')
        ->and($user->avatarUrl())->toBe(Storage::disk('public')->url($user->avatar));

    Storage::disk('public')->assertExists($user->avatar);

    $this->actingAs($user)->get(route('profile.edit'))
        ->assertSuccessful()
        ->assertSee($user->avatarUrl(), false);
});

test('external avatar urls are rendered without a storage prefix', function () {
    $avatarUrl = 'https://example.com/profile/avatar.jpg';
    $user = User::factory()->create(['avatar' => $avatarUrl]);

    $this->actingAs($user)->get(route('profile.edit'))
        ->assertSuccessful()
        ->assertSee($avatarUrl, false)
        ->assertDontSee('/storage/https://example.com/profile/avatar.jpg', false);
});

test('authenticated user can change password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);
    $response = $this->actingAs($user)->post(route('profile.password'), [
        'current_password' => 'old-password',
        'password' => 'new-secure-pass',
        'password_confirmation' => 'new-secure-pass',
    ]);
    $response->assertRedirect();
    $user->refresh();
    $this->assertTrue(Hash::check('new-secure-pass', $user->password));
});

test('authenticated user can update settings', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post(route('profile.settings.update'), [
        'theme_preference' => 'dark',
        'language_preference' => 'hi',
        'notifications' => ['email' => 'on', 'sms' => 'on'],
    ]);
    $response->assertRedirect();
    $user->refresh();
    $this->assertEquals('dark', $user->theme_preference);
    $this->assertEquals('hi', $user->language_preference);
    $this->assertEquals([
        'email' => true,
        'push' => false,
        'sms' => true,
    ], $user->notification_preferences);
});

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@cropyield.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // Create Demo User
        User::create([
            'name' => 'Demo Farmer',
            'email' => 'farmer@cropyield.com',
            'role' => 'user',
            'password' => Hash::make('password'),
        ]);

        $this->call([
            CropSeeder::class,
            WeatherLogSeeder::class,
        ]);
    }
}

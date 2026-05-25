<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Hash;

$email = 'admin@cropyield.com';
$user = User::where('email', $email)->first();
if (! $user) {
    echo "ADMIN NOT FOUND\n";
    exit(1);
}

$user->password = Hash::make('Admin@12345');
$user->password_set = true;
$user->save();

echo "RESET OK for {$email}\n";

return 0;

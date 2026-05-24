<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

$email = 'admin@cropyield.com';
$pwd = 'Admin@12345';

$credentials = ['email' => $email, 'password' => $pwd];

$valid = Auth::validate($credentials);
$user = User::where('email', $email)->first();

echo "Auth::validate => ".($valid ? 'true':'false')."\n";
if ($user) {
    echo "User found: role={$user->role}, password_set={$user->password_set}\n";
    echo "Hash check => ".(Hash::check($pwd, $user->password) ? 'true' : 'false')."\n";
} else {
    echo "User not found\n";
}

if ($valid && $user && $user->role === 'admin') {
    Auth::login($user, true);
    echo "Login invoked for admin. Auth user id: ".Auth::id()."\n";
}

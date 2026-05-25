<?php

namespace App\Http\Controllers;

use App\Mail\LoginOtpMail;
use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // Show Login Page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Step 1: Validate credentials → send OTP
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::validate($credentials)) {
            return back()->withErrors([
                'email' => __('messages.auth_invalid_credentials'),
            ])->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();

        // Admin bypass: log admins in directly
        if ($user && isset($user->role) && $user->role === 'admin') {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(url('/admin'));
        }

        // Generate OTP
        LoginOtp::where('email', $credentials['email'])->delete();
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        LoginOtp::create([
            'email' => $credentials['email'],
            'otp' => bcrypt($otp),
            'expires_at' => now()->addMinutes(1),
        ]);

        Mail::to($credentials['email'])->send(new LoginOtpMail($otp, $user?->name ?? ''));

        $request->session()->put('otp_email', $credentials['email']);
        $request->session()->put('otp_remember', $request->boolean('remember'));
        $request->session()->save();

        // OTP generated and emailed to user

        return redirect()->route('otp.show');
    }

    // Show OTP page
    public function showOtp()
    {
        if (! session('otp_email')) {
            return redirect()->route('login');
        }

        return view('auth.otp');
    }

    // Verify OTP and login
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $record = LoginOtp::where('email', $email)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record || ! Hash::check($request->otp, $record->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        $record->update(['used' => true]);

        $user = User::where('email', $email)->first();
        if (! $user) {
            return redirect()->route('login')->withErrors(['email' => 'User not found.']);
        }

        Auth::login($user, session('otp_remember', false));
        $request->session()->regenerate();
        $request->session()->forget(['otp_email', 'otp_remember']);

        if ($user && isset($user->role) && $user->role === 'admin') {
            return redirect()->intended(url('/admin'))->with('success', __('messages.auth_welcome_back'));
        }

        return redirect()->intended(url('/dashboard'))->with('success', __('messages.auth_welcome_back'));
    }

    // Resend OTP
    public function resendOtp(Request $request)
    {
        $email = session('otp_email');
        if (! $email) {
            return redirect()->route('login');
        }

        $recent = LoginOtp::where('email', $email)
            ->where('created_at', '>', now()->subSeconds(30))
            ->exists();

        if ($recent) {
            return back()->withErrors(['otp' => 'Please wait 30 seconds before requesting a new OTP.']);
        }

        LoginOtp::where('email', $email)->delete();
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        LoginOtp::create([
            'email' => $email,
            'otp' => bcrypt($otp),
            'expires_at' => now()->addMinutes(1),
        ]);

        $user = User::where('email', $email)->first();
        if ($user) {
            Mail::to($email)->send(new LoginOtpMail($otp, $user->name));
        }

        return back()->with('success', 'A new OTP has been sent to your email.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    // Register with strong validation
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^\w]/',
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // New user registered

        Auth::login($user);

        if ($user && isset($user->role) && $user->role === 'admin') {
            return redirect()->intended(url('/admin'))->with('success', __('messages.auth_register_success'));
        }

        return redirect()->intended(url('/dashboard'))->with('success', __('messages.auth_register_success'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', __('messages.auth_logout_success'));
    }

    // Google OAuth redirect
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Google OAuth callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('[GOOGLE] OAuth error: '.$e->getMessage());

            return redirect()->route('login')->withErrors(['email' => 'Google login failed. Please try again.']);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
            // Existing user logged in via Google
        } else {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(32)),
                'password_set' => false,
            ]);
            // New user created via Google OAuth
        }

        Auth::login($user, true);

        if ($user && isset($user->role) && $user->role === 'admin') {
            return redirect()->intended(url('/admin'))->with('success', 'Welcome, '.$user->name.'!');
        }

        return redirect()->intended(url('/dashboard'))->with('success', 'Welcome, '.$user->name.'!');
    }
}

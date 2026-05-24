<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Mail\LoginOtpMail;
use App\Models\LoginOtp;
=======
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
<<<<<<< HEAD
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // ─── Show Login Page ──────────────────────────────────────────────────────
=======

class AuthController extends Controller
{
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
    public function showLogin()
    {
        return view('auth.login');
    }

<<<<<<< HEAD
    // ─── Step 1: Validate credentials → send OTP ─────────────────────────────
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::validate($credentials)) {
            return back()->withErrors([
                'email' => __('messages.auth_invalid_credentials'),
            ])->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();

        // If user is an admin, bypass OTP completely and log them in directly.
        // This preserves remember-me and session regeneration behavior.
        if ($user && isset($user->role) && $user->role === 'admin') {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            Log::info('[AUTH] Admin login - OTP bypassed for: ' . $credentials['email']);

            // Redirect admins straight to the dashboard (preserve intended URL).
            return redirect()->intended(url('/dashboard'));
        }
        // Generate a 6-digit OTP and store it (expire any old ones first)
        LoginOtp::where('email', $credentials['email'])->delete();

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        LoginOtp::create([
            'email'      => $credentials['email'],
            'otp'        => bcrypt($otp),   // store hashed
            'expires_at' => now()->addMinutes(1),
        ]);

        Mail::to($credentials['email'])->send(new LoginOtpMail($otp, $user->name));

        // Store email in session with explicit config
        $request->session()->put('otp_email', $credentials['email']);
        $request->session()->put('otp_remember', $request->boolean('remember'));
        $request->session()->save();

        Log::info('[OTP] Generated OTP for email: ' . $credentials['email']);

        return redirect()->route('otp.show');
    }

    // ─── Show OTP verification page ───────────────────────────────────────────
    public function showOtp()
    {
        if (! session('otp_email')) {
            Log::warning('[OTP] Session missing otp_email, redirecting to login');
            return redirect()->route('login');
        }

        return view('auth.otp');
    }

    // ─── Step 2: Verify OTP → log in ─────────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = session('otp_email');

        Log::info('[OTP VERIFY] Attempting verification for email: ' . $email);
        Log::info('[OTP VERIFY] User entered OTP: ' . $request->otp);

        if (! $email) {
            Log::error('[OTP VERIFY] No email in session');
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        // Fetch the OTP record
        $record = LoginOtp::where('email', $email)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record) {
            Log::error('[OTP VERIFY] No valid OTP record found for: ' . $email);
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        Log::info('[OTP VERIFY] OTP record found, checking hash...');

        // Compare the OTP
        $isValid = Hash::check($request->otp, $record->otp);
        Log::info('[OTP VERIFY] Hash check result: ' . ($isValid ? 'PASS ✓' : 'FAIL ✗'));

        if (! $isValid) {
            Log::error('[OTP VERIFY] Hash mismatch - OTP invalid');
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        // Mark OTP as used
        $record->update(['used' => true]);
        Log::info('[OTP VERIFY] OTP marked as used');

        // Find user and log in
        $user = User::where('email', $email)->first();

        if (! $user) {
            Log::error('[OTP VERIFY] User not found for email: ' . $email);
            return redirect()->route('login')->withErrors(['email' => 'User not found.']);
        }

        Auth::login($user, session('otp_remember', false));
        $request->session()->regenerate();
        
        // Clear OTP session data
        $request->session()->forget(['otp_email', 'otp_remember']);

        Log::info('[OTP VERIFY] User logged in successfully: ' . $email);

        return redirect()->route('home')->with('success', __('messages.auth_welcome_back'));
    }

    // ─── Resend OTP ───────────────────────────────────────────────────────────
    public function resendOtp(Request $request)
    {
        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('login');
        }

        // Rate-limit: only allow resend if last OTP is older than 30 seconds
        $recent = LoginOtp::where('email', $email)
            ->where('created_at', '>', now()->subSeconds(30))
            ->exists();

        if ($recent) {
            Log::warning('[OTP RESEND] Rate limit exceeded for: ' . $email);
            return back()->withErrors(['otp' => 'Please wait 30 seconds before requesting a new OTP.']);
        }

        // Delete old OTP and create new one
        LoginOtp::where('email', $email)->delete();
        
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        LoginOtp::create([
            'email'      => $email,
            'otp'        => bcrypt($otp),
            'expires_at' => now()->addMinutes(1),  // ✅ FIXED: Changed from 10 to 1 minute for consistency
        ]);

        $user = User::where('email', $email)->first();
        
        if ($user) {
            Mail::to($email)->send(new LoginOtpMail($otp, $user->name));
        }

        Log::info('[OTP RESEND] New OTP generated and sent for: ' . $email);

        return back()->with('success', 'A new OTP has been sent to your email.');
    }

    // ─── Show Register Page ───────────────────────────────────────────────────
=======
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Always send users to Home after login to avoid auto-opening dashboard
            return redirect()->route('home')->with('success', __('messages.auth_welcome_back'));
        }

        return back()->withErrors([
            'email' => __('messages.auth_invalid_credentials'),
        ])->onlyInput('email');
    }

>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
    public function showRegister()
    {
        return view('auth.register');
    }

<<<<<<< HEAD
    // ─── Register (with strong password validation) ───────────────────────────
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',      // At least one uppercase letter
                'regex:/[a-z]/',      // At least one lowercase letter
                'regex:/[0-9]/',      // At least one number
                'regex:/[@$!%*?&#^()\-_=+\[\]{};:\'"\\|,.<>\/`~]/',  // At least one special character
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
=======
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

<<<<<<< HEAD
        Log::info('[REGISTER] New user registered: ' . $validated['email']);

        return redirect()->route('home')->with('success', __('messages.auth_register_success'));
    }

    // ─── Logout ───────────────────────────────────────────────────────────────
=======
        return redirect()->route('home')->with('success', __('messages.auth_register_success'));
    }

>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', __('messages.auth_logout_success'));
    }
<<<<<<< HEAD

    // ─── Google OAuth: Redirect ───────────────────────────────────────────────
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // ─── Google OAuth: Callback ───────────────────────────────────────────────
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('[GOOGLE] OAuth error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['email' => 'Google login failed. Please try again.']);
        }

        // Find or create user
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Link Google ID if not already linked
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);
            Log::info('[GOOGLE] Existing user logged in: ' . $googleUser->getEmail());
        } else {
            $user = User::create([
                'name'         => $googleUser->getName(),
                'email'        => $googleUser->getEmail(),
                'google_id'    => $googleUser->getId(),
                'avatar'       => $googleUser->getAvatar(),
                'password'     => Hash::make(Str::random(32)), // random unusable password
                'password_set' => false,
            ]);
            Log::info('[GOOGLE] New user created: ' . $googleUser->getEmail());
        }

        Auth::login($user, true);

        return redirect()->route('home')->with('success', 'Welcome, ' . $user->name . '!');
    }
}
=======
}
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755

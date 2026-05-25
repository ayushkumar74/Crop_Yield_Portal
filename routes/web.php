<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;
use App\Models\WeatherLog;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─── Basic Routes & Named Routes ────────────────────────────────────

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
// Informational & Support Pages (simple, static, production-ready)
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/help', [PageController::class, 'help'])->name('help');
Route::get('/docs', [PageController::class, 'docs'])->name('docs');
Route::get('/report', [ContactController::class, 'show'])->name('report');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
// ───  Locale switching route ──────────────────────────────────────────
Route::get('/locale/{locale}', [PageController::class, 'setLocale'])->name('locale.set');

// ─── All features require authentication ───
Route::middleware('auth')->group(function () {

    // ─── Dashboard & Weather Analytics ───────────────────────────────────────
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/weather', [PageController::class, 'weather'])->name('weather');

    // ─── Profile & Application Settings ───────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');

    // ── JSON Response — Weather API (auth protected) ───────────────
    Route::get('/api/weather', function (Request $request, WeatherService $weatherService) {
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        $refresh = $request->query('refresh') === '1';

        if (! $lat || ! $lon) {
            return response()->json(['success' => false, 'message' => 'Coordinates missing.'], 400);
        }

        return response()->json($weatherService->getWeather((float) $lat, (float) $lon, $refresh))
            ->header('X-Portal', 'CropYieldPortal')
            ->header('X-Data-Source', 'Open-Meteo');
    })->name('api.weather');

    // Debug endpoints removed for production

    Route::post('/api/user-location', function (Request $request) {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'last_detected_location' => 'nullable|string|max:255',
            'location_permission_granted' => 'required|boolean',
        ]);
        $user = auth()->user();

        $user->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'last_detected_location' => $request->last_detected_location,
            'location_permission_granted' => (bool) $request->location_permission_granted,
        ]);

        return response()->json(['success' => true]);
    })->name('api.user_location');

    // ── JSON Response — Crop Suggestions (auth protected) ──────────
    Route::get('/api/crop-suggestions', [PageController::class, 'cropSuggestions'])->name('api.crop_suggestions');
    Route::get('/api/crops/search', [PageController::class, 'searchCrops'])->name('api.crops.search');
    Route::post('/api/crops/find-or-create', [PageController::class, 'findOrCreateCrop'])->name('api.crops.find_or_create');
    Route::get('/api/crop-season-check', [PageController::class, 'cropSeasonCheck'])->name('api.crop_season_check');

    // ─JSON Response — Weather Logs (auth protected) ──────────────
    Route::get('/api/weather-logs', function () {
        return response()->json(WeatherLog::latest()->limit(8)->get());
    })->name('api.weather_logs');

    // ─── Resourceful Controller — Predictions (auth protected) ─────
    Route::resource('predictions', PredictionController::class);

    // ─── Delete shortcut ─────────────────────────────────────────────────────
    Route::delete('/predictions/{prediction}/delete', [PredictionController::class, 'destroy'])
        ->name('predictions.delete');
});

// ─── Prefix Routing — Admin panel ──────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', AdminMiddleware::class])
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/crops', [AdminController::class, 'cropIndex'])->name('crops');
        Route::get('/crops/create', [AdminController::class, 'cropCreate'])->name('crops.create');
        Route::post('/crops', [AdminController::class, 'cropStore'])->name('crops.store');
        Route::get('/crops/{crop}/edit', [AdminController::class, 'cropEdit'])->name('crops.edit');
        Route::put('/crops/{crop}', [AdminController::class, 'cropUpdate'])->name('crops.update');
        Route::delete('/crops/{crop}', [AdminController::class, 'cropDestroy'])->name('crops.destroy');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        Route::get('/predictions', [AdminController::class, 'predictions'])->name('predictions');
        Route::delete('/predictions/{prediction}', [AdminController::class, 'destroyPrediction'])->name('predictions.destroy');
        Route::get('/tickets', [AdminController::class, 'ticketsIndex'])->name('tickets');
        Route::get('/tickets/{ticket}', [AdminController::class, 'showTicket'])->name('tickets.show');
        Route::patch('/tickets/{ticket}/resolve', [AdminController::class, 'resolveTicket'])->name('tickets.resolve');
        Route::patch('/tickets/{ticket}/status', [AdminController::class, 'updateTicketStatus'])->name('tickets.status');
        // Admin notification polling endpoints
        Route::get('/api/notifications', [AdminController::class, 'ticketNotifications'])->name('api.notifications');
        Route::post('/api/notifications/mark-read', [AdminController::class, 'markNotificationsRead'])->name('api.notifications.mark_read');
    });

// ─── Authentication Routes ────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // ✅ NEW: OTP verification routes
    Route::get('/login/otp', [AuthController::class, 'showOtp'])->name('otp.show');
    Route::post('/login/otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/login/otp/resend', [AuthController::class, 'resendOtp'])->name('otp.resend');
});

// ✅ NEW: Google OAuth routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

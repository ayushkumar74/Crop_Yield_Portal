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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

    // Debug endpoint: returns raw provider and reverse-geocode responses for diagnostics
    Route::get('/api/debug-weather', function (Request $request) {
        try {
            $lat = $request->query('lat');
            $lon = $request->query('lon');

            if (! $lat || ! $lon) {
                return response()->json(['success' => false, 'message' => 'Coordinates missing.'], 400);
            }

            $open = Http::timeout(12)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => (float) $lat,
                'longitude' => (float) $lon,
                'current' => 'temperature_2m,apparent_temperature,relative_humidity_2m,precipitation,rain,showers,precipitation_probability,weather_code,wind_speed_10m,wind_direction_10m',
                'hourly' => 'temperature_2m,apparent_temperature,relative_humidity_2m,precipitation,precipitation_probability,weather_code,wind_speed_10m,wind_direction_10m',
                'daily' => 'precipitation_sum,precipitation_probability_max',
                'timezone' => 'auto',
                'temperature_unit' => 'celsius',
                'precipitation_unit' => 'mm',
                'wind_speed_unit' => 'kmh',
                'forecast_days' => 2,
            ]);

            $nominatim = Http::timeout(6)
                ->withHeaders(['User-Agent' => 'CropYieldPortal/1.0'])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat' => (float) $lat,
                    'lon' => (float) $lon,
                    'format' => 'json',
                    'zoom' => 14,
                    'addressdetails' => 1,
                ]);

            return response()->json([
                'success' => true,
                'open_meteo' => $open->successful() ? $open->json() : ['status' => $open->status(), 'body' => $open->body()],
                'nominatim' => $nominatim->successful() ? $nominatim->json() : ['status' => $nominatim->status(), 'body' => $nominatim->body()],
            ]);
        } catch (Exception $e) {
            Log::error('DebugWeather route error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error calling providers', 'error' => $e->getMessage()], 500);
        }
    })->name('api.debug_weather');

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

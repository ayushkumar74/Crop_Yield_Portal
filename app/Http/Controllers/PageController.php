<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Prediction;
use App\Models\WeatherLog;

class PageController extends Controller
{
    // Public landing page
    public function home()
    {
        $cropCount = Crop::count();
        $predictionCount = Prediction::count();
        $recentPredictions = Prediction::with('crop')->latest()->limit(5)->get();

        return view('home', compact('cropCount', 'predictionCount', 'recentPredictions'));
    }

    // Authenticated dashboard
    public function dashboard()
    {
        $user = auth()->user();

        $userPredictions = Prediction::with('crop')->where('user_id', $user->id)->latest()->get();
        $totalPredictions = $userPredictions->count();
        $avgYield = $userPredictions->avg('predicted_yield');
        $bestCrop = $userPredictions->groupBy('crop.name')->map->max('predicted_yield')->sortDesc()->keys()->first();

        $chartPredictions = $userPredictions->take(7)->reverse()->values();
        $yieldTrendLabels = $chartPredictions->map(fn ($p) => ($p->crop->translated_crop_name ?? $p->crop->name).' ('.$p->created_at->format('d M').')')->toArray();
        $yieldTrendData = $chartPredictions->map(fn ($p) => $p->predicted_yield)->toArray();

        $cropDistribution = $userPredictions->groupBy(fn ($p) => $p->crop->translated_crop_name ?? $p->crop->name)->map->count();

        $recentWeather = null;
        if ($user && $user->latitude && $user->longitude) {
            $recentWeather = WeatherLog::whereBetween('latitude', [$user->latitude - 0.5, $user->latitude + 0.5])
                ->whereBetween('longitude', [$user->longitude - 0.5, $user->longitude + 0.5])
                ->latest()
                ->first();
        }

        if (! $recentWeather) {
            $recentWeather = WeatherLog::latest()->first();
        }

        return view('dashboard', compact('user', 'userPredictions', 'totalPredictions', 'avgYield', 'bestCrop', 'yieldTrendLabels', 'yieldTrendData', 'cropDistribution', 'recentWeather'));
    }

    // Weather analytics
    public function weather()
    {
        $weatherLogs = WeatherLog::latest()->paginate(12);
        $chartLogs = WeatherLog::latest()->limit(8)->get()->reverse()->values();
        $weatherLabels = $chartLogs->map(fn ($w) => $w->city)->toArray();
        $tempData = $chartLogs->map(fn ($w) => $w->temperature)->toArray();
        $humidityData = $chartLogs->map(fn ($w) => $w->humidity)->toArray();
        $rainfallData = $chartLogs->map(fn ($w) => $w->rainfall)->toArray();

        return view('weather', compact('weatherLogs', 'weatherLabels', 'tempData', 'humidityData', 'rainfallData'));
    }

    // Static pages
    public function privacy()
    {
        return view('privacy');
    }

    public function terms()
    {
        return view('terms');
    }

    public function faq()
    {
        return view('faq');
    }

    public function help()
    {
        return view('help');
    }

    public function docs()
    {
        return view('docs');
    }

    // Crop suggestions JSON endpoint
    public function cropSuggestions()
    {
        $temperature = request()->query('temperature', 25);
        $rainfall = request()->query('rainfall', 800);
        $humidity = request()->query('humidity', 65);

        $suggestions = Crop::all()->map(function ($crop) use ($temperature, $rainfall, $humidity) {
            $tempOk = $temperature >= $crop->min_temp && $temperature <= $crop->max_temp;
            $rainOk = $rainfall >= $crop->min_rainfall && $rainfall <= $crop->max_rainfall;
            $humOk = $humidity >= $crop->min_humidity && $humidity <= $crop->max_humidity;

            $score = ($tempOk ? 1 : 0) + ($rainOk ? 1 : 0) + ($humOk ? 1 : 0);

            return [
                'id' => $crop->id,
                'name' => $crop->translated_crop_name ?? $crop->name,
                'score' => $score,
            ];
        })->sortByDesc('score')->values();

        return response()->json(['success' => true, 'data' => $suggestions]);
    }

    // Helper for find-or-create crop (used by UI)
    // Note: "discover" does not persist; it returns existing crops or attributes for new ones
    public function findOrCreateCrop()
    {
        $name = request()->input('name');
        if (! $name) {
            return response()->json(['success' => false, 'message' => 'Name missing'], 400);
        }

        // Check if crop exists; if not, return attributes without persisting
        $crop = Crop::where('name', $name)->first();
        if ($crop) {
            return response()->json(['success' => true, 'id' => $crop->id, 'name' => $crop->name]);
        }

        // Return attributes for non-persisted crop discovery
        return response()->json(['success' => true, 'id' => null, 'name' => $name]);
    }

    public function cropSeasonCheck()
    {
        $cropId = request()->query('crop_id');
        $crop = Crop::find($cropId);
        if (! $crop) {
            return response()->json(['success' => false, 'message' => 'Crop not found'], 404);
        }

        // Simple stub: return available months (this app may enrich later)
        return response()->json(['success' => true, 'months' => range(1, 12)]);
    }
}

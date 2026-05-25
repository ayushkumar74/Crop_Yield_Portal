<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Prediction;
use App\Models\WeatherLog;
use App\Services\CropGuidanceService;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    /**
     * Switch application locale.
     */
    public function setLocale($locale)
    {
        if (in_array($locale, ['en', 'hi'])) {
            session(['locale' => $locale]);
            cookie()->queue(cookie()->forever('locale', $locale));
        }

        return redirect()->back();
    }

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

            $matchPercent = (int) round(($score / 3) * 100);

            return [
                'id' => $crop->id,
                'name' => $crop->name,
                'translated_name' => $crop->translated_crop_name ?? $crop->name,
                'match_score' => $matchPercent,
                'suitable' => $score >= 2,
            ];
        })->sortByDesc('match_score')->values()->all();

        return response()->json($suggestions);
    }

    // Search crops by query (name and aliases). Returns DB-driven results.
    public function searchCrops()
    {
        $q = (string) request()->query('query', '');
        $qNorm = strtolower(trim($q));

        if ($qNorm === '') {
            return response()->json([]);
        }

        $results = Crop::whereRaw('LOWER(name) LIKE ?', ["%{$qNorm}%"])
            ->orWhereRaw('LOWER(aliases) LIKE ?', ["%{$qNorm}%"])
            ->limit(100)
            ->get()
            ->map(function ($crop) {
                return [
                    'id' => $crop->id,
                    'nameEn' => strtolower($crop->name),
                    'displayName' => $crop->translated_crop_name ?? $crop->name,
                    'aliases' => array_filter(array_map('trim', explode(',', $crop->aliases ?? ''))),
                    'minTemp' => $crop->min_temp,
                    'maxTemp' => $crop->max_temp,
                    'isDynamic' => false,
                ];
            })->values();

        return response()->json($results);
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
        $cropName = request()->query('crop_name');

        // Try to find crop by ID first, then by name
        $crop = null;
        if ($cropId) {
            $crop = Crop::find($cropId);
        }
        if (! $crop && $cropName) {
            $crop = Crop::whereRaw('LOWER(name) = ?', [strtolower(trim($cropName))])
                ->orWhereRaw('LOWER(aliases) LIKE ?', ['%'.strtolower(trim($cropName)).'%'])
                ->first();
        }
        if (! $crop) {
            return response()->json(['success' => false, 'message' => 'Crop not found'], 404);
        }

        try {
            $guidance = app(CropGuidanceService::class);
            $profile = $guidance->getCropProfile($crop->name) ?? [];
            $sowing = $profile['sowing_season'] ?? null;

            // Gather reported environment from query (numbers may be strings)
            $temperature = is_numeric(request()->query('temperature')) ? (float) request()->query('temperature') : null;
            $rainfall = is_numeric(request()->query('rainfall')) ? (float) request()->query('rainfall') : null;
            $humidity = is_numeric(request()->query('humidity')) ? (float) request()->query('humidity') : null;

            // Parse allowed sowing months from guidance profile
            $monthMap = [
                'january' => 1, 'jan' => 1, 'february' => 2, 'feb' => 2, 'march' => 3, 'mar' => 3,
                'april' => 4, 'apr' => 4, 'may' => 5, 'june' => 6, 'jun' => 6, 'july' => 7, 'jul' => 7,
                'august' => 8, 'aug' => 8, 'september' => 9, 'sep' => 9, 'october' => 10, 'oct' => 10,
                'november' => 11, 'nov' => 11, 'december' => 12, 'dec' => 12,
            ];

            $allowedMonths = [];
            if ($sowing) {
                $lower = strtolower($sowing);

                // Split multiple seasons by "/" or "and"
                $seasons = preg_split('/\s*\/\s*|\s+and\s+/', $lower);
                $allMonths = [];

                foreach ($seasons as $season) {
                    $season = trim($season);
                    if (empty($season)) {
                        continue;
                    }

                    // Extract month names from this season
                    preg_match_all('/\b(january|february|march|april|may|june|july|august|september|october|november|december|jan|feb|mar|apr|jun|jul|aug|sep|oct|nov|dec)\b/i', $season, $matches);
                    $found = $matches[1] ?? [];

                    if (count($found) >= 2) {
                        // Season with month range
                        $firstMonth = reset($found);
                        $lastMonth = end($found);

                        $start = $monthMap[$firstMonth] ?? null;
                        $end = $monthMap[$lastMonth] ?? null;

                        if ($start !== null && $end !== null) {
                            if ($start <= $end) {
                                $seasonMonths = range($start, $end);
                            } else {
                                // Wrap-around range (e.g., November to February)
                                $seasonMonths = array_merge(range($start, 12), range(1, $end));
                            }
                            $allMonths = array_merge($allMonths, $seasonMonths);
                        }
                    } elseif (count($found) === 1) {
                        // Single month in season
                        $month = reset($found);
                        $monthNum = $monthMap[$month] ?? null;
                        if ($monthNum !== null) {
                            $allMonths[] = $monthNum;
                        }
                    } else {
                        // No specific months; try season keywords for this segment
                        if (str_contains($season, 'kharif')) {
                            $allMonths = array_merge($allMonths, [6, 7, 8, 9]);
                        } elseif (str_contains($season, 'rabi')) {
                            $allMonths = array_merge($allMonths, [11, 12, 1, 2]);
                        } elseif (str_contains($season, 'autumn') || str_contains($season, 'fall')) {
                            $allMonths = array_merge($allMonths, [10, 11]);
                        } elseif (str_contains($season, 'spring')) {
                            $allMonths = array_merge($allMonths, [2, 3, 4]);
                        } elseif (str_contains($season, 'summer')) {
                            $allMonths = array_merge($allMonths, [3, 4, 5, 6]);
                        } elseif (str_contains($season, 'monsoon')) {
                            $allMonths = array_merge($allMonths, [6, 7, 8, 9]);
                        } elseif (str_contains($season, 'perennial') || str_contains($season, 'year-round')) {
                            $allMonths = array_merge($allMonths, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
                        }
                    }
                }

                // Remove duplicates and sort
                $allowedMonths = array_unique($allMonths);
                sort($allowedMonths);
            }

            $currentMonth = (int) now()->format('n');

            // Check environmental thresholds
            $minTemp = $crop->min_temp ?? ($profile['min_temp'] ?? null);
            $maxTemp = $crop->max_temp ?? ($profile['max_temp'] ?? null);
            $minRain = $crop->min_rainfall ?? ($profile['min_rainfall'] ?? null);
            $maxRain = $crop->max_rainfall ?? ($profile['max_rainfall'] ?? null);
            $minHum = $crop->min_humidity ?? ($profile['min_humidity'] ?? null);
            $maxHum = $crop->max_humidity ?? ($profile['max_humidity'] ?? null);

            $issues = [];

            if ($temperature !== null && $minTemp !== null && $maxTemp !== null) {
                if ($temperature < $minTemp || $temperature > $maxTemp) {
                    $issues['temperature'] = ['current' => $temperature, 'min' => $minTemp, 'max' => $maxTemp];
                }
            }

            if ($rainfall !== null && $minRain !== null && $maxRain !== null) {
                if ($rainfall < $minRain || $rainfall > $maxRain) {
                    $issues['rainfall'] = ['current' => $rainfall, 'min' => $minRain, 'max' => $maxRain];
                }
            }

            if ($humidity !== null && $minHum !== null && $maxHum !== null) {
                if ($humidity < $minHum || $humidity > $maxHum) {
                    $issues['humidity'] = ['current' => $humidity, 'min' => $minHum, 'max' => $maxHum];
                }
            }

            // Determine seasonal mismatch
            $seasonMismatch = ! empty($allowedMonths) && ! in_array($currentMonth, $allowedMonths, true);

            // Return warning if season or conditions mismatch
            if ($seasonMismatch || ! empty($issues)) {
                $locale = app()->getLocale();
                $cropName = $crop->translated_crop_name ?? $crop->name;

                $monthsEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                $monthsHi = ['जनवरी', 'फरवरी', 'मार्च', 'अप्रैल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितंबर', 'अक्टूबर', 'नवंबर', 'दिसंबर'];

                $currentMonthNameEn = $monthsEn[$currentMonth - 1];
                $currentMonthNameHi = $monthsHi[$currentMonth - 1];

                // Format clean message
                $sowingFormatted = $sowing ? str_replace(' - ', '–', $sowing) : null;

                if ($locale === 'hi') {
                    $msg = "{$cropName} की खेती के लिए अनुशंसित समय: ".($sowingFormatted ?: 'उपलब्ध नहीं')."। वर्तमान महीना: {$currentMonthNameHi}।";
                } else {
                    $msg = "{$cropName} is recommended during ".($sowingFormatted ?: 'not specified')." season. Current month: {$currentMonthNameEn}.";
                }

                return response()->json(['success' => true, 'warning' => true, 'message' => $msg]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::warning('cropSeasonCheck error: '.$e->getMessage());

            return response()->json(['success' => true]);
        }
    }
}

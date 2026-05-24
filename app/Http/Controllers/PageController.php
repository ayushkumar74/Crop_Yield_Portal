<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Prediction;
use App\Models\WeatherLog;

class PageController extends Controller
{
    /**
     * Public landing page.
     */
    public function home()
    {
        $cropCount = Crop::count();
        $predictionCount = Prediction::count();
        $recentPredictions = Prediction::with('crop')->latest()->limit(5)->get();

        return view('home', compact(
            'cropCount',
            'predictionCount',
            'recentPredictions'
        ));
    }

    /**
     * Authenticated user dashboard with analytics data.
     */
    public function dashboard()
    {
        $user = auth()->user();

        $userPredictions = Prediction::with('crop')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $totalPredictions = $userPredictions->count();

        $avgYield = $userPredictions->avg('predicted_yield');

        $bestCrop = $userPredictions
            ->groupBy('crop.name')
            ->map->max('predicted_yield')
            ->sortDesc()
            ->keys()
            ->first();

        // Chart.js data
        $chartPredictions = $userPredictions
            ->take(7)
            ->reverse()
            ->values();

        $yieldTrendLabels = $chartPredictions
            ->map(fn ($p) =>
                ($p->crop->translated_crop_name ?? $p->crop->name)
                .' ('.$p->created_at->format('d M').')'
            )
            ->toArray();

        $yieldTrendData = $chartPredictions
            ->map(fn ($p) => $p->predicted_yield)
            ->toArray();

        $cropDistribution = $userPredictions
            ->groupBy(fn ($p) =>
                $p->crop->translated_crop_name ?? $p->crop->name
            )
            ->map->count();

        // Weather personalization
        $recentWeather = null;

        if ($user && $user->latitude && $user->longitude) {

            $recentWeather = WeatherLog::whereBetween(
                'latitude',
                [$user->latitude - 0.5, $user->latitude + 0.5]
            )
            ->whereBetween(
                'longitude',
                [$user->longitude - 0.5, $user->longitude + 0.5]
            )
            ->latest()
            ->first();
        }

        if (! $recentWeather) {
            $recentWeather = WeatherLog::latest()->first();
        }

        return view('dashboard', compact(
            'user',
            'userPredictions',
            'totalPredictions',
            'avgYield',
            'bestCrop',
            'yieldTrendLabels',
            'yieldTrendData',
            'cropDistribution',
            'recentWeather'
        ));
    }

    /**
     * Weather analytics page.
     */
    public function weather()
    {
        $weatherLogs = WeatherLog::latest()->paginate(12);

        $chartLogs = WeatherLog::latest()
            ->limit(8)
            ->get()
            ->reverse()
            ->values();

        $weatherLabels = $chartLogs
            ->map(fn ($w) => $w->city)
            ->toArray();

        $tempData = $chartLogs
            ->map(fn ($w) => $w->temperature)
            ->toArray();

        $humidityData = $chartLogs
            ->map(fn ($w) => $w->humidity)
            ->toArray();

        $rainfallData = $chartLogs
            ->map(fn ($w) => $w->rainfall)
            ->toArray();

        return view('weather', compact(
            'weatherLogs',
            'weatherLabels',
            'tempData',
            'humidityData',
            'rainfallData'
        ));
    }

    /**
     * Static informational pages
     */
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

    /**
     * Crop suggestions JSON endpoint.
     */
    public function cropSuggestions()
    {
        $temperature = request()->query('temperature', 25);
        $rainfall = request()->query('rainfall', 800);
        $humidity = request()->query('humidity', 65);

        $suggestions = Crop::all()
            ->map(function ($crop) use (
                $temperature,
                $rainfall,
                $humidity
            ) {

                $tempOk =
                    $temperature >= $crop->min_temp &&
                    $temperature <= $crop->max_temp;

                $rainOk =
                    $rainfall >= $crop->min_rainfall &&
                    $rainfall <= $crop->max_rainfall;

                $humidOk =
                    $humidity >= $crop->min_humidity &&
                    $humidity <= $crop->max_humidity;

                $score =
                    ($tempOk ? 34 : 0) +
                    ($rainOk ? 33 : 0) +
                    ($humidOk ? 33 : 0);

                return [
                    'id' => $crop->id,
                    'name' => $crop->name,
                    'translated_name' => $crop->translated_crop_name,
                    'match_score' => $score,
                    'suitable' => $score >= 67,
                ];
            })
            ->filter(fn ($c) => $c['match_score'] > 0)
            ->sortByDesc('match_score')
            ->values();

        return response()->json($suggestions);
    }

    /**
     * Crop season + climate suitability checker
     */
    public function cropSeasonCheck()
    {
        $cropId = request()->query('crop_id');
        $cropName = request()->query('crop_name');

        // FIXED REAL VALUES
        $temperature = request()->filled('temperature')
            ? (float) request()->query('temperature')
            : null;

        $rainfall = request()->filled('rainfall')
            ? (float) request()->query('rainfall')
            : null;

        $humidity = request()->filled('humidity')
            ? (float) request()->query('humidity')
            : null;

        $month = (int) request()->query('month', date('n'));

        // Load dataset
        $path = resource_path('data/indian_crops.json');

        $dataset = [];

        if (file_exists($path)) {
            $dataset = json_decode(
                file_get_contents($path),
                true
            ) ?: [];
        }

        $found = null;
        $name = null;

        // Find crop by ID
        if ($cropId) {

            $model = Crop::find($cropId);

            if ($model) {
                $name = $model->name;
            }
        }

        // fallback
        if (empty($name) && $cropName) {
            $name = $cropName;
        }

        // Search crop
        if (! empty($name)) {

            foreach ($dataset as $entry) {

                $variants = array_map(
                    'strtolower',
                    $entry['name_variants'] ?? []
                );

                if (
                    strcasecmp($entry['name'], $name) === 0 ||
                    in_array(strtolower($name), $variants)
                ) {
                    $found = $entry;
                    break;
                }
            }
        }

        // no crop profile
        if (! $found) {

            return response()->json([
                'warning' => false
            ]);
        }

        $warnings = [];

        $locale = app()->getLocale();

        // =========================
        // SOWING MONTH CHECK
        // =========================

        if (
            ! empty($found['sowing_months']) &&
            is_array($found['sowing_months'])
        ) {

            if (! in_array($month, $found['sowing_months'])) {

                $monthName = date(
                    'F',
                    mktime(0, 0, 0, $month, 1)
                );

                $sowingMonths = implode(
                    ', ',
                    array_map(
                        fn ($m) => date(
                            'M',
                            mktime(0, 0, 0, $m, 1)
                        ),
                        $found['sowing_months']
                    )
                );

                if ($locale === 'hi') {

                    $warnings[] = sprintf(
                        '%s की बुवाई सामान्यतः %s में नहीं की जाती। सामान्य बुवाई महीने: %s।',
                        $found['name'],
                        $monthName,
                        $sowingMonths
                    );

                } else {

                    $warnings[] = sprintf(
                        '%s is not usually sown in %s. Typical sowing months: %s.',
                        $found['name'],
                        $monthName,
                        $sowingMonths
                    );
                }
            }
        }

        // =========================
        // TEMPERATURE CHECK
        // =========================

        if (
            isset($found['ideal_temperature']) &&
            $temperature !== null
        ) {

            if (
                $temperature < $found['ideal_temperature']['min'] ||
                $temperature > $found['ideal_temperature']['max']
            ) {

                if ($locale === 'hi') {

                    $warnings[] = sprintf(
                        '%s के लिए उपयुक्त तापमान %s–%s°C है। वर्तमान: %s°C।',
                        $found['name'],
                        $found['ideal_temperature']['min'],
                        $found['ideal_temperature']['max'],
                        $temperature
                    );

                } else {

                    $warnings[] = sprintf(
                        'Expected temperature for %s is %s–%s°C. Current: %s°C.',
                        $found['name'],
                        $found['ideal_temperature']['min'],
                        $found['ideal_temperature']['max'],
                        $temperature
                    );
                }
            }
        }

        // =========================
        // RAINFALL CHECK
        // =========================

        if (
            isset($found['rainfall']) &&
            $rainfall !== null
        ) {

            if (
                $rainfall < $found['rainfall']['min'] ||
                $rainfall > $found['rainfall']['max']
            ) {

                if ($locale === 'hi') {

                    $warnings[] = sprintf(
                        '%s के लिए सामान्य वार्षिक वर्षा %s–%s मिमी है। वर्तमान: %s मिमी।',
                        $found['name'],
                        $found['rainfall']['min'],
                        $found['rainfall']['max'],
                        $rainfall
                    );

                } else {

                    $warnings[] = sprintf(
                        'Typical annual rainfall for %s is %s–%s mm. Current: %s mm.',
                        $found['name'],
                        $found['rainfall']['min'],
                        $found['rainfall']['max'],
                        $rainfall
                    );
                }
            }
        }

        // =========================
        // HUMIDITY CHECK
        // =========================

        if (
            isset($found['humidity']) &&
            $humidity !== null
        ) {

            if (
                $humidity < $found['humidity']['min'] ||
                $humidity > $found['humidity']['max']
            ) {

                if ($locale === 'hi') {

                    $warnings[] = sprintf(
                        '%s के लिए उपयुक्त आर्द्रता %s–%s%% है। वर्तमान: %s%%।',
                        $found['name'],
                        $found['humidity']['min'],
                        $found['humidity']['max'],
                        $humidity
                    );

                } else {

                    $warnings[] = sprintf(
                        'Preferred humidity for %s is %s–%s%%. Current: %s%%.',
                        $found['name'],
                        $found['humidity']['min'],
                        $found['humidity']['max'],
                        $humidity
                    );
                }
            }
        }

        // no warnings
        if (count($warnings) === 0) {

            return response()->json([
                'warning' => false
            ]);
        }

        return response()->json([
            'warning' => true,
            'message' => implode(' ', $warnings)
        ]);
    }

    /**
     * Switch locale
     */
    public function setLocale(string $locale)
    {
        if (in_array($locale, ['en', 'hi'])) {
            session(['locale' => $locale]);
        }

        return redirect()
            ->back()
            ->withCookie(cookie('locale', $locale, 43200));
    }

    /**
     * About us page
     */
    public function about()
    {
        return view('about');
    }

    /**
     * Team page
     */
    public function team()
    {
        $members = [
            [
                'name' => 'Deepak Kumar Kashyap',
                'role' => 'Frontend Developer & UI Designer',
                'unit' => 'Unit I, III',
                'responsibilities' => [
                    'Landing page design',
                    'Blade templates',
                    'Tailwind CSS',
                    'Responsive layouts',
                    'Dark mode',
                ],
                'image' => 'https://ui-avatars.com/api/?name=Deepak+Kashyap&background=16a34a&color=fff&size=128',
                'color' => 'green',
            ],
        ];

        return view('team', compact('members'));
    }

    /**
     * Find or create crop
     */
    public function findOrCreateCrop()
    {
        $name = request()->input('name');

        if (! $name) {

            return response()->json([
                'success' => false,
                'message' => 'Crop name is required.'
            ], 400);
        }

        $crop = Crop::where('name', 'like', $name)->first();

        if ($crop) {

            return response()->json([
                'success' => true,
                'id' => $crop->id,
                'name' => $crop->name,
                'translated_name' => $crop->translated_crop_name,
                'min_temp' => $crop->min_temp,
                'max_temp' => $crop->max_temp,
            ]);
        }

        return response()->json([
            'success' => true,
            'id' => null,
            'name' => ucfirst($name),
            'translated_name' => ucfirst($name),
            'min_temp' => 15,
            'max_temp' => 35,
        ]);
    }
}
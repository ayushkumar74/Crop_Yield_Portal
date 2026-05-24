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

<<<<<<< HEAD
        return view('home', compact(
            'cropCount',
            'predictionCount',
            'recentPredictions'
        ));
=======
        return view('home', compact('cropCount', 'predictionCount', 'recentPredictions'));
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
    }

    /**
     * Authenticated user dashboard with analytics data.
     */
    public function dashboard()
    {
        $user = auth()->user();
<<<<<<< HEAD

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
=======
        $userPredictions = Prediction::with('crop')->where('user_id', $user->id)->latest()->get();

        $totalPredictions = $userPredictions->count();
        $avgYield = $userPredictions->avg('predicted_yield');
        $bestCrop = $userPredictions->groupBy('crop.name')->map->max('predicted_yield')->sortDesc()->keys()->first();

        // Chart.js data — last 7 predictions yield trend
        $chartPredictions = $userPredictions->take(7)->reverse()->values();
        $yieldTrendLabels = $chartPredictions->map(fn ($p) => ($p->crop->translated_crop_name ?? $p->crop->name).' ('.$p->created_at->format('d M').')')->toArray();
        $yieldTrendData = $chartPredictions->map(fn ($p) => $p->predicted_yield)->toArray();

        // Crop distribution (doughnut chart)
        $cropDistribution = $userPredictions->groupBy(fn ($p) => $p->crop->translated_crop_name ?? $p->crop->name)->map->count();

        // Personalize weather dashboard logic based on user GPS coordinates
        $recentWeather = null;
        if ($user && $user->latitude && $user->longitude) {
            $recentWeather = WeatherLog::whereBetween('latitude', [$user->latitude - 0.5, $user->latitude + 0.5])
                ->whereBetween('longitude', [$user->longitude - 0.5, $user->longitude + 0.5])
                ->latest()
                ->first();
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
        }

        if (! $recentWeather) {
            $recentWeather = WeatherLog::latest()->first();
        }

        return view('dashboard', compact(
<<<<<<< HEAD
            'user',
            'userPredictions',
            'totalPredictions',
            'avgYield',
            'bestCrop',
            'yieldTrendLabels',
            'yieldTrendData',
            'cropDistribution',
            'recentWeather'
=======
            'user', 'userPredictions', 'totalPredictions', 'avgYield', 'bestCrop',
            'yieldTrendLabels', 'yieldTrendData', 'cropDistribution', 'recentWeather'
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
        ));
    }

    /**
     * Weather analytics page.
     */
    public function weather()
    {
        $weatherLogs = WeatherLog::latest()->paginate(12);

<<<<<<< HEAD
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
=======
        // Chart data — temperature & humidity trend from logs
        $chartLogs = WeatherLog::latest()->limit(8)->get()->reverse()->values();
        $weatherLabels = $chartLogs->map(fn ($w) => $w->city)->toArray();
        $tempData = $chartLogs->map(fn ($w) => $w->temperature)->toArray();
        $humidityData = $chartLogs->map(fn ($w) => $w->humidity)->toArray();
        $rainfallData = $chartLogs->map(fn ($w) => $w->rainfall)->toArray();

        return view('weather', compact('weatherLogs', 'weatherLabels', 'tempData', 'humidityData', 'rainfallData'));
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
    }

    /**
     * Crop suggestions JSON endpoint.
     */
    public function cropSuggestions()
    {
        $temperature = request()->query('temperature', 25);
        $rainfall = request()->query('rainfall', 800);
        $humidity = request()->query('humidity', 65);

<<<<<<< HEAD
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
=======
        $suggestions = Crop::all()->map(function ($crop) use ($temperature, $rainfall, $humidity) {
            $tempOk = $temperature >= $crop->min_temp && $temperature <= $crop->max_temp;
            $rainOk = $rainfall >= $crop->min_rainfall && $rainfall <= $crop->max_rainfall;
            $humidOk = $humidity >= $crop->min_humidity && $humidity <= $crop->max_humidity;
            $score = ($tempOk ? 34 : 0) + ($rainOk ? 33 : 0) + ($humidOk ? 33 : 0);

            return [
                'id' => $crop->id,
                'name' => $crop->name,
                'translated_name' => $crop->translated_crop_name,
                'match_score' => $score,
                'suitable' => $score >= 67,
            ];
        })->filter(fn ($c) => $c['match_score'] > 0)->sortByDesc('match_score')->values();
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755

        return response()->json($suggestions);
    }

    /**
<<<<<<< HEAD
     * Crop season + climate suitability checker
=======
     * Check crop season and climate suitability before prediction.
     * Returns a friendly warning if current month or weather looks unsuitable.
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
     */
    public function cropSeasonCheck()
    {
        $cropId = request()->query('crop_id');
        $cropName = request()->query('crop_name');
<<<<<<< HEAD

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

=======
        $temperature = (float) request()->query('temperature', 999);
        $rainfall = (float) request()->query('rainfall', 99999);
        $humidity = (float) request()->query('humidity', 999);
        $month = (int) request()->query('month', date('n'));

        // Load JSON dataset
        $path = resource_path('data/indian_crops.json');
        $dataset = [];
        if (file_exists($path)) {
            $dataset = json_decode(file_get_contents($path), true) ?: [];
        }

        $found = null;
        if ($cropId) {
            $model = Crop::find($cropId);
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
            if ($model) {
                $name = $model->name;
            }
        }

<<<<<<< HEAD
        // fallback
=======
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
        if (empty($name) && $cropName) {
            $name = $cropName;
        }

<<<<<<< HEAD
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
=======
        if (! empty($name)) {
            foreach ($dataset as $entry) {
                if (strcasecmp($entry['name'], $name) === 0 || in_array(strtolower($name), array_map('strtolower', $entry['name_variants'] ?? []))) {
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                    $found = $entry;
                    break;
                }
            }
        }

<<<<<<< HEAD
        // no crop profile
        if (! $found) {

            return response()->json([
                'warning' => false
            ]);
=======
        // If no detailed profile found, return harmless response
        if (! $found) {
            return response()->json(['warning' => false]);
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
        }

        $warnings = [];

<<<<<<< HEAD
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
=======
        // Check month vs sowing/harvest months
        if (! empty($found['sowing_months']) && is_array($found['sowing_months'])) {
            if (! in_array($month, $found['sowing_months'])) {
                $warnings[] = sprintf('%s is not usually sown in %s. Typical sowing months: %s.', $found['name'], date('F', mktime(0, 0, 0, $month, 1)), implode(', ', array_map(fn ($m) => date('M', mktime(0, 0, 0, $m, 1)), $found['sowing_months'])));
            }
        }

        // Check simple climate suitability using ranges if provided
        if (isset($found['ideal_temperature'])) {
            if ($temperature !== 999 && ($temperature < $found['ideal_temperature']['min'] || $temperature > $found['ideal_temperature']['max'])) {
                $warnings[] = sprintf('Expected temperature for %s is %s–%s°C. Current: %s°C.', $found['name'], $found['ideal_temperature']['min'], $found['ideal_temperature']['max'], $temperature);
            }
        }
        if (isset($found['rainfall'])) {
            if ($rainfall !== 99999 && ($rainfall < $found['rainfall']['min'] || $rainfall > $found['rainfall']['max'])) {
                $warnings[] = sprintf('Typical annual rainfall for %s is %s–%s mm. Current: %s mm.', $found['name'], $found['rainfall']['min'], $found['rainfall']['max'], $rainfall);
            }
        }
        if (isset($found['humidity'])) {
            if ($humidity !== 999 && ($humidity < $found['humidity']['min'] || $humidity > $found['humidity']['max'])) {
                $warnings[] = sprintf('Preferred humidity for %s is %s–%s%%. Current: %s%%.', $found['name'], $found['humidity']['min'], $found['humidity']['max'], $humidity);
            }
        }

        if (count($warnings) === 0) {
            return response()->json(['warning' => false]);
        }

        // Compose a friendly message
        $message = implode(' ', $warnings);

        return response()->json(['warning' => true, 'message' => $message]);
    }

    /**
     * Switch application locale.
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
     */
    public function setLocale(string $locale)
    {
        if (in_array($locale, ['en', 'hi'])) {
            session(['locale' => $locale]);
        }

<<<<<<< HEAD
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
=======
        return redirect()->back()->withCookie(cookie('locale', $locale, 43200));
    }

    /**
     * Team page.
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
     */
    public function team()
    {
        $members = [
            [
                'name' => 'Deepak Kumar Kashyap',
                'role' => 'Frontend Developer & UI Designer',
                'unit' => 'Unit I, III',
<<<<<<< HEAD
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
=======
                'responsibilities' => ['Landing page design', 'Blade templates', 'Tailwind CSS', 'Responsive layouts', 'Dark mode'],
                'image' => 'https://ui-avatars.com/api/?name=Deepak+Kashyap&background=16a34a&color=fff&size=128',
                'color' => 'green',
            ],
            [
                'name' => 'Member 2',
                'role' => 'Backend & API Integration',
                'unit' => 'Unit II, IV, VI',
                'responsibilities' => ['Controllers', 'Routing', 'Weather API', 'Prediction logic', 'Email', 'Validation'],
                'image' => 'https://ui-avatars.com/api/?name=Member+2&background=d97706&color=fff&size=128',
                'color' => 'amber',
            ],
            [
                'name' => 'Member 3',
                'role' => 'Database & Admin Panel',
                'unit' => 'Unit V, VI',
                'responsibilities' => ['MySQL schema', 'Migrations', 'Seeders', 'Admin dashboard', 'Localization', 'Reports'],
                'image' => 'https://ui-avatars.com/api/?name=Member+3&background=0891b2&color=fff&size=128',
                'color' => 'cyan',
            ],
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
        ];

        return view('team', compact('members'));
    }

    /**
<<<<<<< HEAD
     * Find or create crop
=======
     * Find or dynamically create a crop by name.
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
     */
    public function findOrCreateCrop()
    {
        $name = request()->input('name');
<<<<<<< HEAD

        if (! $name) {

            return response()->json([
                'success' => false,
                'message' => 'Crop name is required.'
            ], 400);
        }

        $crop = Crop::where('name', 'like', $name)->first();

        if ($crop) {

=======
        if (! $name) {
            return response()->json(['success' => false, 'message' => 'Crop name is required.'], 400);
        }

        // Case-insensitive lookup
        $crop = Crop::where('name', 'like', $name)->first();
        if ($crop) {
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
            return response()->json([
                'success' => true,
                'id' => $crop->id,
                'name' => $crop->name,
                'translated_name' => $crop->translated_crop_name,
                'min_temp' => $crop->min_temp,
                'max_temp' => $crop->max_temp,
            ]);
        }

<<<<<<< HEAD
=======
        // Consult centralized JSON dataset to find matching predefined crop attributes
        $datasetPath = resource_path('data/indian_crops.json');
        $matchedPredefined = null;
        if (file_exists($datasetPath)) {
            $list = json_decode(file_get_contents($datasetPath), true) ?: [];
            foreach ($list as $entry) {
                if (strcasecmp($entry['name'] ?? '', $name) === 0 || in_array(strtolower($name), array_map('strtolower', $entry['name_variants'] ?? []))) {
                    $matchedPredefined = [
                        'name' => $entry['name'] ?? ucfirst($name),
                        'min_temp' => $entry['ideal_temperature']['min'] ?? 15,
                        'max_temp' => $entry['ideal_temperature']['max'] ?? 35,
                        'min_rainfall' => $entry['rainfall']['min'] ?? 400,
                        'max_rainfall' => $entry['rainfall']['max'] ?? 1500,
                        'min_humidity' => $entry['humidity']['min'] ?? 30,
                        'max_humidity' => $entry['humidity']['max'] ?? 90,
                        'base_yield' => $entry['base_yield'] ?? 5.0,
                    ];
                    break;
                }
            }
        }

        if ($matchedPredefined) {
            // Return the matched predefined attributes without persisting to DB.
            return response()->json([
                'success' => true,
                'id' => null,
                'name' => $matchedPredefined['name'],
                'translated_name' => $matchedPredefined['name'],
                'min_temp' => $matchedPredefined['min_temp'],
                'max_temp' => $matchedPredefined['max_temp'],
            ]);
        }

        // Generic fallback: return non-persisted generic attributes so frontend can show a temporary card.
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
        return response()->json([
            'success' => true,
            'id' => null,
            'name' => ucfirst($name),
            'translated_name' => ucfirst($name),
            'min_temp' => 15,
            'max_temp' => 35,
        ]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755

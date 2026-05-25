<?php

namespace App\Http\Controllers;

use App\Mail\PredictionSummaryMail;
use App\Models\Crop;
use App\Models\Prediction;
use App\Rules\ValidRainfall;
use App\Services\CropGuidanceService;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PredictionController extends Controller
{
    /**
     * Display prediction history with pagination.
     */
    public function index()
    {
        $predictions = Prediction::with('crop')
            ->when(Auth::check() && ! Auth::user()->isAdmin(), function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('predictions.index', compact('predictions'));
    }

    /**
     * Show the prediction form.
     */
    public function create()
    {
        // Show 5-7 popular crops by default in the crop card grid
        $defaultNames = ['Rice', 'Wheat', 'Maize (Corn)', 'Sugarcane', 'Cotton', 'Potato', 'Tomato'];

        // Load crops and ensure uniqueness by name to prevent duplicate cards
        $crops = Crop::orderBy('id')->get()->unique('name')->values();

        // Filter to get only default crops for the grid
        $defaultCrops = $crops->filter(function ($crop) use ($defaultNames) {
            return in_array($crop->name, $defaultNames);
        })->values();

        // If there's an old crop selected that is not default, include it in the rendered list
        $oldCropId = old('crop_id');
        if ($oldCropId) {
            $oldCrop = $crops->firstWhere('id', $oldCropId);
            if ($oldCrop && ! in_array($oldCrop->name, $defaultNames)) {
                $defaultCrops->push($oldCrop);
            }
        }

        // Prepare bilingual database crops for Javascript search
        $dbCrops = $crops->map(function ($c) {
            $key = 'messages.crop_'.strtolower(str_replace(' ', '_', $c->name));
            $nameHi = __($key, [], 'hi') !== $key ? __($key, [], 'hi') : $c->name;
            $nameEn = strtolower($c->name);

            return [
                'id' => $c->id,
                'nameEn' => $nameEn,
                'nameHi' => strtolower($nameHi),
                'nameTrans' => strtolower($c->translated_crop_name),
                'displayName' => $c->translated_crop_name,
                'minTemp' => (float) $c->min_temp,
                'maxTemp' => (float) $c->max_temp,
            ];
        });

        $recentCropIds = session('recent_crops', []);
        $recentCrops = Crop::whereIn('id', $recentCropIds)->get();

        // NOTE: The frontend no longer relies on the resources/data/indian_crops.json
        // file being merged into the client payload. All crops should be persisted
        // in the database and served via the API. Ensure $dbCrops contains only
        // persisted crops (no dynamic merging).

        // production: do not emit debug logs here

        return view('predictions.create', compact('crops', 'defaultCrops', 'dbCrops', 'recentCrops'));
    }

    /**
     * Store prediction with intelligent yield formula.
     */
    public function store(Request $request, GeminiService $gemini)
    {

        $validated = $request->validate([
            'crop_id' => 'required_without:crop_name|nullable|exists:crops,id',
            'crop_name' => 'required_without:crop_id|nullable|string|max:255',
            'temperature' => 'required|numeric|between:-10,60',
            'rainfall' => ['required', 'numeric', 'min:0', 'max:10000', new ValidRainfall],
            'humidity' => 'required|numeric|between:0,100',
            'soil_ph' => 'required|numeric|between:0,14',
        ], [
            'crop_id.required' => __('messages.validation_crop_required'),
            'temperature.between' => __('messages.validation_temp_between'),
            'humidity.between' => __('messages.validation_humidity_between'),
            'soil_ph.between' => __('messages.validation_soil_ph_between'),
        ]);

        // If crop_id is not provided, create/find crop by name now (persist) before prediction.
        $validatedCropId = $validated['crop_id'] ?? null;
        $validatedCropName = $validated['crop_name'] ?? null;

        if (empty($validatedCropId) && ! empty($validatedCropName)) {
            $name = $validated['crop_name'];
            // Try to find existing crop by name
            $crop = Crop::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
            if (! $crop) {
                // Try to find by aliases stored in the database
                $crop = Crop::whereRaw('LOWER(aliases) LIKE ?', ['%'.strtolower($name).'%'])->first();
            }

            if (! $crop) {
                // Generic creation for unknown crop names
                $crop = Crop::create([
                    'name' => ucfirst($name),
                    'min_temp' => 15, 'max_temp' => 35,
                    'min_rainfall' => 400, 'max_rainfall' => 1200,
                    'min_humidity' => 40, 'max_humidity' => 80,
                    'base_yield' => 5.0,
                ]);
            }
        } else {
            $crop = Crop::findOrFail($validatedCropId);
        }

        // Advanced yield prediction algorithm
        $tempScore = $this->calculateFactorScore($validated['temperature'], $crop->min_temp, $crop->max_temp);
        $rainScore = $this->calculateFactorScore($validated['rainfall'], $crop->min_rainfall, $crop->max_rainfall);
        $humidityScore = $this->calculateFactorScore($validated['humidity'], $crop->min_humidity, $crop->max_humidity);
        $phScore = $this->calculateFactorScore($validated['soil_ph'], 6.0, 7.5);

        // Weighted suitability — rainfall & temperature have higher weight
        $suitabilityScore = round(($tempScore * 0.35) + ($rainScore * 0.35) + ($humidityScore * 0.20) + ($phScore * 0.10));

        // Predicted yield with non-linear scaling for realism
        $predictedYield = round($crop->base_yield * (pow($suitabilityScore / 100, 0.6)), 2);

        $riskLevel = match (true) {
            $suitabilityScore > 75 => 'Low',
            $suitabilityScore > 45 => 'Medium',
            default => 'High',
        };

        // Get AI recommendation from Google Gemini
        $recommendation = $gemini->generateFarmingAdvice(
            $crop->name,
            $validated['temperature'],
            $validated['rainfall'],
            $validated['humidity'],
            $validated['soil_ph'],
            $riskLevel,
            $suitabilityScore
        );

        $prediction = Prediction::create([
            'user_id' => Auth::id(),
            'crop_id' => $crop->id,
            'temperature' => $validated['temperature'],
            'rainfall' => $validated['rainfall'],
            'humidity' => $validated['humidity'],
            'soil_ph' => $validated['soil_ph'],
            'predicted_yield' => $predictedYield,
            'suitability_score' => $suitabilityScore,
            'risk_level' => $riskLevel,
            'recommendation' => $recommendation,
        ]);

        $recentCrops = session('recent_crops', []);
        $recentCrops = array_unique(array_merge([$crop->id], $recentCrops));
        session(['recent_crops' => array_slice($recentCrops, 0, 5)]);

        if (Auth::check() && Auth::user()->email) {
            try {
                Mail::to(Auth::user()->email)->send(new PredictionSummaryMail($prediction->load('crop')));
            } catch (\Exception $e) {
                // Silently fail email — don't interrupt user flow
            }
        }

        return redirect()->route('predictions.show', $prediction)
            ->with('success', __('messages.prediction_generate_success'));
    }

    /**
     * Display a single prediction result with chart data.
     */
    public function show(Prediction $prediction)
    {
        app()->setLocale(session('locale', 'en'));
        $prediction->load('crop', 'user');

        $guidanceService = new CropGuidanceService;
        $baseProfile = $guidanceService->getCropProfile($prediction->crop->name);

        $aiData = json_decode($prediction->recommendation, true);
        if (is_array($aiData)) {
            $cropProfile = array_merge($baseProfile, $aiData);
        } else {
            $cropProfile = $baseProfile;
            $cropProfile['ai_insight'] = $prediction->recommendation;
        }

        // --- Core Metric Calculations ---
        $tempScore = $this->calculateFactorScore($prediction->temperature, $prediction->crop->min_temp, $prediction->crop->max_temp);
        $rainScore = $this->calculateFactorScore($prediction->rainfall, $prediction->crop->min_rainfall, $prediction->crop->max_rainfall);
        $humidScore = $this->calculateFactorScore($prediction->humidity, $prediction->crop->min_humidity, $prediction->crop->max_humidity);
        $phScore = $this->calculateFactorScore($prediction->soil_ph, 6.0, 7.5);

        // 1. Crop Health & Compatibilities
        $weatherComp = round(($tempScore + $rainScore + $humidScore) / 3);
        $soilComp = round($phScore);
        $cropHealth = round(($weatherComp * 0.7) + ($soilComp * 0.3));

        // 2. Believable & Responsive Risk Breakdown (%)
        $tempRange = max(1, $prediction->crop->max_temp - $prediction->crop->min_temp);

        if ($prediction->temperature > $prediction->crop->max_temp) {
            $heatStress = 50 + (($prediction->temperature - $prediction->crop->max_temp) / 5) * 50;
        } elseif ($prediction->temperature > ($prediction->crop->max_temp - 5)) {
            $heatStress = (($prediction->temperature - ($prediction->crop->max_temp - 5)) / 5) * 50;
        } elseif ($prediction->temperature < $prediction->crop->min_temp) {
            $minTemp = max(1, $prediction->crop->min_temp);
            $heatStress = 50 + (($prediction->crop->min_temp - $prediction->temperature) / $minTemp) * 50;
        } else {
            $heatStress = 5 + (($prediction->temperature - $prediction->crop->min_temp) / $tempRange) * 15;
        }
        $heatStress = min(100, max(5, $heatStress));

        if ($prediction->rainfall < $prediction->crop->min_rainfall) {
            $minRain = max(1, $prediction->crop->min_rainfall);
            $droughtRisk = 50 + (($prediction->crop->min_rainfall - $prediction->rainfall) / $minRain) * 50;
        } elseif ($prediction->rainfall < ($prediction->crop->min_rainfall + 200)) {
            $droughtRisk = (($prediction->crop->min_rainfall + 200 - $prediction->rainfall) / 200) * 50;
        } else {
            $droughtRisk = 5 + (($prediction->temperature / 40) * 10);
        }
        $droughtRisk = min(100, max(5, $droughtRisk));

        if ($prediction->rainfall > $prediction->crop->max_rainfall) {
            $maxRain = max(1, $prediction->crop->max_rainfall);
            $waterlogRisk = 50 + (($prediction->rainfall - $prediction->crop->max_rainfall) / $maxRain) * 50;
        } elseif ($prediction->rainfall > ($prediction->crop->max_rainfall - 200)) {
            $waterlogRisk = (($prediction->rainfall - ($prediction->crop->max_rainfall - 200)) / 200) * 50;
        } else {
            $waterlogRisk = 5 + (($prediction->humidity / 100) * 10);
        }
        $waterlogRisk = min(100, max(5, $waterlogRisk));

        $riskBreakdown = [
            'heat' => round($heatStress),
            'drought' => round($droughtRisk),
            'waterlog' => round($waterlogRisk),
        ];

        // 3. Profit Estimation
        $yieldAcre = round($prediction->predicted_yield * 0.404686, 2); // 1 Hectare = 2.471 Acres -> Yield / 2.471 = Yield * 0.404686
        $pricePerTon = $guidanceService->getMarketPricePerTon($prediction->crop->name);
        $estimatedProfitPerHa = round($prediction->predicted_yield * $pricePerTon); // Native INR calculation
        $estimatedProfitPerAcre = round($estimatedProfitPerHa * 0.404686);

        // 4. Farming Difficulty
        $difficulty = $guidanceService->getDifficulty($prediction->crop->name);

        // Chart Data
        $chartData = [
            'labels' => ['Temperature', 'Rainfall', 'Humidity', 'Soil pH'],
            'actual' => [
                $this->normalizeToPercent($prediction->temperature, $prediction->crop->min_temp, $prediction->crop->max_temp),
                $this->normalizeToPercent($prediction->rainfall, $prediction->crop->min_rainfall, $prediction->crop->max_rainfall),
                $this->normalizeToPercent($prediction->humidity, $prediction->crop->min_humidity, $prediction->crop->max_humidity),
                $this->normalizeToPercent($prediction->soil_ph, 6.0, 7.5),
            ],
            'ideal' => [100, 100, 100, 100],
        ];

        return view('predictions.show', compact(
            'prediction', 'chartData', 'cropProfile',
            'cropHealth', 'weatherComp', 'soilComp', 'riskBreakdown',
            'yieldAcre', 'estimatedProfitPerHa', 'estimatedProfitPerAcre', 'difficulty'
        ));
    }

    public function edit(Prediction $prediction) {}

    public function update(Request $request, Prediction $prediction) {}

    public function destroy(Prediction $prediction)
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access only.');
        }

        $prediction->delete();

        return redirect()->route('predictions.index')->with('success', __('messages.prediction_delete_success'));
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    /** Calculates how well an actual value fits within an ideal range (0–100). */
    private function calculateFactorScore(float $actual, float $min, float $max): float
    {
        if ($actual >= $min && $actual <= $max) {
            return 100;
        }

        $range = max($max - $min, 1);
        $distance = $actual < $min ? ($min - $actual) : ($actual - $max);
        $penalty = ($distance / $range) * 100;

        return max(0, 100 - $penalty);
    }

    /** Normalize a value to 0-100 scale within a range for charting. */
    private function normalizeToPercent(float $value, float $min, float $max): float
    {
        if ($max === $min) {
            return 100;
        }
        $clamped = min($max, max($min, $value));

        return round((($clamped - $min) / ($max - $min)) * 100);
    }
}

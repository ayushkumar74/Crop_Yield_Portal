<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    /**
     * Generate an AI recommendation for crop yield using Google Gemini.
     */
    public function generateFarmingAdvice(
        string $cropName,
        float $temperature,
        float $rainfall,
        float $humidity,
        float $soilPh,
        string $riskLevel,
        int $suitabilityScore
    ): string {
        $locale = App::getLocale();
        $langPrompt = $locale === 'hi' ? 'You MUST respond entirely in Hindi language (Devanagari script).' : 'You MUST respond in English.';
        $apiKey = config('services.gemini.key');

        if (! empty($apiKey)) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

            $prompt = "You are an expert Indian agronomist. A farmer in India is planting {$cropName}.
Field conditions: Temperature {$temperature}°C, Rainfall {$rainfall}mm, Humidity {$humidity}%, Soil pH {$soilPh}.
Risk level: {$riskLevel} (Score: {$suitabilityScore}/100).

Generate highly specific, realistic farming guidance tailored EXACTLY to {$cropName} under these exact Indian conditions. Do not output generic advice. Ensure every detail relates to the Indian agriculture market.
{$langPrompt}

Output strictly a valid JSON object with EXACTLY these keys:
- sowing_season: (Best months in India, e.g. Kharif/Rabi)
- irrigation_schedule: (Specific watering needs based on {$rainfall}mm rainfall)
- npk_guidance: (Exact N:P:K ratio)
- fertilizer_recommendations: (Detailed fertilizer schedule)
- soil_preparation: (Tillage and soil prep)
- disease_pest: (Specific Indian pests/diseases for {$cropName} and prevention)
- harvest_duration: (How long to harvest)
- maturity_time: (Total days to maturity)
- profitability: (Expected profit margin context in India)
- market_demand: (Demand in Indian markets)
- yield_tips: (Specific tricks to boost {$cropName} yield)
- weather_risks: (How the {$temperature}°C temp or rainfall affects it)
- water_requirement: (Total mm required)
- maintenance_difficulty: (Low/Medium/High)
- suitable_regions: (Best Indian states for this)
- organic_suggestions: (Bio-fertilizers/organic methods in India)
- ai_insight: (One sentence summary of the biggest risk and action to take right now)";

            try {
                // Increase timeout for full JSON generation
                $response = Http::timeout(20)->post($url, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

                if ($response->successful()) {
                    $aiJson = $response->json('candidates.0.content.parts.0.text');
                    if (! empty($aiJson)) {
                        return $aiJson;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Gemini Service Exception: '.$e->getMessage());
            }
        }

        // Fallback to static guidance if API fails
        $guidanceService = new CropGuidanceService;
        $cropProfile = $guidanceService->getCropProfile($cropName);

        $fallbackInsight = match ($riskLevel) {
            'Low' => 'Conditions are optimal. Continue standard practices and monitor for pests regularly.',
            'Medium' => 'Conditions are moderately suitable. Implement supplemental irrigation and correct soil pH to maximize yield potential.',
            'High' => 'Crop faces severe environmental stress. Immediate protective measures like shading or intensive soil conditioning are required.',
            default => 'Monitor crop closely and consult local agricultural extensions.'
        };

        $finalData = array_merge($cropProfile, [
            'ai_insight' => $fallbackInsight,
        ]);

        return json_encode($finalData, JSON_UNESCAPED_UNICODE);
    }
}

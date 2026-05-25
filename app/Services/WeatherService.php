<?php

namespace App\Services;

use App\Models\WeatherLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WeatherService
{
    /**
     * Fetch current weather data from Open-Meteo and return normalized structure.
     *
     * @return array<string, mixed>
     */
    public function getWeather(float $lat, float $lon, bool $refresh = false): array
    {
        $cacheKey = 'weather_'.round($lat, 5).'_'.round($lon, 5);

        if ($refresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($lat, $lon) {
            try {
                $response = Http::connectTimeout(4)->timeout(12)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'current_weather' => true,
                    'hourly' => 'temperature_2m,apparent_temperature,relative_humidity_2m,precipitation,precipitation_probability,weathercode,wind_speed_10m,wind_direction_10m',
                    'daily' => 'precipitation_sum,precipitation_probability_max',
                    'timezone' => 'auto',
                ]);

                if (! $response->successful()) {
                    Log::warning('WeatherService: Open-Meteo request failed.', ['status' => $response->status()]);

                    return ['success' => false, 'message' => 'Weather provider unavailable'];
                }

                $data = $response->json();
                $current = $data['current_weather'] ?? ($data['current'] ?? []);
                $hourly = $data['hourly'] ?? [];

                $currentIndex = $this->currentHourlyIndex($data, $current['time'] ?? null);

                $temperature = $this->number($current['temperature'] ?? $current['temperature_2m'] ?? null)
                    ?? $this->hourlyValue($data, 'temperature_2m', $currentIndex);

                if ($temperature === null) {
                    return ['success' => false, 'message' => 'Current weather unavailable'];
                }

                $humidity = $this->number($current['relative_humidity_2m'] ?? null)
                    ?? $this->hourlyValue($data, 'relative_humidity_2m', $currentIndex);

                $feelsLike = $this->number($current['apparent_temperature'] ?? null)
                    ?? $this->hourlyValue($data, 'apparent_temperature', $currentIndex)
                    ?? $temperature;

                [$precipitation, $precipitationSource, $precipitationProbability] = $this->resolvePrecipitation($data, $current, $currentIndex);

                [$annualRain, $annualRainSource] = $this->resolveAnnualRainfall($lat, $lon, $data, $precipitation ?? 0.0);

                $windSpeed = $this->number($current['wind_speed'] ?? $current['wind_speed_10m'] ?? null)
                    ?? $this->hourlyValue($data, 'wind_speed_10m', $currentIndex);

                $windDegrees = $this->number($current['wind_direction'] ?? $current['wind_direction_10m'] ?? null)
                    ?? $this->hourlyValue($data, 'wind_direction_10m', $currentIndex);

                $weatherCode = $this->number($current['weathercode'] ?? $current['weather_code'] ?? null)
                    ?? $this->hourlyValue($data, 'weathercode', $currentIndex)
                    ?? $this->hourlyValue($data, 'weather_code', $currentIndex);

                $location = $this->resolveLocation($lat, $lon);

                $result = [
                    'success' => true,
                    'temperature' => $temperature,
                    'humidity' => $humidity,
                    'feels_like' => $feelsLike,
                    'precipitation' => $precipitation ?? 0.0,
                    'rain' => $precipitation ?? 0.0,
                    'rainfall' => $precipitation ?? 0.0,
                    'rainfall_source' => $precipitationSource ?? null,
                    'annual_rain' => $annualRain,
                    'annual_rain_source' => $annualRainSource,
                    'precipitation_probability' => $precipitationProbability ?? null,
                    'wind_speed' => $windSpeed,
                    'wind_direction' => $windDegrees === null ? null : $this->decodeWindDirection((int) round($windDegrees)),
                    'weather_condition' => $weatherCode === null ? null : $this->decodeWeatherCode((int) round($weatherCode)),
                    'city' => $location['formatted_location'],
                    'city_name' => $location['city_name'],
                    'state_name' => $location['state_name'],
                    'formatted_location' => $location['formatted_location'],
                    'hourly' => $this->hourlyForecast($data, $currentIndex),
                ];

                $this->logWeather($result, $lat, $lon);

                return $result;
            } catch (Throwable $e) {
                Log::error('WeatherService: unexpected error. '.$e->getMessage());

                return ['success' => false, 'message' => 'Unexpected error'];
            }
        });
    }

    /** @param array<string,mixed> $data */
    private function currentHourlyIndex(array $data, mixed $currentTime): ?int
    {
        if (! is_string($currentTime) || ! isset($data['hourly']['time']) || ! is_array($data['hourly']['time'])) {
            return null;
        }

        $index = array_search($currentTime, $data['hourly']['time'], true);
        if ($index === false) {
            $currentHour = substr($currentTime, 0, 13);
            foreach ($data['hourly']['time'] as $i => $t) {
                if (is_string($t) && str_starts_with($t, $currentHour)) {
                    return $i;
                }
            }
        }

        return $index === false ? null : $index;
    }

    private function hourlyValue(array $data, string $field, ?int $index): ?float
    {
        if ($index === null) {
            return null;
        }

        return $this->number($data['hourly'][$field][$index] ?? null);
    }

    private function nearestHourlyValue(array $data, string $field, ?int $index): ?float
    {
        $values = $data['hourly'][$field] ?? [];
        if (! is_array($values) || $values === []) {
            return null;
        }

        if ($index !== null) {
            for ($d = 0; $d < count($values); $d++) {
                foreach ([$index + $d, $index - $d] as $i) {
                    $v = $this->number($values[$i] ?? null);
                    if ($v !== null) {
                        return $v;
                    }
                }
            }
        }

        foreach ($values as $v) {
            $n = $this->number($v);
            if ($n !== null) {
                return $n;
            }
        }

        return null;
    }

    private function resolvePrecipitation(array $data, array $current, ?int $currentIndex): array
    {
        $probability = $this->number($current['precipitation_probability'] ?? null)
            ?? $this->hourlyValue($data, 'precipitation_probability', $currentIndex)
            ?? $this->number($data['daily']['precipitation_probability_max'][0] ?? null);

        $rain = $this->number($current['rain'] ?? null);
        $showers = $this->number($current['showers'] ?? null);

        if ($rain !== null || $showers !== null) {
            return [($rain ?? 0.0) + ($showers ?? 0.0), 'current_rain_and_showers', $probability];
        }

        $precipitation = $this->nearestHourlyValue($data, 'precipitation', $currentIndex);
        if ($precipitation !== null) {
            return [$precipitation, 'hourly_precipitation', $probability];
        }

        $dailyPrecipitation = $this->number($data['daily']['precipitation_sum'][0] ?? null);
        if ($dailyPrecipitation !== null && $dailyPrecipitation > 0) {
            return [$dailyPrecipitation, 'daily_precipitation_sum', $probability];
        }

        if ($probability !== null) {
            return [round($probability / 100, 2), 'precipitation_probability_estimate', $probability];
        }

        return [null, 'precipitation_unavailable', null];
    }

    private function resolveAnnualRainfall(float $lat, float $lon, array $data, float $precipitation = 0.0): array
    {
        // Try to use daily precipitation from the main provider first (may be a list)
        $daily = $data['daily']['precipitation_sum'] ?? null;
        if (is_array($daily) && $daily !== []) {
            $sum = 0.0;
            foreach ($daily as $val) {
                $n = $this->number($val);
                if ($n !== null) {
                    $sum += $n;
                }
            }
            if ($sum > 0) {
                return [$sum, 'daily_sum'];
            }
        }

        // If main provider didn't provide annual daily sums, try the archive API
        try {
            $year = date('Y');
            $resp = Http::connectTimeout(4)->timeout(10)->get('https://archive-api.open-meteo.com/v1/archive', [
                'latitude' => $lat,
                'longitude' => $lon,
                'start_date' => $year.'-01-01',
                'end_date' => $year.'-12-31',
                'daily' => 'precipitation_sum',
                'timezone' => 'auto',
            ]);

            if ($resp->successful()) {
                $archive = $resp->json();
                $dailyA = $archive['daily']['precipitation_sum'] ?? null;
                if (is_array($dailyA) && $dailyA !== []) {
                    $sumA = 0.0;
                    foreach ($dailyA as $v) {
                        $n = $this->number($v);
                        if ($n !== null) {
                            $sumA += $n;
                        }
                    }
                    if ($sumA > 0) {
                        return [$sumA, 'archive_daily_sum'];
                    }
                }
            }

            // If archive responded but had no useful data, mark unavailable
            if (! $resp->successful()) {
                return [null, 'unavailable'];
            }
        } catch (Throwable $e) {
            Log::debug('WeatherService: archive API failed.', ['message' => $e->getMessage()]);

            return [null, 'unavailable'];
        }

        return [null, 'unavailable'];
    }

    private function resolveLocation(float $lat, float $lon): array
    {
        $locale = app()->getLocale();
        $smallPlaces = ['', 'unknown'];

        try {
            $response = Http::connectTimeout(3)->timeout(6)->get('https://nominatim.openstreetmap.org/reverse', [
                'lat' => $lat,
                'lon' => $lon,
                'format' => 'json',
                'zoom' => 10,
                'addressdetails' => 1,
            ])->throw();

            $address = $response->json('address', []);
            $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? null;
            $state = $address['state'] ?? $address['region'] ?? '';

            // If Nominatim returns a small settlement (village/hamlet) and we have a Google Maps key,
            // consult Google Maps to prefer the larger administrative area instead.
            if (is_array($address) && (isset($address['village']) || isset($address['hamlet']))) {
                $mapsKey = config('services.google_maps.key') ?: env('GOOGLE_MAPS_API_KEY');
                if ($mapsKey) {
                    try {
                        $maps = Http::connectTimeout(3)->timeout(6)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                            'latlng' => $lat.','.$lon,
                            'key' => $mapsKey,
                        ])->throw();

                        $results = $maps->json('results', []);
                        if (! empty($results) && isset($results[0]['address_components']) && is_array($results[0]['address_components'])) {
                            $components = $results[0]['address_components'];
                            $town = $this->googleComponent($components, 'administrative_area_level_3')
                                ?? $this->googleComponent($components, 'administrative_area_level_2')
                                ?? $this->googleComponent($components, 'locality')
                                ?? null;

                            $state = $this->googleComponent($components, 'administrative_area_level_1') ?? $state;
                            if (is_string($town) && $town !== '') {
                                return $this->formatLocation($town, is_string($state) ? $state : '');
                            }
                        }
                    } catch (Throwable $e) {
                        Log::debug('WeatherService: Google Maps geocode (village fallback) failed.', ['message' => $e->getMessage()]);
                    }
                }
            }

            if (is_string($city) && $city !== '' && ! in_array($city, $smallPlaces, true)) {
                return $this->formatLocation($city, is_string($state) ? $state : '');
            }
        } catch (Throwable $e) {
            Log::debug('WeatherService: Nominatim failed.', ['message' => $e->getMessage()]);
        }

        try {
            $response = Http::connectTimeout(3)->timeout(6)->get('https://api.bigdatacloud.net/data/reverse-geocode-client', [
                'latitude' => $lat,
                'longitude' => $lon,
                'localityLanguage' => $locale,
            ])->throw();

            $city = $response->json('city') ?: $response->json('locality');
            $state = $response->json('principalSubdivision', '');

            if (is_string($city) && $city !== '' && ! in_array($city, $smallPlaces, true)) {
                return $this->formatLocation($city, is_string($state) ? $state : '');
            }
        } catch (Throwable $e) {
            Log::debug('WeatherService: BigDataCloud failed.', ['message' => $e->getMessage()]);
        }

        // If Google Maps API key configured, try to fetch place components to prefer larger administrative area
        try {
            $mapsKey = config('services.google_maps.key') ?: env('GOOGLE_MAPS_API_KEY');
            if ($mapsKey) {
                $maps = Http::connectTimeout(3)->timeout(6)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'latlng' => $lat.','.$lon,
                    'key' => $mapsKey,
                ])->throw();

                $results = $maps->json('results', []);
                if (! empty($results) && isset($results[0]['address_components']) && is_array($results[0]['address_components'])) {
                    $components = $results[0]['address_components'];
                    // Prefer administrative area level 3/2 etc. Fall back to locality
                    $town = $this->googleComponent($components, 'administrative_area_level_3')
                        ?? $this->googleComponent($components, 'administrative_area_level_2')
                        ?? $this->googleComponent($components, 'locality')
                        ?? $this->googleComponent($components, 'sublocality')
                        ?? null;

                    $state = $this->googleComponent($components, 'administrative_area_level_1') ?? $state;

                    if (is_string($town) && $town !== '' && ! in_array(strtolower($town), $smallPlaces, true)) {
                        return $this->formatLocation($town, is_string($state) ? $state : '');
                    }
                }
            }
        } catch (Throwable $e) {
            Log::debug('WeatherService: Google Maps geocode failed.', ['message' => $e->getMessage()]);
        }

        return $this->formatLocation('Detected Location', '');
    }

    private function googleComponent(array $components, string $type): ?string
    {
        foreach ($components as $c) {
            if (in_array($type, $c['types'] ?? [], true) && isset($c['long_name']) && is_string($c['long_name'])) {
                return $c['long_name'];
            }
        }

        return null;
    }

    private function formatLocation(string $city, string $state): array
    {
        $city = $this->cleanLocationName($city);
        $state = $this->cleanLocationName($state);

        return [
            'city_name' => $city,
            'state_name' => $state,
            'formatted_location' => $state === '' ? $city : $city.', '.$state,
        ];
    }

    private function cleanLocationName(string $location): string
    {
        $cleaned = preg_replace('/\s+(tahsil|tehsil|block|district|village)$/i', '', trim($location));

        return $cleaned === null || $cleaned === '' ? trim($location) : $cleaned;
    }

    private function logWeather(array $weather, float $lat, float $lon): void
    {
        if (! isset($weather['humidity']) || ! isset($weather['wind_speed'])) {
            return;
        }

        try {
            WeatherLog::create([
                'city' => $weather['city'] ?? '',
                'temperature' => round((float) ($weather['temperature'] ?? 0), 2),
                'humidity' => round((float) ($weather['humidity'] ?? 0), 2),
                'rainfall' => round((float) ($weather['precipitation'] ?? 0), 2),
                'wind_speed' => round((float) ($weather['wind_speed'] ?? 0), 2),
                'weather_condition' => $weather['weather_condition'] ?? null,
                'latitude' => $lat,
                'longitude' => $lon,
            ]);
        } catch (Throwable $e) {
            Log::warning('WeatherService: WeatherLog failed.', ['message' => $e->getMessage()]);
        }
    }

    private function number(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function decodeWeatherCode(int $code): string
    {
        return match (true) {
            $code === 0 => 'Clear Sky',
            in_array($code, [1, 2, 3], true) => 'Partly Cloudy',
            in_array($code, [45, 48], true) => 'Foggy',
            in_array($code, [51, 53, 55], true) => 'Drizzle',
            in_array($code, [61, 63, 65], true) => 'Rainy',
            in_array($code, [71, 73, 75], true) => 'Snowy',
            in_array($code, [80, 81, 82], true) => 'Rain Showers',
            in_array($code, [95, 96, 99], true) => 'Thunderstorm',
            default => 'Variable',
        };
    }

    private function decodeWindDirection(int $degrees): string
    {
        $directions = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'];
        $normalized = (($degrees % 360) + 360) % 360;

        return $directions[(int) round($normalized / 45)];
    }

    private function hourlyForecast(array $data, ?int $currentIndex): array
    {
        $hourly = $data['hourly'] ?? [];
        $times = $hourly['time'] ?? [];
        $result = [];

        // Determine the starting index (next hours after currentIndex)
        $start = 0;
        if (is_int($currentIndex)) {
            $start = $currentIndex + 1;
        }

        for ($i = $start; $i < count($times) && count($result) < 5; $i++) {
            $result[] = [
                'time' => $times[$i],
                'temp' => $this->number($hourly['temperature_2m'][$i] ?? null),
                'rain' => $this->number($hourly['precipitation'][$i] ?? null),
                'weather_code' => $this->number($hourly['weathercode'][$i] ?? $hourly['weather_code'][$i] ?? null),
            ];
        }

        return $result;
    }
}

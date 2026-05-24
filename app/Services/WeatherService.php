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
     * @return array<string, mixed>
     */
    public function getWeather(float $lat, float $lon, bool $refresh = false): array
    {
        $cacheKey = 'weather_'.app()->getLocale().'_'.round($lat, 5).'_'.round($lon, 5);

        if ($refresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addMinutes(3), function () use ($lat, $lon, $refresh): array {
            try {
                $response = Http::connectTimeout(4)->timeout(12)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude'  => $lat,
                    'longitude' => $lon,
                    'current'   => implode(',', [
                        'temperature_2m',
                        'relative_humidity_2m',
                        'apparent_temperature',
                        'precipitation',
                        'rain',
                        'showers',
                        'precipitation_probability',
                        'weather_code',
                        'wind_speed_10m',
                        'wind_direction_10m',
                    ]),
                    'hourly' => implode(',', [
                        'temperature_2m',
                        'apparent_temperature',
                        'relative_humidity_2m',
                        'dew_point_2m',
                        'precipitation',
                        'rain',
                        'showers',
                        'precipitation_probability',
                        'weather_code',
                        'wind_speed_10m',
                        'wind_direction_10m',
                    ]),
                                        'daily'              => 'precipitation_sum,precipitation_probability_max',
                    'timezone'           => 'auto',
                    'temperature_unit'   => 'celsius',
                    'precipitation_unit' => 'mm',
                    'wind_speed_unit'    => 'kmh',
                    'forecast_days'      => 2,
                ]);

                if (! $response->successful()) {
                    Log::warning('WeatherService: Open-Meteo request failed.', ['status' => $response->status()]);
                    return ['success' => false, 'message' => 'Weather provider unavailable'];
                }

                $data         = $response->json();
                $current      = $data['current'] ?? [];
                $legacyCurrent = $data['current_weather'] ?? [];
                $currentTime  = $current['time'] ?? $legacyCurrent['time'] ?? null;
                $currentIndex = $this->currentHourlyIndex($data, $currentTime);

                $temperature = $this->number($current['temperature_2m'] ?? $legacyCurrent['temperature'] ?? null)
                    ?? $this->hourlyValue($data, 'temperature_2m', $currentIndex);

                if ($temperature === null) {
                    return ['success' => false, 'message' => 'Current weather unavailable'];
                }

                $humidity  = $this->number($current['relative_humidity_2m'] ?? null)
                    ?? $this->hourlyValue($data, 'relative_humidity_2m', $currentIndex);
                $feelsLike = $this->number($current['apparent_temperature'] ?? null)
                    ?? $this->hourlyValue($data, 'apparent_temperature', $currentIndex)
                    ?? $temperature;

                [$precipitation, $precipitationSource, $precipitationProbability] =
                    $this->resolvePrecipitation($data, $current, $currentIndex);

                if ($precipitation === null) {
                    return ['success' => false, 'message' => 'Current rainfall unavailable'];
                }

                [$annualRain, $annualRainSource] =
                    $this->resolveAnnualRainfall($lat, $lon, $data, $precipitation, $refresh);

                $windSpeed   = $this->number($current['wind_speed_10m'] ?? $legacyCurrent['windspeed'] ?? null)
                    ?? $this->hourlyValue($data, 'wind_speed_10m', $currentIndex);
                $windDegrees = $this->number($current['wind_direction_10m'] ?? $legacyCurrent['winddirection'] ?? null)
                    ?? $this->hourlyValue($data, 'wind_direction_10m', $currentIndex);
                $weatherCode = $this->number($current['weather_code'] ?? $legacyCurrent['weathercode'] ?? null)
                    ?? $this->hourlyValue($data, 'weather_code', $currentIndex)
                    ?? $this->hourlyValue($data, 'weathercode', $currentIndex);

                $location = $this->resolveLocation($lat, $lon);

                $result = [
                    'success'                  => true,
                    'temperature'              => $temperature,
                    'humidity'                 => $humidity,
                    'feels_like'               => $feelsLike,
                    'apparent_temperature'     => $feelsLike,
                    'precipitation'            => $precipitation,
                    'rain'                     => $precipitation,
                    'rainfall'                 => $precipitation,
                    'rainfall_source'          => $precipitationSource,
                    'annual_rain'              => $annualRain,
                    'annual_rain_source'       => $annualRainSource,
                    'precipitation_probability' => $precipitationProbability,
                    'wind_speed'               => $windSpeed,
                    'wind_direction'           => $windDegrees === null ? null : $this->decodeWindDirection((int) round($windDegrees)),
                    'weather_condition'        => $weatherCode === null ? null : $this->decodeWeatherCode((int) round($weatherCode)),
                    'city'                     => $location['formatted_location'],
                    'city_name'                => $location['city_name'],
                    'state_name'               => $location['state_name'],
                    'formatted_location'       => $location['formatted_location'],
                    'hourly'                   => $this->hourlyForecast($data, $currentIndex),
                ];

                $this->logWeather($result, $lat, $lon);

                return $result;

            } catch (Throwable $exception) {
                Log::error('WeatherService: unexpected error.', ['message' => $exception->getMessage()]);
                return ['success' => false, 'message' => 'Unexpected error'];
            }
        });
    }

    /** @param array<string, mixed> $data */
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

    /** @param array<string, mixed> $data */
    private function hourlyValue(array $data, string $field, ?int $index): ?float
    {
        if ($index === null) return null;
        return $this->number($data['hourly'][$field][$index] ?? null);
    }

    /** @param array<string, mixed> $data */
    private function nearestHourlyValue(array $data, string $field, ?int $index): ?float
    {
        $values = $data['hourly'][$field] ?? [];
        if (! is_array($values) || $values === []) return null;

        if ($index !== null) {
            for ($d = 0; $d < count($values); $d++) {
                foreach ([$index + $d, $index - $d] as $i) {
                    $v = $this->number($values[$i] ?? null);
                    if ($v !== null) return $v;
                }
            }
        }

        foreach ($values as $v) {
            $n = $this->number($v);
            if ($n !== null) return $n;
        }

        return null;
    }

    /**
     * @param  array<string, mixed> $data
     * @param  array<string, mixed> $current
     * @return array{0: ?float, 1: string, 2: ?float}
     */
    private function resolvePrecipitation(array $data, array $current, ?int $currentIndex): array
    {
        $probability = $this->number($current['precipitation_probability'] ?? null)
            ?? $this->hourlyValue($data, 'precipitation_probability', $currentIndex)
            ?? $this->number($data['daily']['precipitation_probability_max'][0] ?? null);



        $rain    = $this->number($current['rain'] ?? null);
        $showers = $this->number($current['showers'] ?? null);
        if ($rain !== null || $showers !== null) {
            return [($rain ?? 0.0) + ($showers ?? 0.0), 'current_rain_and_showers', $probability];
        }

        $precipitation = $this->nearestHourlyValue($data, 'precipitation', $currentIndex);
        if ($precipitation !== null) return [$precipitation, 'hourly_precipitation', $probability];

        $dailyPrecipitation = $this->number($data['daily']['precipitation_sum'][0] ?? null);
if ($dailyPrecipitation !== null && $dailyPrecipitation > 0) return [$dailyPrecipitation, 'daily_precipitation_sum', $probability];

        if ($probability !== null) return [round($probability / 100, 2), 'precipitation_probability_estimate', $probability];

        return [null, 'precipitation_unavailable', null];
    }

    /**
     * @param  array<string, mixed> $forecast
     * @return array{0: ?float, 1: string}
     */
    private function resolveAnnualRainfall(float $lat, float $lon, array $forecast, float $currentRainfall, bool $refresh): array
    {
        $cacheKey = 'annual_rain_'.app()->getLocale().'_'.round($lat, 5).'_'.round($lon, 5);

        if ($refresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($lat, $lon): array {
            try {
                $response = Http::connectTimeout(4)->timeout(12)->get('https://archive-api.open-meteo.com/v1/archive', [
                    'latitude'           => $lat,
                    'longitude'          => $lon,
                    'start_date'         => now()->subDays(365)->toDateString(),
                    'end_date'           => now()->subDay()->toDateString(),
                    'daily'              => 'precipitation_sum',
                    'timezone'           => 'auto',
                    'precipitation_unit' => 'mm',
                ]);

                if ($response->successful()) {
                    $dailyRainfall = array_filter(
                        $response->json('daily.precipitation_sum', []),
                        fn (mixed $v): bool => is_numeric($v)
                    );

                    if ($dailyRainfall !== []) {
                        return [round(array_sum($dailyRainfall), 1), 'historical_365_day_precipitation'];
                    }
                }
            } catch (Throwable $exception) {
                Log::debug('WeatherService: historical rainfall failed.', ['message' => $exception->getMessage()]);
            }

            // ── Fallback: state-based research estimate ──────────────────
            // Called only when archive API is unreachable
            try {
                $location = $this->resolveLocation($lat, $lon);
                $state    = strtolower($location['state_name'] ?? '');
            } catch (Throwable $e) {
                $state = '';
            }

            $estimate = match (true) {
                str_contains($state, 'punjab') || str_contains($state, 'haryana') || str_contains($state, 'delhi')      => 650,
                str_contains($state, 'rajasthan')                                                                         => 420,
                str_contains($state, 'uttar pradesh') || str_contains($state, 'bihar') || str_contains($state, 'jharkhand') => 1050,
                str_contains($state, 'west bengal') || str_contains($state, 'assam') || str_contains($state, 'meghalaya')  => 2000,
                str_contains($state, 'kerala') || str_contains($state, 'goa') || str_contains($state, 'karnataka')         => 2600,
                str_contains($state, 'maharashtra') || str_contains($state, 'gujarat') || str_contains($state, 'madhya pradesh') => 975,
                str_contains($state, 'tamil nadu') || str_contains($state, 'andhra pradesh') || str_contains($state, 'telangana') => 925,
                str_contains($state, 'himachal') || str_contains($state, 'uttarakhand') || str_contains($state, 'jammu')    => 1300,
                default => null,
            };

            return $estimate !== null
                ? [(float) $estimate, 'state_estimate_fallback']
                : [null, 'unavailable'];
        });
    }

    /**
     * @param  array<string, mixed> $data
     * @return list<array{time: string, temp: ?float, condition: ?string, rainfall: ?float}>
     */
    private function hourlyForecast(array $data, ?int $currentIndex): array
    {
        $times = $data['hourly']['time'] ?? [];
        if (! is_array($times) || $currentIndex === null) return [];

        $hourly    = [];
        $lastIndex = min(count($times) - 1, $currentIndex + 5);

        for ($i = $currentIndex + 1; $i <= $lastIndex; $i++) {
            $code     = $this->number($data['hourly']['weather_code'][$i] ?? $data['hourly']['weathercode'][$i] ?? null);
            $hourly[] = [
                'time'      => (string) $times[$i],
                'temp'      => $this->number($data['hourly']['temperature_2m'][$i] ?? null),
                'condition' => $code === null ? null : $this->decodeWeatherCode((int) round($code)),
                'rainfall'  => $this->number($data['hourly']['precipitation'][$i] ?? null),
            ];
        }

        return $hourly;
    }

    /** @return array{city_name: string, state_name: string, formatted_location: string} */
    private function resolveLocation(float $lat, float $lon): array
    {
        $locale          = app()->getLocale() === 'hi' ? 'hi' : 'en';
        $smallPlaces     = [];
        $broaderLocation = null;

        foreach ([10, 12] as $zoom) {
            try {
                $response = Http::connectTimeout(3)->timeout(6)
                    ->withHeaders(['User-Agent' => 'CropYieldPortal/1.0'])
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'lat'             => $lat,
                        'lon'             => $lon,
                        'format'          => 'jsonv2',
                        'zoom'            => $zoom,
                        'addressdetails'  => 1,
                        'accept-language' => $locale,
                        'layer'           => 'address',
                    ]);

                if (! $response->successful()) continue;

                $address = $response->json('address', []);

                foreach (['village', 'hamlet', 'isolated_dwelling'] as $type) {
                    if (isset($address[$type]) && is_string($address[$type])) {
                        $smallPlaces[] = $address[$type];
                    }
                }

                $city     = $address['city'] ?? $address['town'] ?? $address['municipality'] ?? $address['city_district'] ?? null;
                $state    = $address['state'] ?? $address['province'] ?? '';
                $district = $address['state_district'] ?? $address['county'] ?? null;

                if (is_string($city) && $city !== '') {
                    return $this->formatLocation($city, is_string($state) ? $state : '');
                }

                if ($broaderLocation === null && is_string($district) && $district !== '') {
                    $broaderLocation = $this->formatLocation($district, is_string($state) ? $state : '');
                }
            } catch (Throwable $e) {
                Log::debug('WeatherService: Nominatim failed.', ['message' => $e->getMessage()]);
            }
        }

        $googleMapsKey = config('services.google_maps.key');
        if (is_string($googleMapsKey) && $googleMapsKey !== '') {
            try {
                $response = Http::connectTimeout(3)->timeout(6)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'latlng'   => $lat.','.$lon,
                    'key'      => $googleMapsKey,
                    'language' => $locale,
                ]);

                if ($response->successful()) {
                    $components = $response->json('results.0.address_components', []);
                    $state      = $this->googleComponent($components, 'administrative_area_level_1') ?? '';

                    foreach (['locality', 'postal_town', 'administrative_area_level_3', 'administrative_area_level_2'] as $type) {
                        $city = $this->googleComponent($components, $type);
                        if ($city !== null && ! in_array($city, $smallPlaces, true)) {
                            return $this->formatLocation($city, $state);
                        }
                    }
                }
            } catch (Throwable $e) {
                Log::debug('WeatherService: Google geocoding failed.', ['message' => $e->getMessage()]);
            }
        }

        try {
            $response = Http::connectTimeout(3)->timeout(6)->get('https://api.bigdatacloud.net/data/reverse-geocode-client', [
                'latitude'         => $lat,
                'longitude'        => $lon,
                'localityLanguage' => $locale,
            ]);

            if ($response->successful()) {
                $city  = $response->json('city') ?: $response->json('locality');
                $state = $response->json('principalSubdivision', '');

                if (is_string($city) && $city !== '' && ! in_array($city, $smallPlaces, true)) {
                    return $this->formatLocation($city, is_string($state) ? $state : '');
                }
            }
        } catch (Throwable $e) {
            Log::debug('WeatherService: BigDataCloud failed.', ['message' => $e->getMessage()]);
        }

        return $broaderLocation ?? $this->formatLocation('Detected Location', '');
    }

    /** @param array<int, array<string, mixed>> $components */
    private function googleComponent(array $components, string $type): ?string
    {
        foreach ($components as $c) {
            if (in_array($type, $c['types'] ?? [], true) && isset($c['long_name']) && is_string($c['long_name'])) {
                return $c['long_name'];
            }
        }
        return null;
    }

    /** @return array{city_name: string, state_name: string, formatted_location: string} */
    private function formatLocation(string $city, string $state): array
    {
        $city  = $this->cleanLocationName($city);
        $state = $this->cleanLocationName($state);
        return [
            'city_name'          => $city,
            'state_name'         => $state,
            'formatted_location' => $state === '' ? $city : $city.', '.$state,
        ];
    }

    private function cleanLocationName(string $location): string
    {
        $cleaned = preg_replace('/\s+(tahsil|tehsil|block|district|village)$/i', '', trim($location));
        return $cleaned === null || $cleaned === '' ? trim($location) : $cleaned;
    }

    /** @param array<string, mixed> $weather */
    private function logWeather(array $weather, float $lat, float $lon): void
    {
        if (! is_numeric($weather['humidity']) || ! is_numeric($weather['wind_speed'])) return;

        try {
            WeatherLog::create([
                'city'              => $weather['city'],
                'temperature'       => round((float) $weather['temperature'], 2),
                'humidity'          => round((float) $weather['humidity'], 2),
                'rainfall'          => round((float) $weather['precipitation'], 2),
                'wind_speed'        => round((float) $weather['wind_speed'], 2),
                'weather_condition' => $weather['weather_condition'],
                'latitude'          => $lat,
                'longitude'         => $lon,
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
            $code === 0                              => 'Clear Sky',
            in_array($code, [1, 2, 3], true)        => 'Partly Cloudy',
            in_array($code, [45, 48], true)         => 'Foggy',
            in_array($code, [51, 53, 55], true)     => 'Drizzle',
            in_array($code, [61, 63, 65], true)     => 'Rainy',
            in_array($code, [71, 73, 75], true)     => 'Snowy',
            in_array($code, [80, 81, 82], true)     => 'Rain Showers',
            in_array($code, [95, 96, 99], true)     => 'Thunderstorm',
            default                                  => 'Variable',
        };
    }

    private function decodeWindDirection(int $degrees): string
    {
        $directions       = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'];
        $normalizedDegrees = (($degrees % 360) + 360) % 360;
        return $directions[(int) round($normalizedDegrees / 45)];
    }
}
@extends('layouts.app')
@section('title', __('messages.weather_title') . ' — ' . __('messages.app_name'))

@section('content')
<div class="page-wrapper">

    {{-- Page Header --}}
    <div class="mb-6">
        <p class="section-label">{{ __('messages.weather_subtitle') }}</p>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.weather_title') }}</h1>
    </div>

    {{-- Location Detect Card --}}
    <div class="stat-card mb-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-gray-900 dark:text-white text-sm mb-0.5">{{ __('messages.weather_your_current') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.weather_subtitle') }}</p>
                <div id="auto-location-badge" class="hidden mt-2 inline-flex items-center gap-1.5 text-xs text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-2.5 py-1 rounded-full font-medium">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                    {{ __('messages.weather_auto_detected') }}
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                {{-- City Search Autocomplete --}}
                <div class="relative w-full sm:w-64">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="opacity-50 text-xs">🔍</span>
                        </span>
                        <input type="text" id="city-search-input"
                            class="form-input w-full text-sm py-2 pl-8 pr-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 transition-colors"
                            placeholder="{{ __('messages.weather_search_placeholder') }}"
                            autocomplete="off">
                    </div>
                    <ul id="city-suggestions-list"
                        class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-y-auto hidden text-sm">
                    </ul>
                </div>

                <button id="fetch-weather-btn" onclick="window.fetchWeatherByLocation()" class="btn-primary py-2 px-4 text-sm flex items-center justify-center gap-2 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>{{ __('messages.weather_detect_location') }}</span>
                </button>
            </div>
        </div>

        <div id="location-denied-warning" class="hidden mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-md text-xs text-amber-800 dark:text-amber-300">
            <strong>{{ __('messages.weather_location_denied') }}</strong>
        </div>

        <div id="auto-detect-spinner" class="hidden mt-3 flex items-center gap-2 text-gray-500 text-xs">
            <div class="animate-spin h-3.5 w-3.5 border-2 border-green-500 border-t-transparent rounded-full"></div>
            {{ __('messages.weather_auto_loading') }}
        </div>

        <div id="weather-status" class="mt-2"></div>
    </div>

    {{-- Charts --}}
    <div class="grid lg:grid-cols-2 gap-5 mb-5">
        <div class="stat-card">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">{{ __('messages.weather_temp_by_city') }}</h3>
            <div class="chart-container" style="height:240px">
                <canvas id="tempChart"></canvas>
            </div>
        </div>
        <div class="stat-card">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">{{ __('messages.weather_humidity_rainfall') }}</h3>
            <div class="chart-container" style="height:240px">
                <canvas id="humidityChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Weather Logs Table --}}
    <div class="stat-card overflow-hidden p-0">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ __('messages.weather_recent_logs') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.weather_table_city') }}</th>
                        <th>{{ __('messages.weather_table_temp') }}</th>
                        <th>{{ __('messages.weather_table_humidity') }}</th>
                        <th>{{ __('messages.weather_table_rainfall') }}</th>
                        <th class="hidden sm:table-cell">{{ __('messages.weather_table_wind') }}</th>
                        <th class="hidden md:table-cell">{{ __('messages.weather_table_condition') }}</th>
                        <th class="hidden lg:table-cell">{{ __('messages.weather_table_time') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weatherLogs as $log)
                    <tr>
                        <td class="font-medium text-gray-900 dark:text-white">
                            {{ $log->city === 'Unknown' ? __('messages.weather_unknown') : ($log->city ?? __('messages.weather_unknown')) }}
                        </td>
                        <td class="font-semibold text-orange-600 dark:text-orange-400">{{ $log->temperature }}°C</td>
                        <td class="text-blue-600 dark:text-blue-400">{{ $log->humidity }}%</td>
                        <td class="text-cyan-600 dark:text-cyan-400">{{ $log->rainfall }}mm</td>
                        <td class="hidden sm:table-cell text-gray-500 dark:text-gray-400">{{ $log->wind_speed }} {{ __('messages.weather_km_h') }}</td>
                        <td class="hidden md:table-cell">
                            <span class="badge bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800">
                                {{ Lang::has('messages.weather_cond_' . strtolower(str_replace(' ', '_', $log->weather_condition ?? ''))) ? __('messages.weather_cond_' . strtolower(str_replace(' ', '_', $log->weather_condition ?? ''))) : ($log->weather_condition ?? '—') }}
                            </span>
                        </td>
                        <td class="hidden lg:table-cell text-gray-400 text-xs">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($weatherLogs->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $weatherLogs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
const isDark = document.documentElement.classList.contains('dark');
const labelColor = isDark ? '#9ca3af' : '#6b7280';
const gridColor  = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
const labels = @json($weatherLabels);
const baseOpts = {
    responsive: true, maintainAspectRatio: false,
    scales: {
        x: { grid: { color: gridColor }, ticks: { color: labelColor, font: { size: 10 } } },
        y: { grid: { color: gridColor }, ticks: { color: labelColor } }
    },
    plugins: { legend: { labels: { color: labelColor, font: { size: 11 } } } }
};

new Chart(document.getElementById('tempChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{ label: '{{ __('messages.temperature_label') }} (°C)', data: @json($tempData),
            backgroundColor: 'rgba(249,115,22,0.7)', borderColor: '#f97316', borderWidth: 1, borderRadius: 4 }]
    }, options: baseOpts
});

new Chart(document.getElementById('humidityChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: '{{ __('messages.humidity_label') }} (%)', data: @json($humidityData), backgroundColor: 'rgba(14,165,233,0.7)', borderColor: '#0ea5e9', borderWidth: 1, borderRadius: 4 },
            { label: '{{ __('messages.rainfall_label') }} (mm)', data: @json($rainfallData), backgroundColor: 'rgba(6,182,212,0.5)', borderColor: '#06b6d4', borderWidth: 1, borderRadius: 4 },
        ]
    }, options: baseOpts
});
</script>
@endpush

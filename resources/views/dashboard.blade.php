@extends('layouts.app')
@section('title', __('messages.nav_dashboard') . ' — ' . __('messages.app_name'))

@section('content')
<div class="page-wrapper">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <p class="section-label">{{ __('messages.dashboard_welcome_back') }}, {{ $user->name }}</p>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.dashboard_title') }}</h1>
        </div>
        <a href="{{ route('predictions.create') }}" class="btn-primary py-2 px-4">
            + {{ __('messages.hero_cta') }}
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="stat-card flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-md flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalPredictions }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.total_predictions') }}</p>
            </div>
        </div>

        <div class="stat-card flex items-center gap-4">
            <div class="w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-md flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($avgYield, 2) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.avg_yield') }}</p>
            </div>
        </div>

        <div class="stat-card flex items-center gap-4">
            <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-md flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-900 dark:text-white truncate max-w-[120px]">
                    @if($bestCrop)
                        {{ Lang::has('messages.crop_' . strtolower(str_replace(' ', '_', $bestCrop))) ? __('messages.crop_' . strtolower(str_replace(' ', '_', $bestCrop))) : $bestCrop }}
                    @else —
                    @endif
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.best_crop') }}</p>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid lg:grid-cols-3 gap-5 mb-5">
        <div class="lg:col-span-2 stat-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ __('messages.yield_trend') }}</h3>
                <span class="text-xs text-gray-400">{{ __('messages.last_7_predictions') }}</span>
            </div>
            @if($totalPredictions > 0)
                <div class="chart-container">
                    <canvas id="yieldTrendChart"></canvas>
                </div>
            @else
                <div class="flex items-center justify-center h-48 text-gray-400 text-sm">{{ __('messages.dashboard_no_trend') }}</div>
            @endif
        </div>

        <div class="stat-card">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">{{ __('messages.crop_distribution') }}</h3>
            @if($totalPredictions > 0 && $cropDistribution->count() > 0)
                <div class="chart-container" style="height:220px">
                    <canvas id="cropDoughnutChart"></canvas>
                </div>
            @else
                <div class="flex items-center justify-center h-32 text-gray-400 text-sm">{{ __('messages.no_data') }}</div>
            @endif
        </div>
    </div>

    {{-- Recent Predictions Table --}}
    <div class="stat-card overflow-hidden p-0">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ __('messages.recent_activity') }}</h3>
            <a href="{{ route('predictions.index') }}" class="text-xs text-green-600 dark:text-green-400 hover:underline font-medium">{{ __('messages.view_all') }} →</a>
        </div>
        @if($userPredictions->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.crop') }}</th>
                        <th>{{ __('messages.predicted_yield') }}</th>
                        <th class="hidden sm:table-cell">{{ __('messages.risk_level') }}</th>
                        <th class="hidden md:table-cell">{{ __('messages.predicted_on') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($userPredictions->take(5) as $pred)
                    <tr>
                        <td class="font-medium text-gray-900 dark:text-white">{{ $pred->crop->translated_crop_name ?? 'N/A' }}</td>
                        <td class="font-semibold text-green-600 dark:text-green-400">
                            {{ $pred->predicted_yield }}
                            <span class="text-gray-400 font-normal text-xs">{{ __('messages.tonnes_per_hectare') }}</span>
                        </td>
                        <td class="hidden sm:table-cell">
                            <span class="badge {{ $pred->risk_level === 'Low' ? 'badge-low' : ($pred->risk_level === 'Medium' ? 'badge-medium' : 'badge-high') }}">
                                {{ __('messages.risk_' . strtolower($pred->risk_level)) }}
                            </span>
                        </td>
                        <td class="hidden md:table-cell text-gray-400 text-xs">{{ $pred->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('predictions.show', $pred) }}" class="text-xs text-green-600 dark:text-green-400 hover:underline">{{ __('messages.view') }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-12 text-gray-400">
                <svg class="w-8 h-8 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm mb-3">{{ __('messages.prediction_no_data') }}</p>
                <a href="{{ route('predictions.create') }}" class="btn-primary text-xs py-1.5 px-3">{{ __('messages.hero_cta') }}</a>
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

    @if($totalPredictions > 0)
        new Chart(document.getElementById('yieldTrendChart'), {
            type: 'line',
            data: {
                labels: @json($yieldTrendLabels),
                datasets: [{
                    label: '{{ __('messages.predicted_yield') }} ({{ __('messages.tonnes_per_hectare') }})',
                    data: @json($yieldTrendData),
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22,163,74,0.06)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#16a34a',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: labelColor, font: { size: 10 }, maxRotation: 35 } },
                    y: { grid: { color: gridColor }, ticks: { color: labelColor }, beginAtZero: true }
                },
                plugins: { legend: { display: false } }
            }
        });

        @if($cropDistribution->count() > 0)
            new Chart(document.getElementById('cropDoughnutChart'), {
                type: 'doughnut',
                data: {
                    labels: @json($cropDistribution->keys()),
                    datasets: [{
                        data: @json($cropDistribution->values()),
                        backgroundColor: ['#16a34a','#0891b2','#d97706','#7c3aed','#db2777','#dc2626','#059669','#ca8a04'],
                        borderWidth: 2,
                        borderColor: isDark ? '#1f2937' : '#ffffff',
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: labelColor, font: { size: 10 }, padding: 8 } } }
                }
            });
        @endif
    @endif
</script>
@endpush
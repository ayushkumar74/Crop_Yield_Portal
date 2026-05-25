@extends('layouts.app')
@section('title', __('messages.result_title') . ' — ' . $prediction->crop->translated_crop_name)

@section('content')
<div class="page-wrapper max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
        <div>
            <a href="{{ route('predictions.index') }}" class="text-sm text-gray-500 hover:text-gray-900 dark:hover:text-gray-300 mb-2 inline-flex items-center gap-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ __('messages.prediction_history') }}
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                {{ $prediction->crop->translated_crop_name }} 
                <span class="text-lg font-medium px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full border border-green-200 dark:border-green-800">
                    {{ __('messages.crop_analysis') }}
                </span>
            </h1>
            <p class="text-sm text-gray-500 mt-2">{{ __('messages.generated_on') }} {{ $prediction->created_at->format('d M Y, g:i A') }}</p>
        </div>
        <div class="flex items-center gap-2">
            @auth
                @if(auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('admin.predictions.destroy', $prediction) }}" 
                        onsubmit="return confirm('{{ __('messages.delete_confirm') }}')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger py-2.5 px-5 shadow-sm text-xs font-semibold rounded-lg flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </form>
                @endif
            @endauth
            <a href="{{ route('predictions.create') }}" class="btn-primary py-2.5 px-5 shadow-sm text-decoration-none">
                {{ __('messages.new_prediction_btn') }}
            </a>
        </div>
    </div>

    {{-- Top Metrics Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        
        {{-- Crop Health Score --}}
        <div class="stat-card flex flex-col justify-between">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('messages.crop_health_score') }}</p>
                <div class="p-1.5 bg-blue-50 dark:bg-blue-900/20 rounded-md text-blue-600 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div>
                <div class="flex items-end gap-2">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $cropHealth }}</h3>
                    <span class="text-sm text-gray-500 mb-1">/ 100</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-3">
                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $cropHealth }}%"></div>
                </div>
            </div>
        </div>

        {{-- Yield & Risk --}}
        <div class="stat-card bg-green-600 dark:bg-green-700 border-green-500 text-white flex flex-col justify-between">
            <div class="flex justify-between items-start mb-2">
                <p class="text-green-100 text-xs font-semibold uppercase tracking-wide">{{ __('messages.predicted_yield') }}</p>
                <span class="px-2 py-0.5 rounded text-xs font-medium bg-white/20 text-white border border-white/20">
                    {{ __('messages.risk_' . strtolower($prediction->risk_level)) }}
                </span>
            </div>
            <div>
                <div class="flex items-end gap-2">
                    <h3 class="text-4xl font-bold text-white leading-none">{{ $prediction->predicted_yield }}</h3>
                    <span class="text-green-200 text-sm font-medium mb-1">t/ha</span>
                </div>
                <p class="text-xs text-green-200 mt-2 font-medium">{{ __('messages.tonnes_per_acre_est', ['yield' => $yieldAcre]) }}</p>
            </div>
        </div>

        {{-- Profitability --}}
        <div class="stat-card flex flex-col justify-between">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('messages.est_revenue') }}</p>
                <div class="p-1.5 bg-amber-50 dark:bg-amber-900/20 rounded-md text-amber-600 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">₹{{ number_format($estimatedProfitPerAcre) }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ __('messages.per_acre_est') }}</p>
                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">{{ __('messages.difficulty_label') }}: <span class="font-bold text-gray-600 dark:text-gray-300">{{ $difficulty }}</span></p>
            </div>
        </div>

        {{-- Input Summary --}}
        <div class="stat-card">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ __('messages.your_input_data') }}</p>
            <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase">{{ __('messages.temperature_label') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $prediction->temperature }}°C</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase">{{ __('messages.rainfall_label') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $prediction->rainfall }} mm</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase">{{ __('messages.humidity_label') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $prediction->humidity }}%</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase">{{ __('messages.soil_ph') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $prediction->soil_ph }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid lg:grid-cols-3 gap-6">
        
        {{-- Left Column: Charts and Risk --}}
        <div class="space-y-6">
            {{-- Radar Chart --}}
            <div class="stat-card">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">{{ __('messages.conditions_vs_ideal') }}</h3>
                <div class="chart-container" style="height:240px"><canvas id="radarChart"></canvas></div>
            </div>

            {{-- Risk Breakdown --}}
            <div class="stat-card">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">{{ __('messages.risk_analysis_breakdown') }}</h3>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.heat_stress_risk') }}</span>
                            <span class="text-gray-500">{{ $riskBreakdown['heat'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                            <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $riskBreakdown['heat'] }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.drought_risk') }}</span>
                            <span class="text-gray-500">{{ $riskBreakdown['drought'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $riskBreakdown['drought'] }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.waterlogging_risk') }}</span>
                            <span class="text-gray-500">{{ $riskBreakdown['waterlog'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                            <div class="bg-cyan-500 h-1.5 rounded-full" style="width: {{ $riskBreakdown['waterlog'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- AI Insight --}}
            <div class="stat-card border-l-4 border-l-green-500">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    {{ __('messages.ai_expert_insight') }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    {{ $cropProfile['ai_insight'] ?? 'Ensure optimal irrigation and timely fertilizer application for maximum yield.' }}
                </p>
            </div>
        </div>

        {{-- Right Column: Detailed Tabbed Guidance --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Actionable Timelines --}}
            <div class="stat-card">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-5 border-b border-gray-100 dark:border-gray-700 pb-3">{{ __('messages.farming_action_timeline') }}</h3>
                
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 flex items-center justify-center font-bold text-xs">1</div>
                            <div class="h-full w-px bg-gray-200 dark:bg-gray-700 mt-2"></div>
                        </div>
                        <div class="pb-6">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ __('messages.sowing_soil_prep') }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2"><span class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.season_label') }}:</span> {{ $cropProfile['sowing_season'] ?? 'Check local guidelines' }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed bg-gray-50 dark:bg-gray-800/50 p-2.5 rounded border border-gray-100 dark:border-gray-700">{{ $cropProfile['soil_preparation'] ?? 'Prepare well-drained soil.' }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 flex items-center justify-center font-bold text-xs">2</div>
                            <div class="h-full w-px bg-gray-200 dark:bg-gray-700 mt-2"></div>
                        </div>
                        <div class="pb-6">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ __('messages.irrigation_schedule_label') }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2"><span class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.water_req_label') }}:</span> {{ $cropProfile['water_requirement'] ?? 'Medium' }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed bg-blue-50/50 dark:bg-blue-900/10 p-2.5 rounded border border-blue-100/50 dark:border-blue-800/30">{{ $cropProfile['irrigation_schedule'] ?? 'Maintain regular moisture.' }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400 flex items-center justify-center font-bold text-xs">3</div>
                            <div class="h-full w-px bg-gray-200 dark:bg-gray-700 mt-2"></div>
                        </div>
                        <div class="pb-6">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ __('messages.fertilizer_npk') }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2"><span class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.npk_guide_label') }}:</span> {{ $cropProfile['npk_guidance'] ?? 'As per soil test' }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed bg-green-50/50 dark:bg-green-900/10 p-2.5 rounded border border-green-100/50 dark:border-green-800/30">{{ $cropProfile['fertilizer_recommendations'] ?? 'Apply basal dose and split nitrogen.' }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 flex items-center justify-center font-bold text-xs">4</div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ __('messages.harvest_maturity') }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2"><span class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.maturity_label') }}:</span> {{ $cropProfile['maturity_time'] ?? '90-120 days' }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed bg-amber-50/50 dark:bg-amber-900/10 p-2.5 rounded border border-amber-100/50 dark:border-amber-800/30">{{ $cropProfile['harvest_duration'] ?? 'Harvest at physiological maturity.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Advanced Crop Management --}}
            <div class="grid sm:grid-cols-2 gap-6">
                
                <div class="stat-card">
                    <h4 class="text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span>🛡️</span> {{ __('messages.disease_pest_precautions') }}
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">{{ $cropProfile['disease_pest'] ?? 'Monitor regularly and apply integrated pest management (IPM).' }}</p>
                </div>

                <div class="stat-card">
                    <h4 class="text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span>🍃</span> {{ __('messages.organic_alternatives') }}
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">{{ $cropProfile['organic_suggestions'] ?? 'Use crop rotation, farmyard manure, and biopesticides.' }}</p>
                </div>

                <div class="stat-card">
                    <h4 class="text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span>📈</span> {{ __('messages.yield_optimization_tips') }}
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">{{ $cropProfile['yield_tips'] ?? 'Maintain exact plant population and weed early.' }}</p>
                </div>

                <div class="stat-card">
                    <h4 class="text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span>🌦️</span> {{ __('messages.weather_risk_warnings') }}
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">{{ $cropProfile['weather_risks'] ?? 'Vulnerable to extreme temperatures and unseasonal rain.' }}</p>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const isDark = document.documentElement.classList.contains('dark');
const gridColor  = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
const labelColor = isDark ? '#9ca3af' : '#6b7280';

new Chart(document.getElementById('radarChart'), {
    type: 'radar',
    data: {
        labels: @json($chartData['labels']),
        datasets: [
            { label: '{{ __('messages.chart_your_conditions') }}', data: @json($chartData['actual']),
              backgroundColor: 'rgba(22,163,74,0.12)', borderColor: '#16a34a', borderWidth: 2,
              pointBackgroundColor: '#16a34a', pointRadius: 3 },
            { label: '{{ __('messages.chart_ideal') }}', data: @json($chartData['ideal']),
              backgroundColor: 'rgba(107,114,128,0.06)', borderColor: 'rgba(107,114,128,0.4)',
              borderWidth: 1, borderDash: [4,4], pointRadius: 0 },
        ],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        scales: { r: { min: 0, max: 100, grid: { color: gridColor },
            ticks: { color: labelColor, backdropColor: 'transparent', stepSize: 25, font: { size: 9 } },
            pointLabels: { color: labelColor, font: { size: 10, weight: '500' } } } },
        plugins: { legend: { labels: { color: labelColor, font: { size: 11 } } } }
    }
});
</script>
@endpush

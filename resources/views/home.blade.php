@extends('layouts.app')

@section('title', __('messages.nav_home') . ' — ' . __('messages.app_name'))

@section('content')

{{-- ─── Hero ─────────────────────────────────────────────────────────────── --}}
<section class="hero-section">
<<<<<<< HEAD
    <div class="max-w-7xl mx-auto px-6 py-12 lg:py-16">
        <div class="grid lg:grid-cols-2 gap-10 items-center">

            {{-- Left: Text --}}
            <div class="flex flex-col justify-center">
                <h1 class="text-3xl lg:text-4xl font-semibold text-gray-900 dark:text-white leading-tight mb-4">
                    {{ __('messages.hero_title') }}
                </h1>
                <p class="text-base text-gray-600 dark:text-gray-300 leading-relaxed mb-6 max-w-lg">
                    {{ __('messages.hero_subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 mb-8">
                    <a href="{{ auth()->check() ? route('predictions.create') : route('login') }}" class="btn-primary py-2 px-4 text-center sm:text-left sm:inline-block text-sm">
                        {{ __('messages.hero_cta') }}
                    </a>
                    <a href="{{ auth()->check() ? route('weather') : route('login') }}" class="btn-secondary py-2 px-4 text-sm text-center sm:text-left sm:inline-block">
=======
    <div class="max-w-7xl mx-auto px-6 py-16 lg:py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            {{-- Left: Text --}}
            <div>
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-2.5 py-1 rounded-full mb-5">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                    {{ __('messages.hero_tag') }}
                </span>
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white leading-tight mb-4">
                    {{ __('messages.hero_title') }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 leading-relaxed mb-8 max-w-lg text-sm">
                    {{ __('messages.hero_subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 mb-8">
                    <a href="{{ auth()->check() ? route('predictions.create') : route('login') }}" class="btn-primary py-2.5 px-5">
                        {{ __('messages.hero_cta') }}
                    </a>
                    <a href="{{ auth()->check() ? route('weather') : route('login') }}" class="btn-secondary py-2.5 px-5">
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                        {{ __('messages.weather_title') }}
                    </a>
                </div>

                {{-- Trust Badges Row --}}
<<<<<<< HEAD
                <div class="flex flex-col sm:flex-row gap-y-3 gap-x-4 mb-8 text-sm text-gray-600 dark:text-gray-400 border-t border-gray-200 dark:border-gray-800 pt-6">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
=======
                <div class="flex flex-wrap items-center gap-y-2 gap-x-5 mb-10 text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('messages.trust_accuracy') }}</span>
                    </div>
<<<<<<< HEAD
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <span>{{ __('messages.trust_weather') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
=======
                    <div class="w-1 h-1 bg-stone-300 dark:bg-stone-700 rounded-full hidden sm:block"></div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ __('messages.trust_weather') }}</span>
                    </div>
                    <div class="w-1 h-1 bg-stone-300 dark:bg-stone-700 rounded-full hidden sm:block"></div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 0A18.015 18.015 0 0110 14.588M6 10a18.27 18.27 0 005.135 5.865M11 21l4-10m4 10a18.257 18.257 0 00-5.138-5.863" />
                        </svg>
                        <span>{{ __('messages.trust_bilingual') }}</span>
                    </div>
                </div>

                {{-- Stats Row --}}
<<<<<<< HEAD
                <div class="grid grid-cols-3 gap-8">
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $cropCount }}+</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.stats_crops') }}</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $predictionCount }}+</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.stats_predictions') }}</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">94%</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.stats_accuracy') }}</p>
=======
                <div class="flex items-center gap-8 pt-6 border-t border-stone-200 dark:border-stone-800">
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $cropCount }}+</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('messages.stats_crops') }}</p>
                    </div>
                    <div class="w-px h-8 bg-stone-200 dark:bg-stone-800"></div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $predictionCount }}+</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('messages.stats_predictions') }}</p>
                    </div>
                    <div class="w-px h-8 bg-stone-200 dark:bg-stone-800"></div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">94%</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('messages.stats_accuracy') }}</p>
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                    </div>
                </div>
            </div>

<<<<<<< HEAD
            {{-- Right: Premium Dashboard Preview --}}
            <div class="hidden lg:flex items-center justify-center">
                <div class="w-full max-w-sm">
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.live_weather_dashboard') }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-semibold">
                                    <span class="w-2 h-2 bg-emerald-400 dark:bg-emerald-400 rounded-full"></span>
                                    {{ __('messages.live_badge') }}
                                </span>
                            </div>
                        </div>

                        <div class="p-4 space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-3 border border-gray-100 dark:border-gray-800">
                                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('messages.temperature_label') }}</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">28°C</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-3 border border-gray-100 dark:border-gray-800">
                                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('messages.humidity_label') }}</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">72%</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-3 border border-gray-100 dark:border-gray-800">
                                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('messages.rainfall_label') }}</p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">850mm</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-3 border border-gray-100 dark:border-gray-800">
                                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('messages.predicted_yield') }}</p>
                                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">4.2 t/ha</p>
                                </div>
                            </div>

                            <div class="rounded-md p-3 bg-gray-100 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 text-gray-900 dark:text-white">
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">{{ __('messages.suitability_score') }}</p>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xl font-bold">87</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.risk_low') }}</p>
                                    </div>
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                            <p class="text-xs text-gray-600 dark:text-gray-400">Real-time agriculture analytics</p>
=======
            {{-- Right: Preview Card --}}
            <div class="hidden lg:block">
                <div class="hero-preview-wrapper">
                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-stone-100 dark:border-stone-800">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('messages.live_weather_dashboard') }}</p>
                            </div>
                            <span class="flex items-center gap-1 text-xs font-medium text-green-600 dark:text-green-400">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                {{ __('messages.live_badge') }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="p-3 bg-stone-50 dark:bg-stone-900 rounded-md border border-stone-100 dark:border-stone-900">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.temperature_label') }}</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">28°C</p>
                            </div>
                            <div class="p-3 bg-stone-50 dark:bg-stone-900 rounded-md border border-stone-100 dark:border-stone-900">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.humidity_label') }}</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">72%</p>
                            </div>
                            <div class="p-3 bg-stone-50 dark:bg-stone-900 rounded-md border border-stone-100 dark:border-stone-900">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.rainfall_label') }}</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">850mm</p>
                            </div>
                            <div class="p-3 bg-stone-50 dark:bg-stone-900 rounded-md border border-stone-100 dark:border-stone-900">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.predicted_yield') }}</p>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">4.2 t/ha</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-green-600 rounded-md text-white">
                            <div>
                                <p class="text-xs text-green-100 mb-0.5">{{ __('messages.suitability_score') }}</p>
                                <p class="font-bold">87/100 — {{ __('messages.risk_low') }}</p>
                            </div>
                            <svg class="w-5 h-5 text-green-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ─── Features ─────────────────────────────────────────────────────────── --}}
<<<<<<< HEAD
<section class="py-20 bg-gray-50 dark:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
            <p class="section-label">{{ __('messages.why_choose_us') }}</p>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">{{ __('messages.built_for_modern_agri') }}</h2>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto text-lg leading-relaxed">{{ __('messages.why_choose_us_desc') }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
=======
<section class="py-16 bg-gray-50 dark:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-10">
            <p class="section-label">{{ __('messages.why_choose_us') }}</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.built_for_modern_agri') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto text-sm">{{ __('messages.why_choose_us_desc') }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-5">
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
            @php
            $features = [
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>', 'title' => __('messages.feature_weather'), 'desc' => __('messages.feature_weather_desc')],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>', 'title' => __('messages.feature_analytics'), 'desc' => __('messages.feature_analytics_desc')],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>', 'title' => __('messages.feature_risk'), 'desc' => __('messages.feature_risk_desc')],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>', 'title' => __('messages.feature_location'), 'desc' => __('messages.feature_location_desc')],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>', 'title' => __('messages.feature_ai'), 'desc' => __('messages.feature_ai_desc')],
                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>', 'title' => __('messages.feature_crops'), 'desc' => __('messages.feature_crops_desc')],
            ];
            @endphp

            @foreach($features as $f)
<<<<<<< HEAD
            <div class="stat-card hover:shadow-lg transition-shadow">
                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $f['icon'] !!}</svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $f['title'] }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $f['desc'] }}</p>
=======
            <div class="stat-card">
                <div class="w-8 h-8 bg-green-50 dark:bg-green-900/20 rounded-md flex items-center justify-center mb-3">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $f['icon'] !!}</svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1 text-sm">{{ $f['title'] }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-xs leading-relaxed">{{ $f['desc'] }}</p>
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── How It Works ─────────────────────────────────────────────────────── --}}
<<<<<<< HEAD
<section class="py-20">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
            <p class="section-label">{{ __('messages.how_it_works') }}</p>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('messages.how_it_works') }}</h2>
        </div>
        <div class="grid md:grid-cols-4 gap-8 relative">
=======
<section class="py-16">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-10">
            <p class="section-label">{{ __('messages.how_it_works') }}</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.how_it_works') }}</h2>
        </div>
        <div class="grid md:grid-cols-4 gap-6 relative">
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
            @foreach([
                ['step' => '1', 'title' => __('messages.step_detect_location'), 'desc' => __('messages.step_detect_location_desc')],
                ['step' => '2', 'title' => __('messages.step_fetch_weather'), 'desc' => __('messages.step_fetch_weather_desc')],
                ['step' => '3', 'title' => __('messages.step_select_crop'), 'desc' => __('messages.step_select_crop_desc')],
                ['step' => '4', 'title' => __('messages.step_get_results'), 'desc' => __('messages.step_get_results_desc')],
            ] as $step)
            <div class="relative text-center">
                @if(!$loop->last)
                    <div class="hidden md:block absolute top-4 left-[55%] w-[90%] h-px bg-gray-200 dark:bg-gray-700"></div>
                @endif
<<<<<<< HEAD
                <div class="w-12 h-12 bg-emerald-600 dark:bg-emerald-700 text-white rounded-full flex items-center justify-center text-base font-bold mx-auto mb-4 relative z-10 hover:shadow-lg transition-shadow">{{ $step['step'] }}</div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2 text-lg">{{ $step['title'] }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $step['desc'] }}</p>
=======
                <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-xs font-bold mx-auto mb-3 relative z-10">{{ $step['step'] }}</div>
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">{{ $step['title'] }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">{{ $step['desc'] }}</p>
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── Recent Predictions ────────────────────────────────────────────────── --}}
<<<<<<< HEAD
@auth
@if($recentPredictions->count() > 0)
<section class="py-14 bg-gray-50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <p class="section-label">{{ __('messages.live_activity') }}</p>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.recent_activity') }}</h2>
            </div>
            <a href="{{ route('predictions.create') }}" class="btn-primary text-xs py-2 px-4">
=======
@if($recentPredictions->count() > 0)
<section class="py-12 bg-gray-50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="section-label">{{ __('messages.live_activity') }}</p>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('messages.recent_activity') }}</h2>
            </div>
            <a href="{{ route('predictions.create') }}" class="btn-primary text-xs py-1.5 px-3">
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                {{ __('messages.hero_cta') }}
            </a>
        </div>
        <div class="stat-card overflow-hidden p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.crop') }}</th>
                        <th>{{ __('messages.predicted_yield') }}</th>
                        <th>{{ __('messages.suitability_score') }}</th>
                        <th>{{ __('messages.risk_level') }}</th>
                        <th class="hidden sm:table-cell">{{ __('messages.predicted_on') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPredictions as $pred)
                    <tr>
                        <td class="font-medium text-gray-900 dark:text-white">{{ $pred->crop->translated_crop_name ?? '—' }}</td>
<<<<<<< HEAD
                        <td class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $pred->predicted_yield }} <span class="text-gray-400 font-normal text-xs">{{ __('messages.tonnes_per_hectare') }}</span></td>
=======
                        <td class="font-semibold text-green-600 dark:text-green-400">{{ $pred->predicted_yield }} <span class="text-gray-400 font-normal text-xs">{{ __('messages.tonnes_per_hectare') }}</span></td>
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                        <td class="text-gray-600 dark:text-gray-400">{{ $pred->suitability_score }}/100</td>
                        <td>
                            <span class="badge {{ $pred->risk_level === 'Low' ? 'badge-low' : ($pred->risk_level === 'Medium' ? 'badge-medium' : 'badge-high') }}">
                                {{ __('messages.risk_' . strtolower($pred->risk_level)) }}
                            </span>
                        </td>
                        <td class="hidden sm:table-cell text-gray-400 text-xs">{{ $pred->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endif
<<<<<<< HEAD
@endauth

{{-- ─── CTA Banner ────────────────────────────────────────────────────────── --}}
<section class="py-20">
    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-gradient-to-br from-emerald-600 to-teal-600 dark:from-emerald-700 dark:to-teal-700 rounded-xl p-12 lg:p-16 text-center shadow-xl">
            <h2 class="text-3xl lg:text-4xl font-bold text-white mb-3">{{ __('messages.cta_title') }}</h2>
            <p class="text-emerald-100 text-lg mb-8 max-w-xl mx-auto">{{ __('messages.cta_subtitle') }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ auth()->check() ? route('predictions.create') : route('login') }}"
                   class="bg-white text-emerald-700 font-semibold px-7 py-3 rounded-lg text-base hover:bg-emerald-50 transition-colors inline-block">
=======

{{-- ─── CTA Banner ────────────────────────────────────────────────────────── --}}
<section class="py-16">
    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-green-600 dark:bg-green-700 rounded-lg p-10 text-center">
            <h2 class="text-2xl font-bold text-white mb-2">{{ __('messages.cta_title') }}</h2>
            <p class="text-green-100 text-sm mb-6 max-w-md mx-auto">{{ __('messages.cta_subtitle') }}</p>
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ auth()->check() ? route('predictions.create') : route('login') }}"
                   class="bg-white text-green-700 font-semibold px-5 py-2.5 rounded-md text-sm hover:bg-green-50 transition-colors">
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                    {{ __('messages.cta_btn') }}
                </a>
                @guest
                <a href="{{ route('register') }}"
<<<<<<< HEAD
                   class="border-2 border-white text-white font-semibold px-7 py-3 rounded-lg text-base hover:bg-white hover:text-emerald-700 transition-colors inline-block">
=======
                   class="border border-green-300 text-white font-semibold px-5 py-2.5 rounded-md text-sm hover:bg-green-500 transition-colors">
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                    {{ __('messages.register_btn') }}
                </a>
                @endguest
            </div>
        </div>
    </div>
</section>

@endsection

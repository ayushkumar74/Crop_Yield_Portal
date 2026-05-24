@extends('layouts.app')
@section('title', __('messages.prediction_history') . ' — ' . __('messages.app_name'))

@section('content')
<div class="page-wrapper">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <p class="section-label">{{ __('messages.recent_activity') }}</p>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.prediction_history') }}</h1>
        </div>
        <a href="{{ route('predictions.create') }}" class="btn-primary py-2 px-4">
            + {{ __('messages.predict_btn') }}
        </a>
    </div>

    @if($predictions->count() > 0)
    <div class="stat-card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="pl-5">#</th>
                        <th>{{ __('messages.crop') }}</th>
                        <th class="hidden sm:table-cell">{{ __('messages.weather_conditions') }}</th>
                        <th>{{ __('messages.predicted_yield') }}</th>
                        <th class="hidden md:table-cell">{{ __('messages.suitability_score') }}</th>
                        <th>{{ __('messages.risk_level') }}</th>
                        <th class="hidden lg:table-cell">{{ __('messages.predicted_on') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($predictions as $pred)
                    <tr>
                        <td class="pl-5 text-gray-400 text-xs">{{ $pred->id }}</td>
                        <td class="font-semibold text-gray-900 dark:text-white">
                            {{ $pred->crop->translated_crop_name ?? '—' }}
                        </td>
                        <td class="hidden sm:table-cell text-xs text-gray-500 dark:text-gray-400">
                            {{ $pred->temperature }}°C · {{ $pred->rainfall }}mm · {{ $pred->humidity }}%
                        </td>
                        <td>
                            <span class="font-semibold text-green-600 dark:text-green-400">{{ $pred->predicted_yield }}</span>
                            <span class="text-xs text-gray-400 ml-0.5">{{ __('messages.tonnes_per_hectare') }}</span>
                        </td>
                        <td class="hidden md:table-cell">
                            <div class="flex items-center gap-2">
                                <div class="w-16 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $pred->suitability_score > 75 ? 'bg-green-500' : ($pred->suitability_score > 45 ? 'bg-amber-500' : 'bg-red-500') }}"
                                        style="width: {{ $pred->suitability_score }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $pred->suitability_score }}%</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $pred->risk_level === 'Low' ? 'badge-low' : ($pred->risk_level === 'Medium' ? 'badge-medium' : 'badge-high') }}">
                                @if($pred->risk_level === 'Low') {{ __('messages.risk_low') }}
                                @elseif($pred->risk_level === 'Medium') {{ __('messages.risk_medium') }}
                                @else {{ __('messages.risk_high') }} @endif
                            </span>
                        </td>
                        <td class="hidden lg:table-cell text-xs text-gray-400">{{ $pred->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('predictions.show', $pred) }}"
                                    class="text-xs text-green-600 dark:text-green-400 hover:underline font-medium">
                                    {{ __('messages.view') }} →
                                </a>
                                @auth
                                    @if(auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('admin.predictions.destroy', $pred) }}"
                                            onsubmit="return confirm('{{ __('messages.delete_confirm') }}')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline font-medium bg-transparent border-none cursor-pointer p-0">
                                                {{ __('messages.delete') }}
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($predictions->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $predictions->links() }}
        </div>
        @endif
    </div>

    @else
    <div class="stat-card text-center py-16">
        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ __('messages.prediction_no_data_title') }}</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">{{ __('messages.prediction_no_data_subtitle') }}</p>
        <a href="{{ route('predictions.create') }}" class="btn-primary py-2 px-4">{{ __('messages.hero_cta') }}</a>
    </div>
    @endif

</div>
@endsection

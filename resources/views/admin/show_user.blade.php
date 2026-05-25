@extends('layouts.app')
@section('title', 'User Details: ' . $user->name . ' — Admin')

@section('content')
<div class="page-wrapper max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb and Page Header --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-2 mb-1.5 text-xs text-gray-400">
                <a href="{{ route('admin.dashboard') }}" class="hover:underline">Admin Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.users') }}" class="hover:underline">Users</a>
                <span>/</span>
                <span class="text-gray-500 dark:text-gray-400">Profile Details</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                {{ $user->name }}
                <span class="badge {{ $user->role === 'admin' ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600' }} text-xs">
                    {{ ucfirst($user->role) }}
                </span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Account ID: #{{ $user->id }} · Joined {{ $user->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="flex gap-2">
            @if($user->id !== auth()->id())
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                onsubmit="return confirm('Completely remove this user and all their prediction data? This action cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger py-2 px-4 text-xs font-semibold rounded-lg shadow-sm">Remove Account</button>
            </form>
            @endif
            <a href="{{ route('admin.users') }}" class="btn-secondary py-2 px-4 text-xs font-semibold rounded-lg shadow-sm">Back to List</a>
        </div>
    </div>

    {{-- Info Cards Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        {{-- Card 1: Complete User Info & Preferences --}}
        <div class="stat-card flex flex-col justify-between space-y-6">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-4 pb-2 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2">
                    <span>👤</span> Full Account Details
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider">Email Address</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->email }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider">Theme Preference</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($user->theme_preference ?? 'light') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider">Language Preference</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ strtoupper($user->language_preference ?? 'en') }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-1.5">Notification Channels</p>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $prefs = $user->notification_preferences ?? [];
                                $emailOpt = data_get($prefs, 'email', true);
                                $smsOpt = data_get($prefs, 'sms', false);
                                $pushOpt = data_get($prefs, 'push', false);
                            @endphp
                            <span class="badge {{ $emailOpt ? 'bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-800 text-gray-400 border-gray-150 dark:border-gray-700' }} text-xs">
                                Email: {{ $emailOpt ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="badge {{ $smsOpt ? 'bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-800 text-gray-400 border-gray-150 dark:border-gray-700' }} text-xs">
                                SMS: {{ $smsOpt ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="badge {{ $pushOpt ? 'bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-800 text-gray-400 border-gray-150 dark:border-gray-700' }} text-xs">
                                Push: {{ $pushOpt ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                <h4 class="font-semibold text-gray-900 dark:text-white text-xs mb-3 flex items-center gap-2">
                    <span>📍</span> Geolocation & Coordinates
                </h4>
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider">Latitude</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->latitude !== null ? number_format($user->latitude, 6) : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider">Longitude</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->longitude !== null ? number_format($user->longitude, 6) : '—' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider">Last Detected Location</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->last_detected_location ?? 'Not Registered' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wider">Geolocation Permission</p>
                        <span class="badge {{ $user->location_permission_granted ? 'bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800' }} text-xs mt-1">
                            {{ $user->location_permission_granted ? 'Granted' : 'Denied / Prompts' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Crop-Wise Prediction Breakdown --}}
        <div class="stat-card lg:col-span-2 flex flex-col justify-between">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-4 pb-2 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2">
                    <span>📊</span> Crop-Wise Analytics
                </h3>
                
                @if($cropWiseStats->count() > 0)
                <div class="overflow-x-auto">
                    <table class="data-table text-left">
                        <thead>
                            <tr>
                                <th>Crop</th>
                                <th class="text-center">Predictions</th>
                                <th class="text-right">Avg Yield</th>
                                <th class="text-right">Avg Suitability</th>
                                <th class="text-center">Risk Profile</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cropWiseStats as $stat)
                            <tr>
                                <td class="font-semibold text-gray-900 dark:text-white">{{ $stat['crop_name'] }}</td>
                                <td class="text-center font-medium text-gray-700 dark:text-gray-300">{{ $stat['count'] }}</td>
                                <td class="text-right font-semibold text-green-600 dark:text-green-400">{{ $stat['avg_yield'] }} t/ha</td>
                                <td class="text-right font-medium text-gray-700 dark:text-gray-300">{{ $stat['avg_suitability'] }}%</td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        @if($stat['low_risk_count'] > 0)
                                        <span class="badge badge-low text-[10px] py-0.5 px-1.5" title="Low risk counts">L:{{ $stat['low_risk_count'] }}</span>
                                        @endif
                                        @if($stat['med_risk_count'] > 0)
                                        <span class="badge badge-medium text-[10px] py-0.5 px-1.5" title="Medium risk counts">M:{{ $stat['med_risk_count'] }}</span>
                                        @endif
                                        @if($stat['high_risk_count'] > 0)
                                        <span class="badge badge-high text-[10px] py-0.5 px-1.5" title="High risk counts">H:{{ $stat['high_risk_count'] }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <span class="text-3xl mb-2">🌾</span>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No predictions have been recorded for this user yet.</p>
                </div>
                @endif
            </div>
            
            <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center text-xs text-gray-500">
                <span>Unique Crop Species Explored: <strong>{{ $cropWiseStats->count() }}</strong></span>
                <span>Cumulative Predictions: <strong>{{ $user->predictions_count }}</strong></span>
            </div>
        </div>

    </div>

    {{-- Prediction History Table --}}
    <div class="stat-card p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center flex-wrap gap-2">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                <span>🕒</span> Prediction Records History
            </h3>
            <span class="badge bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800 text-xs">
                Page {{ $predictions->currentPage() }} of {{ max(1, $predictions->lastPage()) }}
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="pl-5">ID</th>
                        <th>Crop</th>
                        <th class="hidden sm:table-cell">Conditions</th>
                        <th>Predicted Yield</th>
                        <th class="hidden md:table-cell">Suitability</th>
                        <th>Risk Level</th>
                        <th class="hidden lg:table-cell">Created At</th>
                        <th class="pr-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($predictions as $pred)
                    <tr>
                        <td class="pl-5 text-xs text-gray-400">#{{ $pred->id }}</td>
                        <td class="font-medium text-gray-900 dark:text-white">{{ $pred->crop->translated_crop_name ?? $pred->crop->name ?? '—' }}</td>
                        <td class="hidden sm:table-cell text-xs text-gray-500 dark:text-gray-400">
                            {{ $pred->temperature }}°C · {{ $pred->rainfall }}mm · {{ $pred->humidity }}%
                        </td>
                        <td class="font-semibold text-green-600 dark:text-green-400">{{ $pred->predicted_yield }} t/ha</td>
                        <td class="hidden md:table-cell text-gray-500 dark:text-gray-400">{{ $pred->suitability_score }}%</td>
                        <td>
                            <span class="badge {{ $pred->risk_level === 'Low' ? 'badge-low' : ($pred->risk_level === 'Medium' ? 'badge-medium' : 'badge-high') }}">
                                {{ $pred->risk_level }}
                            </span>
                        </td>
                        <td class="hidden lg:table-cell text-xs text-gray-400">{{ $pred->created_at->format('d M Y, h:i A') }}</td>
                        <td class="pr-5 text-right">
                            <div class="inline-flex items-center gap-3">
                                <a href="{{ route('predictions.show', $pred) }}" class="text-xs text-green-600 dark:text-green-400 hover:underline font-medium">View</a>
                                <form method="POST" action="{{ route('admin.predictions.destroy', $pred) }}" 
                                    onsubmit="return confirm('Permanently delete this prediction record #{{ $pred->id }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline font-medium bg-transparent border-none cursor-pointer p-0">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-400 dark:text-gray-500">
                            No predictions made yet by this user.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($predictions->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $predictions->appends(['predictions_page' => $predictions->currentPage()])->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

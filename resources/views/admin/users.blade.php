@extends('layouts.app')
@section('title', 'Users — Admin')

@section('content')
<div class="page-wrapper">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mb-1 block">← Admin Dashboard</a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Users</h1>
        </div>
    </div>

    <div class="stat-card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="hidden md:table-cell">Account Details</th>
                        <th class="hidden lg:table-cell">Total Predictions</th>
                        <th class="hidden lg:table-cell">Prediction Activity</th>
                        <th class="hidden sm:table-cell">Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td>
                            <a href="{{ route('admin.users.show', $u) }}" class="font-semibold text-green-600 dark:text-green-400 hover:underline">
                                {{ $u->name }}
                            </a>
                        </td>
                        <td class="text-gray-500 dark:text-gray-400">{{ $u->email }}</td>
                        <td class="hidden md:table-cell">
                            <div class="flex flex-col gap-1">
                                <span class="badge w-max {{ $u->role === 'admin' ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600' }}">
                                    {{ ucfirst($u->role) }}
                                </span>
                                <span class="text-[10px] text-gray-400 leading-none">
                                    Lang: {{ strtoupper($u->language_preference ?? 'en') }} · Theme: {{ ucfirst($u->theme_preference ?? 'light') }}
                                </span>
                            </div>
                        </td>
                        <td class="hidden lg:table-cell font-medium text-gray-900 dark:text-white">{{ $u->predictions_count }}</td>
                        <td class="hidden lg:table-cell">
                            @if($u->latestPrediction)
                            <div class="text-xs">
                                <span class="font-medium text-gray-700 dark:text-gray-300">
                                    {{ $u->latestPrediction->crop->translated_crop_name ?? $u->latestPrediction->crop->name ?? '—' }}
                                </span>
                                <span class="text-[10px] text-gray-400 block mt-0.5">
                                    Latest: {{ $u->latestPrediction->created_at->diffForHumans() }}
                                </span>
                            </div>
                            @else
                            <span class="text-xs text-gray-400">No predictions</span>
                            @endif
                        </td>
                        <td class="hidden sm:table-cell text-xs text-gray-500 dark:text-gray-400">{{ $u->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.users.show', $u) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Profile</a>
                                @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                    onsubmit="return confirm('Remove {{ addslashes($u->name) }}? This will delete all their predictions.')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline font-medium bg-transparent border-none cursor-pointer p-0">Remove</button>
                                </form>
                                @else
                                <span class="text-xs text-gray-300 dark:text-gray-600">You</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection

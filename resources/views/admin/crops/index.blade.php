@extends('layouts.app')
@section('title', 'Manage Crops — Admin')

@section('content')
<div class="page-wrapper">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mb-1 block">← Admin Dashboard</a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manage Crops</h1>
        </div>
        <a href="{{ route('admin.crops.create') }}" class="btn-primary py-2 px-4">+ Add New Crop</a>
    </div>

    <div class="stat-card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Crop Name</th>
                        <th class="hidden sm:table-cell">Temp Range</th>
                        <th class="hidden md:table-cell">Rainfall</th>
                        <th class="hidden md:table-cell">Humidity</th>
                        <th>Base Yield</th>
                        <th class="hidden lg:table-cell">Predictions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($crops as $crop)
                    <tr>
                        <td class="font-semibold text-gray-900 dark:text-white">{{ $crop->name }}</td>
                        <td class="hidden sm:table-cell text-gray-500 dark:text-gray-400">{{ $crop->min_temp }}–{{ $crop->max_temp }}°C</td>
                        <td class="hidden md:table-cell text-gray-500 dark:text-gray-400">{{ $crop->min_rainfall }}–{{ $crop->max_rainfall }}mm</td>
                        <td class="hidden md:table-cell text-gray-500 dark:text-gray-400">{{ $crop->min_humidity }}–{{ $crop->max_humidity }}%</td>
                        <td class="font-semibold text-green-600 dark:text-green-400">{{ $crop->base_yield }} t/ha</td>
                        <td class="hidden lg:table-cell text-gray-500 dark:text-gray-400">{{ $crop->predictions_count }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.crops.edit', $crop) }}" class="btn-secondary text-xs py-1 px-2.5">Edit</a>
                                <form method="POST" action="{{ route('admin.crops.destroy', $crop) }}"
                                    onsubmit="return confirm('Delete {{ $crop->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger text-xs py-1 px-2.5">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($crops->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $crops->links() }}</div>
        @endif
    </div>
</div>
@endsection

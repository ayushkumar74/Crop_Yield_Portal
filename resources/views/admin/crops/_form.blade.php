{{-- Shared form partial for create/edit crop --}}
@php $v = fn($f) => old($f, $crop?->$f ?? ''); @endphp

<div class="space-y-4">
    <div>
        <label class="form-label">Crop Name</label>
        <input type="text" name="name" class="form-input" value="{{ $v('name') }}" placeholder="e.g. Rice, Wheat">
        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="form-label">Min Temperature (°C)</label>
            <input type="number" step="0.1" name="min_temp" class="form-input" value="{{ $v('min_temp') }}">
            @error('min_temp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Max Temperature (°C)</label>
            <input type="number" step="0.1" name="max_temp" class="form-input" value="{{ $v('max_temp') }}">
            @error('max_temp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="form-label">Min Rainfall (mm)</label>
            <input type="number" step="1" name="min_rainfall" class="form-input" value="{{ $v('min_rainfall') }}">
            @error('min_rainfall') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Max Rainfall (mm)</label>
            <input type="number" step="1" name="max_rainfall" class="form-input" value="{{ $v('max_rainfall') }}">
            @error('max_rainfall') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="form-label">Min Humidity (%)</label>
            <input type="number" step="0.1" name="min_humidity" class="form-input" value="{{ $v('min_humidity') }}">
            @error('min_humidity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Max Humidity (%)</label>
            <input type="number" step="0.1" name="max_humidity" class="form-input" value="{{ $v('max_humidity') }}">
            @error('max_humidity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="form-label">Base Yield (tonnes/hectare)</label>
        <input type="number" step="0.01" name="base_yield" class="form-input" value="{{ $v('base_yield') }}" placeholder="e.g. 4.5">
        @error('base_yield') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

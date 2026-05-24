@extends('layouts.app')
@section('title', __('messages.predict_title') . ' — ' . __('messages.app_name'))

@section('content')
<div class="page-wrapper">

    {{-- Page Header --}}
    <div class="mb-6">
        <p class="section-label">{{ __('messages.predict_ai_powered') }}</p>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ __('messages.predict_title') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.predict_subtitle') }}</p>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="mb-5 p-3.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
        <p class="font-medium text-red-700 dark:text-red-400 text-sm mb-1.5">{{ __('messages.please_fix_errors') }}</p>
        <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400 space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('predictions.store') }}" method="POST" id="prediction-form">
        @csrf
        <input type="hidden" name="crop_name" id="crop-name-hidden" value="">
        <div class="grid lg:grid-cols-3 gap-5">

            {{-- ── Left Column ─────────────────────────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Crop Selection --}}
                <div class="stat-card">
                    <h2 class="font-semibold text-gray-900 dark:text-white text-sm mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        {{ __('messages.select_crop') }}
                    </h2>

                    {{-- Search Crop Input --}}
                    <div class="relative mb-3.5">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                            <input type="text" id="crop-search-input"
                                class="form-input pl-9 pr-8 w-full"
                                placeholder="{{ app()->getLocale() === 'hi' ? 'फसल खोजें (उदा. गन्ना, टमाटर, Rice)...' : 'Search crop (e.g. Sugarcane, Tomato, Rice)...' }}"
                                autocomplete="off">
                            <button type="button" id="crop-search-clear" class="absolute inset-y-0 right-0 pr-3 items-center hidden text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        {{-- Autocomplete Dropdown suggestions --}}
                        <ul id="crop-suggestions-list"
                            class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-y-auto hidden text-sm transition-all duration-200">
                        </ul>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="crop-grid">
                        @foreach($defaultCrops as $crop)
                        @php
                            $cropKey = 'messages.crop_' . strtolower(str_replace(' ', '_', $crop->name));
                            $nameEn = $crop->name;
                            $nameHi = __($cropKey, [], 'hi') !== $cropKey ? __($cropKey, [], 'hi') : $crop->name;
                        @endphp
                        <label class="cursor-pointer crop-card-label transition-all duration-300 ease-out"
                            data-crop-id="{{ $crop->id }}"
                            data-crop-name-en="{{ strtolower($nameEn) }}"
                            data-crop-name-hi="{{ strtolower($nameHi) }}"
                            data-crop-name-translated="{{ strtolower($crop->translated_crop_name) }}">
                            <input type="radio" name="crop_id" value="{{ $crop->id }}" class="peer sr-only"
                                {{ old('crop_id') == $crop->id ? 'checked' : '' }}>
                            <div class="border border-gray-200 dark:border-gray-600 rounded-md p-2.5 text-center transition-all
                                peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20
                                hover:border-gray-300 dark:hover:border-gray-500">
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 peer-checked:text-green-700 dark:peer-checked:text-green-400">{{ $crop->translated_crop_name }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $crop->min_temp }}–{{ $crop->max_temp }}°C</p>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    {{-- Empty state element --}}
                    <div id="no-crops-found" class="hidden text-center py-6 text-gray-400 dark:text-gray-500 text-sm">
                        <span class="text-lg block mb-1">🔍</span>
                        {{ app()->getLocale() === 'hi' ? 'कोई मेल खाती फसल नहीं मिली।' : 'No matching crops found.' }}
                    </div>

                    @error('crop_id')
                        <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Weather Conditions --}}
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="font-semibold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                            {{ __('messages.weather_conditions') }}
                        </h2>
                        <button type="button" id="fetch-weather-btn"
                            class="btn-secondary text-xs py-1.5 px-3">
                            {{ __('messages.fetch_weather') }}
                        </button>
                    </div>

                    {{-- City Search --}}
                    <div class="relative mb-4">
                        <label class="form-label">{{ __('messages.weather_search_city') }}</label>
                        <input type="text" id="city-search-input"
                            class="form-input" placeholder="{{ __('messages.weather_search_placeholder') }}"
                            autocomplete="off">
                        <ul id="city-suggestions-list"
                            class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden hidden text-sm">
                        </ul>
                    </div>

                    <div id="weather-status"></div>

                    <div class="grid sm:grid-cols-3 gap-4 mt-3">
                        <div>
                            <label class="form-label" for="temperature">{{ __('messages.temperature') }}</label>
                            <input type="number" step="0.1" name="temperature" id="temperature"
                                class="form-input" placeholder="28"
                                value="{{ old('temperature') }}" oninput="fetchCropSuggestions()">
                            @error('temperature') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label" for="rainfall">{{ __('messages.rainfall') }}</label>
<<<<<<< HEAD
                            <input type="number" step="0.1" name="rainfall" id="rainfall"
=======
                            <input type="number" step="1" name="rainfall" id="rainfall"
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                                class="form-input" placeholder="800"
                                value="{{ old('rainfall') }}" oninput="fetchCropSuggestions()">
                            @error('rainfall') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label" for="humidity">{{ __('messages.humidity') }}</label>
                            <input type="number" step="1" name="humidity" id="humidity"
                                class="form-input" placeholder="65"
                                value="{{ old('humidity') }}" oninput="fetchCropSuggestions()">
                            @error('humidity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Soil pH --}}
                    <div class="mt-4">
                        <label class="form-label" for="soil_ph">{{ __('messages.soil_ph') }} (0–14)</label>
                        <div class="flex items-center gap-3 mt-1">
                            <input type="range" name="soil_ph" id="soil_ph_range"
                                min="0" max="14" step="0.1" value="{{ old('soil_ph', 6.5) }}"
                                class="flex-1 accent-green-600"
                                oninput="document.getElementById('soil_ph').value = this.value; document.getElementById('ph_display').textContent = parseFloat(this.value).toFixed(1)">
                            <input type="number" name="soil_ph" id="soil_ph" step="0.1" min="0" max="14"
                                class="form-input w-20 text-center text-sm" value="{{ old('soil_ph', 6.5) }}"
                                oninput="document.getElementById('soil_ph_range').value = this.value; document.getElementById('ph_display').textContent = parseFloat(this.value).toFixed(1)">
                            <span id="ph_display" class="w-10 text-center font-bold text-green-600 dark:text-green-400 text-sm">{{ old('soil_ph', 6.5) }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>0 {{ __('messages.soil_acidic') }}</span>
                            <span class="text-green-600 dark:text-green-400 font-medium">6–7.5 {{ __('messages.ideal') }}</span>
                            <span>14 {{ __('messages.soil_alkaline') }}</span>
                        </div>
                        @error('soil_ph') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- ── Right Column ─────────────────────────────────────────────────── --}}
            <div class="space-y-4">

                {{-- Suggested Crops --}}
                <div class="stat-card">
                    <h3 class="font-semibold text-gray-900 dark:text-white text-xs mb-2">{{ __('messages.suggested_crops') }}</h3>
                    <div id="crop-suggestions" class="flex flex-wrap gap-1.5 text-xs text-gray-400">
                        {{ __('messages.enter_weather_to_suggest') }}
                    </div>
                </div>

                {{-- Recently Used --}}
                @if($recentCrops->count() > 0)
                <div class="stat-card">
                    <h3 class="font-semibold text-gray-900 dark:text-white text-xs mb-2">{{ __('messages.recently_predicted') }}</h3>
                    <div class="space-y-1">
                        @foreach($recentCrops as $rc)
                        <button type="button"
                            onclick="selectRecentSearchCrop({{ $rc->id }}, '{{ $rc->translated_crop_name }}')"
                            class="w-full text-left text-xs px-2.5 py-1.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded hover:bg-green-50 dark:hover:bg-green-900/20 hover:border-green-200 dark:hover:border-green-800 text-gray-600 dark:text-gray-300 font-medium transition-colors">
                            {{ $rc->translated_crop_name }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Submit --}}
                <div class="stat-card">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">{{ __('messages.predict_instant_notice') }}</p>
                    <button type="submit" id="predict-submit" class="btn-primary w-full py-2.5">
                        {{ __('messages.predict_btn') }}
                    </button>
                    <a href="{{ route('home') }}" class="block mt-2 text-center text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        ← {{ __('messages.back_to_home') }}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Inject all database crops
    const dbCrops = @json($dbCrops);

    const range = document.getElementById('soil_ph_range');
    const num   = document.getElementById('soil_ph');
    if (range && num) {
        range.addEventListener('input', () => { num.value = range.value; });
        num.addEventListener('input', () => { range.value = num.value; });
    }

    // Intercept submit to validate crop season/conditions and show non-blocking warning
    document.getElementById('prediction-form')?.addEventListener('submit', async function (e) {
        e.preventDefault();

        const btn = document.getElementById('predict-submit');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '{{ __('messages.generating_prediction') }}';
        }

        const form = this;
        const temp = document.getElementById('temperature')?.value || '';
        const rain = document.getElementById('rainfall')?.value || '';
        const hum = document.getElementById('humidity')?.value || '';
        const month = new Date().getMonth() + 1;

        // Determine selected crop id or fallback to typed name
        const selected = form.querySelector('input[name="crop_id"]:checked');
        const cropId = selected ? selected.value : '';
        const cropName = cropSearchInput.value.trim();

        try {
            const params = new URLSearchParams({ crop_id: cropId, crop_name: cropName, temperature: temp, rainfall: rain, humidity: hum, month });
            const res = await fetch(`/api/crop-season-check?${params.toString()}`);
            const data = await res.json();

            if (data.warning) {
                // Show a subtle inline warning with option to continue
                let banner = document.getElementById('season-warning-banner');
                if (!banner) {
                    banner = document.createElement('div');
                    banner.id = 'season-warning-banner';
                    banner.className = 'mb-4 p-3 rounded border border-amber-200 bg-amber-50 text-amber-800';
                    const container = form.querySelector('.stat-card') || form;
                    container.parentNode.insertBefore(banner, container);
                }
<<<<<<< HEAD
                banner.innerHTML = `<strong>⚠️ {{ app()->getLocale() == 'hi' ? '⚠️ मौसमी चेतावनी:' : '⚠️ Seasonal warning:' }}</strong> <div class="text-sm mt-1">${data.message}</div>
                    <div class="mt-3 flex gap-2">
                        <button id="season-continue" type="button" class="btn-primary px-3 py-1 text-sm">{{ app()->getLocale() == 'hi' ? 'जारी रखें' : 'Continue Anyway' }}</button>
                        <button id="season-cancel" type="button" class="btn-secondary px-3 py-1 text-sm">{{ app()->getLocale() == 'hi' ? 'रद्द करें' : 'Cancel' }}</button>
=======
                banner.innerHTML = `<strong>⚠️ Seasonal warning:</strong> <div class="text-sm mt-1">${data.message}</div>
                    <div class="mt-3 flex gap-2">
                        <button id="season-continue" type="button" class="btn-primary px-3 py-1 text-sm">Continue Anyway</button>
                        <button id="season-cancel" type="button" class="btn-secondary px-3 py-1 text-sm">Cancel</button>
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                    </div>`;

                document.getElementById('season-continue')?.addEventListener('click', () => {
                    banner.remove();
                    form.submit();
                });
                document.getElementById('season-cancel')?.addEventListener('click', () => {
                    banner.remove();
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '{{ __('messages.predict_btn') }}';
                    }
                });
                return;
            }
        } catch (err) {
            // If season check fails, allow proceed gracefully
            console.error('Season check failed', err);
        }

        // No warnings — submit form
        form.submit();
    });

    // --- Crop Search & Autocomplete Implementation ---
    const cropSearchInput = document.getElementById('crop-search-input');
    const cropSearchClear = document.getElementById('crop-search-clear');
    const cropSuggestionsList = document.getElementById('crop-suggestions-list');
    const cropGrid = document.getElementById('crop-grid');
    const noCropsFound = document.getElementById('no-crops-found');
    
    let activeSuggestIndex = -1;
    let cropSearchDebounce;

    // Load centralized Indian crop dataset passed from controller
    const allIndianCrops = @json($indianCrops ?? []);

    const isHi = document.documentElement.lang?.startsWith('hi');
    const cropDataset = [];
    const seenNames = new Set();

    // 1. Load crops from database (passed via Blade json directive)
    dbCrops.forEach(item => {
        const card = document.querySelector(`.crop-card-label[data-crop-id="${item.id}"]`);
        
        seenNames.add(item.nameEn);
        seenNames.add(item.nameHi);
        seenNames.add(item.nameTrans);

        cropDataset.push({
            id: item.id,
            nameEn: item.nameEn,
            nameHi: item.nameHi,
            nameTrans: item.nameTrans,
            displayName: item.displayName,
            card: card || null,
            isDynamic: false,
            minTemp: item.minTemp,
            maxTemp: item.maxTemp
        });
    });

    // 2. Add predefined crops if they are not already in the database
    allIndianCrops.forEach(c => {
        const name = (c.name || '').toString();
        const keyEn = name.toLowerCase();
        const keyHi = (c.name_variants && c.name_variants.length) ? c.name_variants[0].toLowerCase() : keyEn;
        if (!seenNames.has(keyEn) && !seenNames.has(keyHi)) {
            const displayName = isHi ? (c.name_hi || c.name || keyEn) : (c.name || keyEn);

            seenNames.add(keyEn);
            seenNames.add(keyHi);
            seenNames.add(displayName.toLowerCase());

            cropDataset.push({
                id: null,
                nameEn: keyEn,
                nameHi: keyHi,
                nameTrans: displayName.toLowerCase(),
                displayName: displayName,
                card: null,
                isDynamic: true,
                rawName: c.name,
                minTemp: (c.ideal_temperature && c.ideal_temperature.min) ? c.ideal_temperature.min : 15,
                maxTemp: (c.ideal_temperature && c.ideal_temperature.max) ? c.ideal_temperature.max : 35
            });
        }
    });

    // Delegated pointer handler for suggestions to ensure clicks/taps register
    // We set a short-lived suppression flag to prevent the document-level
    // click handler from hiding the dropdown before selection completes.
    cropSuggestionsList.addEventListener('pointerdown', (e) => {
        const li = e.target.closest('li');
        if (!li) return;
        // Prevent blur/hide race — handle selection immediately
        e.preventDefault();
        // Suppress the global click-hide for the next tick so selection isn't interrupted
        window._suppressSuggestHide = true;
        setTimeout(() => { window._suppressSuggestHide = false; }, 50);
        handleSelectEvent(li);
    });

    // --- Recent Searches History (Swiggy/Google style) ---
    function getRecentSearches() {
        try {
            return JSON.parse(localStorage.getItem('recent_crop_searches') || '[]');
        } catch (e) {
            return [];
        }
    }

    function saveToRecentSearches(cropId, displayName) {
        let history = getRecentSearches();
        // Remove duplicate of the same crop
        history = history.filter(item => item.id != cropId);
        // Prepend to list
        history.unshift({ id: cropId, name: displayName });
        // Limit to 5 items
        history = history.slice(0, 5);
        localStorage.setItem('recent_crop_searches', JSON.stringify(history));
    }

    function showRecentSearches() {
        const history = getRecentSearches();
        if (history.length === 0) {
            cropSuggestionsList.classList.add('hidden');
            cropSuggestionsList.innerHTML = '';
            return;
        }

        const titleText = isHi ? '🕒 हाल की खोजें' : '🕒 Recent Searches';
        const clearAllText = isHi ? 'साफ़ करें' : 'Clear All';

        let html = `
            <li class="px-4 py-2 bg-gray-50 dark:bg-gray-800/80 text-xs font-semibold text-gray-500 dark:text-gray-400 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/50">
                <span>${titleText}</span>
                <button type="button" id="clear-recent-searches-btn" class="text-red-500 hover:text-red-700 transition-colors">${clearAllText}</button>
            </li>
        `;

        html += history.map((item) => {
            return `
                <li class="px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-700/50 last:border-0 transition-colors flex items-center gap-2"
                    data-history-id="${item.id}" data-name="${item.name}">
                    <span>🔍</span>
                    <span class="flex-1">${item.name}</span>
                </li>
            `;
        }).join('');

        cropSuggestionsList.innerHTML = html;
        cropSuggestionsList.classList.remove('hidden');

        // Attach click listeners to history items
        cropSuggestionsList.querySelectorAll('li[data-history-id]').forEach(li => {
            li.addEventListener('click', () => {
                const id = li.getAttribute('data-history-id');
                const name = li.getAttribute('data-name');
                selectRecentSearchCrop(id, name);
            });
        });

        // Attach listener to clear all button
        const clearBtn = document.getElementById('clear-recent-searches-btn');
        if (clearBtn) {
            clearBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                localStorage.removeItem('recent_crop_searches');
                cropSuggestionsList.classList.add('hidden');
                cropSuggestionsList.innerHTML = '';
            });
        }
    }

    // Helper: Select crop from recent searches or recently predicted list
    window.selectRecentSearchCrop = function(cropId, displayName) {
        const radioButton = document.querySelector(`input[name="crop_id"][value="${cropId}"]`);
        if (radioButton) {
            selectCropById(cropId, displayName);
        } else {
            // It's a non-default crop that was searched before. It should be in our dataset
            const item = cropDataset.find(c => c.id == cropId);
            if (item) {
                createTemporaryCard(item.id, item.nameEn, item.nameHi, item.nameTrans, item.displayName, item.minTemp, item.maxTemp);
            } else {
                // Fallback: search and find or create via AJAX
                discoverAndAddCrop(displayName, displayName);
            }
        }
    };

    // Helper: Update clear button visibility
    function toggleClearButton() {
        if (cropSearchInput && cropSearchClear) {
            if (cropSearchInput.value.length > 0) {
                cropSearchClear.classList.remove('hidden');
            } else {
                cropSearchClear.classList.add('hidden');
            }
        }
    }

    // Helper: Create temporary crop card element in `#crop-grid`
    function createTemporaryCard(cropId, nameEn, nameHi, nameTrans, displayName, minTemp, maxTemp) {
        // Remove previous temporary dynamic cards to prevent clutter
        // But avoid removing the currently selected persistent default cards unintentionally
        cropGrid.querySelectorAll('[data-is-temp-dynamic="true"]').forEach(c => c.remove());

        // Dynamically build and append the new crop card to `#crop-grid`
        const cardLabel = document.createElement('label');
        cardLabel.className = 'cursor-pointer crop-card-label transition-all duration-300 ease-out';
        // If cropId is null (non-persisted), create a temporary id and mark the card with the raw name
        let effectiveId = cropId;
        if (!effectiveId) {
            effectiveId = 'temp-' + Date.now();
            cardLabel.setAttribute('data-temp-name', displayName);
        }
        // If a card with same data-crop-id already exists, reuse it instead of appending duplicate
        const existing = cropGrid.querySelector(`.crop-card-label[data-crop-id="${effectiveId}"]`);
        if (existing) {
            // update display text and return selection
            const existingTitle = existing.querySelector('p.font-semibold');
            if (existingTitle) existingTitle.textContent = displayName;
            selectCropById(effectiveId, displayName);
            return;
        }
        cardLabel.setAttribute('data-crop-id', effectiveId);
        cardLabel.setAttribute('data-crop-name-en', nameEn.toLowerCase());
        cardLabel.setAttribute('data-crop-name-hi', nameHi.toLowerCase());
        cardLabel.setAttribute('data-crop-name-translated', nameTrans.toLowerCase());
        cardLabel.setAttribute('data-is-temp-dynamic', 'true'); // Mark as temporary

        // radio value: for temporary (non-persisted) cards we keep the value empty
        const radioValue = (effectiveId && effectiveId.toString().startsWith('temp-')) ? '' : effectiveId;
        cardLabel.innerHTML = `
            <input type="radio" name="crop_id" value="${radioValue}" class="peer sr-only">
            <div class="border border-gray-200 dark:border-gray-600 rounded-md p-2.5 text-center transition-all
                peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20
                hover:border-gray-300 dark:hover:border-gray-500">
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 peer-checked:text-green-700 dark:peer-checked:text-green-400">${displayName}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${minTemp}–${maxTemp}°C</p>
            </div>
        `;

        cropGrid.appendChild(cardLabel);

        // Add manual click event listener to the newly generated card
        cardLabel.addEventListener('click', () => {
            selectCropById(effectiveId, displayName);
        });

        // Trigger selection
        selectCropById(effectiveId, displayName);
    }

    // Helper: Discover & Add new crop to DB on-demand via AJAX
    function discoverAndAddCrop(rawName, displayName) {
        if (!cropSearchInput || !cropSearchClear) return;
        
        // Show inline loading spinner inside the clear/status button
        cropSearchClear.innerHTML = '<span class="animate-spin text-sm block">⏳</span>';
        cropSearchClear.classList.remove('hidden');
        cropSearchInput.setAttribute('disabled', 'true');
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('{{ route('api.crops.find_or_create') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: rawName })
        })
        .then(res => {
            if (!res.ok) throw new Error('API creation failed');
            return res.json();
        })
        .then(data => {
            // Restore clear button icon and enable input
            cropSearchClear.innerHTML = `<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
            cropSearchInput.removeAttribute('disabled');

            if (data.success) {
                const keyEn = data.name.toLowerCase();
                const keyHi = data.translated_name.toLowerCase();
                
                // Add to cropDataset so we don't hit the server again in this session
                const existing = cropDataset.find(c => c.id == data.id);
                if (!existing) {
                    cropDataset.push({
                        id: data.id,
                        nameEn: keyEn,
                        nameHi: keyHi,
                        nameTrans: keyHi,
                        displayName: data.translated_name,
                        card: null,
                        isDynamic: false,
                        minTemp: data.min_temp,
                        maxTemp: data.max_temp
                    });
                }

                // Create the temporary card and select it!
                createTemporaryCard(data.id, keyEn, keyHi, keyHi, data.translated_name, data.min_temp, data.max_temp);

                // Show beautiful sliding toast feedback
                showDiscoveryToast(data.translated_name);
            }
        })
        .catch(err => {
            cropSearchClear.innerHTML = `<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
            cropSearchInput.removeAttribute('disabled');
            console.error(err);
        });
    }

    // Helper: Show custom sliding toast notification
    function showDiscoveryToast(cropName) {
        let toast = document.getElementById('crop-discovery-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'crop-discovery-toast';
            toast.className = 'fixed bottom-4 right-4 z-50 bg-green-600 dark:bg-green-700 text-white px-4 py-3 rounded-md shadow-lg transform transition-all duration-300 translate-y-10 opacity-0 flex items-center gap-2 text-sm font-medium';
            document.body.appendChild(toast);
        }
        const text = isHi ? `✨ फसल "${cropName}" को सफलतापूर्वक शामिल किया गया!` : `✨ Crop "${cropName}" successfully discovered and added!`;
        toast.innerHTML = `<span>✅</span> <span>${text}</span>`;
        
        setTimeout(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
        }, 10);

        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
        }, 3500);
    }

    // Helper: Filter crop cards and generate suggestions
    function filterCrops() {
        const query = cropSearchInput.value.trim().toLowerCase();
        toggleClearButton();

        if (query.length === 0) {
            // Remove any dynamically added temporary cards
            cropGrid.querySelectorAll('[data-is-temp-dynamic="true"]').forEach(c => c.remove());
            
            // Clear radio selection to properly reset the state
            const checkedRadio = cropGrid.querySelector('input[type="radio"]:checked');
            if (checkedRadio) checkedRadio.checked = false;

            // Reset original grid and hide dropdown
            document.querySelectorAll('.crop-card-label').forEach(card => {
                if (card.getAttribute('data-is-temp-dynamic') !== 'true') {
                    card.style.display = '';
                }
            });
            noCropsFound.classList.add('hidden');
            showRecentSearches();
            activeSuggestIndex = -1;
            return;
        }

        const matches = [];
        cropDataset.forEach(item => {
            const matchEn = item.nameEn.includes(query);
            const matchHi = item.nameHi.includes(query);
            const matchTrans = item.nameTrans.includes(query);

            if (matchEn || matchHi || matchTrans) {
                let priority = 0;
                if (item.nameEn.startsWith(query) || item.nameHi.startsWith(query) || item.nameTrans.startsWith(query)) {
                    priority = 2; // starts-with matches get higher priority
                } else {
                    priority = 1; // contains matches get normal priority
                }
                matches.push({ ...item, priority });
            }
        });

        // Sort matches by priority (starts-with first)
        matches.sort((a, b) => b.priority - a.priority);

        // Filter currently visible DOM cards
        const renderedCards = document.querySelectorAll('.crop-card-label');
        renderedCards.forEach(card => card.style.display = 'none');

        const activeCardMatches = matches.filter(m => m.card !== null);
        if (activeCardMatches.length > 0) {
            noCropsFound.classList.add('hidden');
            activeCardMatches.forEach(m => {
                if (m.card) m.card.style.display = '';
            });
        } else if (matches.length === 0) {
            noCropsFound.classList.remove('hidden');
        } else {
            noCropsFound.classList.add('hidden');
        }

        // Generate suggestions dropdown for length >= 1
        if (query.length >= 1) {
            let suggestionsHtml = matches.map((m, idx) => {
                const tag = m.isDynamic ? '✨' : '🌱';
                const displayId = m.isDynamic ? 'dynamic-' + idx : m.id;
                const dynamicAttr = m.isDynamic ? `data-is-dynamic="true" data-raw-name="${m.rawName}"` : `data-is-dynamic="false" data-crop-id="${m.id}"`;
                return `<li class="px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-700/50 last:border-0 transition-colors"
                            data-index="${idx}" data-name="${m.displayName}" ${dynamicAttr} data-display-id="${displayId}">
                            ${tag} ${m.displayName} ${m.isDynamic ? '<span class="text-xs text-green-600 dark:text-green-400 ml-1.5">(Discover)</span>' : ''}
                        </li>`;
            }).join('');

            // If there's no exact match, allow custom discovery of the input query
            const exactMatchExists = matches.some(m => m.nameEn === query || m.nameHi === query || m.nameTrans === query);
            if (!exactMatchExists && query.length >= 2) {
                const addLabel = isHi ? `➕ "${query}" खोजें और जोड़ें` : `➕ Discover and Add "${query}"`;
                suggestionsHtml += `
                    <li class="px-4 py-2.5 text-sm text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 cursor-pointer border-t border-gray-100 dark:border-gray-700/50 transition-colors font-medium"
                        data-index="${matches.length}" data-name="${query}" data-is-dynamic="true" data-raw-name="${query}" data-display-id="dynamic-custom">
                        ${addLabel}
                    </li>
                `;
            }

            cropSuggestionsList.innerHTML = suggestionsHtml;
            cropSuggestionsList.classList.remove('hidden');
            
            // No per-item click listeners needed because we use delegated pointerdown above.
        } else {
            cropSuggestionsList.classList.add('hidden');
            cropSuggestionsList.innerHTML = '';
        }
        
        activeSuggestIndex = -1;
    }

    // Helper: Route suggestions click or keypress select
    function handleSelectEvent(li) {
        const isDynamic = li.getAttribute('data-is-dynamic') === 'true';
        const displayName = li.getAttribute('data-name');
        
        if (isDynamic) {
            const rawName = li.getAttribute('data-raw-name');
            discoverAndAddCrop(rawName, displayName);
        } else {
            const cropId = li.getAttribute('data-crop-id');
            const radioButton = document.querySelector(`input[name="crop_id"][value="${cropId}"]`);
            if (radioButton) {
                selectCropById(cropId, displayName);
            } else {
                const item = cropDataset.find(c => c.id == cropId);
                if (item) {
                    createTemporaryCard(item.id, item.nameEn, item.nameHi, item.nameTrans, item.displayName, item.minTemp, item.maxTemp);
                }
            }
        }
    }

    // Helper: Select crop by ID
    function selectCropById(cropId, displayName) {
        const radioButton = document.querySelector(`input[name="crop_id"][value="${cropId}"]`);
        let cardLabel = null;

        if (radioButton) {
            radioButton.checked = true;
            radioButton.dispatchEvent(new Event('change'));
            radioButton.dispatchEvent(new Event('input'));
            cardLabel = radioButton.closest('.crop-card-label');
            // Clear hidden crop_name because a persisted crop was selected
            document.getElementById('crop-name-hidden').value = '';
        } else {
            // Try to find label by data-crop-id for temporary cards
            cardLabel = document.querySelector(`.crop-card-label[data-crop-id="${cropId}"]`);
            if (cardLabel) {
                // For temp cards, populate the hidden crop_name for server-side creation on submit
                const tempName = cardLabel.getAttribute('data-temp-name') || displayName || '';
                document.getElementById('crop-name-hidden').value = tempName;
                const radio = cardLabel.querySelector('input[name="crop_id"]');
                if (radio) {
                    radio.checked = true;
                }
            }
        }

        if (cardLabel) {
            // Core requirement: "ONLY the selected crop card should appear in the crop cards section temporarily."
            document.querySelectorAll('.crop-card-label').forEach(card => {
                if (card === cardLabel) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            cardLabel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            cropSearchInput.value = displayName;
            cropSuggestionsList.classList.add('hidden');
            activeSuggestIndex = -1;
            toggleClearButton();
            noCropsFound.classList.add('hidden');

            // Save to recent searches
            saveToRecentSearches(cropId, displayName);
        }
    }

    // Debounced search on typing
    cropSearchInput.addEventListener('input', () => {
        clearTimeout(cropSearchDebounce);
        cropSearchDebounce = setTimeout(filterCrops, 150);
    });

    // Clear search button
    cropSearchClear.addEventListener('click', () => {
        if (cropSearchInput.hasAttribute('disabled')) return;
        cropSearchInput.value = '';
        filterCrops();
        cropSearchInput.focus();
    });

    // Keyboard navigation inside Suggestions Dropdown
    cropSearchInput.addEventListener('keydown', (e) => {
        const items = cropSuggestionsList.querySelectorAll('li');
        if (cropSuggestionsList.classList.contains('hidden') || items.length === 0) {
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeSuggestIndex = (activeSuggestIndex + 1) % items.length;
            highlightSuggestion(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeSuggestIndex = (activeSuggestIndex - 1 + items.length) % items.length;
            highlightSuggestion(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeSuggestIndex >= 0 && activeSuggestIndex < items.length) {
                handleSelectEvent(items[activeSuggestIndex]);
            } else if (items.length > 0) {
                handleSelectEvent(items[0]);
            }
        } else if (e.key === 'Escape') {
            cropSuggestionsList.classList.add('hidden');
            activeSuggestIndex = -1;
        }
    });

    function highlightSuggestion(items) {
        items.forEach((item, idx) => {
            if (idx === activeSuggestIndex) {
                item.classList.add('bg-green-50', 'dark:bg-green-900/30', 'text-green-700', 'dark:text-green-400', 'font-semibold');
                item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                item.classList.remove('bg-green-50', 'dark:bg-green-900/30', 'text-green-700', 'dark:text-green-400', 'font-semibold');
            }
        });
    }

    // Close crop suggestions when clicking outside. Respect suppression flag
    // set while a pointerdown originated inside the suggestions list (prevents
    // the blur-before-click race on some devices/browsers).
    document.addEventListener('click', (e) => {
        if (window._suppressSuggestHide) return;
        if (!cropSearchInput.contains(e.target) && !cropSuggestionsList.contains(e.target)) {
            cropSuggestionsList.classList.add('hidden');
            activeSuggestIndex = -1;
        }
    });

    // Focus handler to show suggestions if input is not empty, or recent searches if empty
    cropSearchInput.addEventListener('focus', () => {
        if (cropSearchInput.value.trim().length > 0) {
            if (cropSuggestionsList.innerHTML !== '') {
                cropSuggestionsList.classList.remove('hidden');
            }
        } else {
            showRecentSearches();
        }
    });

    // Sync search input if user clicks a crop card manually
    document.querySelectorAll('.crop-card-label').forEach(card => {
        card.addEventListener('click', () => {
            const displayName = card.querySelector('p.font-semibold').textContent;
            const cropId = card.querySelector('input[type="radio"]').value;
            selectCropById(cropId, displayName);
        });
    });

    // Initial sync on page load/validation error/old input value
    const selectedRadio = cropGrid.querySelector('input[type="radio"]:checked');
    if (selectedRadio) {
        const parentLabel = selectedRadio.closest('.crop-card-label');
        if (parentLabel) {
            const displayName = parentLabel.querySelector('p.font-semibold').textContent;
            selectCropById(selectedRadio.value, displayName);
        }
    }
});
</script>
@endpush

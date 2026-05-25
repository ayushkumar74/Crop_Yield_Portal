@extends('layouts.app')
@section('title', __('messages.predict_title') . ' — ' . __('messages.app_name'))

@section('content')
<div class="page-wrapper">

    {{-- Page Header --}}
    <div class="mb-4">
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

    {{-- Seasonal Warning Banner (dynamically populated by JavaScript) --}}
    <div id="season-warning-banner" class="hidden mb-5 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-md">
        {{-- Content will be inserted by JavaScript --}}
    </div>

    <form action="{{ route('predictions.store') }}" method="POST" id="prediction-form">
        @csrf
        <input type="hidden" name="crop_name" id="crop-name-hidden" value="">
        <div class="grid lg:grid-cols-3 gap-3">

            {{-- ── Left Column ─────────────────────────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-3">

                {{-- Crop Selection --}}
                <div class="stat-card">
                    <h2 class="font-semibold text-gray-900 dark:text-white text-sm mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        {{ __('messages.select_crop') }}
                    </h2>

                    {{-- Search Crop Input --}}
                    <div class="relative mb-3.5" id="crop-search-container">
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

                {{-- Floating Autocomplete Dropdown Container --}}
                <div id="floating-dropdown" class="floating-dropdown-container">
                    <ul id="crop-suggestions-list"
                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md max-h-80 overflow-y-auto overflow-x-hidden hidden text-sm transition-all duration-200 will-change-transform">
                    </ul>
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
                            <input type="number" step="0.1" name="rainfall" id="rainfall"
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
            <div class="space-y-3">

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
            const res = await fetch(`/api/crop-season-check?${params.toString()}`, { credentials: 'same-origin' });
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
                // Ensure existing placeholder banner is visible and has expected styling
                banner.className = 'mb-4 p-3 rounded border border-amber-200 bg-amber-50 text-amber-800';
                banner.innerHTML = `<strong>⚠️ {{ app()->getLocale() == 'hi' ? '⚠️ मौसमी चेतावनी:' : '⚠️ Seasonal warning:' }}</strong> <div class="text-sm mt-1">${data.message}</div>
                    <div class="mt-3 flex gap-2">
                        <button id="season-continue" type="button" class="btn-primary px-3 py-1 text-sm">{{ app()->getLocale() == 'hi' ? 'जारी रखें' : 'Continue Anyway' }}</button>
                        <button id="season-cancel" type="button" class="btn-secondary px-3 py-1 text-sm">{{ app()->getLocale() == 'hi' ? 'रद्द करें' : 'Cancel' }}</button>
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
        }

        // No warnings — submit form
        form.submit();
    });

    // --- Crop Search & Autocomplete Implementation ---
    const cropSearchInput = document.getElementById('crop-search-input');
    const cropSearchClear = document.getElementById('crop-search-clear');
    const cropSuggestionsList = document.getElementById('crop-suggestions-list');
    const floatingDropdownContainer = document.getElementById('floating-dropdown');
    const cropGrid = document.getElementById('crop-grid');
    const noCropsFound = document.getElementById('no-crops-found');
    
    let activeSuggestIndex = -1;
    let cropSearchDebounce;
    let dropdownActive = false;

    // Function to position the floating dropdown relative to the search input
    function positionFloatingDropdown() {
        if (!cropSearchInput || !floatingDropdownContainer || !cropSuggestionsList) {
            return;
        }

        // Only position if dropdown is active/visible
        if (cropSuggestionsList.classList.contains('hidden')) {
            return;
        }

        const rect = cropSearchInput.getBoundingClientRect();
        const scrollX = window.pageXOffset || document.documentElement.scrollLeft;
        const scrollY = window.pageYOffset || document.documentElement.scrollTop;

        // Position container just below the input
        const containerLeft = scrollX + rect.left;
        const containerTop = scrollY + rect.bottom + 8;

        floatingDropdownContainer.style.left = containerLeft + 'px';
        floatingDropdownContainer.style.top = containerTop + 'px';

        // Set dropdown width to match input
        cropSuggestionsList.style.width = rect.width + 'px';
        cropSuggestionsList.style.minWidth = rect.width + 'px';
        cropSuggestionsList.style.maxWidth = rect.width + 'px';
    }

    // Update dropdown position on scroll, resize, and input events
    function setupDropdownPositioning() {
        window.addEventListener('scroll', positionFloatingDropdown, true);
        window.addEventListener('resize', positionFloatingDropdown);
        cropSearchInput?.addEventListener('input', positionFloatingDropdown);
    }

    setupDropdownPositioning();

    // Helper: Show dropdown with proper positioning
    function showDropdown() {
        cropSuggestionsList.classList.remove('hidden');
        cropSuggestionsList.classList.add('active');
        floatingDropdownContainer.classList.add('active');
        dropdownActive = true;
        // Position immediately; will be re-positioned on next scroll/resize/input event
        positionFloatingDropdown();
    }

    // Helper: Hide dropdown
    function hideDropdown() {
        cropSuggestionsList.classList.add('hidden');
        cropSuggestionsList.classList.remove('active');
        floatingDropdownContainer.classList.remove('active');
        dropdownActive = false;
    }

    // Initialize data structures for crop search
    const seenNames = new Set();
    let cropDataset = [];

    // 1. Load crops dataset passed from controller (includes persistent DB crops
    //    and non-persistent dynamic entries from the indian_crops.json file).
    dbCrops.forEach(item => {
        const card = document.querySelector(`.crop-card-label[data-crop-id="${item.id}"]`);

        const isDynamic = item.isDynamic === true || item.isDynamic === 'true' || !!item.variants;

        // unify names
        const nameEn = (item.nameEn || '').toLowerCase();
        const nameHi = (item.nameHi || '').toLowerCase();
        const nameTrans = (item.nameTrans || '').toLowerCase();

        seenNames.add(nameEn);
        seenNames.add(nameHi);
        seenNames.add(nameTrans);

        cropDataset.push({
            id: item.id,
            nameEn: nameEn,
            nameHi: nameHi,
            nameTrans: nameTrans,
            displayName: item.displayName,
            card: card || null,
            isDynamic: isDynamic,
            variants: item.variants || [],
            minTemp: item.minTemp,
            maxTemp: item.maxTemp
        });
    });

    // Build a normalized searchable text for each crop (lowercase, trimmed,
    // punctuation removed) to guarantee contains-based matching behaves
    // consistently across DB and dataset entries.
    function normalizeForSearch(s) {
        return (s || '').toString().toLowerCase().trim().replace(/[\u200B-\u200D\uFEFF]/g, '').replace(/[\W_]+/g, ' ');
    }
    cropDataset.forEach(item => {
        const parts = [];
        parts.push(item.nameEn || '');
        if (item.displayName) parts.push(item.displayName.toString().toLowerCase());
        if (item.nameHi) parts.push(item.nameHi);
        if (item.nameTrans) parts.push(item.nameTrans);
        if (Array.isArray(item.variants)) parts.push(item.variants.join(' '));
        item.searchText = normalizeForSearch(parts.join(' '));
    });

    // final cropDataset prepared for search

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
            hideDropdown();
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
        showDropdown();

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
                hideDropdown();
                cropSuggestionsList.innerHTML = '';
            });
        }
    }

    // Helper: Select crop from recent searches or recently predicted list
    window.selectRecentSearchCrop = function(cropId, displayName) {
        const radioButton = document.querySelector(`input[name="crop_id"][value="${cropId}"]`);
        if (radioButton) {
            selectCropById(cropId, displayName);
            return;
        }

        // Find in our unified dataset by id or name
        let item = cropDataset.find(c => String(c.id) === String(cropId));
        if (!item && displayName) {
            const q = displayName.toLowerCase();
            item = cropDataset.find(c => (c.nameEn || '').includes(q) || (c.displayName || '').toLowerCase() === q || (c.variants || []).some(v => v.includes(q)));
        }

        if (item) {
            if (!item.isDynamic) {
                // If persisted crop exists but not rendered as a card, reveal and select
                if (item.card) {
                    item.card.style.display = '';
                    selectCropById(item.id, item.displayName);
                } else {
                    // treat as dynamic fallback selection
                    document.getElementById('crop-name-hidden').value = item.displayName;
                    document.querySelectorAll('input[name="crop_id"]')?.forEach(i => i.checked = false);
                    document.querySelectorAll('.crop-card-label').forEach(card => card.style.display = 'none');
                    document.querySelectorAll('[data-is-temp-dynamic="true"]').forEach(c => c.remove());
                    const tempCard = document.createElement('label');
                    tempCard.className = 'cursor-pointer crop-card-label transition-all duration-300 ease-out';
                    tempCard.setAttribute('data-is-temp-dynamic', 'true');
                    const tempRange = (item.minTemp != null && item.minTemp !== '' && item.maxTemp != null && item.maxTemp !== '') ? `${item.minTemp}–${item.maxTemp}°C` : '—';
                    tempCard.innerHTML = `
                        <input type="radio" name="_temp_crop_radio" class="sr-only" />
                        <div class="border border-gray-200 dark:border-gray-600 rounded-md p-2.5 text-center">
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">${item.displayName}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${tempRange}</p>
                        </div>`;
                    document.getElementById('crop-grid').insertBefore(tempCard, document.getElementById('crop-grid').firstChild);
                    cropSearchInput.value = item.displayName;
                }
            } else {
                // Dynamic dataset entry selected from recent searches
                document.getElementById('crop-name-hidden').value = item.displayName;
                document.querySelectorAll('input[name="crop_id"]')?.forEach(i => i.checked = false);
                document.querySelectorAll('.crop-card-label').forEach(card => card.style.display = 'none');
                document.querySelectorAll('[data-is-temp-dynamic="true"]').forEach(c => c.remove());
                const tempCard = document.createElement('label');
                tempCard.className = 'cursor-pointer crop-card-label transition-all duration-300 ease-out';
                tempCard.setAttribute('data-is-temp-dynamic', 'true');
                    const tempRange = (item.minTemp != null && item.minTemp !== '' && item.maxTemp != null && item.maxTemp !== '') ? `${item.minTemp}–${item.maxTemp}°C` : '—';
                tempCard.innerHTML = `
                    <input type="radio" name="_temp_crop_radio" class="sr-only" />
                    <div class="border border-gray-200 dark:border-gray-600 rounded-md p-2.5 text-center">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">${item.displayName}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${tempRange}</p>
                    </div>`;
                document.getElementById('crop-grid').insertBefore(tempCard, document.getElementById('crop-grid').firstChild);
                cropSearchInput.value = item.displayName;
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

    // Manual creation of temporary cards is disabled. All crop options must be
    // authoritative database entries. The UI will only surface `dbCrops` items.

    // Disabled: discoverAndAddCrop removed to prevent client-side creation.

    // Discovery toast removed: we do not surface creation feedback in UI anymore.

    // Helper: Filter crop cards and generate suggestions
    async function filterCrops() {
        const query = cropSearchInput.value.trim().toLowerCase();
        toggleClearButton();

        // incoming query

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

        // As soon as the user types, hide all persisted static crop cards to
        // ensure the floating suggestions and temporary cards are the primary
        // visible affordance while searching.
        document.querySelectorAll('.crop-card-label').forEach(card => card.style.display = 'none');

        // Perform authoritative server-side contains search (DB-driven)
        let matches = [];
        try {
            const resp = await fetch('/api/crops/search?query=' + encodeURIComponent(query), {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
            });

            if (!resp.ok) {
                matches = [];
            } else {
                const json = await resp.json();
                matches = json.map(d => {
                    const card = document.querySelector(`.crop-card-label[data-crop-id="${d.id}"]`);
                    const parts = [];
                    if (d.nameEn) parts.push(d.nameEn);
                    if (d.displayName) parts.push(d.displayName.toString().toLowerCase());
                    if (Array.isArray(d.aliases)) parts.push(d.aliases.join(' '));
                    const searchText = normalizeForSearch(parts.join(' '));
                    const priority = searchText.split(' ').some(tok => tok.startsWith(query)) ? 2 : 1;
                    return { ...d, card: card || null, searchText, priority };
                });
            }
        } catch (e) {
            // Fail silently in production; no matches
        }

        // matches retrieved from server

        // Sort matches by priority (starts-with first)
        matches.sort((a, b) => b.priority - a.priority);

        // Filter currently visible DOM cards
        const renderedCards = document.querySelectorAll('.crop-card-label');
        renderedCards.forEach(card => card.style.display = 'none');

        const activeCardMatches = matches.filter(m => m.card !== null);
        // activeCardMatches length evaluated
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

        // If there are matches but none correspond to pre-rendered cards,
        // render temporary visible cards for the top matches so the grid
        // always shows something relevant to the user. This does not modify
        // persistent data and uses the same visual structure as existing cards.
        document.querySelectorAll('[data-is-temp-dynamic="true"]').forEach(c => c.remove());
        if (matches.length > 0 && activeCardMatches.length === 0) {
            // hide all original persisted cards
            document.querySelectorAll('.crop-card-label').forEach(card => card.style.display = 'none');
            // create up to 4 temporary cards for immediate visual feedback
            const maxTemp = 4;
            matches.slice(0, maxTemp).forEach(item => {
                const tempCard = document.createElement('label');
                tempCard.className = 'cursor-pointer crop-card-label transition-all duration-300 ease-out';
                tempCard.setAttribute('data-is-temp-dynamic', 'true');
                tempCard.setAttribute('data-crop-id', item.id);
                tempCard.setAttribute('data-name', item.displayName);
                const tempRange = (item.minTemp != null && item.minTemp !== '' && item.maxTemp != null && item.maxTemp !== '') ? `${item.minTemp}–${item.maxTemp}°C` : '—';
                tempCard.innerHTML = `
                    <input type="radio" name="_temp_crop_radio" class="sr-only" />
                    <div class="border border-gray-200 dark:border-gray-600 rounded-md p-2.5 text-center hover:border-green-400 dark:hover:border-green-500 transition-colors">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">${item.displayName}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${tempRange}</p>
                    </div>`;
                document.getElementById('crop-grid').insertBefore(tempCard, document.getElementById('crop-grid').firstChild);
            });
        }

        // Generate suggestions dropdown for length >= 1
        if (query.length >= 1) {
            // Allow both persistent (DB) and dynamic (dataset) matches in the dropdown
            const persistentMatches = matches.filter(m => true);
            let suggestionsHtml = persistentMatches.map((m, idx) => {
                const tag = '🌱';
                const displayId = m.id;
                const dynamicAttr = `data-is-dynamic="${m.isDynamic ? 'true' : 'false'}" data-crop-id="${m.id}"`;
                const tempRange = (m.minTemp != null && m.minTemp !== '' && m.maxTemp != null && m.maxTemp !== '') ? `${m.minTemp}–${m.maxTemp}°C` : '—';
                return `<li class="px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-700/50 last:border-0 transition-colors flex items-center justify-between"
                            data-index="${idx}" data-name="${m.displayName}" ${dynamicAttr} data-display-id="${displayId}" data-min-temp="${m.minTemp || ''}" data-max-temp="${m.maxTemp || ''}">
                            <span>${tag} ${m.displayName}</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">${tempRange}</span>
                        </li>`;
            }).join('');

            cropSuggestionsList.innerHTML = suggestionsHtml;
            showDropdown();
            
            // No per-item click listeners needed because we use delegated pointerdown above.
        } else {
            hideDropdown();
            cropSuggestionsList.innerHTML = '';
        }
        
        activeSuggestIndex = -1;
    }

    // Helper: Route suggestions click or keypress select
    function handleSelectEvent(li) {
        const isDynamic = li.getAttribute('data-is-dynamic') === 'true';
        const displayName = li.getAttribute('data-name');
        const cropId = li.getAttribute('data-crop-id');
        const minTemp = li.getAttribute('data-min-temp');
        const maxTemp = li.getAttribute('data-max-temp');

        if (!isDynamic) {
            // Persistent crop selected from dropdown
            const radioButton = document.querySelector(`input[name="crop_id"][value="${cropId}"]`);
            if (radioButton) {
                // Radio button exists in form - use standard selection flow
                selectCropById(cropId, displayName);
            } else {
                // Persistent crop from DB but no rendered radio button - treat as temp selection
                document.getElementById('crop-name-hidden').value = displayName;
                document.querySelectorAll('input[name="crop_id"]')?.forEach(i => i.checked = false);
                document.querySelectorAll('.crop-card-label').forEach(card => card.style.display = 'none');
                document.querySelectorAll('[data-is-temp-dynamic="true"]').forEach(c => c.remove());
                
                const tempCard = document.createElement('label');
                tempCard.className = 'cursor-pointer crop-card-label transition-all duration-300 ease-out';
                tempCard.setAttribute('data-is-temp-dynamic', 'true');
                tempCard.setAttribute('data-crop-id', cropId);
                const tempRange = (minTemp != null && minTemp !== '' && maxTemp != null && maxTemp !== '') ? `${minTemp}–${maxTemp}°C` : '—';
                tempCard.innerHTML = `
                    <input type="radio" name="_temp_crop_selected" class="peer sr-only" checked />
                    <div class="border border-green-500 bg-green-50 dark:bg-green-900/20 rounded-md p-2.5 text-center peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20">
                        <p class="text-xs font-semibold text-green-700 dark:text-green-400">${displayName}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${tempRange}</p>
                    </div>`;
                document.getElementById('crop-grid').insertBefore(tempCard, document.getElementById('crop-grid').firstChild);
            }
            cropSearchInput.value = displayName;
            cropSuggestionsList.classList.add('hidden');
            activeSuggestIndex = -1;
            toggleClearButton();
            noCropsFound.classList.add('hidden');
            return;
        }

        // Dynamic dataset-only crop selected: set the hidden crop_name
        document.getElementById('crop-name-hidden').value = displayName;
        // Clear any selected persisted crop id
        document.querySelectorAll('input[name="crop_id"]')?.forEach(i => i.checked = false);

        // Hide existing persistent cards
        document.querySelectorAll('.crop-card-label').forEach(card => card.style.display = 'none');
        // Remove any previous temp dynamic cards
        document.querySelectorAll('[data-is-temp-dynamic="true"]').forEach(c => c.remove());
        
        // Create a temporary card element with a checked radio button
        const tempCard = document.createElement('label');
        tempCard.className = 'cursor-pointer crop-card-label transition-all duration-300 ease-out';
        tempCard.setAttribute('data-is-temp-dynamic', 'true');
        tempCard.setAttribute('data-crop-id', cropId);
        const tempRange = (minTemp != null && minTemp !== '' && maxTemp != null && maxTemp !== '') ? `${minTemp}–${maxTemp}°C` : '—';
        tempCard.innerHTML = `
            <input type="radio" name="_temp_crop_selected" class="peer sr-only" checked />
            <div class="border border-green-500 bg-green-50 dark:bg-green-900/20 rounded-md p-2.5 text-center peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20">
                <p class="text-xs font-semibold text-green-700 dark:text-green-400">${displayName}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${tempRange}</p>
            </div>`;
        document.getElementById('crop-grid').insertBefore(tempCard, document.getElementById('crop-grid').firstChild);

        cropSearchInput.value = displayName;
        cropSuggestionsList.classList.add('hidden');
        activeSuggestIndex = -1;
        toggleClearButton();
        noCropsFound.classList.add('hidden');
    }

    // Helper: Select crop by ID
    function selectCropById(cropId, displayName) {
        // Clear any temp dynamic cards first
        document.querySelectorAll('[data-is-temp-dynamic="true"]').forEach(c => c.remove());
        
        // Find the radio button for this crop
        const radioButton = document.querySelector(`input[name="crop_id"][value="${cropId}"]`);
        if (!radioButton) return;

        // Check the radio button and dispatch events
        radioButton.checked = true;
        radioButton.dispatchEvent(new Event('change'));
        radioButton.dispatchEvent(new Event('input'));

        // Get the card label containing this radio button
        const cardLabel = radioButton.closest('.crop-card-label');
        if (!cardLabel) return;

        // Populate hidden crop_name with the selected display name
        document.getElementById('crop-name-hidden').value = displayName;

        // Hide all other cards, show only selected card
        document.querySelectorAll('.crop-card-label').forEach(card => {
            if (card === cardLabel) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        // Scroll to selected card
        cardLabel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Update UI state
        cropSearchInput.value = displayName;
        hideDropdown();
        activeSuggestIndex = -1;
        toggleClearButton();
        noCropsFound.classList.add('hidden');

        // Save to recent searches
        if (cropId) saveToRecentSearches(cropId, displayName);
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
        if (!floatingDropdownContainer.classList.contains('active') || items.length === 0) {
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
            hideDropdown();
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
    // Close crop suggestions when clicking/tapping outside. Use pointerdown
    // to avoid blur-before-click race; respect suppression flag set during
    // pointer interactions inside the suggestions list.
    document.addEventListener('pointerdown', (e) => {
        if (window._suppressSuggestHide) return;
        // If dropdown not active, nothing to do
        if (!dropdownActive) return;
        if (!cropSearchInput.contains(e.target) && !cropSuggestionsList.contains(e.target) && !floatingDropdownContainer.contains(e.target)) {
            hideDropdown();
            activeSuggestIndex = -1;
        }
    });

    // Focus handler to show suggestions if input is not empty, or recent searches if empty
    cropSearchInput.addEventListener('focus', () => {
        if (cropSearchInput.value.trim().length > 0) {
            if (cropSuggestionsList.innerHTML !== '') {
                showDropdown();
            }
        } else {
            showRecentSearches();
        }
    });

    // Delegate click handler on crop grid so dynamically added/temp cards are clickable
    cropGrid.addEventListener('click', (e) => {
        const card = e.target.closest('.crop-card-label');
        if (!card) return;

        const displayEl = card.querySelector('p.font-semibold');
        const displayName = displayEl ? displayEl.textContent.trim() : '';

        // If card was created as a temp dynamic card, it won't have a persisted crop_id
        if (card.getAttribute('data-is-temp-dynamic') === 'true') {
            // This is a dynamic crop card — ensure it stays selected
            document.getElementById('crop-name-hidden').value = displayName;
            document.querySelectorAll('input[name="crop_id"]')?.forEach(i => i.checked = false);
            document.querySelectorAll('[data-is-temp-dynamic="true"]').forEach(c => {
                if (c !== card) c.style.display = 'none';
            });
            document.querySelectorAll('.crop-card-label:not([data-is-temp-dynamic="true"])').forEach(c => c.style.display = 'none');
            card.style.display = '';
            cropSearchInput.value = displayName;
            hideDropdown();
            activeSuggestIndex = -1;
            toggleClearButton();
            noCropsFound.classList.add('hidden');
            return;
        }

        // Persisted card clicked — determine crop id from the radio input
        const radio = card.querySelector('input[type="radio"][name="crop_id"]');
        if (radio) {
            const cropId = radio.value;
            selectCropById(cropId, displayName);
        }
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

/**
 * Member 1 - Frontend: Main JS - Dark mode, toast notifications, weather fetch, Chart.js helpers
 * Covers: Unit II (AJAX/API calls), Unit V (localStorage for theme preference)
 */

import './bootstrap';

// ========== Dark Mode Toggle with Smooth Animations ==========
(function initDarkMode() {
    const html = document.documentElement;
    const stored = localStorage.getItem('theme');

    if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        html.classList.add('dark');
    }

    window.toggleDarkMode = function () {
        // Prevent rapid clicking
        if (window.themeToggling) return;
        window.themeToggling = true;

        const isDark = html.classList.toggle('dark');

        // Update icons with smooth animation
        document.querySelectorAll('.theme-toggle-icon').forEach(iconEl => {
            iconEl.textContent = isDark ? '🌙' : '☀️';
        });

        // Save preference
        localStorage.setItem('theme', isDark ? 'dark' : 'light');

        // Debounce toggle to prevent rapid switches
        setTimeout(() => {
            window.themeToggling = false;
        }, 600);
    };
})();

// ========== Toast Notification System ==========
window.showToast = function (message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <span class="text-xl">${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</span>
        <span class="font-medium text-sm">${message}</span>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
};

// ========== Geolocation & Weather Fetch with Auto-Detection & Permission Persistence ==========
const GEOLOCATION_KEY = 'cropyield_geolocation_permission';
const GEOLOCATION_COORDS_KEY = 'cropyield_last_coords';
const GEOLOCATION_TIMESTAMP_KEY = 'cropyield_coords_timestamp';
const GEOLOCATION_CACHE_DURATION = 60 * 60 * 1000; // 1 hour
const GEOLOCATION_ASKED_KEY = 'cropyield_geolocation_asked';

/**
 * Check if geolocation permission is already granted based on localStorage or backend meta tags
 */
function isGeolocationPermissionGranted() {
    const metaPermission = document.querySelector('meta[name="user-location-permission"]')?.getAttribute('content');
    return metaPermission === 'granted' || localStorage.getItem(GEOLOCATION_KEY) === 'granted';
}

/**
 * Check if cached coordinates are still fresh
 */
function areCachedCoordsFresh() {
    const timestamp = localStorage.getItem(GEOLOCATION_TIMESTAMP_KEY);
    if (!timestamp) return false;

    const elapsed = Date.now() - parseInt(timestamp);
    return elapsed < GEOLOCATION_CACHE_DURATION;
}

/**
 * Get cached coordinates if available
 */
function getCachedCoords() {
    // Check if we have fresh browser-local coordinates
    if (areCachedCoordsFresh()) {
        const stored = localStorage.getItem(GEOLOCATION_COORDS_KEY);
        if (stored) {
            try {
                return JSON.parse(stored);
            } catch { /* fallback */ }
        }
    }

    // Fall back to database coordinates stored in meta tags
    const metaLat = document.querySelector('meta[name="user-lat"]')?.getAttribute('content');
    const metaLon = document.querySelector('meta[name="user-lon"]')?.getAttribute('content');
    if (metaLat && metaLon && metaLat !== '' && metaLon !== '') {
        const lat = parseFloat(metaLat);
        const lon = parseFloat(metaLon);
        if (!isNaN(lat) && !isNaN(lon)) {
            return { latitude: lat, longitude: lon };
        }
    }

    return null;
}

/**
 * Save coordinates and permission state to localStorage
 */
function saveCoordsAndPermission(latitude, longitude, accuracy = null) {
    localStorage.setItem(GEOLOCATION_KEY, 'granted');
    localStorage.setItem(GEOLOCATION_COORDS_KEY, JSON.stringify({ latitude, longitude, accuracy }));
    localStorage.setItem(GEOLOCATION_TIMESTAMP_KEY, Date.now().toString());
    
    // Debug logging
    console.log('%c[GPS] Coordinates Saved:', 'color: #00AA00; font-weight: bold;', {
        latitude: latitude.toFixed(6),
        longitude: longitude.toFixed(6),
        accuracy: accuracy ? accuracy.toFixed(2) + 'm' : 'unknown',
        timestamp: new Date().toISOString()
    });
}

/**
 * Sync coordinates, permission status, and geocoded name to database in the background
 */
async function syncLocationToDatabase(latitude, longitude, locationName, permissionGranted) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) return;

        const res = await fetch('/api/user-location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'accept': 'application/json'
            },
            body: JSON.stringify({
                latitude: latitude,
                longitude: longitude,
                last_detected_location: locationName,
                location_permission_granted: permissionGranted ? 1 : 0
            })
        });

        if (res.ok) {
            // Update page meta tags to stay in sync
            document.querySelector('meta[name="user-lat"]')?.setAttribute('content', latitude.toString());
            document.querySelector('meta[name="user-lon"]')?.setAttribute('content', longitude.toString());
            document.querySelector('meta[name="user-location-name"]')?.setAttribute('content', locationName);
            document.querySelector('meta[name="user-location-permission"]')?.setAttribute('content', permissionGranted ? 'granted' : 'prompt');
        }
    } catch (e) {
        console.error('Failed to sync location to database:', e);
    }
}

/**
 * Mark permission as denied
 */
function markPermissionDenied() {
    localStorage.setItem(GEOLOCATION_KEY, 'denied');
}

/**
 * Auto-detect and fetch weather on page load
 */
async function autoDetectWeatherOnPageLoad() {
    // Only run on weather or predictions page
    if (!document.getElementById('weather-status')) {
        return;
    }

    // Read initial backend geodata from meta tags
    const metaLat = document.querySelector('meta[name="user-lat"]')?.getAttribute('content');
    const metaLon = document.querySelector('meta[name="user-lon"]')?.getAttribute('content');
    const metaPermission = document.querySelector('meta[name="user-location-permission"]')?.getAttribute('content');

    // Query browser geolocator API permission state
    let browserPermission = 'prompt';
    if (navigator.permissions && navigator.permissions.query) {
        try {
            const status = await navigator.permissions.query({ name: 'geolocation' });
            browserPermission = status.state;
        } catch (e) { /* fallback */ }
    }

    // 1. If explicitly denied by browser or locally, show denied warning and load DB cache if present
    if (browserPermission === 'denied' || localStorage.getItem(GEOLOCATION_KEY) === 'denied') {
        showLocationDeniedWarning();
        if (metaLat && metaLon && metaLat !== '' && metaLon !== '') {
            fetchWeatherByCoords(parseFloat(metaLat), parseFloat(metaLon), true);
        }
        return;
    }

    // 2. If permission is granted (browser state or database state), auto-fetch fresh live coordinates silently
    if (browserPermission === 'granted' || metaPermission === 'granted' || isGeolocationPermissionGranted()) {
        showAutoDetectSpinner();
        console.log('%c[GPS] Requesting live coordinates with high accuracy...', 'color: #0099FF; font-weight: bold;');
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude, accuracy } = position.coords;

                console.log('%c[GPS] Location Received:', 'color: #00AA00; font-weight: bold;', {
                    latitude: latitude.toFixed(6),
                    longitude: longitude.toFixed(6),
                    accuracy: accuracy.toFixed(2) + 'm',
                    timestamp: new Date().toISOString()
                });

                // Reject low-accuracy coordinates
                if (accuracy > 1000) {
                    console.warn('%c[GPS] ⚠️ REJECTED: accuracy > 1000m', 'color: #FF6600; font-weight: bold;', {
                        accuracy: accuracy.toFixed(2) + 'm',
                        threshold: '1000m'
                    });
                    hideAutoDetectSpinner();
                    const warningMsg = document.documentElement.lang === 'hi'
                        ? 'जीपीएस सटीकता कम है। कृपया सटीक स्थान/जीपीएस सक्षम करें।'
                        : 'Low GPS accuracy detected. Please enable precise location/GPS.';
                    showToast(warningMsg, 'warning');
                    // Fallback to database coordinates
                    if (metaLat && metaLon && metaLat !== '' && metaLon !== '') {
                        console.log('%c[GPS] Falling back to database coordinates', 'color: #FF9900;');
                        fetchWeatherByCoords(parseFloat(metaLat), parseFloat(metaLon), true);
                    }
                    return;
                }

                console.log('%c[GPS] ✅ ACCEPTED: accuracy within threshold', 'color: #00AA00;');
                saveCoordsAndPermission(latitude, longitude, accuracy);
                showAutoDetectionBadge();
                fetchWeatherByCoords(latitude, longitude, true);
            },
            (err) => {
                hideAutoDetectSpinner();
                console.error('%c[GPS] ⚠️ Error getting position:', 'color: #FF0000; font-weight: bold;', {
                    code: err.code,
                    message: err.message
                });
                if (err.code === err.PERMISSION_DENIED) {
                    markPermissionDenied();
                    showLocationDeniedWarning();
                } else {
                    const errorMsg = document.documentElement.lang === 'hi'
                        ? 'स्थान पहुंच विफल। कृपया मौसम की जानकारी मैन्युअली भरें।'
                        : 'Location access failed. Please fill weather manually.';
                    showToast(errorMsg, 'info');
                    // Already used DB coords above if available; nothing more to do here
                }
            },
            {
                timeout: 30000,
                enableHighAccuracy: true,
                maximumAge: 0
            }
        );
    }

    // 3. First-time use (permission is 'prompt' and no coordinates stored):
    // For new users, prompt once for geolocation to provide immediate, accurate weather.
    // Do not repeatedly prompt if the user already denied or we've asked before.
    const hasAskedBefore = localStorage.getItem(GEOLOCATION_ASKED_KEY) === '1';

    // If we already have DB cached coords, use them while deciding whether to prompt
    if (metaLat && metaLon && metaLat !== '' && metaLon !== '') {
        fetchWeatherByCoords(parseFloat(metaLat), parseFloat(metaLon), true);
    }

    // If browser supports permissions API and state is 'prompt', and we haven't asked yet, prompt now
    if ((browserPermission === 'prompt' || !('permissions' in navigator)) && !hasAskedBefore && localStorage.getItem(GEOLOCATION_KEY) !== 'denied') {
        // Mark that we've asked so we don't prompt repeatedly on subsequent loads
        try { localStorage.setItem(GEOLOCATION_ASKED_KEY, '1'); } catch (e) { /* ignore */ }

        // actively request high-accuracy position to trigger the browser permission prompt
        showAutoDetectSpinner();
        console.log('%c[GPS] First-time prompt: Requesting high-accuracy position...', 'color: #0099FF; font-weight: bold;');
        navigator.geolocation.getCurrentPosition(
            (position) => {
                hideAutoDetectSpinner();
                const { latitude, longitude, accuracy } = position.coords;
                
                console.log('%c[GPS] First-time Location Received:', 'color: #00AA00; font-weight: bold;', {
                    latitude: latitude.toFixed(6),
                    longitude: longitude.toFixed(6),
                    accuracy: accuracy.toFixed(2) + 'm',
                    timestamp: new Date().toISOString()
                });

                // Reject low-accuracy coordinates even on first prompt
                if (accuracy > 1000) {
                    console.warn('%c[GPS] ⚠️ REJECTED: First-time accuracy > 1000m', 'color: #FF6600; font-weight: bold;', {
                        accuracy: accuracy.toFixed(2) + 'm'
                    });
                    const warningMsg = document.documentElement.lang === 'hi'
                        ? 'जीपीएस सटीकता कम है। कृपया सटीक स्थान/जीपीएस सक्षम करें।'
                        : 'Low GPS accuracy detected. Please enable precise location/GPS.';
                    showToast(warningMsg, 'warning');
                    return;
                }

                console.log('%c[GPS] ✅ ACCEPTED: First-time accuracy within threshold', 'color: #00AA00;');
                saveCoordsAndPermission(latitude, longitude, accuracy);
                showAutoDetectionBadge();
                fetchWeatherByCoords(latitude, longitude, true);
            },
            (err) => {
                hideAutoDetectSpinner();
                console.error('%c[GPS] ⚠️ First-time error:', 'color: #FF0000; font-weight: bold;', {
                    code: err.code,
                    message: err.message
                });
                // If user denied permission or an error occurred, record denial and fall back to DB coords
                if (err && err.code === err.PERMISSION_DENIED) {
                    markPermissionDenied();
                    showLocationDeniedWarning();
                }
                // Already used DB coords above if available; nothing more to do here
            },
            {
                timeout: 30000,
                enableHighAccuracy: true,
                maximumAge: 0
            }
        );
    }
}

/**
 * Manual weather fetch (button click)
 */
window.fetchWeatherByLocation = function () {
    const btn = document.getElementById('fetch-weather-btn');
    const statusEl = document.getElementById('weather-status');

    if (!navigator.geolocation) {
        showToast('Geolocation not supported by your browser', 'error');
        return;
    }

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<span class="animate-spin inline-block mr-2">⏳</span> ${document.documentElement.lang === 'hi' ? 'स्थान प्राप्त कर रहा है...' : 'Fetching location...'}`;
    }
    if (statusEl) statusEl.textContent = document.documentElement.lang === 'hi' ? 'स्थान का पता लगा रहा है...' : 'Detecting location...';

    console.log('%c[GPS] Manual location button clicked - Requesting high-accuracy position...', 'color: #0099FF; font-weight: bold;');

    // High accuracy, timeout set to 15 seconds for button-click scenario
    navigator.geolocation.getCurrentPosition(
        (position) => {
            const { latitude, longitude, accuracy } = position.coords;
            
            console.log('%c[GPS] Manual Location Received:', 'color: #00AA00; font-weight: bold;', {
                latitude: latitude.toFixed(6),
                longitude: longitude.toFixed(6),
                accuracy: accuracy.toFixed(2) + 'm',
                timestamp: new Date().toISOString()
            });

            // Reject low-accuracy coordinates
            if (accuracy > 1000) {
                console.warn('%c[GPS] ⚠️ REJECTED: Manual location accuracy > 1000m', 'color: #FF6600; font-weight: bold;', {
                    accuracy: accuracy.toFixed(2) + 'm'
                });
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = document.documentElement.lang === 'hi' ? '📍 स्थान प्राप्त करें' : '📍 Get Location';
                }
                if (statusEl) statusEl.textContent = '';
                const warningMsg = document.documentElement.lang === 'hi'
                    ? 'जीपीएस सटीकता कम है। कृपया सटीक स्थान/जीपीएस सक्षम करें।'
                    : 'Low GPS accuracy detected. Please enable precise location/GPS.';
                showToast(warningMsg, 'warning');
                return;
            }

            console.log('%c[GPS] ✅ ACCEPTED: Manual location accuracy within threshold', 'color: #00AA00;');
            saveCoordsAndPermission(latitude, longitude, accuracy);
            showAutoDetectionBadge();
            fetchWeatherByCoords(latitude, longitude);
        },
        (err) => {
            console.error('%c[GPS] ⚠️ Manual location error:', 'color: #FF0000; font-weight: bold;', {
                code: err.code,
                message: err.message
            });
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = document.documentElement.lang === 'hi' ? `📍 मेरे स्थान का मौसम प्राप्त करें` : `📍 Fetch My Location Weather`;
            }
            if (statusEl) statusEl.textContent = '';
            
            if (err.code === err.PERMISSION_DENIED) {
                markPermissionDenied();
                showLocationDeniedWarning();
                const msg = document.documentElement.lang === 'hi' ? 'स्थान पहुँच अस्वीकार कर दी गई। कृपया मैन्युअल रूप से दर्ज करें।' : 'Location access denied. Please enter city manually.';
                showToast(msg, 'info');
            } else {
                const msg = document.documentElement.lang === 'hi' ? 'स्थान पहुँच विफल रही। कृपया मैन्युअल रूप से भरें।' : 'Location access failed. Please fill weather manually.';
                showToast(msg, 'info');
            }
        },
        {
            timeout: 30000,
            enableHighAccuracy: true,
            maximumAge: 0
        }
    );
};

function showAutoDetectionBadge() {
    const badge = document.getElementById('auto-location-badge');
    if (badge) badge.classList.remove('hidden');
}

function hideAutoDetectionBadge() {
    const badge = document.getElementById('auto-location-badge');
    if (badge) badge.classList.add('hidden');
}

function showAutoDetectSpinner() {
    const spinner = document.getElementById('auto-detect-spinner');
    if (spinner) spinner.classList.remove('hidden');
}

function hideAutoDetectSpinner() {
    const spinner = document.getElementById('auto-detect-spinner');
    if (spinner) spinner.classList.add('hidden');
}

function showLocationDeniedWarning() {
    const warning = document.getElementById('location-denied-warning');
    if (warning) warning.classList.remove('hidden');
}

function hideLocationDeniedWarning() {
    const warning = document.getElementById('location-denied-warning');
    if (warning) warning.classList.add('hidden');
}

function setFieldValue(id, value) {
    const el = document.getElementById(id);
    if (el && value !== undefined && value !== '') {
        el.value = value;
        el.dispatchEvent(new Event('input'));
        // Highlight the field
        el.style.borderColor = '#16a34a';
        setTimeout(() => { el.style.borderColor = ''; }, 2000);
    }
}

// ========== Animated Counters ==========
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('[data-counter]');
    counters.forEach(counter => {
        const target = parseInt(counter.dataset.counter, 10);
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current).toLocaleString();
        }, 16);
    });
});

// ========== Crop Suggestions Live Preview ==========
window.fetchCropSuggestions = function () {
    clearTimeout(suggestTimeout);
    suggestTimeout = setTimeout(async () => {
        const temp = document.getElementById('temperature')?.value;
        const rain = document.getElementById('rainfall')?.value;
        const hum = document.getElementById('humidity')?.value;

        if (!temp || !rain || !hum) return;

        try {
            const res = await fetch(`/api/crop-suggestions?temperature=${temp}&rainfall=${rain}&humidity=${hum}`);
            const crops = await res.json();
            const container = document.getElementById('crop-suggestions');
            if (!container) return;

            const suitable = crops.filter(c => c.suitable).slice(0, 4);
            if (suitable.length === 0) {
                container.innerHTML = `<p class="text-sm text-gray-500">${document.documentElement.lang === 'hi' ? 'इन परिस्थितियों के लिए कोई अत्यधिक उपयुक्त फसल नहीं मिली।' : 'No highly suitable crops found for these conditions.'}</p>`;
                return;
            }

            container.innerHTML = suitable.map(c => `
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-full text-xs font-semibold">
                    nslated_name || c.name} <span class="opacity-70">(${c.match_score}%)</span>
                </span>
            `).join('');
        } catch (e) { /* silent fail */ }
    }, 600);
};

// ========== Auto-attach weather fetch on form and auto-detect on page load ==========
document.addEventListener('DOMContentLoaded', () => {
    // Trigger auto-detection on page load
    autoDetectWeatherOnPageLoad();

    const fetchBtn = document.getElementById('fetch-weather-btn');
    if (fetchBtn) {
        fetchBtn.addEventListener('click', () => fetchWeatherByLocation());
    }

    // Live crop suggestions on input change
    ['temperature', 'rainfall', 'humidity'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', fetchCropSuggestions);
    });

    // Dark mode icon init
    const iconEl = document.getElementById('dark-mode-icon');
    if (iconEl) {
        const isDark = document.documentElement.classList.contains('dark');
        iconEl.textContent = isDark ? String.fromCharCode(9728, 65039) : String.fromCharCode(128313);
    }

    // ========== City Search Autocomplete ==========
    const cityInput = document.getElementById('city-search-input');
    const suggestionsList = document.getElementById('city-suggestions-list');
    if (!cityInput || !suggestionsList) return;

    let cityDebounce;
    let activeCitySuggestIndex = -1;

    // Helper to select a city suggestion
    function selectCitySuggestion(li) {
        const lat = parseFloat(li.dataset.lat);
        const lon = parseFloat(li.dataset.lon);
        const label = li.dataset.label;

        cityInput.value = label;
        suggestionsList.classList.add('hidden');
        activeCitySuggestIndex = -1;
        fetchWeatherByCoords(lat, lon);
    }

    // Helper to highlight a city suggestion in the dropdown
    function highlightCitySuggestion(items) {
        items.forEach((item, idx) => {
            if (idx === activeCitySuggestIndex) {
                item.classList.add('bg-green-50', 'dark:bg-green-900/30', 'text-green-700', 'dark:text-green-400', 'font-semibold');
                item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                item.classList.remove('bg-green-50', 'dark:bg-green-900/30', 'text-green-700', 'dark:text-green-400', 'font-semibold');
            }
        });
    }

    cityInput.addEventListener('input', () => {
        clearTimeout(cityDebounce);
        const q = cityInput.value.trim();

        if (q.length < 2) {
            suggestionsList.classList.add('hidden');
            suggestionsList.innerHTML = '';
            activeCitySuggestIndex = -1;
            return;
        }

        // Show immediate loading state inside suggestion list
        const isHi = document.documentElement.lang?.startsWith('hi');
        const loadingMsg = isHi ? 'स्थान खोजे जा रहे हैं...' : 'Searching locations...';
        suggestionsList.innerHTML = `
            <li class="px-4 py-3 text-sm text-slate-400 dark:text-slate-500 flex items-center gap-2">
                <span class="animate-spin text-base">n> ${loadingMsg}
            </li>
        `;
        suggestionsList.classList.remove('hidden');
        activeCitySuggestIndex = -1;

        cityDebounce = setTimeout(async () => {
            try {
                // Step 1: Prioritize Indian locations by restricting countrycodes to India
                let res = await fetch(
                    `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=8&countrycodes=in&addressdetails=1`,
                    { headers: { 'accept-Language': isHi ? 'hi,en' : 'en' } }
                );
                let results = await res.json();

                // Step 2: Fallback to global search if no Indian results matched the query
                if (!results || results.length === 0) {
                    res = await fetch(
                        `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=8&addressdetails=1`,
                        { headers: { 'accept-Language': isHi ? 'hi,en' : 'en' } }
                    );
                    results = await res.json();
                }

                if (!results || !results.length) {
                    const noResultsMsg = isHi ? 'कोई स्थान नहीं मिला' : 'No locations found';
                    suggestionsList.innerHTML = `<li class="px-4 py-3 text-sm text-slate-400 dark:text-slate-500">${noResultsMsg}</li>`;
                    suggestionsList.classList.remove('hidden');
                    return;
                }

                suggestionsList.innerHTML = results.map((r, idx) => {
                    // Extract precise and prioritized address information
                    const city = r.address?.city || r.address?.town || r.address?.village || r.address?.suburb || r.name;
                    const district = r.address?.county || r.address?.district || '';
                    const state = r.address?.state || '';
                    const country = r.address?.country || '';
                    const label = [city, district, state, country].filter(Boolean).join(', ');

                    return `<li class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300 hover:bg-green-50 dark:hover:bg-green-900/20 cursor-pointer border-b border-slate-100 dark:border-slate-700 last:border-0 transition"
                                data-lat="${r.lat}" data-lon="${r.lon}" data-label="${label}" data-index="${idx}">
                                n('');

                suggestionsList.classList.remove('hidden');

                // Attach click listeners to loaded suggestions
                suggestionsList.querySelectorAll('li[data-lat]').forEach(li => {
                    li.addEventListener('click', () => selectCitySuggestion(li));
                });
            } catch (err) {
                // Graceful fallback for API issues or offline state
                const errorMsg = isHi ? 'स्थान खोज में त्रुटि' : 'Error searching locations';
                suggestionsList.innerHTML = '<li class="px-4 py-3 text-sm text-red-500">' + errorMsg + '</li>';
                suggestionsList.classList.remove('hidden');
            }
        }, 300);
    });

    // Keyboard navigation inside City Search Dropdown
    cityInput.addEventListener('keydown', (e) => {
        const items = suggestionsList.querySelectorAll('li[data-lat]');
        if (suggestionsList.classList.contains('hidden') || items.length === 0) {
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeCitySuggestIndex = (activeCitySuggestIndex + 1) % items.length;
            highlightCitySuggestion(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeCitySuggestIndex = (activeCitySuggestIndex - 1 + items.length) % items.length;
            highlightCitySuggestion(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeCitySuggestIndex >= 0 && activeCitySuggestIndex < items.length) {
                selectCitySuggestion(items[activeCitySuggestIndex]);
            } else if (items.length > 0) {
                selectCitySuggestion(items[0]); // Select first suggestion by default
            }
        } else if (e.key === 'Escape') {
            suggestionsList.classList.add('hidden');
            activeCitySuggestIndex = -1;
            cityInput.blur();
        }
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!cityInput.contains(e.target) && !suggestionsList.contains(e.target)) {
            suggestionsList.classList.add('hidden');
            activeCitySuggestIndex = -1;
        }
    });

    // Show suggestions on focus if query already entered
    cityInput.addEventListener('focus', () => {
        if (cityInput.value.trim().length >= 2 && suggestionsList.innerHTML !== '') {
            suggestionsList.classList.remove('hidden');
        }
    });
});

// nates with deduplication nst WEATHER_FETCH_MIN_INTERVAL = 5000; // Minimum 5 seconds between fetches

window.fetchWeatherByCoords = async function (latitude, longitude, isAuto = false) {
    // Prevent duplicate API calls within 5 seconds
    const now = Date.now();
    if (now - lastWeatherFetchTime < WEATHER_FETCH_MIN_INTERVAL) {
        const btn = document.getElementById('fetch-weather-btn');
        if (btn && !isAuto) {
            const isHi = document.documentElement.lang?.startsWith('hi');
            btn.disabled = false;
            btn.innerHTML = isHi ? '📍 मेरे स्थान का मौसम' : '📍 Fetch My Location Weather';
        }
        return; // Skip if too soon
    }
    lastWeatherFetchTime = now;

    // === FLOW START: GPS ng nsole.log('%cnt-weight: bold;');
    console.log('%c[FLOW] GPS ng ndering', 'color: #00AA00; font-weight: bold; font-size: 12px;');
    console.log('%cnt-weight: bold;');

    console.log('%c[STEP 1] GPS Coordinates Received:', 'color: #0099FF; font-weight: bold;', {
        latitude: latitude.toFixed(6),
        longitude: longitude.toFixed(6),
        isAutoDetect: isAuto,
        timestamp: new Date().toISOString()
    });

    const btn = document.getElementById('fetch-weather-btn');
    const statusEl = document.getElementById('weather-status');

    if (btn && !isAuto) {
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin inline-block mr-2">⏳</span> ' + (document.documentElement.lang?.startsWith('hi') ? 'लाया जा रहा है...' : 'Fetching...');
    }
    if (statusEl && !isAuto) statusEl.textContent = 'Loading weather data...';

    try {
        console.log('%c[STEP 2] Calling Weather API...', 'color: #0099FF; font-weight: bold;', { endpoint: '/api/weather?lat=' + latitude + '&lon=' + longitude, refresh: !isAuto ? '1' : '0' });

        const res = await fetch('/api/weather?lat=' + latitude + '&lon=' + longitude + (!isAuto ? '&refresh=1' : ''));
        const data = await res.json();

        console.log('%c[STEP 2.1] Weather API Response:', 'color: #00AA00; font-weight: bold;', {
            city: data.city || 'N/A',
            temperature: data.temperature,
            rainfall: data.rainfall ?? data.precipitation ?? data.rain,
            weather_condition: data.weather_condition,
            success: data.success
        });
        if (data.success) {
            // === Location Resolution Complete ===
            console.log('%c[STEP 3] Reverse Geocoding Result:', 'color: #00AA00; font-weight: bold;', {
            });

            // Store rounded integer values in inputs to mimic real weather UIs
            setFieldValue('temperature', data.temperature !== undefined && data.temperature !== null ? String(Math.round(data.temperature)) : '');
            const rainVal = data.rainfall ?? data.precipitation ?? data.rain;
            const annualRainVal = data.annual_rain ?? rainVal;
            if (annualRainVal !== undefined && annualRainVal !== null) {
                setFieldValue('rainfall', String(Math.round(annualRainVal * 10) / 10));
            }
            setFieldValue('humidity', data.humidity !== undefined && data.humidity !== null ? String(Math.round(data.humidity)) : '');

            console.log('%c[STEP 4] Form Fields Populated:', 'color: #00AA00; font-weight: bold;', {
                temperature: String(Math.round(data.temperature)) + 'ac',
                rainfall: String(Math.round(annualRainVal * 10) / 10) + 'mm',
                humidity: String(Math.round(data.humidity)) + '%'
            });

            // Proactively sync coordinates and resolved village/city name to the user database
            const metaLat = document.querySelector('meta[name="user-lat"]')?.getAttribute('content');
            const metaLon = document.querySelector('meta[name="user-lon"]')?.getAttribute('content');
            const metaName = document.querySelector('meta[name="user-location-name"]')?.getAttribute('content');
            const metaPermission = document.querySelector('meta[name="user-location-permission"]')?.getAttribute('content');
            
            const parsedMetaLat = metaLat ? parseFloat(metaLat) : null;
            const parsedMetaLon = metaLon ? parseFloat(metaLon) : null;
            
            if (
                !parsedMetaLat || !parsedMetaLon ||
                Math.abs(parsedMetaLat - latitude) > 0.005 ||
                Math.abs(parsedMetaLon - longitude) > 0.005 ||
                metaName !== data.city ||
                metaPermission !== 'granted'
            ) {
                console.log('%c[STEP 5] Syncing location to database...', 'color: #0099FF; font-weight: bold;');
                syncLocationToDatabase(latitude, longitude, data.city || '', true);
            }

            if (statusEl) {
                const lang = document.documentElement.lang || 'en';
                const isHi = lang.startsWith('hi');
                
                const weatherMapping = {
                    'clear sky': 'स्पष्ट आकाश',
                    'clear': 'स्पष्ट',
                    'clouds': 'बादल',
                    'cloudy': 'बादल',
                    'overcast': 'घने बादल',
                    'rain': 'बारिश',
                    'rainy': 'बारिश',
                    'showers': 'बारिश',
                    'thunderstorm': 'तड़ित तूफान',
                    'thunderstorms': 'तड़ित तूफान'
                };
                
                const conditionRaw = data.weather_condition || '';
                const translatedCondition = isHi ? (weatherMapping[conditionRaw.toLowerCase().trim()] || conditionRaw) : conditionRaw;
                const windUnit = isHi ? 'किमी/घंटा' : 'km/h';
                const defaultLoc = isHi ? 'मेरा स्थान' : 'My Location';
                const loadedMsg = isHi ? `${data.city || defaultLoc} का मौसम लोड हो गया!` : `Weather for ${data.city || 'My Location'} loaded!`;

                let hourlyHtml = '';
                if (data.hourly && data.hourly.length > 0) {
                    hourlyHtml = `
                        <div class="mt-4 pt-3 border-t border-green-200 dark:border-green-800/50">
                            <p class="text-xs font-semibold text-green-800 dark:text-green-300 mb-2">${isHi ? 'प्रति घंटा' : 'Hourly'}</p>
                            <div class="flex items-center overflow-x-auto gap-2 pb-1">
                                ${data.hourly.map(h => `
                                    <div class="flex flex-col items-center shrink-0 bg-white dark:bg-gray-800 rounded px-2 py-1.5 shadow-sm border border-green-100 dark:border-gray-700">
                                        <span class="text-[10px] text-gray-500">${h.time}</span>
                                        <span class="text-sm font-bold text-gray-800 dark:text-gray-200">${Math.round(h.temp)}°</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                // Build a compact status card
                statusEl.innerHTML = `
                    <div class="mt-3 p-4 bg-green-50/80 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800/50 shadow-sm transition-all">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-bold text-green-900 dark:text-green-100 text-base">${loadedMsg}</p>
                                <p class="text-sm text-green-600 dark:text-green-400 mt-0.5 capitalize">${translatedCondition}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-700 dark:text-green-300">${Math.round(data.temperature)}°C</p>
                                <p class="text-xs text-green-600 dark:text-green-400">${isHi ? 'अनुभव: ' : 'Feels: '}${Math.round(data.apparent_temperature)}°C</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="rounded-lg p-3 border border-green-100 dark:border-gray-700 shadow-sm text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-gray-400 text-[10px] uppercase tracking-wider mb-1">${isHi ? 'आर्द्रता' : 'Humidity'}</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">${data.humidity}%</span>
                                </div>
                            </div>
                            <div class="rounded-lg p-3 border border-green-100 dark:border-gray-700 shadow-sm text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-gray-400 text-[10px] uppercase tracking-wider mb-1">${isHi ? 'हवा' : 'Wind'}</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">${Math.round(data.wind_speed)} ${windUnit}</span>
                                </div>
                            </div>
                            <div class="rounded-lg p-3 border border-green-100 dark:border-gray-700 shadow-sm text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-gray-400 text-[10px] uppercase tracking-wider mb-1">${isHi ? 'वर्षा' : 'Rainfall'}</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">${data.rainfall}mm</span>
                                </div>
                            </div>
                        </div>
                        ${hourlyHtml}
                    </div>
                `;
                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-sm">${Math.round(data.humidity)}%</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-gray-400 text-[10px] uppercase tracking-wider mb-1">${isHi ? 'nd'}</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-sm">${Math.round(data.wind_speed)} <span class="text-[10px] font-normal">${windUnit} ${data.wind_direction}</span></span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-gray-400 text-[10px] uppercase tracking-wider mb-1">${isHi ? 'वर्षा' : 'Rainfall'}</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-sm">${Math.round(data.precipitation ?? data.rain ?? 0)}mm</span>
                            </div>
                        </div>
                        ${hourlyHtml}
                    </div>`;
                    
                if (!isAuto) {
                    showToast(loadedMsg, 'success');
                }
            }
        } else {
            const errorMsg = document.documentElement.lang?.startsWith('hi') ? 'मौसम लोड नहीं कर सकते।' : 'Could not load weather.';
            showToast(data.message || errorMsg, 'error');
        }
    } catch {
        const netErrorMsg = document.documentElement.lang?.startsWith('hi') ? 'नेटवर्क त्रुटि: मौसम लोड नहीं कर सकते।' : 'Network error: Could not fetch weather.';
        showToast(netErrorMsg, 'error');
    } finally {
        if (btn && !isAuto) {
            const isHi = document.documentElement.lang?.startsWith('hi');
            btn.disabled = false;
            btn.innerHTML = isHi ? '📍 मेरे स्थान का मौसम' : '📍 Fetch My Location Weather';
        }
    }
};








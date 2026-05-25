<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-lat" content="{{ auth()->user()->latitude }}">
    <meta name="user-lon" content="{{ auth()->user()->longitude }}">
    <meta name="user-location-name" content="{{ auth()->user()->last_detected_location }}">
    <meta name="user-location-permission" content="{{ auth()->user()->location_permission_granted ? 'granted' : 'prompt' }}">
    @endauth
    <meta name="description" content="{{ __('messages.tagline') }}">
    <title>@yield('title', __('messages.app_name') . ' — ' . __('messages.tagline'))</title>

    {{-- Display + Sans Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    {{-- Vite: CSS + JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Maps Geocoding API (for precise location reverse geocoding) --}}
    <script>
        // Configure Google Maps API key from environment or meta tag
        window.GOOGLE_MAPS_API_KEY = '{{ env("GOOGLE_MAPS_API_KEY", "") }}' || document.querySelector('meta[name="google-maps-api-key"]')?.content || '';
        if (window.GOOGLE_MAPS_API_KEY) {
            console.log('[CONFIG] Google Maps API key configured');
        } else {
            console.warn('[CONFIG] Google Maps API key not configured - location will fall back to IP-based detection');
        }
    </script>

    {{-- Dark Mode Immediate Blocking Script --}}
    <script>
        (function () {
            @if(session()->has('theme_updated'))
                localStorage.setItem('theme', '{{ session('theme_updated') }}');
            @endif

            let theme = localStorage.getItem('theme');
            
            @auth
            if (!theme) {
                const dbTheme = "{{ auth()->user()->theme_preference }}";
                if (dbTheme === 'dark' || dbTheme === 'light') {
                    theme = dbTheme;
                    localStorage.setItem('theme', theme);
                }
            }
            @endauth

            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    {{-- Favicon SVG (Clean wheat icon) --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 32 32%22 fill=%22none%22><path d=%22M16 4V26%22 stroke=%22%2310b981%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22/><g stroke=%22%2310b981%22 stroke-width=%221.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 fill=%22none%22><path d=%22M12 8L10 6%22/><path d=%22M11 10L8 9%22/><path d=%22M11 13L8 14%22/><path d=%22M12 16L10 18%22/></g><g stroke=%22%2310b981%22 stroke-width=%221.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 fill=%22none%22><path d=%22M20 8L22 6%22/><path d=%22M21 10L24 9%22/><path d=%22M21 13L24 14%22/><path d=%22M20 16L22 18%22/></g><g stroke=%22%2310b981%22 stroke-width=%221.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 fill=%22none%22><path d=%22M16 4L16 8%22/><path d=%22M14 6L16 7%22/><path d=%22M18 6L16 7%22/></g><circle cx=%2216%22 cy=%2228%22 r=%222%22 fill=%22%2310b981%22 opacity=%220.6%22/><path d=%22M14 27L18 27%22 stroke=%22%2310b981%22 stroke-width=%221%22 stroke-linecap=%22round%22 opacity=%220.4%22/></svg>">

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @stack('head')
</head>
<body class="antialiased dark:bg-gray-900 transition-colors duration-200">

    {{-- ─── Navbar --}}
    <nav class="glass-nav sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-12 flex items-center justify-between gap-2">

            {{-- Logo --}}
            <x-app-logo href="{{ route('home') }}" size="md" />

            {{-- Desktop Nav Links --}}
            <div class="hidden lg:flex items-center gap-1">
                <a href="{{ route('home') }}" class="nav-link px-2 py-1 {{ request()->routeIs('home') ? 'active' : '' }}">
                    {{ __('messages.nav_home') }}
                </a>
                <a href="{{ auth()->check() ? route('predictions.create') : route('login') }}" class="nav-link px-2 py-1 {{ request()->routeIs('predictions.create') ? 'active' : '' }}">
                    {{ __('messages.nav_predict') }}
                </a>
                <a href="{{ auth()->check() ? route('weather') : route('login') }}" class="nav-link px-2 py-1 {{ request()->routeIs('weather') ? 'active' : '' }}">
                    {{ __('messages.nav_weather') }}
                </a>
                <a href="{{ auth()->check() ? route('predictions.index') : route('login') }}" class="nav-link px-2 py-1 {{ request()->routeIs('predictions.index') ? 'active' : '' }}">
                    {{ __('messages.nav_history') }}
                </a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link text-amber-600 dark:text-amber-400 {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                            {{ __('messages.nav_admin') }}
                        </a>
                    @endif
                @endauth
            </div>

            {{-- Right Actions --}}
            <div class="flex items-center gap-2">

                @guest
                    <a href="{{ route('login') }}" class="nav-link hidden sm:block px-2 py-1 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 text-sm">
                        {{ __('messages.nav_login') }}
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-500 border border-transparent hover:bg-emerald-50 px-2 py-1 rounded-md">
                        {{ __('messages.nav_register') }}
                    </a>

                    {{-- Guest Settings Dropdown --}}
                    <div class="relative inline-block text-left">
                        <button id="guest-settings-btn" onclick="toggleGuestSettings(event)"
                            class="w-7 h-7 rounded-md border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-750 flex items-center justify-center transition-colors cursor-pointer select-none text-sm"
                            aria-label="Application settings">
                            ⚙️
                        </button>

                        <div id="guest-settings-menu" class="hidden absolute right-0 mt-2 w-56 rounded-md border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 py-1 z-50 transform transition-all opacity-0 translate-y-1 pointer-events-none scale-95 duration-150">
                            {{-- Theme Toggle --}}
                            <div class="px-4 py-2 flex items-center justify-between">
                                <span class="text-[13px] font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2.5">
                                    <span class="opacity-60 text-sm">🌗</span> {{ __('messages.dropdown_theme') }}
                                </span>
                                <button onclick="toggleDarkMode(event)" class="w-6 h-6 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center justify-center transition-colors cursor-pointer">
                                    <span class="theme-toggle-icon text-[11px]">🌙</span>
                                </button>
                            </div>

                            {{-- Language switcher --}}
                            <div class="px-4 py-2 flex items-center justify-between">
                                <span class="text-[13px] font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2.5">
                                    <span class="opacity-60 text-sm">🌐</span> {{ __('messages.dropdown_lang') }}
                                </span>
                                <div class="flex items-center bg-gray-100 dark:bg-gray-800/80 rounded-md p-1 border border-gray-200 dark:border-gray-700/50">
                                    <a href="{{ route('locale.set', 'en') }}"
                                       class="w-8 text-center py-1 rounded text-[10px] font-semibold transition-all {{ app()->getLocale() === 'en' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm ring-1 ring-black/5 dark:ring-white/5' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">EN</a>
                                    <a href="{{ route('locale.set', 'hi') }}"
                                       class="w-8 text-center py-1 rounded text-[10px] font-semibold transition-all {{ app()->getLocale() === 'hi' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm ring-1 ring-black/5 dark:ring-white/5' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">HI</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @auth
                        <div class="relative inline-block text-left">
                            <button id="profile-dropdown-btn" onclick="toggleProfileDropdown(event)"
                                class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-800/60 transition-colors cursor-pointer select-none">
                                <div class="w-9 h-9 rounded-full overflow-hidden border border-gray-200 dark:border-gray-600 flex items-center justify-center bg-emerald-500 text-white font-semibold text-sm flex-shrink-0">
                                            @if(auth()->user()->avatar)
                                                <img src="{{ auth()->user()->avatarUrl() }}" class="w-full h-full object-cover object-center" alt="User Avatar">
                                    @else
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    @endif
                                </div>
                                <span class="hidden sm:inline text-sm font-semibold text-gray-700 dark:text-gray-300 max-w-[110px] truncate">{{ auth()->user()->name }}</span>
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div id="profile-dropdown-menu" class="hidden absolute right-0 mt-2 min-w-[220px] max-w-xs rounded-lg border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 py-1 z-50 transform transition-all opacity-0 translate-y-1 pointer-events-none scale-95 duration-150">
                                <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2">
                                    <div class="w-9 h-9 rounded-full overflow-hidden flex items-center justify-center bg-emerald-500 text-white font-semibold text-xs flex-shrink-0 border border-gray-200 dark:border-gray-700">
                                        @if(auth()->user()->avatar)
                                            <img src="{{ auth()->user()->avatarUrl() }}" class="w-full h-full object-cover object-center" alt="User Avatar">
                                        @else
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="flex flex-col min-w-0 leading-tight">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5 max-w-[170px]">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>

                                <div class="py-1 px-1 space-y-1">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">{{ __('messages.tab_profile') }}</a>
                                    <a href="{{ route('profile.edit', ['tab' => 'security']) }}" class="block px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">{{ __('messages.tab_security') }}</a>
                                    <a href="{{ route('profile.edit', ['tab' => 'settings']) }}" class="block px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">{{ __('messages.dropdown_settings') }}</a>
                                </div>

                                <div class="border-t border-gray-100 dark:border-gray-800"></div>

                                <div class="px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 flex items-center justify-between">
                                    <span class="flex items-center gap-2">🌗 {{ __('messages.dropdown_theme') }}</span>
                                    <button onclick="toggleDarkMode(event)" class="w-5 h-5 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center justify-center transition-colors cursor-pointer text-[9px]">
                                        <span class="theme-toggle-icon">🌙</span>
                                    </button>
                                </div>

                                <div class="px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 flex items-center justify-between">
                                    <span class="flex items-center gap-2">🌐 {{ __('messages.dropdown_lang') }}</span>
                                    <div class="flex items-center bg-gray-100 dark:bg-gray-800/80 rounded p-0.5 border border-gray-200 dark:border-gray-700">
                                        <a href="{{ route('locale.set', 'en') }}" class="w-6 text-center py-0.5 rounded text-[9px] font-semibold transition-all {{ app()->getLocale() === 'en' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">EN</a>
                                        <a href="{{ route('locale.set', 'hi') }}" class="w-6 text-center py-0.5 rounded text-[9px] font-semibold transition-all {{ app()->getLocale() === 'hi' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">HI</a>
                                    </div>
                                </div>

                                <div class="border-t border-gray-100 dark:border-gray-800"></div>

                                <div class="px-0.5 py-0.5">
                                    <form action="{{ route('logout') }}" method="POST" class="block w-full">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">{{ __('messages.nav_logout') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endauth
                @endguest

                {{-- Mobile Menu Button --}}
                <button id="mobile-menu-btn" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                    class="lg:hidden w-8 h-8 flex items-center justify-center rounded-md border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 space-y-1">
            <a href="{{ route('home') }}" class="block nav-link py-2 px-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('messages.nav_home') }}</a>
            <a href="{{ auth()->check() ? route('predictions.create') : route('login') }}" class="block nav-link py-2 px-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('messages.nav_predict') }}</a>
            <a href="{{ auth()->check() ? route('weather') : route('login') }}" class="block nav-link py-2 px-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('messages.nav_weather') }}</a>
            <a href="{{ auth()->check() ? route('predictions.index') : route('login') }}" class="block nav-link py-2 px-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('messages.nav_history') }}</a>
            @auth
                <div class="pt-2 border-t border-gray-100 dark:border-gray-700 mt-2 space-y-1">
                    <a href="{{ route('profile.edit') }}" class="block nav-link py-2 px-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('messages.tab_profile') }}</a>
                    <a href="{{ route('profile.edit', ['tab' => 'settings']) }}" class="block nav-link py-2 px-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('messages.dropdown_settings') }}</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="block w-full text-left nav-link py-2 px-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-red-500 cursor-pointer">{{ __('messages.nav_logout') }}</button>
                    </form>
                </div>
            @else
                <div class="pt-2 border-t border-gray-100 dark:border-gray-700 mt-2 space-y-1">
                    <a href="{{ route('login') }}" class="block nav-link py-2 px-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('messages.nav_login') }}</a>
                    <a href="{{ route('register') }}" class="block nav-link py-2 px-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('messages.nav_register') }}</a>
                </div>
            @endauth
        </div>
    </nav>

    {{-- ─── Flash Messages --}}
    @if(session('success'))
        <script>document.addEventListener('DOMContentLoaded', () => showToast('{{ addslashes(session('success')) }}', 'success'));</script>
    @endif
    @if(session('error'))
        <script>document.addEventListener('DOMContentLoaded', () => showToast('{{ addslashes(session('error')) }}', 'error'));</script>
    @endif

    {{-- ─── Main Content --}}
    <main id="main-content">
        @yield('content')
    </main>

    {{-- ─── Footer --}}
    <footer class="bg-gray-900 dark:bg-gray-950 mt-8 border-t border-gray-800 dark:border-gray-700/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
            {{-- Main Footer Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
                
                {{-- Brand Section --}}
                <div class="col-span-1 sm:col-span-2 lg:col-span-1">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-4 hover:opacity-80 transition-opacity">
                        <x-app-logo size="md" :showText="false" />
                        <div>
                            <p class="font-semibold text-white text-sm">{{ __('messages.app_name') === 'फसल उपज पोर्टल' ? 'फसल उपज' : 'Crop Yield' }}</p>
                            <p class="text-[11px] text-gray-400">{{ __('messages.tagline') }}</p>
                        </div>
                    </a>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="text-white font-semibold text-sm mb-4 flex items-center gap-2">
                        <span class="w-1 h-4 bg-emerald-500 rounded-full"></span>
                        {{ __('messages.quick_links') }}
                    </h4>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-emerald-400 text-sm transition-colors">{{ __('messages.nav_home') }}</a></li>
                        <li><a href="{{ auth()->check() ? route('predictions.create') : route('login') }}" class="text-gray-400 hover:text-emerald-400 text-sm transition-colors">{{ __('messages.nav_predict') }}</a></li>
                        <li><a href="{{ auth()->check() ? route('weather') : route('login') }}" class="text-gray-400 hover:text-emerald-400 text-sm transition-colors">{{ __('messages.nav_weather') }}</a></li>
                        <li><a href="{{ auth()->check() ? route('predictions.index') : route('login') }}" class="text-gray-400 hover:text-emerald-400 text-sm transition-colors">{{ __('messages.nav_history') }}</a></li>
                    </ul>
                </div>

                {{-- Company --}}
                <div>
                    <h4 class="text-white font-semibold text-sm mb-4 flex items-center gap-2">
                        <span class="w-1 h-4 bg-emerald-500 rounded-full"></span>
                        Company
                    </h4>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-emerald-400 text-sm transition-colors">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-emerald-400 text-sm transition-colors">Contact Us</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-gray-400 hover:text-emerald-400 text-sm transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="text-gray-400 hover:text-emerald-400 text-sm transition-colors">Terms & Conditions</a></li>
                    </ul>
                </div>

                {{-- Support --}}
                <div>
                    <h4 class="text-white font-semibold text-sm mb-4 flex items-center gap-2">
                        <span class="w-1 h-4 bg-emerald-500 rounded-full"></span>
                        Support
                    </h4>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('faq') }}" class="text-gray-400 hover:text-emerald-400 text-sm transition-colors">FAQ</a></li>
                        <li><a href="{{ route('help') }}" class="text-gray-400 hover:text-emerald-400 text-sm transition-colors">Help Center</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-emerald-400 text-sm transition-colors">Report Issue</a></li>
                        <li><a href="{{ route('docs') }}" class="text-gray-400 hover:text-emerald-400 text-sm transition-colors">Documentation</a></li>
                    </ul>
                </div>

                {{-- Contact Info --}}
                <div>
                    <h4 class="text-white font-semibold text-sm mb-4 flex items-center gap-2">
                        <span class="w-1 h-4 bg-emerald-500 rounded-full"></span>
                        Contact
                    </h4>
                    <ul class="space-y-2.5">
                        <li class="text-gray-400 text-sm hover:text-emerald-400 transition-colors">
                            <a href="mailto:support@cropyield.com">support@cropyield.com</a>
                        </li>
                        <li class="text-gray-400 text-sm">Monday - Friday, 9 AM - 6 PM IST</li>
                        <li class="text-gray-400 text-sm">🇮🇳 India</li>
                    </ul>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-gray-800 dark:border-gray-700/50 pt-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-gray-500 text-xs">© {{ date('Y') }} {{ __('messages.app_name') }}. All rights reserved. {{ __('messages.built_with') }}.</p>
                    <div class="flex items-center gap-3 text-xs">
                        <a href="{{ route('locale.set', 'en') }}" class="text-gray-400 hover:text-emerald-400 transition-colors {{ app()->getLocale() === 'en' ? 'text-emerald-400' : '' }}">EN</a>
                        <span class="text-gray-600">·</span>
                        <a href="{{ route('locale.set', 'hi') }}" class="text-gray-400 hover:text-emerald-400 transition-colors {{ app()->getLocale() === 'hi' ? 'text-emerald-400' : '' }}">हिन्दी</a>
                        <span class="text-gray-600">·</span>
                        <span class="text-gray-600">v1.0.0</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
    
    {{-- Animated Dropdowns Script --}}
    <script>
        function updateThemeIcon() {
            const isDark = document.documentElement.classList.contains('dark');
            const icon = isDark ? '☀️' : '🌙';
            
            document.querySelectorAll('.theme-toggle-icon').forEach(iconEl => {
                iconEl.textContent = icon;
            });
        }

        function toggleDropdownElement(menuId) {
            const menu = document.getElementById(menuId);
            if (!menu) return;

            const isOpen = !menu.classList.contains('hidden') && menu.classList.contains('opacity-100');

            closeAllDropdowns();

            if (!isOpen) {
                menu.classList.remove('hidden');
                setTimeout(() => {
                    menu.classList.remove('opacity-0', 'translate-y-2', 'pointer-events-none', 'scale-95');
                    menu.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto', 'scale-100');
                }, 20);
            }
        }

        function toggleProfileDropdown(e) {
            e.stopPropagation();
            toggleDropdownElement('profile-dropdown-menu');
        }

        function toggleGuestSettings(e) {
            e.stopPropagation();
            toggleDropdownElement('guest-settings-menu');
        }

        function closeAllDropdowns() {
            ['profile-dropdown-menu', 'guest-settings-menu'].forEach(menuId => {
                const menu = document.getElementById(menuId);
                if (menu && !menu.classList.contains('hidden')) {
                    menu.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto', 'scale-100');
                    menu.classList.add('opacity-0', 'translate-y-2', 'pointer-events-none', 'scale-95');
                    setTimeout(() => {
                        if (menu.classList.contains('opacity-0')) {
                            menu.classList.add('hidden');
                        }
                    }, 200);
                }
            });
        }

        document.addEventListener('click', (e) => {
            // Do not close if clicking inside the active dropdown card itself
            if (e.target.closest('#profile-dropdown-menu') || e.target.closest('#guest-settings-menu')) {
                return;
            }
            closeAllDropdowns();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeAllDropdowns();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            updateThemeIcon();
            
            updateThemeIcon();
        });
    </script>
</body>
</html>

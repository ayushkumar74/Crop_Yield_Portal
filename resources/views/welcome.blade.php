<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Minimal fallback styles (kept intentionally small) */
                body { font-family: 'Instrument Sans', system-ui, sans-serif; margin: 0; }
                .container { padding: 1rem; max-width: 64rem; margin: 0 auto; }
            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18]">
        <div class="container">
            <header class="w-full text-sm mb-4">
                @if (Route::has('login'))
                    <nav class="flex items-center justify-end gap-2 text-sm">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-block px-3 py-1 dark:text-[#EDEDEC] text-[#1b1b18] rounded-sm">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="inline-block px-3 py-1 text-[#1b1b18] dark:text-[#EDEDEC] rounded-sm">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-block px-3 py-1 text-[#1b1b18] dark:text-[#EDEDEC] rounded-sm">Register</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <main>
                <div class="bg-white dark:bg-[#0a0a0a] rounded-lg shadow-sm p-4">
                    <h1 class="text-2xl font-medium mb-2">Welcome to {{ config('app.name', 'Laravel') }}</h1>
                    <p class="text-sm text-[#706f6c]">A simple, realistic UI — compact and production-ready.</p>
                </div>

                <div class="h-6"></div>
            </main>
        </div>
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login') — {{ __('messages.app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen flex items-center justify-center py-12 px-4">

    <div class="w-full max-w-sm">

        {{-- Logo --}}
        <div class="text-center mb-6">
            <x-app-logo href="{{ route('home') }}" size="md" />
        </div>

        {{-- Card --}}
        <div class="card">
            @yield('auth-content')
        </div>

        {{-- Footer Link --}}
        @yield('auth-footer')

    </div>

</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login') — {{ __('messages.app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Favicon SVG (clean wheat icon) --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 32 32%22 fill=%22none%22><path d=%22M16 4V26%22 stroke=%22%2310b981%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22/><g stroke=%22%2310b981%22 stroke-width=%221.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 fill=%22none%22><path d=%22M12 8L10 6%22/><path d=%22M11 10L8 9%22/><path d=%22M11 13L8 14%22/><path d=%22M12 16L10 18%22/></g><g stroke=%22%2310b981%22 stroke-width=%221.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 fill=%22none%22><path d=%22M20 8L22 6%22/><path d=%22M21 10L24 9%22/><path d=%22M21 13L24 14%22/><path d=%22M20 16L22 18%22/></g><g stroke=%22%2310b981%22 stroke-width=%221.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 fill=%22none%22><path d=%22M16 4L16 8%22/><path d=%22M14 6L16 7%22/><path d=%22M18 6L16 7%22/></g><circle cx=%2216%22 cy=%2228%22 r=%222%22 fill=%22%2310b981%22 opacity=%220.6%22/><path d=%22M14 27L18 27%22 stroke=%22%2310b981%22 stroke-width=%221%22 stroke-linecap=%22round%22 opacity=%220.4%22/></svg>">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen flex items-center justify-center py-12 px-4">

    <div class="w-full max-w-sm">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <x-app-logo href="{{ route('home') }}" size="md" />
        </div>

        {{-- Card --}}
        <div class="stat-card">
            @yield('auth-content')
        </div>

        {{-- Footer Link --}}
        @yield('auth-footer')

    </div>

</body>
</html>

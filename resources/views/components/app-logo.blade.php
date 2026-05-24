{{-- Shared App Logo Component --}}
@props(['size' => 'md', 'showText' => true, 'href' => null])

@php
    $sizeClasses = match($size) {
        'sm' => 'w-5 h-5',
        'lg' => 'w-9 h-9',
        default => 'w-7 h-7', // md (slightly smaller)
    };
    
    $textSize = match($size) {
        'sm' => 'text-sm',
        'lg' => 'text-lg',
        default => 'text-sm', // md (compact)
    };
    
    $logoContent = <<<'SVG'
    <svg class="w-full h-full" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Wheat plant icon - clean and minimal -->
        <!-- Main stem -->
        <path d="M16 4V26" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        
        <!-- Left wheat heads -->
        <g stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none">
            <path d="M12 8L10 6"/>
            <path d="M11 10L8 9"/>
            <path d="M11 13L8 14"/>
            <path d="M12 16L10 18"/>
        </g>
        
        <!-- Right wheat heads -->
        <g stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none">
            <path d="M20 8L22 6"/>
            <path d="M21 10L24 9"/>
            <path d="M21 13L24 14"/>
            <path d="M20 16L22 18"/>
        </g>
        
        <!-- Center wheat head (top) -->
        <g stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none">
            <path d="M16 4L16 8"/>
            <path d="M14 6L16 7"/>
            <path d="M18 6L16 7"/>
        </g>
        
        <!-- Soil/root indicator -->
        <circle cx="16" cy="28" r="2" fill="currentColor" opacity="0.6"/>
        <path d="M14 27L18 27" stroke="currentColor" stroke-width="1" stroke-linecap="round" opacity="0.4"/>
    </svg>
    SVG;
@endphp

@if($href)
    <a href="{{ $href }}" class="flex items-center gap-2 flex-shrink-0 hover:opacity-80 transition-opacity" {{ $attributes }}>
        <div class="flex items-center justify-center flex-shrink-0 text-emerald-600 dark:text-emerald-500 {{ $sizeClasses }}">
            {!! $logoContent !!}
        </div>
        @if($showText)
            <span class="font-bold tracking-tight text-gray-900 dark:text-white {{ $textSize }}">
                {{ __('messages.app_name') === 'फसल उपज पोर्टल' ? 'फसल उपज' : 'Crop Yield' }}
            </span>
        @endif
    </a>
@else
    <div class="flex items-center gap-2 flex-shrink-0" {{ $attributes }}>
        <div class="flex items-center justify-center flex-shrink-0 text-emerald-600 dark:text-emerald-500 {{ $sizeClasses }}">
            {!! $logoContent !!}
        </div>
        @if($showText)
            <span class="font-bold tracking-tight text-gray-900 dark:text-white {{ $textSize }}">
                {{ __('messages.app_name') === 'फसल उपज पोर्टल' ? 'फसल उपज' : 'Crop Yield' }}
            </span>
        @endif
    </div>
@endif

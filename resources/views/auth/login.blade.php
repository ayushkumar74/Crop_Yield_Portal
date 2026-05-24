@extends('layouts.auth')
@section('title', __('messages.login_title'))

@section('auth-content')
<h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ __('messages.login_title') }}</h1>
<p class="text-gray-500 dark:text-gray-400 text-sm mb-6">{{ __('messages.login_subtitle') }}</p>

@if($errors->any())
<div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md text-xs text-red-600 dark:text-red-400">
    {{ $errors->first() }}
</div>
@endif

{{-- ✅ NEW: Google Login Button --}}
<a href="{{ route('auth.google') }}"
   class="flex items-center justify-center gap-3 w-full border border-gray-300 dark:border-gray-600
          rounded-lg py-2.5 px-4 text-sm font-medium text-gray-700 dark:text-gray-200
          bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700
          transition-colors duration-150 mb-4 shadow-sm">
    <svg class="w-5 h-5 shrink-0" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
        <path fill="#EA4335" d="M24 9.5c3.17 0 6.01 1.09 8.25 2.88l6.16-6.16C34.54 3.04 29.55 1 24 1 14.82 1 7.03 6.48 3.58 14.24l7.17 5.57C12.45 13.41 17.77 9.5 24 9.5z"/>
        <path fill="#4285F4" d="M46.1 24.55c0-1.57-.14-3.09-.4-4.55H24v8.61h12.42c-.54 2.9-2.17 5.36-4.63 7.02l7.19 5.59C43.22 37.34 46.1 31.35 46.1 24.55z"/>
        <path fill="#FBBC05" d="M10.75 28.43A14.57 14.57 0 0 1 9.5 24c0-1.54.26-3.03.72-4.43l-7.17-5.57A22.9 22.9 0 0 0 1 24c0 3.68.87 7.15 2.41 10.24l7.34-5.81z"/>
        <path fill="#34A853" d="M24 47c5.45 0 10.03-1.81 13.37-4.89l-7.19-5.59c-1.79 1.2-4.09 1.92-6.18 1.92-6.2 0-11.5-3.88-13.4-9.35l-7.34 5.81C7.1 42.56 14.87 47 24 47z"/>
    </svg>
    Continue with Google
</a>

{{-- ✅ NEW: Divider --}}
<div class="flex items-center gap-3 mb-4">
    <hr class="flex-1 border-gray-200 dark:border-gray-700">
    <span class="text-xs text-gray-400 dark:text-gray-500">or sign in with email</span>
    <hr class="flex-1 border-gray-200 dark:border-gray-700">
</div>

{{-- YOUR ORIGINAL FORM — NO CHANGES --}}
<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf
    <div>
        <label class="form-label" for="email">{{ __('messages.email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required
            class="form-input" placeholder="you@example.com">
    </div>
    <div>
        <label class="form-label" for="password">{{ __('messages.password') }}</label>
        <input id="password" type="password" name="password" required
            class="form-input" placeholder="••••••••">
    </div>
    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 cursor-pointer">
            <input type="checkbox" name="remember" class="rounded accent-green-600">
            {{ __('messages.remember_me') }}
        </label>
    </div>

    {{-- ✅ NEW: OTP note --}}
    <p class="text-xs text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800/60 rounded-md px-3 py-2">
        🔐 A 6-digit OTP will be sent to your email after submitting.
    </p>

    <button type="submit" class="btn-primary w-full py-2.5">
        {{ __('messages.login_btn') }}
    </button>
</form>
@endsection

@section('auth-footer')
<p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-4">
    {{ __('messages.login_footer') }}
    <a href="{{ route('register') }}" class="text-green-600 dark:text-green-400 font-medium hover:underline">{{ __('messages.create_account') }}</a>
</p>
@endsection
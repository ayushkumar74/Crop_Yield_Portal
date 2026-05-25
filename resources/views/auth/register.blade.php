@extends('layouts.auth')
@section('title', __('messages.register_title'))

@section('auth-content')
<h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ __('messages.register_title') }}</h1>
<p class="text-gray-500 dark:text-gray-400 text-sm mb-6">{{ __('messages.register_subtitle') }}</p>

@if($errors->any())
<div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md text-xs text-red-600 dark:text-red-400">
    <ul class="space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf

    <div>
        <label class="form-label" for="name">{{ __('messages.name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required
            class="form-input" placeholder="Your full name">
    </div>

    <div>
        <label class="form-label" for="email">{{ __('messages.email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required
            class="form-input" placeholder="you@example.com">
    </div>

    <div>
        <label class="form-label" for="password">{{ __('messages.password') }}</label>
        <input id="password" type="password" name="password" required
            class="form-input" placeholder="Min. 8 characters"
            oninput="checkStrength(this.value)">

        <div class="mt-2 h-1.5 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
            <div id="strength-bar" class="h-full rounded-full transition-all duration-300" style="width:0%"></div>
        </div>

        <p id="strength-label" class="mt-1 text-xs" style="color:#9ca3af"></p>

        <ul class="mt-2 space-y-1">
            <li id="rule-length"  class="flex items-center gap-2 text-xs" style="color:#9ca3af"><span class="icon">○</span> At least 8 characters</li>
            <li id="rule-upper"   class="flex items-center gap-2 text-xs" style="color:#9ca3af"><span class="icon">○</span> One uppercase letter (A-Z)</li>
            <li id="rule-lower"   class="flex items-center gap-2 text-xs" style="color:#9ca3af"><span class="icon">○</span> One lowercase letter (a-z)</li>
            <li id="rule-number"  class="flex items-center gap-2 text-xs" style="color:#9ca3af"><span class="icon">○</span> One number (0-9)</li>
            <li id="rule-special" class="flex items-center gap-2 text-xs" style="color:#9ca3af"><span class="icon">○</span> One special character (!@#$%^&* …)</li>
        </ul>
    </div>

    <div>
        <label class="form-label" for="password_confirmation">{{ __('messages.confirm_password') }}</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required
            class="form-input" placeholder="Repeat password"
            oninput="checkMatch()">
        <p id="match-msg" class="mt-1 text-xs" style="display:none"></p>
    </div>

    <button type="submit" class="btn-primary w-full py-2.5">
        {{ __('messages.register_btn') }}
    </button>
</form>

<!-- Google OAuth Divider -->
<div class="relative my-5">
    <div class="absolute inset-0 flex items-center">
        <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
    </div>
    <div class="relative flex justify-center text-xs uppercase">
        <span class="px-2 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 font-medium">Or</span>
    </div>
</div>

<!-- Google OAuth Button -->
<a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-2 py-2 px-4 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M23.745 12.27c0-.79-.1-1.54-.257-2.26H12v4.26h6.235c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.08z" fill="#4285F4"/>
        <path d="M12 24c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 24 12 24z" fill="#34A853"/>
        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 4.47 2.18 9.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
    </svg>
    Continue with Google
</a>

@endsection

@section('auth-footer')
<p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-4">
    {{ __('messages.register_footer') }}
    <a href="{{ route('login') }}" class="text-green-600 dark:text-green-400 font-medium hover:underline">{{ __('messages.sign_in_link') }}</a>
</p>
@endsection

@push('scripts')
<script>
    function checkStrength(val) {
        var checks = {
            'rule-length':  val.length >= 8,
            'rule-upper':   /[A-Z]/.test(val),
            'rule-lower':   /[a-z]/.test(val),
            'rule-number':  /[0-9]/.test(val),
            'rule-special': /[@$!%*?&#^()\-_=+\[\]{};:'"\\|,.<>\/`~]/.test(val),
        };

        var passed = 0;
        for (var id in checks) {
            var li   = document.getElementById(id);
            var icon = li.querySelector('.icon');
            if (checks[id]) {
                passed++;
                li.style.color   = '#16a34a';
                icon.textContent = '✓';
            } else {
                li.style.color   = '#9ca3af';
                icon.textContent = '○';
            }
        }

        var bar    = document.getElementById('strength-bar');
        var label  = document.getElementById('strength-label');
        var widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
        var colors = ['', '#ef4444', '#f97316', '#eab308', '#84cc16', '#16a34a'];
        var labels = ['', 'Very Weak', 'Weak', 'Fair', 'Good', 'Strong ✓'];

        bar.style.width           = widths[passed];
        bar.style.backgroundColor = colors[passed];
        label.textContent         = passed > 0 ? labels[passed] : '';
        label.style.color         = colors[passed];
    }

    function checkMatch() {
        var pwd  = document.getElementById('password').value;
        var conf = document.getElementById('password_confirmation').value;
        var msg  = document.getElementById('match-msg');
        if (!conf) { msg.style.display = 'none'; return; }
        msg.style.display = 'block';
        if (pwd === conf) {
            msg.textContent = '✓ Passwords match';
            msg.style.color = '#16a34a';
        } else {
            msg.textContent = '✗ Passwords do not match';
            msg.style.color = '#ef4444';
        }
    }
</script>
@endpush

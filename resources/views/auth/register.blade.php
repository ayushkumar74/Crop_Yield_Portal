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
<<<<<<< HEAD

    {{-- NAME FIELD --}}
=======
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
    <div>
        <label class="form-label" for="name">{{ __('messages.name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required
            class="form-input" placeholder="Your full name">
    </div>
<<<<<<< HEAD

    {{-- EMAIL FIELD --}}
=======
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
    <div>
        <label class="form-label" for="email">{{ __('messages.email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required
            class="form-input" placeholder="you@example.com">
    </div>
<<<<<<< HEAD

    {{-- PASSWORD FIELD --}}
    <div>
        <label class="form-label" for="password">{{ __('messages.password') }}</label>
        <input id="password" type="password" name="password" required
            class="form-input" placeholder="Min. 8 characters"
            oninput="checkStrength(this.value)">

        {{-- Strength bar --}}
        <div class="mt-2 h-1.5 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
            <div id="strength-bar" class="h-full rounded-full transition-all duration-300" style="width:0%"></div>
        </div>

        {{-- Strength label --}}
        <p id="strength-label" class="mt-1 text-xs" style="color:#9ca3af"></p>

        {{-- Rules checklist --}}
        <ul class="mt-2 space-y-1">
            <li id="rule-length"  class="flex items-center gap-2 text-xs" style="color:#9ca3af"><span class="icon">○</span> At least 8 characters</li>
            <li id="rule-upper"   class="flex items-center gap-2 text-xs" style="color:#9ca3af"><span class="icon">○</span> One uppercase letter (A-Z)</li>
            <li id="rule-lower"   class="flex items-center gap-2 text-xs" style="color:#9ca3af"><span class="icon">○</span> One lowercase letter (a-z)</li>
            <li id="rule-number"  class="flex items-center gap-2 text-xs" style="color:#9ca3af"><span class="icon">○</span> One number (0-9)</li>
            <li id="rule-special" class="flex items-center gap-2 text-xs" style="color:#9ca3af"><span class="icon">○</span> One special character (!@#$%^&* …)</li>
        </ul>
    </div>

    {{-- CONFIRM PASSWORD FIELD --}}
    <div>
        <label class="form-label" for="password_confirmation">{{ __('messages.confirm_password') }}</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required
            class="form-input" placeholder="Repeat password"
            oninput="checkMatch()">
        <p id="match-msg" class="mt-1 text-xs" style="display:none"></p>
    </div>

    {{-- SUBMIT BUTTON --}}
=======
    <div>
        <label class="form-label" for="password">{{ __('messages.password') }}</label>
        <input id="password" type="password" name="password" required
            class="form-input" placeholder="Min. 8 characters">
    </div>
    <div>
        <label class="form-label" for="password_confirmation">{{ __('messages.confirm_password') }}</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required
            class="form-input" placeholder="Repeat password">
    </div>
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
    <button type="submit" class="btn-primary w-full py-2.5">
        {{ __('messages.register_btn') }}
    </button>
</form>
<<<<<<< HEAD

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

=======
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
@endsection

@section('auth-footer')
<p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-4">
    {{ __('messages.register_footer') }}
    <a href="{{ route('login') }}" class="text-green-600 dark:text-green-400 font-medium hover:underline">{{ __('messages.sign_in_link') }}</a>
</p>
<<<<<<< HEAD
@endsection
=======
@endsection
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755

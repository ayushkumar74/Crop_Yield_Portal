@extends('layouts.auth')
@section('title', 'Verify OTP')

@section('auth-content')
<div class="text-center mb-5">
    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-3">
        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Check your email</h1>
    <p class="text-gray-500 dark:text-gray-400 text-sm">
        We sent a 6-digit code to<br>
        <span class="font-medium text-gray-700 dark:text-gray-300">{{ session('otp_email') }}</span>
    </p>
</div>

@if($errors->any())
<div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md text-xs text-red-600 dark:text-red-400">
    {{ $errors->first() }}
</div>
@endif

@if(session('success'))
<div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md text-xs text-green-700 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('otp.verify') }}" class="space-y-4">
    @csrf

    {{-- 6-box OTP input --}}
    <div>
        <label class="form-label text-center block mb-3">Enter your 6-digit code</label>
        <div style="display:flex; gap:8px; justify-content:center;" id="otp-boxes">
            @for ($i = 0; $i < 6; $i++)
            <input
                type="text"
                inputmode="numeric"
                maxlength="1"
                pattern="\d"
                style="width:44px; height:48px; text-align:center; font-size:20px; font-weight:700; padding:0; border-radius:8px; border:1px solid #374151; background:transparent; color:inherit; outline:none;"
                class="otp-digit focus:border-green-500"
                autocomplete="off"
            >
            @endfor
        </div>
        {{-- Hidden real input --}}
        <input type="hidden" name="otp" id="otp-value">
    </div>

    <p class="text-xs text-center text-gray-400 dark:text-gray-500">
        Code expires in <span id="otp-timer" class="font-semibold text-green-600 dark:text-green-400">01:00</span>
    </p>

    <button type="submit" id="verify-btn" class="btn-primary w-full py-2.5" disabled>
        Verify &amp; Sign In
    </button>
</form>

<div class="mt-4 text-center">
    <form method="POST" action="{{ route('otp.resend') }}">
        @csrf
        <button type="submit"
                class="text-xs text-green-600 dark:text-green-400 hover:underline font-medium">
            Didn't receive it? Resend OTP
        </button>
    </form>
    <a href="{{ route('login') }}" class="block mt-2 text-xs text-gray-400 dark:text-gray-500 hover:underline">
        ← Back to login
    </a>
</div>

<script>
// ─── OTP box auto-advance ────────────────────────────────────────────────────
var boxes  = document.querySelectorAll('.otp-digit');
var hidden = document.getElementById('otp-value');
var btn    = document.getElementById('verify-btn');

function syncHidden() {
    var val = '';
    boxes.forEach(function(b) { val += b.value; });
    hidden.value = val;
    btn.disabled = val.length < 6 || !/^\d{6}$/.test(val);
}

boxes.forEach(function(box, i) {
    box.addEventListener('input', function() {
        box.value = box.value.replace(/\D/g, '').slice(-1);
        syncHidden();
        if (box.value && i < boxes.length - 1) boxes[i + 1].focus();
    });

    box.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !box.value && i > 0) {
            boxes[i - 1].value = '';
            boxes[i - 1].focus();
            syncHidden();
        }
        if (e.key === 'ArrowLeft'  && i > 0)              boxes[i - 1].focus();
        if (e.key === 'ArrowRight' && i < boxes.length-1) boxes[i + 1].focus();
    });

    box.addEventListener('paste', function(e) {
        e.preventDefault();
        var text = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
        for (var j = 0; j < text.length; j++) {
            if (boxes[j]) boxes[j].value = text[j];
        }
        syncHidden();
        var nextEmpty = -1;
        boxes.forEach(function(b, idx) { if (nextEmpty === -1 && !b.value) nextEmpty = idx; });
        if (nextEmpty !== -1) boxes[nextEmpty].focus();
        else boxes[5].focus();
    });
});

// ─── Countdown timer — 1 minute ─────────────────────────────────────────────
var remaining = 60;
var timerEl   = document.getElementById('otp-timer');

var interval = setInterval(function() {
    remaining--;
    if (remaining <= 0) {
        clearInterval(interval);
        timerEl.textContent      = 'Expired';
        timerEl.style.color      = '#ef4444';
        btn.disabled             = true;
        return;
    }
    var m = Math.floor(remaining / 60).toString().padStart(2, '0');
    var s = (remaining % 60).toString().padStart(2, '0');
    timerEl.textContent = m + ':' + s;
}, 1000);
</script>
@endsection
@extends('layouts.app')

@section('title', __('messages.profile_title') . ' — ' . __('messages.app_name'))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.profile_title') }}</h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.profile_desc') }}</p>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-600 dark:text-red-400">
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="flex flex-col md:flex-row gap-6">
        {{-- Tabs Sidebar --}}
        <div class="w-full md:w-64 shrink-0">
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-xl p-2.5 space-y-1 shadow-sm">
                <button type="button" onclick="switchTab('profile')" id="tab-btn-profile"
                    class="tab-btn w-full text-left px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2.5 transition-all text-green-700 bg-green-50 dark:text-green-400 dark:bg-green-900/10">
                    👤 {{ __('messages.tab_profile') }}
                </button>
                <button type="button" onclick="switchTab('security')" id="tab-btn-security"
                    class="tab-btn w-full text-left px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2.5 transition-all text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700/30">
                    🔒 {{ __('messages.tab_security') }}
                </button>
                <button type="button" onclick="switchTab('settings')" id="tab-btn-settings"
                    class="tab-btn w-full text-left px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2.5 transition-all text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700/30">
                    ⚙️ {{ __('messages.tab_settings') }}
                </button>
            </div>
        </div>

        {{-- Active View Card --}}
        <div class="grow bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-xl shadow-sm p-6">
            
            {{-- Tab 1: Edit Profile --}}
            <div id="tab-view-profile" class="tab-view space-y-6">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider pb-3 border-b border-gray-100 dark:border-gray-700/60">{{ __('messages.tab_profile') }}</h2>
                
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    
<<<<<<< HEAD
                   {{-- Avatar Upload Section --}}
                    <div class="flex items-center gap-5">
                        <div class="relative w-20 h-20 rounded-full overflow-hidden border-2 border-gray-200 dark:border-gray-700 shrink-0">
                            @if($user->avatar)
<img src="{{ asset('storage/app/public/avatars' . $user->avatar) }}" id="avatar-preview" class="w-full h-full object-cover" alt="Avatar">
                                <div id="avatar-initial" class="hidden"></div>
=======
                    {{-- Avatar Upload Section --}}
                    <div class="flex items-center gap-5">
                        <div class="relative w-20 h-20 rounded-full overflow-hidden border-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 shrink-0 flex items-center justify-center">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" id="avatar-preview" class="w-full h-full object-cover" alt="Avatar">
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                            @else
                                <div id="avatar-initial" class="w-full h-full bg-green-600 flex items-center justify-center text-white text-3xl font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
<<<<<<< HEAD
                                <img src="" id="avatar-preview" class="hidden absolute inset-0 w-full h-full object-cover" alt="Avatar">
                            @endif
                        </div>

=======
                                <img src="" id="avatar-preview" class="hidden w-full h-full object-cover" alt="Avatar">
                            @endif
                        </div>
                        
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
                        <div class="space-y-1 grow">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.profile_avatar_label') }}</label>
                            <input type="file" name="avatar" accept="image/*" onchange="previewAvatar(this)"
                                class="block w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 dark:file:bg-green-950 dark:file:text-green-400 hover:file:bg-green-100 dark:hover:file:bg-green-900 cursor-pointer">
                            <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ __('messages.profile_avatar_help') }}</p>
                        </div>
                    </div>

                    {{-- Form Fields --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.profile_name_label') }}</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="input-field text-sm w-full">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.profile_email_label') }}</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="input-field text-sm w-full">
                    </div>

                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700/60 flex justify-end">
                        <button type="submit" class="btn-primary text-xs py-2 px-5 font-semibold">
                            {{ __('messages.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tab 2: Security & Password --}}
            <div id="tab-view-security" class="tab-view hidden space-y-6">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider pb-3 border-b border-gray-100 dark:border-gray-700/60">{{ __('messages.tab_security') }}</h2>
                
                <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.current_password_label') }}</label>
                        <input type="password" name="current_password" required
                            class="input-field text-sm w-full">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.new_password_label') }}</label>
                        <input type="password" name="password" required
                            class="input-field text-sm w-full">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.confirm_password_label') }}</label>
                        <input type="password" name="password_confirmation" required
                            class="input-field text-sm w-full">
                    </div>

                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700/60 flex justify-end">
                        <button type="submit" class="btn-primary text-xs py-2 px-5 font-semibold">
                            {{ __('messages.update_password') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tab 3: Settings & Preferences --}}
            <div id="tab-view-settings" class="tab-view hidden space-y-6">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider pb-3 border-b border-gray-100 dark:border-gray-700/60">{{ __('messages.tab_settings') }}</h2>
                
                <form action="{{ route('profile.settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    {{-- Theme Preference --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.theme_label') }}</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="theme_preference" value="light" {{ $user->theme_preference === 'light' ? 'checked' : '' }} class="peer sr-only">
                                <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg text-center text-xs font-medium peer-checked:border-green-600 peer-checked:text-green-700 dark:peer-checked:text-green-400 peer-checked:bg-green-50/30 transition-all hover:bg-gray-50 dark:hover:bg-gray-800">
                                    ☀️ {{ __('messages.theme_light') }}
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="theme_preference" value="dark" {{ $user->theme_preference === 'dark' ? 'checked' : '' }} class="peer sr-only">
                                <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg text-center text-xs font-medium peer-checked:border-green-600 peer-checked:text-green-700 dark:peer-checked:text-green-400 peer-checked:bg-green-50/30 transition-all hover:bg-gray-50 dark:hover:bg-gray-800">
                                    🌙 {{ __('messages.theme_dark') }}
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="theme_preference" value="system" {{ $user->theme_preference === 'system' ? 'checked' : '' }} class="peer sr-only">
                                <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg text-center text-xs font-medium peer-checked:border-green-600 peer-checked:text-green-700 dark:peer-checked:text-green-400 peer-checked:bg-green-50/30 transition-all hover:bg-gray-50 dark:hover:bg-gray-800">
                                    💻 {{ __('messages.theme_system') }}
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Language Preference --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('messages.language_label') }}</label>
                        <select name="language_preference" class="input-field text-sm w-full">
                            <option value="en" {{ $user->language_preference === 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                            <option value="hi" {{ $user->language_preference === 'hi' ? 'selected' : '' }}>🇮🇳 हिन्दी (Hindi)</option>
                        </select>
                    </div>

                    {{-- Notification Preferences --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2.5">{{ __('messages.notification_title') }}</label>
                        <div class="space-y-3 bg-gray-50 dark:bg-gray-800/40 p-4 border border-gray-100 dark:border-gray-700/60 rounded-xl shadow-inner">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="notifications[email]" class="rounded border-gray-300 dark:border-gray-700 text-green-600 focus:ring-green-500"
                                    {{ !empty($user->notification_preferences['email']) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-700 dark:text-gray-300 font-medium">{{ __('messages.notify_email') }}</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="notifications[push]" class="rounded border-gray-300 dark:border-gray-700 text-green-600 focus:ring-green-500"
                                    {{ !empty($user->notification_preferences['push']) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-700 dark:text-gray-300 font-medium">{{ __('messages.notify_push') }}</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="notifications[sms]" class="rounded border-gray-300 dark:border-gray-700 text-green-600 focus:ring-green-500"
                                    {{ !empty($user->notification_preferences['sms']) ? 'checked' : '' }}>
                                <span class="text-xs text-gray-700 dark:text-gray-300 font-medium">{{ __('messages.notify_sms') }}</span>
                            </label>
                        </div>
                    </div>

                    {{-- Account & Location Permissions --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2.5">{{ __('messages.account_prefs') }}</label>
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-700/60 rounded-xl">
                            <div class="space-y-0.5">
                                <span class="block text-xs text-gray-700 dark:text-gray-300 font-bold uppercase tracking-wide">{{ __('messages.location_perm') }}</span>
                                <span class="block text-[10px] text-gray-400 dark:text-gray-500">{{ __('messages.location_perm_help') }}</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" name="location_permission_granted" value="1" class="sr-only peer"
                                    {{ $user->location_permission_granted ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700/60 flex justify-end">
                        <button type="submit" class="btn-primary text-xs py-2 px-5 font-semibold">
                            {{ __('messages.save_preferences') }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab switching logic
    function switchTab(tabId) {
        // Toggle view visibility
        document.querySelectorAll('.tab-view').forEach(view => {
            view.classList.add('hidden');
        });
        document.getElementById(`tab-view-${tabId}`).classList.remove('hidden');

        // Toggle button states
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.className = 'tab-btn w-full text-left px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2.5 transition-all text-gray-500 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700/30';
        });
        const activeBtn = document.getElementById(`tab-btn-${tabId}`);
        if (activeBtn) {
            activeBtn.className = 'tab-btn w-full text-left px-4 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-2.5 transition-all text-green-700 bg-green-50 dark:text-green-400 dark:bg-green-900/10';
        }

        // Push state or update URL search parameter
        const url = new URL(window.location);
        url.searchParams.set('tab', tabId);
        window.history.pushState({}, '', url);
    }

    // Avatar preview helper
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                const fallback = document.getElementById('avatar-initial');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (fallback) {
                    fallback.classList.add('hidden');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Auto-select tab based on URL param
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const activeTab = params.get('tab') || 'profile';
        if (['profile', 'security', 'settings'].includes(activeTab)) {
            switchTab(activeTab);
        }
        
        // Theme preference radio sync with dark mode
        const themeRadios = document.querySelectorAll('input[name="theme_preference"]');
        themeRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                const choice = e.target.value;
                localStorage.setItem('theme', choice);
                if (choice === 'dark' || (choice === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            });
        });
    });
</script>
@endpush

@extends('layouts.app')

@section('title', 'Contact Us - CropYield Portal')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        
        {{-- Hero Section --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                Contact <span class="text-emerald-600 dark:text-emerald-500">Us</span>
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Have questions? We're here to help. Reach out to us anytime.
            </p>
        </div>

        {{-- Success flash after ticket creation --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-md">
                <p class="font-medium text-emerald-700 dark:text-emerald-300 text-sm">{{ session('success') }}</p>
            </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
            
            {{-- Contact Info Cards --}}
            <div class="card text-center hover:shadow-sm transition-shadow">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 mb-4 mx-auto">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Email</h3>
                <a href="mailto:support@cropyield.com" class="text-emerald-600 dark:text-emerald-400 hover:underline break-all">
                    support@cropyield.com
                </a>
            </div>

            <div class="card text-center hover:shadow-sm transition-shadow">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mb-4 mx-auto">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Support Hours</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Monday - Friday<br>
                    9:00 AM - 6:00 PM IST
                </p>
            </div>

            <div class="card text-center hover:shadow-sm transition-shadow">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 mb-4 mx-auto">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Location</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    🇮🇳 India<br>
                    Headquarters in India
                </p>
            </div>
        </div>

        {{-- Contact Form --}}
        @auth
        <div class="card">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Send us a Message</h2>
            
            <form id="contact-form" action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Name Field --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', auth()->user()->name) }}"
                        placeholder="Your full name"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 @error('name') ring-2 ring-red-500 @enderror"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email Field --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', auth()->user()->email) }}"
                        placeholder="your@email.com"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 @error('email') ring-2 ring-red-500 @enderror"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Subject Field --}}
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="subject" 
                        name="subject" 
                        value="{{ old('subject') }}"
                        placeholder="How can we help?"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 @error('subject') ring-2 ring-red-500 @enderror"
                    >
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Message Field --}}
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="message" 
                        name="message" 
                        rows="6"
                        placeholder="Tell us more about your inquiry..."
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 resize-none @error('message') ring-2 ring-red-500 @enderror">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="flex items-center gap-3">
                    <button 
                        type="submit" 
                        id="submit-btn"
                        class="inline-flex items-center justify-center gap-2 bg-emerald-600 dark:bg-emerald-600 hover:bg-emerald-700 dark:hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span id="submit-text">Send Message</span>
                        <svg id="submit-spinner" class="hidden w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4M4.22 4.22l2.83 2.83m8.1 8.1l2.83 2.83M2 12h4m12 0h4M4.22 19.78l2.83-2.83m8.1-8.1l2.83-2.83"/>
                        </svg>
                    </button>
                    <p class="text-sm text-gray-600 dark:text-gray-400">We'll get back to you shortly.</p>
                </div>
            </form>
        </div>
        @else
        <div class="card text-center bg-blue-50 dark:bg-blue-900/20">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Sign In to Contact Us</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Please log in or create an account to submit a support ticket. This helps us respond to your inquiries more effectively.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
                    Sign In
                </a>
                <a href="{{ route('register') }}" class="inline-block border border-blue-600 dark:border-blue-400 text-blue-600 dark:text-blue-400 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                    Create Account
                </a>
            </div>
        </div>
        @endauth

        {{-- FAQ Section --}}
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Frequently Asked Questions</h2>
            
            <div class="space-y-4 max-w-3xl mx-auto">
                <details class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 group cursor-pointer">
                    <summary class="font-semibold text-gray-900 dark:text-white flex items-center justify-between">
                        How quickly will I get a response?
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </summary>
                    <p class="text-gray-600 dark:text-gray-400 mt-4">We typically respond to all inquiries within 24 business hours. For urgent issues, please mention it in your message.</p>
                </details>

                <details class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 group cursor-pointer">
                    <summary class="font-semibold text-gray-900 dark:text-white flex items-center justify-between">
                        Do you provide training or support?
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </summary>
                    <p class="text-gray-600 dark:text-gray-400 mt-4">Yes! We provide comprehensive documentation, video tutorials, and direct support to help you get the most out of CropYield. Contact us to learn about training programs.</p>
                </details>

                <details class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 group cursor-pointer">
                    <summary class="font-semibold text-gray-900 dark:text-white flex items-center justify-between">
                        Is there a free trial or demo available?
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </summary>
                    <p class="text-gray-600 dark:text-gray-400 mt-4">Yes! You can sign up for a free account and immediately start using all features including crop prediction and weather analytics. No credit card required.</p>
                </details>

                <details class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 group cursor-pointer">
                    <summary class="font-semibold text-gray-900 dark:text-white flex items-center justify-between">
                        How is my data kept secure?
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </summary>
                    <p class="text-gray-600 dark:text-gray-400 mt-4">We use industry-standard encryption (SSL/TLS), regular security audits, and comply with international data protection regulations including GDPR. Your privacy is our priority.</p>
                </details>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('contact-form')?.addEventListener('submit', async function(e) {
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        const submitSpinner = document.getElementById('submit-spinner');
        
        submitBtn.disabled = true;
        submitText.textContent = 'Sending...';
        submitSpinner.classList.remove('hidden');
    });
</script>
@endsection

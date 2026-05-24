@extends('layouts.app')

@section('title', 'About Us - CropYield Portal')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        
        {{-- Hero Section --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                About <span class="text-emerald-600 dark:text-emerald-500">CropYield</span>
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Empowering farmers with AI-driven crop prediction and smart farming solutions
            </p>
        </div>

        {{-- Mission Section --}}
        <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/50 rounded-md p-6 mb-12">
            <div class="flex items-start gap-4 mb-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-emerald-600 text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Our Mission</h2>
                    <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                        We're committed to revolutionizing agriculture through artificial intelligence and data-driven insights. 
                        By combining machine learning, real-time weather analytics, and smart farming techniques, we help farmers 
                        make informed decisions that maximize crop yield and sustainability.
                    </p>
                </div>
            </div>
        </div>

        {{-- Features Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            
            {{-- AI Crop Prediction --}}
            <div class="card hover:shadow-sm transition-shadow">
                <div class="mb-4">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">AI Crop Prediction</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Our advanced machine learning models analyze multiple factors including soil type, weather patterns, 
                    crop history, and seasonal data to predict optimal crop yields with high accuracy.
                </p>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-500">✓</span> Real-time yield forecasting
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-500">✓</span> Crop variety recommendations
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-500">✓</span> Historical prediction tracking
                    </li>
                </ul>
            </div>

            {{-- Weather Analytics --}}
            <div class="card hover:shadow-sm transition-shadow">
                <div class="mb-4">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-lg bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Weather Analytics</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Integrated real-time weather data from globally recognized sources provides farmers with accurate 
                    forecasts crucial for crop planning, irrigation scheduling, and pest management strategies.
                </p>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-500">✓</span> Real-time weather updates
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-500">✓</span> Rainfall & humidity tracking
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-500">✓</span> 7-day forecasts
                    </li>
                </ul>
            </div>

            {{-- Smart Farming --}}
            <div class="card hover:shadow-sm transition-shadow">
                <div class="mb-4">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Smart Farming Insights</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Evidence-based farming practices tailored to your specific region, soil conditions, and available resources 
                    to optimize productivity while promoting sustainable agriculture.
                </p>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-500">✓</span> Seasonal planting guides
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-500">✓</span> Irrigation recommendations
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-500">✓</span> Pest management tips
                    </li>
                </ul>
            </div>

            {{-- Data Security --}}
            <div class="card hover:shadow-sm transition-shadow">
                <div class="mb-4">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Data Security & Privacy</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Your farm data is protected with industry-standard encryption and security practices. We respect your privacy 
                    and never share personal information with third parties.
                </p>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-500">✓</span> End-to-end encryption
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-500">✓</span> GDPR compliant
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-emerald-500">✓</span> Regular backups
                    </li>
                </ul>
            </div>
        </div>

        {{-- Technologies Section --}}
        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-md border border-gray-200 dark:border-gray-700 p-6 mb-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Technologies & Tools</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Backend & Infrastructure</h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">•</span> Laravel 12 (PHP 8.2)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">•</span> MySQL/PostgreSQL Database
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">•</span> RESTful API Architecture
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">•</span> Machine Learning Integration
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Frontend & Data</h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">•</span> Tailwind CSS v4 & Alpine.js
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">•</span> Chart.js for Analytics
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">•</span> Google Maps & Geocoding
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">•</span> Open-Meteo Weather API
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Future Roadmap --}}
        <div class="card mb-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Future Roadmap</h2>
            
            <div class="space-y-4">
                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-semibold">1</div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Mobile App Launch</h3>
                        <p class="text-gray-600 dark:text-gray-400">Native iOS and Android applications for on-field crop monitoring and real-time notifications.</p>
                    </div>
                </div>

                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-semibold">2</div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">IoT Integration</h3>
                        <p class="text-gray-600 dark:text-gray-400">Support for soil sensors and weather stations for hyper-localized data collection and analysis.</p>
                    </div>
                </div>

                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-semibold">3</div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Marketplace Integration</h3>
                        <p class="text-gray-600 dark:text-gray-400">Direct connections with agricultural suppliers, buyers, and commodity markets.</p>
                    </div>
                </div>

                <div class="flex gap-4 items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-semibold">4</div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Advanced Analytics Dashboard</h3>
                        <p class="text-gray-600 dark:text-gray-400">Comprehensive data visualization with predictive modeling and long-term trend analysis.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CTA Section --}}
        <div class="bg-emerald-600 dark:bg-emerald-700 text-white rounded-md p-6 text-center">
            <h2 class="text-2xl font-bold mb-4">Ready to Transform Your Farming?</h2>
            <p class="text-emerald-100 mb-4 max-w-2xl mx-auto">
                Join thousands of farmers who are already using CropYield to make smarter decisions and increase their yields.
            </p>
            @guest
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="inline-block bg-white text-emerald-600 font-semibold px-6 py-3 rounded-lg hover:bg-emerald-50 transition-colors">
                        Get Started Free
                    </a>
                    <a href="{{ route('contact') }}" class="inline-block border border-white text-white font-semibold px-6 py-3 rounded-lg hover:bg-emerald-500 transition-colors">
                        Contact Us
                    </a>
                </div>
            @else
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('dashboard') }}" class="inline-block bg-white text-emerald-600 font-semibold px-6 py-3 rounded-lg hover:bg-emerald-50 transition-colors">
                        Go to Dashboard
                    </a>
                    <a href="{{ route('contact') }}" class="inline-block border border-white text-white font-semibold px-6 py-3 rounded-lg hover:bg-emerald-500 transition-colors">
                        Contact Us
                    </a>
                </div>
            @endguest
        </div>
    </div>
</div>
@endsection

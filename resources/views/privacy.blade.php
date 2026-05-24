@extends('layouts.app')

@section('title', 'Privacy Policy — ' . __('messages.app_name'))

@section('content')
<div class="min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="card">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-3">Privacy Policy</h1>
            <p class="text-gray-600 dark:text-gray-300 mb-4">At {{ __('messages.app_name') }}, we take privacy seriously. This page explains what data we collect, why we collect it, and how you can manage your information.</p>

            <h2 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Data We Collect</h2>
            <ul class="list-disc ml-5 text-gray-600 dark:text-gray-300">
                <li>Account details (name, email)</li>
                <li>Crop and prediction data submitted by you</li>
                <li>Location (when you permit us) to provide localized weather and suggestions</li>
            </ul>

            <h2 class="text-lg font-medium text-gray-900 dark:text-white mt-4">How We Use Your Data</h2>
            <p class="text-gray-600 dark:text-gray-300">We use data to provide personalized crop recommendations, weather analytics, and to improve model accuracy. We never sell personal data to third parties.</p>

            <h2 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Contact</h2>
            <p class="text-gray-600 dark:text-gray-300">If you have questions about privacy, email us at <a href="mailto:support@cropyield.com" class="text-emerald-400">support@cropyield.com</a>.</p>
        </div>
    </div>
</div>
@endsection

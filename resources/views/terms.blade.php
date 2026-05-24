@extends('layouts.app')

@section('title', 'Terms & Conditions — ' . __('messages.app_name'))

@section('content')
<div class="min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="card">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-3">Terms & Conditions</h1>
            <p class="text-gray-600 dark:text-gray-300 mb-4">These terms govern your use of {{ __('messages.app_name') }}. By using our service you agree to these terms.</p>

            <h2 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Using the Service</h2>
            <p class="text-gray-600 dark:text-gray-300">You may use the service in accordance with applicable laws. Do not misuse the platform.</p>

            <h2 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Intellectual Property</h2>
            <p class="text-gray-600 dark:text-gray-300">All content and models provided are © the provider. You may not reproduce without permission.</p>

            <h2 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Contact</h2>
            <p class="text-gray-600 dark:text-gray-300">For legal questions, contact <a href="mailto:support@cropyield.com" class="text-emerald-400">support@cropyield.com</a>.</p>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'FAQ — ' . __('messages.app_name'))

@section('content')
<div class="min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Frequently Asked Questions</h1>
            <p class="text-gray-600 dark:text-gray-300">Common questions about using the Crop Yield Portal.</p>
        </div>

        <div class="space-y-4">
            <details class="card p-4">
                <summary class="font-medium text-gray-900 dark:text-white">How accurate are predictions?</summary>
                <p class="text-gray-600 dark:text-gray-300 mt-2">Our models achieve high accuracy on historical datasets; results may vary by region and input quality.</p>
            </details>

            <details class="card p-4">
                <summary class="font-medium text-gray-900 dark:text-white">How do I change my location?</summary>
                <p class="text-gray-600 dark:text-gray-300 mt-2">You can update location in your profile settings. We use it to show localized weather and suggestions.</p>
            </details>

            <details class="card p-4">
                <summary class="font-medium text-gray-900 dark:text-white">Is my data shared?</summary>
                <p class="text-gray-600 dark:text-gray-300 mt-2">No. We do not sell personal data. Aggregated anonymized data helps improve models.</p>
            </details>
        </div>
    </div>
</div>
@endsection

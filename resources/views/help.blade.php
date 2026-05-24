@extends('layouts.app')

@section('title', 'Help Center — ' . __('messages.app_name'))

@section('content')
<div class="min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="card">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-3">Help Center</h1>
            <p class="text-gray-600 dark:text-gray-300 mb-4">Find guides and support resources to get the most from Crop Yield Portal.</p>

            <h2 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Getting Started</h2>
            <p class="text-gray-600 dark:text-gray-300">Create an account, set your location, and run your first prediction from the Predict tab.</p>

            <h2 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Support</h2>
            <p class="text-gray-600 dark:text-gray-300">If you can't find an answer, <a href="{{ route('contact') }}" class="text-emerald-400">contact support</a> and we'll assist you.</p>
        </div>
    </div>
</div>
@endsection

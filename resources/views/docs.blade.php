@extends('layouts.app')

@section('title', 'Documentation — ' . __('messages.app_name'))

@section('content')
<div class="min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="card">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-3">Documentation</h1>
            <p class="text-gray-600 dark:text-gray-300 mb-4">Developer and user documentation for Crop Yield Portal. This includes API endpoints, usage guides, and integration tips.</p>

            <h2 class="text-lg font-medium text-gray-900 dark:text-white mt-4">API</h2>
            <p class="text-gray-600 dark:text-gray-300">Use the authenticated endpoints under <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">/api/</code> — see <a href="{{ route('home') }}" class="text-emerald-400">home</a> for usage examples.</p>

            <h2 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Client Integration</h2>
            <p class="text-gray-600 dark:text-gray-300">You can integrate predictions into external dashboards using our API. Contact support for dedicated assistance.</p>
        </div>
    </div>
</div>
@endsection

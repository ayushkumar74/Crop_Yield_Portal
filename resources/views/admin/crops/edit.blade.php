@extends('layouts.app')
@section('title', 'Edit Crop — Admin')

@section('content')
<div class="max-w-2xl mx-auto px-6 py-12">
    <a href="{{ route('admin.crops') }}" class="text-sm text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 mb-4 block">← Back to Crops</a>
    <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-8">Edit Crop: {{ $crop->name }}</h1>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl text-sm text-red-600 dark:text-red-400">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('admin.crops.update', $crop) }}" method="POST" class="stat-card space-y-5">
        @csrf @method('PUT')
        @include('admin.crops._form', ['crop' => $crop])
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">Update Crop</button>
            <a href="{{ route('admin.crops') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

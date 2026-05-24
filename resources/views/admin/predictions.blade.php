@extends('layouts.app')
@section('title', 'All Predictions — Admin')

@section('content')
<div class="page-wrapper">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mb-1 block">← Admin Dashboard</a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">All Predictions</h1>
        </div>
    </div>

    <div class="stat-card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="pl-5">ID</th>
                        <th>User</th>
                        <th>Crop</th>
                        <th class="hidden sm:table-cell">Conditions</th>
                        <th>Yield</th>
                        <th class="hidden md:table-cell">Score</th>
                        <th>Risk</th>
                        <th class="hidden lg:table-cell">Date</th>
                        <th class="pr-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($predictions as $pred)
                    <tr>
                        <td class="pl-5 text-xs text-gray-400">{{ $pred->id }}</td>
                        <td class="font-medium text-gray-900 dark:text-white">{{ $pred->user->name ?? 'Guest' }}</td>
                        <td class="text-gray-700 dark:text-gray-300">{{ $pred->crop->name ?? '—' }}</td>
                        <td class="hidden sm:table-cell text-xs text-gray-500 dark:text-gray-400">
                            {{ $pred->temperature }}°C · {{ $pred->rainfall }}mm · {{ $pred->humidity }}%
                        </td>
                        <td class="font-semibold text-green-600 dark:text-green-400">{{ $pred->predicted_yield }} t/ha</td>
                        <td class="hidden md:table-cell text-gray-500 dark:text-gray-400">{{ $pred->suitability_score }}%</td>
                        <td>
                            <span class="badge {{ $pred->risk_level === 'Low' ? 'badge-low' : ($pred->risk_level === 'Medium' ? 'badge-medium' : 'badge-high') }}">
                                {{ $pred->risk_level }}
                            </span>
                        </td>
                        <td class="hidden lg:table-cell text-xs text-gray-400">{{ $pred->created_at->format('d M Y') }}</td>
                        <td class="pr-5 text-right">
                            <div class="inline-flex items-center gap-3">
                                <a href="{{ route('predictions.show', $pred) }}" class="text-xs text-green-600 dark:text-green-400 hover:underline font-medium">View</a>
                                <button type="button" 
                                        onclick="openDeleteModal('{{ route('admin.predictions.destroy', $pred) }}', '{{ $pred->id }}', '{{ $pred->crop->name ?? 'Crop' }}', '{{ $pred->user->name ?? 'Guest' }}')" 
                                        class="text-xs text-red-600 dark:text-red-400 hover:underline font-medium bg-transparent border-none cursor-pointer p-0">
                                    {{ __('messages.delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-gray-400 dark:text-gray-500">
                            {{ __('messages.no_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($predictions->hasPages())
        <div class="px-5 py-3 border-t border-stone-100 dark:border-stone-850">{{ $predictions->links() }}</div>
        @endif
    </div>
</div>

{{-- Custom Deletion Confirmation Modal --}}
<div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-xs hidden transition-opacity duration-300 opacity-0">
    <div id="delete-modal-card" class="bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 rounded-xl max-w-md w-full p-6 shadow-2xl transition-all duration-300 scale-95 opacity-0">
        {{-- Alert Header Icon --}}
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-950/30 flex items-center justify-center text-red-600 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white font-display">{{ __('messages.delete') }}</h3>
        </div>
        
        {{-- Modal Description --}}
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
            {{ __('messages.delete_confirm') }} <span id="modal-prediction-details" class="font-semibold text-gray-800 dark:text-gray-200"></span>. <br>
            <span class="text-xs text-red-500 font-medium">This action cannot be undone.</span>
        </p>
        
        {{-- Action Buttons --}}
        <div class="flex items-center justify-end gap-3">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 bg-stone-105 dark:bg-stone-850 hover:bg-stone-200 dark:hover:bg-stone-800 rounded-lg transition-colors border border-stone-200 dark:border-stone-850">
                {{ __('messages.cancel') }}
            </button>
            <form id="delete-form" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-xs font-medium text-white bg-red-600 hover:bg-red-700 active:bg-red-800 rounded-lg transition-colors shadow-sm shadow-red-600/10">
                    {{ __('messages.delete') }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(actionUrl, predId, cropName, userName) {
        const modal = document.getElementById('delete-modal');
        const card = document.getElementById('delete-modal-card');
        const form = document.getElementById('delete-form');
        const detailsSpan = document.getElementById('modal-prediction-details');

        // Set the correct prediction info and action path
        form.action = actionUrl;
        detailsSpan.textContent = `(ID: ${predId} — ${cropName} by ${userName})`;

        // Unhide and trigger CSS transition
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            card.classList.remove('scale-95', 'opacity-0');
        }, 10);
    }

    function closeDeleteModal() {
        const modal = document.getElementById('delete-modal');
        const card = document.getElementById('delete-modal-card');

        // Trigger transition close
        modal.classList.add('opacity-0');
        card.classList.add('scale-95', 'opacity-0');

        // Hide after transitions complete (300ms)
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Close modal when clicking on background overlay
    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endsection

@extends('layouts.app')

@section('title', 'Ticket: ' . $ticket->ticket_number . ' - Admin Panel')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.tickets') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline text-sm mb-4 inline-block">
                ← Back to Tickets
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $ticket->ticket_number }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ $ticket->subject }}</p>
                </div>
                <span class="px-4 py-2 rounded-lg text-sm font-semibold {{ $ticket->getStatusBadgeClass() }}">
                    {{ $ticket->getStatusLabel() }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Ticket Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Ticket Information</h2>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Status</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $ticket->getStatusLabel() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Priority</p>
                            <p class="font-semibold text-gray-900 dark:text-white">Normal</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Created</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $ticket->created_at->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Assigned To</p>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                @if($ticket->assignedAdmin)
                                    {{ $ticket->assignedAdmin->name }}
                                @else
                                    Unassigned
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- User Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">User Information</h2>
                    
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden flex items-center justify-center bg-emerald-600 text-white font-bold text-lg flex-shrink-0">
                            @if($ticket->user->avatar)
                                <img src="{{ $ticket->user->avatarUrl() }}" class="w-full h-full object-cover" alt="User Avatar">
                            @else
                                {{ strtoupper(substr($ticket->user->name, 0, 1)) }}
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $ticket->user->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $ticket->user->email }}</p>
                            <a href="{{ route('admin.users.show', $ticket->user) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline text-sm mt-2 inline-block">
                                View User Profile →
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Ticket Message --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Message</h2>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-gray-700 dark:text-gray-300 whitespace-pre-wrap break-words">
                        {{ $ticket->message }}
                    </div>
                </div>

                {{-- Admin Response Section --}}
                @if($ticket->isResolved())
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-900/50 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">✓ Resolution Response</h2>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-gray-700 dark:text-gray-300 whitespace-pre-wrap break-words border border-emerald-200 dark:border-emerald-900/50">
                            {{ $ticket->admin_response }}
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">
                            Resolved on {{ $ticket->resolved_at->format('M d, Y \a\t h:i A') }}
                        </p>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Resolve Ticket</h2>
                        
                        <form action="{{ route('admin.tickets.resolve', $ticket) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            
                            <div>
                                <label for="admin_response" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Response Message <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    id="admin_response" 
                                    name="admin_response" 
                                    rows="6"
                                    required
                                    placeholder="Provide a detailed response to resolve the user's issue..."
                                    class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                >{{ old('admin_response') }}</textarea>
                                @error('admin_response')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <button 
                                type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 dark:hover:bg-emerald-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors"
                            >
                                <span>✓ Mark as Resolved & Notify User</span>
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Status Update Form --}}
                @if(!$ticket->isResolved())
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Update Status</h2>
                        
                        <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Ticket Status
                                </label>
                                <select 
                                    id="status" 
                                    name="status"
                                    class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:focus:ring-emerald-400 text-gray-900 dark:text-white"
                                >
                                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>

                            <button 
                                type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 dark:hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors"
                            >
                                Update Status
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                <div class="sticky top-20 space-y-4">
                    {{-- Quick Info Card --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Quick Info</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Ticket Status</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $ticket->getStatusLabel() }}</p>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Open for</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $ticket->created_at->diffForHumans() }}</p>
                            </div>
                            @if($ticket->resolved_at)
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                    <p class="text-gray-600 dark:text-gray-400 mb-1">Resolved</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $ticket->resolved_at->diffForHumans() }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Action Links --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                        <a href="{{ route('admin.users.show', $ticket->user) }}" class="block w-full text-center py-2 px-4 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm font-semibold">
                            View User
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

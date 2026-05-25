@extends('layouts.app')

@section('title', 'Support Tickets - Admin Panel')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Support Tickets</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage customer support tickets and respond to inquiries</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Tickets</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                    <div class="text-3xl opacity-50">🎫</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Open</p>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $stats['open'] }}</p>
                    </div>
                    <div class="text-3xl opacity-50">🔴</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">In Progress</p>
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['in_progress'] }}</p>
                    </div>
                    <div class="text-3xl opacity-50">🟡</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Resolved</p>
                        <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['resolved'] }}</p>
                    </div>
                    <div class="text-3xl opacity-50">✅</div>
                </div>
            </div>
        </div>

        {{-- Tickets Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if($tickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Ticket #</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Subject</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">User</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Created</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 dark:text-gray-300">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($tickets as $ticket)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $ticket->ticket_number }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ Str::limit($ticket->subject, 50) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <a href="{{ route('admin.users.show', $ticket->user) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">
                                            {{ $ticket->user->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $ticket->getStatusBadgeClass() }}">
                                            {{ $ticket->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $ticket->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm">
                                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-semibold">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                    {{ $tickets->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="text-5xl mb-4">📭</div>
                    <p class="text-gray-600 dark:text-gray-400 text-lg">No support tickets yet</p>
                    <p class="text-gray-500 dark:text-gray-500 text-sm mt-2">Customer support tickets will appear here</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

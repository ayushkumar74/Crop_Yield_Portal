@extends('layouts.app')
@section('title', 'Admin Dashboard — ' . __('messages.app_name'))

@section('content')
<div class="page-wrapper">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <span class="badge bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800 mb-2">Admin Panel</span>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.crops.create') }}" class="btn-primary py-2 px-4">+ Add Crop</a>
            <a href="{{ route('admin.users') }}" class="btn-secondary py-2 px-4">Users</a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        @foreach([
            ['label' => 'Total Users',       'value' => $stats['total_users'],         'color' => 'blue'],
            ['label' => 'Total Predictions', 'value' => $stats['total_predictions'],   'color' => 'green'],
            ['label' => 'Crop Types',         'value' => $stats['total_crops'],         'color' => 'amber'],
            ['label' => 'Weather Logs',       'value' => $stats['total_weather_logs'],  'color' => 'cyan'],
        ] as $stat)
        <div class="stat-card">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid lg:grid-cols-2 gap-5 mb-5">
        <div class="stat-card">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">Predictions by Crop</h3>
            <div class="chart-container" style="height:220px"><canvas id="cropBarChart"></canvas></div>
        </div>
        <div class="stat-card">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-4">Risk Level Distribution</h3>
            <div class="chart-container" style="height:220px"><canvas id="riskChart"></canvas></div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="grid sm:grid-cols-3 gap-4 mb-5">
        <a href="{{ route('admin.crops') }}" class="stat-card hover:border-green-300 dark:hover:border-green-700 transition-colors group">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <p class="font-semibold text-gray-900 dark:text-white text-sm">Manage Crops</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">CRUD operations</p>
        </a>
        <a href="{{ route('admin.users') }}" class="stat-card hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <p class="font-semibold text-gray-900 dark:text-white text-sm">Manage Users</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">View all accounts</p>
        </a>
        <a href="{{ route('admin.predictions') }}" class="stat-card hover:border-purple-300 dark:hover:border-purple-700 transition-colors">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <p class="font-semibold text-gray-900 dark:text-white text-sm">All Predictions</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Browse & manage</p>
        </a>
<<<<<<< HEAD
        <a href="{{ route('admin.tickets') }}" class="stat-card hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            <p class="font-semibold text-gray-900 dark:text-white text-sm">Support Tickets</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Manage customer issues</p>
        </a>
=======
    </div>
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755

    {{-- Recent Predictions --}}
    <div class="stat-card overflow-hidden p-0">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Recent Predictions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        @foreach(['User', 'Crop', 'Yield', 'Risk', 'Score', 'Date', ''] as $h)
                        <th>{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPredictions as $pred)
                    <tr>
                        <td class="font-medium text-gray-900 dark:text-white">{{ $pred->user->name ?? 'Guest' }}</td>
                        <td class="text-gray-600 dark:text-gray-400">{{ $pred->crop->name ?? '—' }}</td>
                        <td class="font-semibold text-green-600 dark:text-green-400">{{ $pred->predicted_yield }} t/ha</td>
                        <td>
                            <span class="badge {{ $pred->risk_level === 'Low' ? 'badge-low' : ($pred->risk_level === 'Medium' ? 'badge-medium' : 'badge-high') }}">
                                {{ $pred->risk_level }}
                            </span>
                        </td>
                        <td class="text-gray-500 dark:text-gray-400">{{ $pred->suitability_score }}%</td>
                        <td class="text-xs text-gray-400">{{ $pred->created_at->format('d M Y') }}</td>
                        <td><a href="{{ route('predictions.show', $pred) }}" class="text-xs text-green-600 dark:text-green-400 hover:underline">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const isDark = document.documentElement.classList.contains('dark');
const lc = isDark ? '#9ca3af' : '#6b7280';
const gc = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
const baseOpts = { responsive:true, maintainAspectRatio:false,
    scales:{ x:{grid:{color:gc},ticks:{color:lc,font:{size:10}}}, y:{grid:{color:gc},ticks:{color:lc}} },
    plugins:{ legend:{ display:false } } };

new Chart(document.getElementById('cropBarChart'), {
    type:'bar',
    data:{ labels:@json($cropPredictions->keys()), datasets:[{
        label:'Predictions', data:@json($cropPredictions->values()),
        backgroundColor:'rgba(22,163,74,0.7)', borderColor:'#16a34a', borderWidth:1, borderRadius:4
    }] },
    options:baseOpts
});
new Chart(document.getElementById('riskChart'), {
    type:'doughnut',
    data:{ labels:['Low','Medium','High'], datasets:[{
        data:[@json($riskData['Low']),@json($riskData['Medium']),@json($riskData['High'])],
        backgroundColor:['#16a34a','#d97706','#dc2626'],
        borderWidth:2, borderColor: isDark ? '#1f2937' : '#fff'
    }] },
    options:{ responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{ position:'bottom', labels:{ color:lc, font:{size:10} } } } }
});
</script>
@endpush

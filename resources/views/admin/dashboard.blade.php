@extends('admin.layouts.app')

@section('title', 'Dashboard - KoboPoint Admin')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 200px;
    }
    .mini-chart {
        height: 60px;
        display: flex;
        align-items: flex-end;
        gap: 4px;
    }
    .chart-bar {
        flex: 1;
        background: linear-gradient(to top, #10B981, #34D399);
        border-radius: 4px 4px 0 0;
        min-height: 4px;
    }
    .gradient-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .gradient-card-green {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    }
    .gradient-card-blue {
        background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
    }
    .gradient-card-orange {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
    }
</style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="text-sm text-gray-600 mb-2">
            <span>Dashboard</span> <span class="mx-2">/</span> <span class="text-gray-900 font-medium">Main Dashboard</span>
        </nav>
        <h1 class="text-3xl font-bold text-gray-900">Main Dashboard</h1>
        <p class="mt-1 text-sm text-gray-600">Welcome back, {{ Auth::user()->name }}! Here's what's happening today.</p>
    </div>

    <!-- Top Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Overall Revenue -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Overall Revenue</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl p-4">
                    <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                    <i class="fas fa-arrow-up mr-1"></i>+{{ $stats['transaction_growth_rate'] }}%
                </span>
                <span class="text-xs text-gray-500">vs last month</span>
            </div>
            <!-- Mini Chart -->
            <div class="mt-4 mini-chart">
                @foreach($stats['last_7_days'] as $day)
                    <div class="chart-bar" style="height: {{ $day['volume'] > 0 ? (($day['volume'] / max(array_column($stats['last_7_days'], 'volume'))) * 100) : 4 }}%"></div>
                @endforeach
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                </div>
                <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl p-4">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                    <i class="fas fa-arrow-up mr-1"></i>+{{ $stats['user_growth_rate'] }}%
                </span>
                <span class="text-xs text-gray-500">{{ $stats['new_users_month'] }} new this month</span>
            </div>
            <!-- Mini Chart -->
            <div class="mt-4 mini-chart">
                @foreach($stats['last_6_months'] as $month)
                    <div class="chart-bar" style="height: {{ $month['users'] > 0 ? (($month['users'] / max(array_column($stats['last_6_months'], 'users'))) * 100) : 4 }}%; background: linear-gradient(to top, #3B82F6, #60A5FA);"></div>
                @endforeach
            </div>
        </div>

        <!-- Transaction Volume -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Transaction Volume</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($stats['total_volume'] / 1000000, 2) }}M</p>
                </div>
                <div class="bg-gradient-to-br from-green-100 to-green-200 rounded-xl p-4">
                    <i class="fas fa-exchange-alt text-green-600 text-2xl"></i>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                    <i class="fas fa-check-circle mr-1"></i>{{ number_format($stats['today_transactions']) }} today
                </span>
                <span class="text-xs text-gray-500">₦{{ number_format($stats['today_volume'], 2) }}</span>
            </div>
            <!-- Mini Chart -->
            <div class="mt-4 mini-chart">
                @foreach($stats['last_7_days'] as $day)
                    <div class="chart-bar" style="height: {{ $day['volume'] > 0 ? (($day['volume'] / max(array_column($stats['last_7_days'], 'volume'))) * 100) : 4 }}%; background: linear-gradient(to top, #10B981, #34D399);"></div>
                @endforeach
            </div>
        </div>

        <!-- Active Wallets -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Wallet Balance</p>
                    <p class="text-3xl font-bold text-gray-900">₦{{ number_format($stats['total_wallet_balance'] / 1000000, 2) }}M</p>
                </div>
                <div class="bg-gradient-to-br from-orange-100 to-orange-200 rounded-xl p-4">
                    <i class="fas fa-wallet text-orange-600 text-2xl"></i>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded">
                    <i class="fas fa-wallet mr-1"></i>{{ number_format($stats['active_wallets']) }} active
                </span>
                <span class="text-xs text-gray-500">{{ number_format($stats['total_wallets']) }} total</span>
            </div>
            <!-- Mini Chart -->
            <div class="mt-4 mini-chart">
                @foreach($stats['last_7_days'] as $day)
                    <div class="chart-bar" style="height: {{ $day['volume'] > 0 ? (($day['volume'] / max(array_column($stats['last_7_days'], 'volume'))) * 100) : 4 }}%; background: linear-gradient(to top, #F59E0B, #FBBF24);"></div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Second Row - Additional Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Active Users -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-sm border border-green-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Active Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_users']) }}</p>
                    <p class="text-xs text-green-600 mt-2">
                        <i class="fas fa-check-circle mr-1"></i>KYC Approved
                    </p>
                </div>
                <div class="bg-green-100 rounded-xl p-4">
                    <i class="fas fa-user-check text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending KYC -->
        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl shadow-sm border border-yellow-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending KYC</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_kyc']) }}</p>
                    <p class="text-xs text-yellow-600 mt-2">
                        <i class="fas fa-clock mr-1"></i>Awaiting review
                    </p>
                </div>
                <div class="bg-yellow-100 rounded-xl p-4">
                    <i class="fas fa-file-alt text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Merchants -->
        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl shadow-sm border border-purple-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Active Merchants</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_merchants']) }}</p>
                    <p class="text-xs text-purple-600 mt-2">
                        <i class="fas fa-store mr-1"></i>{{ number_format($stats['total_merchants']) }} total
                    </p>
                </div>
                <div class="bg-purple-100 rounded-xl p-4">
                    <i class="fas fa-store text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Thrift Groups -->
        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl shadow-sm border border-indigo-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Thrift Groups</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_thrift_groups']) }}</p>
                    <p class="text-xs text-indigo-600 mt-2">
                        <i class="fas fa-users-cog mr-1"></i>Active groups
                    </p>
                </div>
                <div class="bg-indigo-100 rounded-xl p-4">
                    <i class="fas fa-piggy-bank text-indigo-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Detailed Stats Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Transaction Volume Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Transaction Volume</h3>
                    <p class="text-sm text-gray-500">Last 7 days performance</p>
                </div>
                <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option>Last 7 days</option>
                    <option>Last 30 days</option>
                    <option>Last 3 months</option>
                </select>
            </div>
            <div class="chart-container">
                <canvas id="volumeChart"></canvas>
            </div>
        </div>

        <!-- Transaction Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Transaction Status</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-700">Settled</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['settled_transactions']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-700">Pending</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['pending_transactions']) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                        <span class="text-sm text-gray-700">Failed</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['failed_transactions']) }}</span>
                </div>
            </div>
            <!-- Donut Chart Placeholder -->
            <div class="mt-6 flex items-center justify-center">
                <div class="relative w-32 h-32">
                    <svg class="transform -rotate-90 w-32 h-32">
                        <circle cx="64" cy="64" r="56" stroke="#E5E7EB" stroke-width="12" fill="none"></circle>
                        @php
                            $total = $stats['settled_transactions'] + $stats['pending_transactions'] + $stats['failed_transactions'];
                            $settledPercent = $total > 0 ? ($stats['settled_transactions'] / $total) * 100 : 0;
                            $pendingPercent = $total > 0 ? ($stats['pending_transactions'] / $total) * 100 : 0;
                            $failedPercent = $total > 0 ? ($stats['failed_transactions'] / $total) * 100 : 0;
                            $circumference = 2 * M_PI * 56;
                            $settledOffset = $circumference - ($settledPercent / 100) * $circumference;
                            $pendingOffset = $circumference - (($settledPercent + $pendingPercent) / 100) * $circumference;
                        @endphp
                        <circle cx="64" cy="64" r="56" stroke="#10B981" stroke-width="12" fill="none" 
                                stroke-dasharray="{{ $circumference }}" 
                                stroke-dashoffset="{{ $settledOffset }}"></circle>
                        <circle cx="64" cy="64" r="56" stroke="#F59E0B" stroke-width="12" fill="none" 
                                stroke-dasharray="{{ $circumference }}" 
                                stroke-dashoffset="{{ $pendingOffset }}"
                                style="stroke-dashoffset: {{ $pendingOffset }};"></circle>
                        <circle cx="64" cy="64" r="56" stroke="#EF4444" stroke-width="12" fill="none" 
                                stroke-dasharray="{{ $circumference }}" 
                                stroke-dashoffset="0"
                                style="stroke-dashoffset: {{ $circumference - (($settledPercent + $pendingPercent + $failedPercent) / 100) * $circumference }};"></circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($total) }}</p>
                            <p class="text-xs text-gray-500">Total</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row - Tables and Lists -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-exchange-alt mr-2 text-green-600"></i>Recent Transactions
                    </h3>
                    <a href="#" class="text-sm text-green-600 hover:text-green-700 font-medium">
                        View all <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($stats['recent_transactions']->take(6) as $transaction)
                <div class="px-6 py-4 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-arrow-right text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $transaction->walletFrom->user->name ?? 'System' }} → {{ $transaction->walletTo->user->name ?? 'System' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $transaction->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">₦{{ number_format($transaction->amount, 2) }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                {{ $transaction->status === 'settled' ? 'bg-green-100 text-green-800' : 
                                   ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-sm text-gray-500">
                    <i class="fas fa-inbox text-3xl text-gray-300 mb-2 block"></i>
                    No transactions found
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-user-friends mr-2 text-blue-600"></i>Recent Users
                    </h3>
                    <a href="#" class="text-sm text-green-600 hover:text-green-700 font-medium">
                        View all <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($stats['recent_users'] as $user)
                <div class="px-6 py-4 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs px-2 py-1 rounded-full 
                                {{ $user->kyc_status === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($user->kyc_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($user->kyc_status ?? 'pending') }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-sm text-gray-500">
                    <i class="fas fa-inbox text-3xl text-gray-300 mb-2 block"></i>
                    No users found
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Savings Accounts -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-piggy-bank mr-2 text-indigo-600"></i>Savings
                </h3>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($stats['total_savings_accounts']) }}</p>
            <p class="text-sm text-gray-500 mb-4">Total Accounts</p>
            <div class="pt-4 border-t border-gray-200">
                <p class="text-lg font-semibold text-gray-900">₦{{ number_format($stats['total_savings_balance'], 2) }}</p>
                <p class="text-xs text-gray-500">Total Balance</p>
            </div>
        </div>

        <!-- Active Agents -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-tie mr-2 text-purple-600"></i>Agents
                </h3>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($stats['active_agents']) }}</p>
            <p class="text-sm text-gray-500 mb-4">Active Agents</p>
            <div class="pt-4 border-t border-gray-200">
                <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_agents']) }}</p>
                <p class="text-xs text-gray-500">Total Agents</p>
            </div>
        </div>

        <!-- Today's Summary -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-sm border border-green-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-day mr-2 text-green-600"></i>Today's Summary
                </h3>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Transactions</span>
                    <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['today_transactions']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Volume</span>
                    <span class="text-sm font-semibold text-gray-900">₦{{ number_format($stats['today_volume'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">New Users</span>
                    <span class="text-sm font-semibold text-gray-900">{{ number_format($stats['new_users_today']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Revenue</span>
                    <span class="text-sm font-semibold text-green-600">₦{{ number_format($stats['today_revenue'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Transaction Volume Chart
    const volumeCtx = document.getElementById('volumeChart');
    if (volumeCtx) {
        const volumeChart = new Chart(volumeCtx, {
            type: 'line',
            data: {
                labels: @json(array_column($stats['last_7_days'], 'day')),
                datasets: [{
                    label: 'Transaction Volume',
                    data: @json(array_column($stats['last_7_days'], 'volume')),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#10B981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '₦' + (value / 1000000).toFixed(1) + 'M';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
</script>
@endpush

@extends('admin.layouts.app')

@section('title', 'Virtual Account Details - KoboPoint Admin')

@push('styles')
<style>
    .info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .detail-row {
        padding: 0.75rem 0;
        border-bottom: 1px solid #E5E7EB;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-green-600">Dashboard</a> 
            <span class="mx-2">/</span> 
            <a href="{{ route('admin.virtual-accounts') }}" class="hover:text-green-600">Virtual Accounts</a>
            <span class="mx-2">/</span> 
            <span class="text-gray-900 font-medium">Account Details</span>
        </nav>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Virtual Account Details</h1>
                <p class="mt-1 text-sm text-gray-600">View complete account information and transactions</p>
            </div>
            <a href="{{ route('admin.virtual-accounts') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Account Overview Card -->
    <div class="info-card rounded-2xl shadow-lg p-8 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">{{ $accountData['accountName'] ?? 'N/A' }}</h2>
                <p class="text-lg opacity-90">{{ $accountData['accountNumber'] ?? 'N/A' }}</p>
                <p class="text-sm opacity-75 mt-1">Bell Microfinance Bank</p>
            </div>
            <div class="text-right">
                @php
                    $status = strtolower($accountData['status'] ?? 'active');
                @endphp
                <span class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-full text-sm font-semibold">
                    <i class="fas fa-circle text-xs mr-2"></i>
                    {{ ucfirst($status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Account Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-info-circle mr-2 text-green-600"></i>Account Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="detail-row">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-500">Account Number</span>
                                <span class="text-sm font-semibold text-gray-900 text-right">{{ $accountData['accountNumber'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-500">Account Type</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($accountData['accountType'] ?? 'individual') == 'corporate' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        <i class="fas {{ ($accountData['accountType'] ?? 'individual') == 'corporate' ? 'fa-building' : 'fa-user' }} mr-1"></i>
                                        {{ ucfirst($accountData['accountType'] ?? 'individual') }}
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-500">Account Name</span>
                                <span class="text-sm font-semibold text-gray-900 text-right">{{ $accountData['accountName'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                        @if(isset($accountData['firstname']) || isset($accountData['lastname']))
                        <div class="detail-row">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-500">Full Name</span>
                                <span class="text-sm font-semibold text-gray-900 text-right">
                                    {{ ($accountData['firstname'] ?? '') . ' ' . ($accountData['middlename'] ?? '') . ' ' . ($accountData['lastname'] ?? '') }}
                                </span>
                            </div>
                        </div>
                        @endif
                        @if(isset($accountData['mobileNumber']) || isset($accountData['phoneNumber']))
                        <div class="detail-row">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-500">Phone Number</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $accountData['mobileNumber'] ?? $accountData['phoneNumber'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                        @endif
                        @if(isset($accountData['emailAddress']))
                        <div class="detail-row">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-500">Email Address</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $accountData['emailAddress'] }}</span>
                            </div>
                        </div>
                        @endif
                        @if(isset($accountData['bvn']))
                        <div class="detail-row">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-500">BVN</span>
                                <span class="text-sm font-semibold text-gray-900">{{ substr($accountData['bvn'], 0, 3) . '****' . substr($accountData['bvn'], -4) }}</span>
                            </div>
                        </div>
                        @endif
                        @if(isset($accountData['validityType']))
                        <div class="detail-row">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-500">Validity Type</span>
                                <span class="text-sm font-semibold text-gray-900">{{ ucfirst($accountData['validityType']) }}</span>
                            </div>
                        </div>
                        @endif
                        @if(isset($accountData['createdAt']))
                        <div class="detail-row">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-500">Created At</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::createFromTimestampMs($accountData['createdAt'])->format('M d, Y h:i A') }}
                                </span>
                            </div>
                        </div>
                        @endif
                        @if(isset($accountData['updatedAt']))
                        <div class="detail-row">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-500">Last Updated</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::createFromTimestampMs($accountData['updatedAt'])->format('M d, Y h:i A') }}
                                </span>
                            </div>
                        </div>
                        @endif
                        @if(isset($accountData['id']) || isset($accountData['externalReference']))
                        <div class="detail-row">
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-500">Account ID</span>
                                <span class="text-sm font-mono text-gray-900 break-all">{{ $accountData['id'] ?? $accountData['externalReference'] }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer & KYC Info -->
        <div class="space-y-6">
            @if($localAccount && $localAccount->user)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-user mr-2 text-green-600"></i>KoboPoint Customer
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-500">Customer Name</span>
                            <p class="text-sm font-semibold text-gray-900">{{ $localAccount->user->name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Email</span>
                            <p class="text-sm font-semibold text-gray-900">{{ $localAccount->user->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Phone</span>
                            <p class="text-sm font-semibold text-gray-900">{{ $localAccount->user->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">KYC Status</span>
                            <p class="text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $localAccount->user->kyc_status == 'approved' ? 'bg-green-100 text-green-800' : 
                                       ($localAccount->user->kyc_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($localAccount->user->kyc_status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">BVN Source</span>
                            <p class="text-sm">
                                @if($localAccount->created_by_director)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        <i class="fas fa-user-shield mr-1"></i>Director BVN
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-user-check mr-1"></i>User BVN
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-bolt mr-2 text-green-600"></i>Quick Actions
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    @if(isset($accountData['id']) || isset($accountData['externalReference']))
                    <button onclick="viewTransactions()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-left">
                        <i class="fas fa-exchange-alt mr-2"></i>View Transactions
                    </button>
                    @endif
                    @if($localAccount && $localAccount->user)
                    <a href="#" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-left">
                        <i class="fas fa-user mr-2"></i>View Customer Profile
                    </a>
                    @endif
                    <button onclick="copyAccountNumber()" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-left">
                        <i class="fas fa-copy mr-2"></i>Copy Account Number
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Section -->
    @if(!empty($transactions))
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-exchange-alt mr-2 text-green-600"></i>Recent Transactions
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach(array_slice($transactions, 0, 10) as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ isset($transaction['createdAt']) ? \Carbon\Carbon::createFromTimestampMs($transaction['createdAt'])->format('M d, Y h:i A') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ ucfirst($transaction['type'] ?? 'N/A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            ₦{{ number_format($transaction['amount'] ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ strtolower($transaction['status'] ?? '') == 'successful' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($transaction['status'] ?? 'Pending') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                            {{ $transaction['reference'] ?? $transaction['transactionReference'] ?? 'N/A' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
    function copyAccountNumber() {
        const accountNumber = '{{ $accountData['accountNumber'] ?? '' }}';
        navigator.clipboard.writeText(accountNumber).then(() => {
            alert('Account number copied to clipboard!');
        });
    }

    function viewTransactions() {
        // Scroll to transactions section if exists, or implement modal/separate page
        const transactionsSection = document.querySelector('[data-transactions]');
        if (transactionsSection) {
            transactionsSection.scrollIntoView({ behavior: 'smooth' });
        }
    }
</script>
@endpush


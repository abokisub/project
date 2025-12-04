@extends('admin.layouts.app')

@section('title', 'Virtual Accounts - KoboPoint Admin')

@push('styles')
<style>
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-active {
        background-color: #D1FAE5;
        color: #065F46;
    }
    .status-inactive {
        background-color: #FEE2E2;
        color: #991B1B;
    }
    .status-pending {
        background-color: #FEF3C7;
        color: #92400E;
    }
    .account-type-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .type-individual {
        background-color: #DBEAFE;
        color: #1E40AF;
    }
    .type-corporate {
        background-color: #E9D5FF;
        color: #6B21A8;
    }
</style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="text-sm text-gray-600 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-green-600">Dashboard</a> 
            <span class="mx-2">/</span> 
            <a href="{{ route('admin.virtual-accounts') }}" class="hover:text-green-600">Wallet & Accounts</a>
            <span class="mx-2">/</span> 
            <span class="text-gray-900 font-medium">Virtual Accounts</span>
        </nav>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Virtual Accounts</h1>
                <p class="mt-1 text-sm text-gray-600">Manage all customer virtual accounts from BellBank</p>
            </div>
            <div class="flex gap-3">
                <button onclick="openCreateModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center">
                    <i class="fas fa-plus mr-2"></i>Create Account
                </button>
                <button onclick="refreshAccounts()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded">
            <div class="flex">
                <i class="fas fa-check-circle mr-2 mt-0.5"></i>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
            <div class="flex">
                <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if(isset($error))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded">
            <div class="flex">
                <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
                <div>
                    <p class="font-semibold">Error loading virtual accounts</p>
                    <p class="text-sm">{{ $error }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Accounts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_accounts']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-university text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Individual</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['individual_accounts']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Corporate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['corporate_accounts']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_accounts']) }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Director BVN</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['director_bvn_accounts']) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-shield text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <form method="GET" action="{{ route('admin.virtual-accounts') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                <select name="account_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Types</option>
                    <option value="individual" {{ request('account_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                    <option value="corporate" {{ request('account_type') == 'corporate' ? 'selected' : '' }}>Corporate</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                <select name="limit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="30" {{ request('limit', 30) == 30 ? 'selected' : '' }}>30</option>
                    <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('limit') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.virtual-accounts') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Virtual Accounts Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-list mr-2 text-green-600"></i>All Virtual Accounts
            </h3>
        </div>

        @if($mergedAccounts->isEmpty())
            <div class="p-12 text-center">
                <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-600 font-medium">No virtual accounts found</p>
                <p class="text-sm text-gray-500 mt-2">Try adjusting your filters or refresh the page</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BVN Source</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creation Source</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($mergedAccounts as $account)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $account['account_number'] ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $account['account_name'] ?? 'N/A' }}</div>
                                        @if($account['id'])
                                            <div class="text-xs text-gray-400 mt-1">ID: {{ Str::limit($account['id'], 20) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($account['user'])
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $account['user']->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $account['user']->email ?? $account['user']->phone }}</div>
                                        </div>
                                    @else
                                        <div>
                                            <div class="text-sm text-gray-900">{{ ($account['firstname'] ?? '') . ' ' . ($account['lastname'] ?? '') }}</div>
                                            <div class="text-xs text-gray-500">{{ $account['phone_number'] ?? $account['email'] ?? 'N/A' }}</div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="account-type-badge {{ $account['account_type'] == 'corporate' ? 'type-corporate' : 'type-individual' }}">
                                        <i class="fas {{ $account['account_type'] == 'corporate' ? 'fa-building' : 'fa-user' }} mr-1"></i>
                                        {{ ucfirst($account['account_type'] ?? 'individual') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $status = strtolower($account['status'] ?? 'active');
                                        $statusClass = 'status-active';
                                        if($status == 'inactive' || $status == 'suspended') {
                                            $statusClass = 'status-inactive';
                                        } elseif($status == 'pending') {
                                            $statusClass = 'status-pending';
                                        }
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        <i class="fas fa-circle text-xs mr-1"></i>
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($account['created_by_director'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            <i class="fas fa-user-shield mr-1"></i>Director BVN
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-user-check mr-1"></i>User BVN
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $source = $account['creation_source'] ?? null;
                                        $sourceLabels = [
                                            'auto_registration' => ['label' => 'Auto Registration', 'icon' => 'fa-user-plus', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                                            'admin_manual' => ['label' => 'Admin Manual', 'icon' => 'fa-user-cog', 'bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
                                            'kyc_upgrade' => ['label' => 'KYC Upgrade', 'icon' => 'fa-arrow-up', 'bg' => 'bg-green-100', 'text' => 'text-green-800'],
                                            'auto' => ['label' => 'Auto', 'icon' => 'fa-magic', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
                                        ];
                                        $sourceInfo = $sourceLabels[$source] ?? ['label' => ucfirst(str_replace('_', ' ', $source ?? 'Unknown')), 'icon' => 'fa-question', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sourceInfo['bg'] }} {{ $sourceInfo['text'] }}" title="{{ $sourceInfo['label'] }}">
                                        <i class="fas {{ $sourceInfo['icon'] }} mr-1"></i>
                                        {{ $sourceInfo['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($account['created_at'])
                                        <div>{{ \Carbon\Carbon::parse($account['created_at'])->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($account['created_at'])->format('h:i A') }}</div>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        @php
                                            // Use local_account_id if available, otherwise use BellBank ID or account number
                                            $viewId = $account['local_account_id'] ?? $account['id'] ?? $account['account_number'] ?? null;
                                            $deleteId = $account['local_account_id'] ?? $account['id'] ?? null;
                                        @endphp
                                        @if($viewId)
                                            <a href="{{ route('admin.virtual-account-details', $viewId) }}" 
                                               class="text-green-600 hover:text-green-900 p-2 rounded hover:bg-green-50 transition" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        @if($deleteId)
                                            <button onclick="confirmDelete('{{ $deleteId }}', '{{ $account['account_number'] ?? 'N/A' }}')" 
                                                    class="text-red-600 hover:text-red-900 p-2 rounded hover:bg-red-50 transition" 
                                                    title="Delete Account">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($pagination && isset($pagination['totalPages']) && $pagination['totalPages'] > 1)
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing page {{ $pagination['currentPage'] ?? 1 }} of {{ $pagination['totalPages'] ?? 1 }}
                        </div>
                        <div class="flex gap-2">
                            @if(isset($pagination['currentPage']) && $pagination['currentPage'] > 1)
                                <a href="{{ route('admin.virtual-accounts', array_merge(request()->all(), ['page' => ($pagination['currentPage'] ?? 1) - 1])) }}" 
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            @endif
                            @if(isset($pagination['currentPage']) && $pagination['currentPage'] < ($pagination['totalPages'] ?? 1))
                                <a href="{{ route('admin.virtual-accounts', array_merge(request()->all(), ['page' => ($pagination['currentPage'] ?? 1) + 1])) }}" 
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Create Virtual Account Modal -->
    <div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-plus-circle mr-2 text-green-600"></i>Create Virtual Account
                </h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.virtual-accounts.create') }}" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select User *</label>
                        <select name="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">-- Select User --</option>
                            @foreach(\App\Models\User::orderBy('first_name')->orderBy('last_name')->get() as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->email ?? $user->phone }})
                                    @if($user->bvn)
                                        - BVN: {{ substr($user->bvn, 0, 3) }}****{{ substr($user->bvn, -4) }}
                                    @else
                                        - No BVN
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Select the user to create a virtual account for</p>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="use_director_bvn" id="use_director_bvn" value="1" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <label for="use_director_bvn" class="ml-2 text-sm text-gray-700">
                            Use Director BVN (for users without BVN)
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name (Optional)</label>
                        <input type="text" name="middlename" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address (Optional)</label>
                        <textarea name="address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gender (Optional)</label>
                            <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">-- Select --</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth (Optional)</label>
                            <input type="date" name="date_of_birth" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-plus mr-2"></i>Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                <h3 class="text-lg font-semibold text-red-900">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 mb-4">Are you sure you want to delete this virtual account?</p>
                <p class="text-sm text-gray-600 mb-2"><strong>Account Number:</strong> <span id="deleteAccountNumber"></span></p>
                <p class="text-xs text-red-600">This action cannot be undone!</p>
            </div>
            <form id="deleteForm" method="POST" class="p-6 pt-0">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function refreshAccounts() {
        window.location.reload();
    }

    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function confirmDelete(accountId, accountNumber) {
        document.getElementById('deleteAccountNumber').textContent = accountNumber;
        document.getElementById('deleteForm').action = '{{ url("/secure/virtual-accounts") }}/' + accountId;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modals on outside click
    document.getElementById('createModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeCreateModal();
        }
    });

    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endpush


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use App\Models\KycSubmission;
use App\Models\ThriftGroup;
use App\Models\Wallet;
use App\Models\Merchant;
use App\Models\Agent;
use App\Models\SavingsAccount;
use App\Models\BellbankAccount;
use App\Services\BellBankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Show admin dashboard.
     */
    public function dashboard()
    {
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        $lastWeek = Carbon::now()->subWeek();
        $today = Carbon::today();

        // User Statistics
        $totalUsers = User::count();
        $activeUsers = User::where('kyc_status', 'approved')->count();
        $newUsersToday = User::whereDate('created_at', $today)->count();
        $newUsersThisMonth = User::where('created_at', '>=', $lastMonth)->count();
        $userGrowthRate = $totalUsers > 0 ? (($newUsersThisMonth / $totalUsers) * 100) : 0;

        // Transaction Statistics
        $totalTransactions = Transaction::count();
        $totalVolume = Transaction::where('status', 'settled')->sum('amount') ?? 0;
        $todayTransactions = Transaction::whereDate('created_at', $today)->count();
        $todayVolume = Transaction::whereDate('created_at', $today)->where('status', 'settled')->sum('amount') ?? 0;
        $monthTransactions = Transaction::where('created_at', '>=', $lastMonth)->count();
        $monthVolume = Transaction::where('created_at', '>=', $lastMonth)->where('status', 'settled')->sum('amount') ?? 0;
        $transactionGrowthRate = $totalTransactions > 0 ? (($monthTransactions / $totalTransactions) * 100) : 0;

        // KYC Statistics
        $pendingKyc = KycSubmission::where('status', 'pending')->count();
        $approvedKyc = KycSubmission::where('status', 'approved')->count();
        $rejectedKyc = KycSubmission::where('status', 'rejected')->count();

        // Wallet Statistics
        $totalWallets = Wallet::count();
        $totalWalletBalance = Wallet::sum('balance') ?? 0;
        $activeWallets = Wallet::where('status', 'active')->count();

        // Merchant & Agent Statistics
        $totalMerchants = Merchant::count();
        $activeMerchants = Merchant::where('status', 'active')->count();
        $totalAgents = Agent::count();
        $activeAgents = Agent::where('status', 'active')->count();

        // Thrift & Savings Statistics
        $activeThriftGroups = ThriftGroup::where('status', 'active')->count();
        $totalThriftGroups = ThriftGroup::count();
        $totalSavingsAccounts = SavingsAccount::count();
        $totalSavingsBalance = SavingsAccount::sum('current_amount') ?? 0;

        // Transaction Status Breakdown
        $settledTransactions = Transaction::where('status', 'settled')->count();
        $pendingTransactions = Transaction::where('status', 'pending')->count();
        $failedTransactions = Transaction::where('status', 'failed')->count();

        // Revenue (from fees - if you have a fee model)
        $totalRevenue = Transaction::where('status', 'settled')->sum('fee') ?? 0;
        $todayRevenue = Transaction::whereDate('created_at', $today)->where('status', 'settled')->sum('fee') ?? 0;
        $monthRevenue = Transaction::where('created_at', '>=', $lastMonth)->where('status', 'settled')->sum('fee') ?? 0;

        // Last 7 days transaction volume for chart
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayVolume = Transaction::whereDate('created_at', $date)
                ->where('status', 'settled')
                ->sum('amount') ?? 0;
            $last7Days[] = [
                'date' => $date->format('M d'),
                'day' => $date->format('D'),
                'volume' => $dayVolume,
            ];
        }

        // Last 6 months user growth for chart
        $last6Months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthUsers = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $last6Months[] = [
                'month' => $month->format('M'),
                'users' => $monthUsers,
            ];
        }

        // Recent Activity
        $recentUsers = User::latest()->limit(5)->get();
        $recentTransactions = Transaction::with(['walletFrom.user', 'walletTo.user'])
            ->latest()
            ->limit(8)
            ->get();

        // Top Merchants by Transaction Volume
        $topMerchants = Merchant::with('user')
            ->withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            // User Stats
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'new_users_today' => $newUsersToday,
            'new_users_month' => $newUsersThisMonth,
            'user_growth_rate' => round($userGrowthRate, 2),
            
            // Transaction Stats
            'total_transactions' => $totalTransactions,
            'total_volume' => $totalVolume,
            'today_transactions' => $todayTransactions,
            'today_volume' => $todayVolume,
            'month_transactions' => $monthTransactions,
            'month_volume' => $monthVolume,
            'transaction_growth_rate' => round($transactionGrowthRate, 2),
            
            // KYC Stats
            'pending_kyc' => $pendingKyc,
            'approved_kyc' => $approvedKyc,
            'rejected_kyc' => $rejectedKyc,
            
            // Wallet Stats
            'total_wallets' => $totalWallets,
            'total_wallet_balance' => $totalWalletBalance,
            'active_wallets' => $activeWallets,
            
            // Merchant & Agent Stats
            'total_merchants' => $totalMerchants,
            'active_merchants' => $activeMerchants,
            'total_agents' => $totalAgents,
            'active_agents' => $activeAgents,
            
            // Thrift & Savings Stats
            'active_thrift_groups' => $activeThriftGroups,
            'total_thrift_groups' => $totalThriftGroups,
            'total_savings_accounts' => $totalSavingsAccounts,
            'total_savings_balance' => $totalSavingsBalance,
            
            // Transaction Status
            'settled_transactions' => $settledTransactions,
            'pending_transactions' => $pendingTransactions,
            'failed_transactions' => $failedTransactions,
            
            // Revenue
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'month_revenue' => $monthRevenue,
            
            // Charts Data
            'last_7_days' => $last7Days,
            'last_6_months' => $last6Months,
            
            // Recent Activity
            'recent_users' => $recentUsers,
            'recent_transactions' => $recentTransactions,
            'top_merchants' => $topMerchants,
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Show virtual accounts management page.
     */
    public function virtualAccounts(Request $request, BellBankService $bellBankService)
    {
        try {
            // Get filters from request
            $filters = [
                'accountType' => $request->get('account_type'),
                'validityType' => $request->get('validity_type'),
                'status' => $request->get('status'),
                'page' => $request->get('page', 1),
                'limit' => $request->get('limit', 30),
            ];

            // Remove null values
            $filters = array_filter($filters, function($value) {
                return $value !== null;
            });

            // Fetch virtual accounts from BellBank
            $response = $bellBankService->listVirtualAccounts($filters);
            $accounts = $response['data'] ?? [];
            $pagination = $response['pagination'] ?? null;

            // Also get local database records for matching
            $localAccounts = BellbankAccount::with('user')
                ->whereNotNull('virtual_account_id')
                ->get()
                ->keyBy('virtual_account_id');

            // Merge BellBank data with local user data
            $mergedAccounts = collect($accounts)->map(function($account) use ($localAccounts) {
                $localAccount = $localAccounts->get($account['id'] ?? $account['externalReference'] ?? null);
                return [
                    'id' => $account['id'] ?? $account['externalReference'] ?? null,
                    'account_number' => $account['accountNumber'] ?? null,
                    'account_name' => $account['accountName'] ?? null,
                    'account_type' => $account['accountType'] ?? 'individual',
                    'firstname' => $account['firstname'] ?? null,
                    'lastname' => $account['lastname'] ?? null,
                    'middlename' => $account['middlename'] ?? null,
                    'phone_number' => $account['mobileNumber'] ?? $account['phoneNumber'] ?? null,
                    'email' => $account['emailAddress'] ?? null,
                    'bvn' => $account['bvn'] ?? null,
                    'status' => $account['status'] ?? 'active',
                    'validity_type' => $account['validityType'] ?? null,
                    'created_at' => isset($account['createdAt']) ? Carbon::createFromTimestampMs($account['createdAt'])->format('Y-m-d H:i:s') : null,
                    'updated_at' => isset($account['updatedAt']) ? Carbon::createFromTimestampMs($account['updatedAt'])->format('Y-m-d H:i:s') : null,
                    'user' => $localAccount ? $localAccount->user : null,
                    'created_by_director' => $localAccount ? $localAccount->created_by_director : false,
                    'creation_source' => $localAccount ? $localAccount->creation_source : null,
                    'local_account_id' => $localAccount ? $localAccount->id : null,
                ];
            });

            // Statistics
            $stats = [
                'total_accounts' => count($accounts),
                'individual_accounts' => collect($accounts)->where('accountType', 'individual')->count(),
                'corporate_accounts' => collect($accounts)->where('accountType', 'corporate')->count(),
                'active_accounts' => collect($accounts)->where('status', 'active')->count(),
                'director_bvn_accounts' => BellbankAccount::where('created_by_director', true)->count(),
            ];

            return view('admin.virtual-accounts', compact('mergedAccounts', 'stats', 'pagination', 'filters'));
        } catch (\Exception $e) {
            return view('admin.virtual-accounts', [
                'mergedAccounts' => collect([]),
                'stats' => [
                    'total_accounts' => 0,
                    'individual_accounts' => 0,
                    'corporate_accounts' => 0,
                    'active_accounts' => 0,
                    'director_bvn_accounts' => 0,
                ],
                'pagination' => null,
                'filters' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create virtual account for a user.
     */
    public function createVirtualAccount(Request $request, BellBankService $bellBankService)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'use_director_bvn' => 'sometimes|boolean',
            'middlename' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:500',
            'gender' => 'sometimes|in:male,female',
            'date_of_birth' => 'sometimes|date',
        ]);

        try {
            $useDirectorBvn = $request->boolean('use_director_bvn', false);
            
            $additionalData = [];
            if ($request->filled('middlename')) {
                $additionalData['middlename'] = $request->middlename;
            }
            if ($request->filled('address')) {
                $additionalData['address'] = $request->address;
            }
            if ($request->filled('gender')) {
                $additionalData['gender'] = $request->gender;
            }
            if ($request->filled('date_of_birth')) {
                $additionalData['dateOfBirth'] = $request->date_of_birth;
            }

            $additionalData['creation_source'] = 'admin_manual';
            
            $bellbankAccount = $bellBankService->createVirtualAccount(
                $request->user_id,
                $useDirectorBvn,
                $additionalData
            );

            return redirect()->route('admin.virtual-accounts')
                ->with('success', 'Virtual account created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.virtual-accounts')
                ->with('error', 'Failed to create virtual account: ' . $e->getMessage());
        }
    }

    /**
     * Delete virtual account.
     */
    public function deleteVirtualAccount($accountId, Request $request, BellBankService $bellBankService)
    {
        try {
            // First check if it's a local database ID
            $localAccount = BellbankAccount::withTrashed()->find($accountId);
            
            // If not found by ID, try to find by virtual_account_id or account_number
            if (!$localAccount) {
                $localAccount = BellbankAccount::where('virtual_account_id', $accountId)
                    ->orWhere('account_number', $accountId)
                    ->first();
            }

            // If still not found, try to search in BellBank list to find matching account
            if (!$localAccount) {
                try {
                    $allAccounts = $bellBankService->listVirtualAccounts(['limit' => 1000]);
                    $accounts = $allAccounts['data'] ?? [];
                    foreach ($accounts as $acc) {
                        if (($acc['id'] ?? null) == $accountId || 
                            ($acc['externalReference'] ?? null) == $accountId ||
                            ($acc['accountNumber'] ?? null) == $accountId) {
                            // Found in BellBank, try to find or create local record
                            if (isset($acc['accountNumber'])) {
                                $localAccount = BellbankAccount::where('account_number', $acc['accountNumber'])->first();
                                if (!$localAccount) {
                                    // Account exists in BellBank but not in our DB
                                    return redirect()->route('admin.virtual-accounts')
                                        ->with('error', 'Account found in BellBank but not in local database. Please refresh the page to sync accounts first.');
                                }
                            }
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to search BellBank list: ' . $e->getMessage());
                }
            }

            if ($localAccount) {
                $accountNumber = $localAccount->account_number;
                
                // Note: BellBank API doesn't support deleting virtual accounts via API
                // We'll only soft delete from our local database
                // The account will remain active on BellBank's side
                
                // Soft delete from local database
                $localAccount->delete();

                return redirect()->route('admin.virtual-accounts')
                    ->with('success', "Virtual account ({$accountNumber}) has been removed from the system. Note: The account remains active on BellBank's side as their API doesn't support account deletion.");
            } else {
                // If no local record found, we can't delete it
                return redirect()->route('admin.virtual-accounts')
                    ->with('error', 'Virtual account not found. The account may have already been deleted or does not exist in the system.');
            }
        } catch (\Exception $e) {
            \Log::error('Delete virtual account error: ' . $e->getMessage());
            return redirect()->route('admin.virtual-accounts')
                ->with('error', 'Failed to delete virtual account: ' . $e->getMessage());
        }
    }

    /**
     * Show virtual account details.
     */
    public function virtualAccountDetails($accountId, BellBankService $bellBankService)
    {
        try {
            // First, try to find local account by ID (if it's a local database ID)
            $localAccount = BellbankAccount::with('user')->find($accountId);
            
            $accountData = null;
            $transactions = [];

            // If we have a local account, try to get details from BellBank
            if ($localAccount && $localAccount->virtual_account_id) {
                try {
                    $account = $bellBankService->getClientAccount($localAccount->virtual_account_id);
                    $accountData = $account['data'] ?? $account;
                } catch (\Exception $e) {
                    // If individual account endpoint fails, try to find it in the list
                    \Log::warning('Failed to get individual account, trying list: ' . $e->getMessage());
                    try {
                        $allAccounts = $bellBankService->listVirtualAccounts(['limit' => 1000]);
                        $accounts = $allAccounts['data'] ?? [];
                        foreach ($accounts as $acc) {
                            if (($acc['id'] ?? null) == $localAccount->virtual_account_id || 
                                ($acc['accountNumber'] ?? null) == $localAccount->account_number) {
                                $accountData = $acc;
                                break;
                            }
                        }
                    } catch (\Exception $listError) {
                        \Log::error('Failed to get account from list: ' . $listError->getMessage());
                    }
                }
            } else {
                // Try to find by virtual_account_id or account_number
                $localAccount = BellbankAccount::where('virtual_account_id', $accountId)
                    ->orWhere('account_number', $accountId)
                    ->with('user')
                    ->first();
                
                if ($localAccount && $localAccount->virtual_account_id) {
                    try {
                        $account = $bellBankService->getClientAccount($localAccount->virtual_account_id);
                        $accountData = $account['data'] ?? $account;
                    } catch (\Exception $e) {
                        \Log::warning('Failed to get account details: ' . $e->getMessage());
                    }
                } else {
                    // Try to get from BellBank directly using the ID
                    try {
                        $account = $bellBankService->getClientAccount($accountId);
                        $accountData = $account['data'] ?? $account;
                    } catch (\Exception $e) {
                        // If that fails, search in the list
                        try {
                            $allAccounts = $bellBankService->listVirtualAccounts(['limit' => 1000]);
                            $accounts = $allAccounts['data'] ?? [];
                            foreach ($accounts as $acc) {
                                if (($acc['id'] ?? null) == $accountId || 
                                    ($acc['externalReference'] ?? null) == $accountId ||
                                    ($acc['accountNumber'] ?? null) == $accountId) {
                                    $accountData = $acc;
                                    // Try to find local account by account number
                                    if (!$localAccount && isset($acc['accountNumber'])) {
                                        $localAccount = BellbankAccount::where('account_number', $acc['accountNumber'])
                                            ->with('user')
                                            ->first();
                                    }
                                    break;
                                }
                            }
                        } catch (\Exception $listError) {
                            \Log::error('Failed to search account in list: ' . $listError->getMessage());
                        }
                    }
                }
            }

            // If we still don't have account data, use local account data
            if (!$accountData && $localAccount) {
                $accountData = [
                    'accountNumber' => $localAccount->account_number,
                    'accountName' => $localAccount->user ? $localAccount->user->name : 'N/A',
                    'status' => $localAccount->status,
                ];
            }

            // Get account transactions if we have an account ID
            if ($accountData && (isset($accountData['id']) || isset($accountData['externalReference']))) {
                try {
                    $virtualAccountId = $accountData['id'] ?? $accountData['externalReference'] ?? null;
                    if ($virtualAccountId) {
                        $transactionsResponse = $bellBankService->getVirtualAccountTransactions($virtualAccountId, [
                            'page' => 1,
                            'limit' => 20,
                        ]);
                        $transactions = $transactionsResponse['data'] ?? $transactionsResponse['transactions'] ?? [];
                    }
                } catch (\Exception $e) {
                    // If transactions fail, continue without them
                    \Log::warning('Failed to get transactions: ' . $e->getMessage());
                    $transactions = [];
                }
            }

            if (!$accountData) {
                return redirect()->route('admin.virtual-accounts')
                    ->with('error', 'Account not found. The account may not exist in BellBank or the ID format is incorrect.');
            }

            return view('admin.virtual-account-details', compact('accountData', 'localAccount', 'transactions'));
        } catch (\Exception $e) {
            \Log::error('Virtual account details error: ' . $e->getMessage());
            return redirect()->route('admin.virtual-accounts')
                ->with('error', 'Failed to load account details: ' . $e->getMessage());
        }
    }
}


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
}


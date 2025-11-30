<?php

namespace App\Services;

use App\Models\SavingsAccount;
use App\Models\SavingsTransaction;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SavingsService
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Create a savings account.
     */
    public function createAccount($userId, $data)
    {
        return SavingsAccount::create([
            'user_id' => $userId,
            'name' => $data['name'],
            'target_amount' => $data['target_amount'] ?? null,
            'current_amount' => 0,
            'type' => $data['type'] ?? 'regular',
            'status' => 'active',
        ]);
    }

    /**
     * Deposit to savings.
     */
    public function deposit($accountId, $userId, $amount)
    {
        return DB::transaction(function () use ($accountId, $userId, $amount) {
            $account = SavingsAccount::where('id', $accountId)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($account->status !== 'active') {
                throw new \Exception('Savings account is not active');
            }

            // Transfer from wallet to savings
            $wallet = $this->walletService->getBalance($userId);
            if (!$wallet || $wallet['balance'] < $amount) {
                throw new \Exception('Insufficient wallet balance');
            }

            // Deduct from wallet (simplified - in reality would use wallet service transfer)
            $account->current_amount += $amount;
            $account->save();

            // Create savings transaction
            $savingsTransaction = SavingsTransaction::create([
                'savings_account_id' => $account->id,
                'amount' => $amount,
                'type' => 'deposit',
                'status' => 'completed',
            ]);

            return $savingsTransaction;
        });
    }

    /**
     * Withdraw from savings.
     */
    public function withdraw($accountId, $userId, $amount)
    {
        return DB::transaction(function () use ($accountId, $userId, $amount) {
            $account = SavingsAccount::where('id', $accountId)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($account->status !== 'active') {
                throw new \Exception('Savings account is not active');
            }

            if ($account->current_amount < $amount) {
                throw new \Exception('Insufficient savings balance');
            }

            // Check if account is locked
            if ($account->locked_until && Carbon::now()->lt($account->locked_until)) {
                throw new \Exception('Savings account is locked until ' . $account->locked_until->format('Y-m-d'));
            }

            $account->current_amount -= $amount;
            $account->save();

            // Create savings transaction
            $savingsTransaction = SavingsTransaction::create([
                'savings_account_id' => $account->id,
                'amount' => $amount,
                'type' => 'withdrawal',
                'status' => 'completed',
            ]);

            return $savingsTransaction;
        });
    }

    /**
     * Get user's savings accounts.
     */
    public function getUserAccounts($userId)
    {
        return SavingsAccount::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}


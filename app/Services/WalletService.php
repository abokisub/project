<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\Transaction;
use App\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    protected $walletRepository;

    public function __construct(WalletRepository $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    /**
     * Get wallet balance for user.
     */
    public function getBalance($userId, $currency = 'NGN')
    {
        $wallet = $this->walletRepository->findByUserAndCurrency($userId, $currency);
        
        if (!$wallet) {
            return null;
        }

        return [
            'balance' => $wallet->balance,
            'currency' => $wallet->currency,
            'status' => $wallet->status,
        ];
    }

    /**
     * Fund wallet.
     */
    public function fund($userId, $amount, $currency = 'NGN', $reference = null)
    {
        return DB::transaction(function () use ($userId, $amount, $currency, $reference) {
            $wallet = $this->walletRepository->findOrCreateByUserAndCurrency($userId, $currency);
            
            $wallet->lockForUpdate();
            $wallet->balance += $amount;
            $wallet->save();

            $transaction = Transaction::create([
                'wallet_to_id' => $wallet->id,
                'amount' => $amount,
                'fee' => 0,
                'type' => 'deposit',
                'status' => 'settled',
                'reference' => $reference ?? Str::uuid()->toString(),
            ]);

            return $transaction;
        });
    }

    /**
     * Transfer between wallets.
     */
    public function transfer($fromUserId, $toUserId, $amount, $currency = 'NGN', $fee = 0)
    {
        return DB::transaction(function () use ($fromUserId, $toUserId, $amount, $currency, $fee) {
            $fromWallet = $this->walletRepository->findByUserAndCurrency($fromUserId, $currency);
            $toWallet = $this->walletRepository->findOrCreateByUserAndCurrency($toUserId, $currency);

            if (!$fromWallet || $fromWallet->balance < ($amount + $fee)) {
                throw new \Exception('Insufficient balance');
            }

            $fromWallet->lockForUpdate();
            $toWallet->lockForUpdate();

            $fromWallet->balance -= ($amount + $fee);
            $toWallet->balance += $amount;

            $fromWallet->save();
            $toWallet->save();

            $transaction = Transaction::create([
                'wallet_from_id' => $fromWallet->id,
                'wallet_to_id' => $toWallet->id,
                'amount' => $amount,
                'fee' => $fee,
                'type' => 'transfer',
                'status' => 'settled',
                'reference' => Str::uuid()->toString(),
            ]);

            return $transaction;
        });
    }

    /**
     * Get transaction history.
     */
    public function getTransactions($userId, $limit = 20)
    {
        $wallet = $this->walletRepository->findByUserAndCurrency($userId, 'NGN');
        
        if (!$wallet) {
            return collect([]);
        }

        return Transaction::where('wallet_from_id', $wallet->id)
            ->orWhere('wallet_to_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}


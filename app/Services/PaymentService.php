<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Merchant;
use App\Models\User;
use App\Services\WalletService;
use App\Services\BellBankService;
use App\Services\FeeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    protected $walletService;
    protected $bellBankService;
    protected $feeService;

    public function __construct(WalletService $walletService, BellBankService $bellBankService, FeeService $feeService)
    {
        $this->walletService = $walletService;
        $this->bellBankService = $bellBankService;
        $this->feeService = $feeService;
    }

    /**
     * Send money (wallet to wallet).
     */
    public function send($fromUserId, $toUserId, $amount, $narration = '')
    {
        return $this->walletService->transfer($fromUserId, $toUserId, $amount);
    }

    /**
     * Bank transfer via BellBank.
     */
    public function bankTransfer($userId, $amount, $accountNumber, $bankCode, $narration = '')
    {
        return DB::transaction(function () use ($userId, $amount, $accountNumber, $bankCode, $narration) {
            $user = User::findOrFail($userId);
            
            // Deduct from user's wallet first
            $wallet = $this->walletService->getBalance($userId);
            if (!$wallet || $wallet['balance'] < $amount) {
                throw new \Exception('Insufficient balance');
            }

            // Calculate fee based on user tier
            $fee = $this->calculateFee('bank_transfer', $amount, $user);

            // Create pending transaction
            $transaction = Transaction::create([
                'wallet_from_id' => \App\Models\Wallet::where('user_id', $userId)->first()->id,
                'amount' => $amount,
                'fee' => $fee,
                'type' => 'bank_transfer',
                'status' => 'processing',
                'reference' => Str::uuid()->toString(),
                'meta' => [
                    'account_number' => $accountNumber,
                    'bank_code' => $bankCode,
                    'narration' => $narration,
                    'user_tier' => $user->getTier(),
                ],
            ]);

            // Initiate transfer via BellBank
            try {
                $result = $this->bellBankService->transfer($amount, $accountNumber, $bankCode, $narration);
                
                $transaction->update([
                    'status' => 'settled',
                    'meta' => array_merge($transaction->meta ?? [], ['bellbank_response' => $result]),
                ]);

                return $transaction;
            } catch (\Exception $e) {
                $transaction->update(['status' => 'failed']);
                throw $e;
            }
        });
    }

    /**
     * QR payment to merchant.
     */
    public function qrPayment($userId, $merchantId, $amount, $qrCode = null)
    {
        return DB::transaction(function () use ($userId, $merchantId, $amount, $qrCode) {
            $merchant = Merchant::findOrFail($merchantId);

            if ($merchant->status !== 'active') {
                throw new \Exception('Merchant account is not active');
            }

            // Transfer from user to merchant
            $transaction = $this->walletService->transfer(
                $userId,
                $merchant->user_id,
                $amount
            );

            // Create merchant transaction record
            \App\Models\MerchantTransaction::create([
                'merchant_id' => $merchantId,
                'user_id' => $userId,
                'amount' => $amount,
                'status' => 'completed',
                'transaction_id' => $transaction->id,
            ]);

            return $transaction;
        });
    }

    /**
     * Calculate fee for transaction.
     */
    protected function calculateFee($transactionType, $amount, $user = null)
    {
        return $this->feeService->calculateFee($transactionType, $amount, $user);
    }
}


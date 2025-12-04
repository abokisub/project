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
                // Generate reference if not provided (using business name prefix)
                $businessName = config('app.name', 'KoboPoint');
                $prefix = strtoupper(explode(' ', $businessName)[0]);
                $reference = $prefix . '-' . Str::random(10);
                
                $result = $this->bellBankService->transfer(
                    $amount,
                    $accountNumber, // beneficiaryAccountNumber
                    $bankCode, // beneficiaryBankCode
                    $narration,
                    $reference,
                    $user->name // senderName
                );
                
                // Extract transfer data from response
                $transferData = $result['data'] ?? $result;
                
                // Update transaction with BellBank response
                $transaction->update([
                    'status' => $transferData['status'] ?? 'processing', // pending, processing, settled, failed
                    'reference' => $transferData['reference'] ?? $transaction->reference,
                    'meta' => array_merge($transaction->meta ?? [], [
                        'bellbank_response' => $result,
                        'session_id' => $transferData['sessionId'] ?? null,
                        'transaction_id' => $transferData['transactionId'] ?? null,
                        'net_amount' => $transferData['netAmount'] ?? null,
                        'charge' => $transferData['charge'] ?? null,
                        'destination_account_name' => $transferData['destinationAccountName'] ?? null,
                        'destination_bank_name' => $transferData['destinationBankName'] ?? null,
                    ]),
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


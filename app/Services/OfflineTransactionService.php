<?php

namespace App\Services;

use App\Models\OfflineVoucher;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OfflineTransactionService
{
    /**
     * Generate offline voucher.
     */
    public function generateVoucher($fromUserId, $toUserId, $amount, $expiryHours = 24)
    {
        $code = Str::random(16);
        $signature = $this->generateSignature($code, $amount, $fromUserId, $toUserId);

        $voucher = OfflineVoucher::create([
            'code' => $code,
            'amount' => $amount,
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
            'expires_at' => Carbon::now()->addHours($expiryHours),
            'signature' => $signature,
        ]);

        return $voucher;
    }

    /**
     * Redeem offline voucher.
     */
    public function redeemVoucher($code, $userId)
    {
        $voucher = OfflineVoucher::where('code', $code)
            ->where('redeemed', false)
            ->where('expires_at', '>', Carbon::now())
            ->firstOrFail();

        if ($voucher->to_user_id && $voucher->to_user_id !== $userId) {
            throw new \Exception('Voucher is not assigned to this user');
        }

        // Verify signature
        if (!$this->verifySignature($voucher->code, $voucher->amount, $voucher->from_user_id, $voucher->to_user_id, $voucher->signature)) {
            throw new \Exception('Invalid voucher signature');
        }

        $voucher->update([
            'to_user_id' => $userId,
            'redeemed' => true,
            'redeemed_at' => Carbon::now(),
        ]);

        return $voucher;
    }

    /**
     * Sync offline transactions.
     */
    public function syncTransactions($userId, $transactions)
    {
        $synced = [];
        $failed = [];

        foreach ($transactions as $transactionData) {
            try {
                // Validate and process each transaction
                // This would integrate with the wallet service to actually transfer funds
                $synced[] = $transactionData;
            } catch (\Exception $e) {
                $failed[] = [
                    'transaction' => $transactionData,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'synced' => $synced,
            'failed' => $failed,
        ];
    }

    /**
     * Generate signature for voucher.
     */
    protected function generateSignature($code, $amount, $fromUserId, $toUserId)
    {
        $data = $code . $amount . $fromUserId . ($toUserId ?? '');
        return hash_hmac('sha256', $data, env('APP_KEY'));
    }

    /**
     * Verify signature.
     */
    protected function verifySignature($code, $amount, $fromUserId, $toUserId, $signature)
    {
        $expectedSignature = $this->generateSignature($code, $amount, $fromUserId, $toUserId);
        return hash_equals($expectedSignature, $signature);
    }
}


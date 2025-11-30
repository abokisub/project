<?php

namespace App\Services;

use App\Models\Fee;
use App\Models\User;

class FeeService
{
    /**
     * Calculate fee for a transaction.
     */
    public function calculateFee(string $transactionType, float $amount, User $user = null): float
    {
        $userTier = $user ? $user->getTier() : 'tier1';
        
        // Get fee configuration for this transaction type and user tier
        $fee = Fee::where('transaction_type', $transactionType)
            ->where('user_tier', $userTier)
            ->where('is_active', true)
            ->first();

        if (!$fee) {
            // Default fee if not configured
            return 0;
        }

        // Calculate fee: (amount * percentage) + fixed fee
        $percentageFee = ($amount * $fee->fee_percentage) / 100;
        $totalFee = $percentageFee + $fee->fee_fixed;

        // Apply min/max constraints
        if ($fee->min_fee > 0 && $totalFee < $fee->min_fee) {
            $totalFee = $fee->min_fee;
        }

        if ($fee->max_fee && $totalFee > $fee->max_fee) {
            $totalFee = $fee->max_fee;
        }

        return round($totalFee, 2);
    }

    /**
     * Get fee breakdown for display.
     */
    public function getFeeBreakdown(string $transactionType, float $amount, User $user = null): array
    {
        $userTier = $user ? $user->getTier() : 'tier1';
        
        $fee = Fee::where('transaction_type', $transactionType)
            ->where('user_tier', $userTier)
            ->where('is_active', true)
            ->first();

        if (!$fee) {
            return [
                'fee_percentage' => 0,
                'fee_fixed' => 0,
                'total_fee' => 0,
                'amount' => $amount,
                'total_amount' => $amount,
            ];
        }

        $percentageFee = ($amount * $fee->fee_percentage) / 100;
        $totalFee = $percentageFee + $fee->fee_fixed;

        // Apply min/max
        if ($fee->min_fee > 0 && $totalFee < $fee->min_fee) {
            $totalFee = $fee->min_fee;
        }
        if ($fee->max_fee && $totalFee > $fee->max_fee) {
            $totalFee = $fee->max_fee;
        }

        return [
            'fee_percentage' => $fee->fee_percentage,
            'fee_fixed' => $fee->fee_fixed,
            'percentage_amount' => round($percentageFee, 2),
            'total_fee' => round($totalFee, 2),
            'amount' => $amount,
            'total_amount' => round($amount + $totalFee, 2),
            'user_tier' => $userTier,
        ];
    }
}


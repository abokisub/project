<?php

namespace App\Repositories;

use App\Models\Wallet;

class WalletRepository
{
    /**
     * Find wallet by user and currency.
     */
    public function findByUserAndCurrency($userId, $currency = 'NGN')
    {
        return Wallet::where('user_id', $userId)
            ->where('currency', $currency)
            ->first();
    }

    /**
     * Find or create wallet by user and currency.
     */
    public function findOrCreateByUserAndCurrency($userId, $currency = 'NGN')
    {
        return Wallet::firstOrCreate(
            ['user_id' => $userId, 'currency' => $currency],
            ['balance' => 0, 'status' => 'active']
        );
    }
}


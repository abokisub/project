<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_from_id',
        'wallet_to_id',
        'amount',
        'fee',
        'type',
        'status',
        'reference',
        'meta',
        'offline_flag',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'fee' => 'decimal:2',
            'meta' => 'array',
            'offline_flag' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }

    public function walletFrom()
    {
        return $this->belongsTo(Wallet::class, 'wallet_from_id');
    }

    public function walletTo()
    {
        return $this->belongsTo(Wallet::class, 'wallet_to_id');
    }
}

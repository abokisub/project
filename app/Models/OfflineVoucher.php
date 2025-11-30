<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflineVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'amount',
        'from_user_id',
        'to_user_id',
        'expires_at',
        'redeemed',
        'signature',
        'redeemed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expires_at' => 'datetime',
            'redeemed' => 'boolean',
            'redeemed_at' => 'datetime',
        ];
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}


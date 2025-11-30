<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'qr_code',
        'settlement_account',
        'bank_name',
        'account_number',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function terminals()
    {
        return $this->hasMany(MerchantTerminal::class);
    }

    public function transactions()
    {
        return $this->hasMany(MerchantTransaction::class);
    }
}


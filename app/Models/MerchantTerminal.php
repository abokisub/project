<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantTerminal extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'terminal_id',
        'status',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}


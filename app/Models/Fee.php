<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_type',
        'user_tier',
        'fee_percentage',
        'fee_fixed',
        'min_fee',
        'max_fee',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fee_percentage' => 'decimal:2',
            'fee_fixed' => 'decimal:2',
            'min_fee' => 'decimal:2',
            'max_fee' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function history()
    {
        return $this->hasMany(FeeHistory::class);
    }
}


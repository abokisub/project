<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_transaction_id',
        'internal_transaction_id',
        'status',
        'diff',
        'external_data',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'diff' => 'decimal:2',
            'external_data' => 'array',
        ];
    }

    public function internalTransaction()
    {
        return $this->belongsTo(Transaction::class, 'internal_transaction_id');
    }
}


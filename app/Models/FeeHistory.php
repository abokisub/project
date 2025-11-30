<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_id',
        'old_fee_percentage',
        'new_fee_percentage',
        'old_fee_fixed',
        'new_fee_fixed',
        'changed_by',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'old_fee_percentage' => 'decimal:2',
            'new_fee_percentage' => 'decimal:2',
            'old_fee_fixed' => 'decimal:2',
            'new_fee_fixed' => 'decimal:2',
        ];
    }

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThriftContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'thrift_group_id',
        'user_id',
        'amount',
        'due_date',
        'paid_at',
        'status',
        'transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function thriftGroup()
    {
        return $this->belongsTo(ThriftGroup::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}

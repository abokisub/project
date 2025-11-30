<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThriftMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'thrift_group_id',
        'user_id',
        'position',
        'total_contributed',
        'next_payout_date',
        'has_received_payout',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_contributed' => 'decimal:2',
            'next_payout_date' => 'date',
            'has_received_payout' => 'boolean',
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
}

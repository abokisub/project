<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BellbankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'virtual_account_id',
        'account_number',
        'bank_code',
        'bank_name',
        'status',
        'created_by_director',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'created_by_director' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

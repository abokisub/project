<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThriftGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'organizer_id',
        'contribution_amount',
        'frequency',
        'rotation_order',
        'status',
        'total_members',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'contribution_amount' => 'decimal:2',
            'rotation_order' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function members()
    {
        return $this->hasMany(ThriftMember::class);
    }

    public function contributions()
    {
        return $this->hasMany(ThriftContribution::class);
    }
}

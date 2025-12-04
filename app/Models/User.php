<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Wallet;
use App\Models\Device;
use App\Models\BellbankAccount;
use App\Models\ThriftGroup;
use App\Models\ThriftMember;
use App\Models\KycSubmission;
use App\Models\SavingsAccount;
use App\Models\Merchant;
use App\Models\Agent;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'gender',
        'state',
        'city',
        'street_address',
        'email',
        'phone',
        'password',
        'transaction_pin',
        'bvn',
        'kyc_status',
        'kyc_data',
        'user_tier', // tier1, tier2, tier3, tier4, tier5
        'api_key',
        'app_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'transaction_pin',
        'remember_token',
        'bvn',
        'api_key',
        'app_key',
    ];

    /**
     * The default guard name for the model.
     */
    protected $guard_name = 'web';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'transaction_pin' => 'hashed',
            'kyc_data' => 'array',
        ];
    }

    /**
     * Get activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'username', 'email', 'phone', 'kyc_status', 'user_tier'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Generate API and App keys when user is created
        static::creating(function ($user) {
            if (empty($user->api_key)) {
                $user->api_key = self::generateApiKey();
            }
            if (empty($user->app_key)) {
                $user->app_key = self::generateAppKey();
            }
        });
    }

    /**
     * Generate a unique API key.
     */
    public static function generateApiKey(): string
    {
        do {
            $key = 'kp_' . bin2hex(random_bytes(24));
        } while (self::where('api_key', $key)->exists());

        return $key;
    }

    /**
     * Generate a unique App key.
     */
    public static function generateAppKey(): string
    {
        do {
            $key = 'app_' . bin2hex(random_bytes(24));
        } while (self::where('app_key', $key)->exists());

        return $key;
    }

    /**
     * Get full name attribute (accessor).
     */
    public function getNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is merchant.
     */
    public function isMerchant(): bool
    {
        return $this->hasRole('merchant');
    }

    /**
     * Check if user is agent.
     */
    public function isAgent(): bool
    {
        return $this->hasRole('agent');
    }

    /**
     * Get user tier (tier1-tier5).
     */
    public function getTier(): string
    {
        if ($this->hasRole('admin')) {
            return 'admin';
        }
        if ($this->hasRole('merchant')) {
            return 'merchant';
        }
        if ($this->hasRole('agent')) {
            return 'agent';
        }
        
        // Return tier from user_tier field or role
        $tierRoles = ['tier1', 'tier2', 'tier3', 'tier4', 'tier5'];
        foreach ($tierRoles as $tier) {
            if ($this->hasRole($tier)) {
                return $tier;
            }
        }
        
        return $this->user_tier ?? 'tier1';
    }

    // Relationships
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function bellbankAccount()
    {
        return $this->hasOne(BellbankAccount::class);
    }

    public function thriftGroups()
    {
        return $this->hasMany(ThriftGroup::class, 'organizer_id');
    }

    public function thriftMemberships()
    {
        return $this->hasMany(ThriftMember::class);
    }

    public function kycSubmissions()
    {
        return $this->hasMany(KycSubmission::class);
    }

    public function savingsAccounts()
    {
        return $this->hasMany(SavingsAccount::class);
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }

    public function agent()
    {
        return $this->hasOne(Agent::class);
    }
}

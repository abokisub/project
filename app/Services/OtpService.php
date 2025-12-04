<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OtpService
{
    /**
     * Generate and store OTP for user.
     */
    public static function generate(User $user, string $type = 'register', int $expiresInMinutes = 10): string
    {
        // Delete any existing OTPs of the same type for this user
        Otp::where('user_id', $user->id)
            ->where('type', $type)
            ->where('used', false)
            ->delete();

        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        Otp::create([
            'user_id' => $user->id,
            'type' => $type,
            'code' => $otp,
            'expires_at' => Carbon::now()->addMinutes($expiresInMinutes),
            'used' => false,
        ]);

        return $otp;
    }

    /**
     * Verify OTP.
     */
    public static function verify(User $user, string $code, string $type = 'register'): bool
    {
        $otp = Otp::where('user_id', $user->id)
            ->where('type', $type)
            ->where('code', $code)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return false;
        }

        // Mark as used
        $otp->update(['used' => true, 'used_at' => Carbon::now()]);

        return true;
    }

    /**
     * Check if user has valid OTP.
     */
    public static function hasValidOtp(User $user, string $type = 'register'): bool
    {
        return Otp::where('user_id', $user->id)
            ->where('type', $type)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->exists();
    }
}


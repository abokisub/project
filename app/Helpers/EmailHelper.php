<?php

namespace App\Helpers;

use App\Mail\RegisterOtpMail;
use App\Mail\WelcomeMail;
use App\Mail\LoginOtpMail;
use App\Mail\PasswordResetOtpMail;
use App\Mail\TransactionReceiptMail;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailHelper
{
    /**
     * Send registration OTP email.
     */
    public static function sendRegisterOtp(User $user, string $otpCode, Request $request, int $expiresIn = 1): void
    {
        if (!$user->email) {
            \Log::warning("Cannot send OTP email: User {$user->id} has no email address");
            return; // Skip if no email
        }

        try {
            $clientInfo = EmailService::getClientInfo($request);

            \Log::info("Attempting to send OTP email to: {$user->email}");
            \Log::info("Mail driver: " . config('mail.default'));
            
            Mail::to($user->email)->send(
                new RegisterOtpMail(
                    $user->first_name,
                    $otpCode,
                    $expiresIn,
                    $clientInfo['ip_address'],
                    $clientInfo['location'],
                    $clientInfo['browser']
                )
            );
            
            \Log::info("OTP email sent successfully to: {$user->email}");
        } catch (\Exception $e) {
            \Log::error("Failed to send OTP email to {$user->email}: " . $e->getMessage());
            \Log::error("Exception: " . get_class($e));
            throw $e; // Re-throw so controller can handle it
        }
    }

    /**
     * Send welcome email.
     */
    public static function sendWelcome(User $user): void
    {
        if (!$user->email) {
            return; // Skip if no email
        }

        Mail::to($user->email)->send(
            new WelcomeMail($user->first_name)
        );
    }

    /**
     * Send login OTP email.
     */
    public static function sendLoginOtp(User $user, string $otpCode, Request $request, int $expiresIn = 1): void
    {
        if (!$user->email) {
            return; // Skip if no email
        }

        $clientInfo = EmailService::getClientInfo($request);

        Mail::to($user->email)->send(
            new LoginOtpMail(
                $user->first_name,
                $otpCode,
                $expiresIn,
                $clientInfo['ip_address'],
                $clientInfo['location'],
                $clientInfo['browser']
            )
        );
    }

    /**
     * Send password reset OTP email.
     */
    public static function sendPasswordResetOtp(User $user, string $otpCode, Request $request, int $expiresIn = 1): void
    {
        if (!$user->email) {
            return; // Skip if no email
        }

        $clientInfo = EmailService::getClientInfo($request);

        Mail::to($user->email)->send(
            new PasswordResetOtpMail(
                $user->first_name,
                $otpCode,
                $expiresIn,
                $clientInfo['ip_address'],
                $clientInfo['location'],
                $clientInfo['browser']
            )
        );
    }

    /**
     * Send transaction receipt email.
     */
    public static function sendTransactionReceipt(
        User $user,
        string $transactionId,
        string $transactionType,
        float $amount,
        float $fee,
        float $total,
        string $status,
        string $date,
        ?string $description,
        string $party,
        float $newBalance
    ): void {
        if (!$user->email) {
            return; // Skip if no email
        }

        Mail::to($user->email)->send(
            new TransactionReceiptMail(
                $user->first_name,
                $transactionId,
                $transactionType,
                $amount,
                $fee,
                $total,
                $status,
                $date,
                $description,
                $party,
                $newBalance
            )
        );
    }
}


<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\BellBankService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateVirtualAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $useDirectorBvn;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $useDirectorBvn = false)
    {
        $this->userId = $userId;
        $this->useDirectorBvn = $useDirectorBvn;
    }

    /**
     * Execute the job.
     */
    public function handle(BellBankService $bellBankService): void
    {
        try {
            $user = User::findOrFail($this->userId);

            // Check if user already has a virtual account
            if ($user->bellbankAccount) {
                Log::info("User {$this->userId} already has a virtual account");
                return;
            }

            // Only create if OTP is verified (phone_verified_at is set)
            if (!$user->phone_verified_at && !$user->email_verified_at) {
                Log::info("User {$this->userId} has not verified OTP yet, skipping virtual account creation");
                return;
            }

            // Determine if we should use director BVN
            // Use director BVN if: explicitly set OR user doesn't have BVN
            $shouldUseDirectorBvn = $this->useDirectorBvn || empty($user->bvn);
            
            $bellBankService->createVirtualAccount($this->userId, $shouldUseDirectorBvn, [
                'creation_source' => 'auto_registration'
            ]);
            Log::info("Virtual account created successfully for user {$this->userId} using " . ($shouldUseDirectorBvn ? 'director BVN' : 'user BVN'));
        } catch (\Exception $e) {
            Log::error("Failed to create virtual account for user {$this->userId}: " . $e->getMessage());
            throw $e;
        }
    }
}


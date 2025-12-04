<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\BellBankService;
use App\Jobs\CreateVirtualAccountJob;

class TestVirtualAccount extends Command
{
    protected $signature = 'test:virtual-account {user_id?}';
    protected $description = 'Test virtual account creation for a user';

    public function handle(BellBankService $bellBankService)
    {
        $userId = $this->argument('user_id');
        
        if (!$userId) {
            $user = User::first();
            if (!$user) {
                $this->error('No users found in database');
                return 1;
            }
            $userId = $user->id;
        } else {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found");
                return 1;
            }
        }

        $this->info("=== User Information ===");
        $this->line("ID: {$user->id}");
        $this->line("Name: {$user->name}");
        $this->line("Email: {$user->email}");
        $this->line("Phone: " . ($user->phone ?? 'N/A'));
        $this->line("BVN: " . ($user->bvn ? 'SET' : 'NOT SET'));
        $this->line("KYC Status: {$user->kyc_status}");
        $this->line("Tier: {$user->user_tier}");
        $this->line("Email Verified: " . ($user->email_verified_at ? 'YES' : 'NO'));
        $this->line("Phone Verified: " . ($user->phone_verified_at ? 'YES' : 'NO'));

        $this->newLine();
        $this->info("=== Virtual Account Status ===");
        $account = $user->bellbankAccount;
        
        if ($account) {
            $this->line("Account Number: {$account->account_number}");
            $this->line("Virtual Account ID: {$account->virtual_account_id}");
            $this->line("Bank: {$account->bank_name}");
            $this->line("Created by Director: " . ($account->created_by_director ? 'YES' : 'NO'));
            $this->line("Status: {$account->status}");
            $this->newLine();
            $this->warn("User already has a virtual account!");
            return 0;
        } else {
            $this->line("No virtual account found");
        }

        $this->newLine();
        $this->info("=== Testing Virtual Account Creation ===");
        
        // Check if user is verified
        if (!$user->phone_verified_at && !$user->email_verified_at) {
            $this->warn("User is not verified. Marking as verified for test...");
            $user->update([
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);
        }

        // Determine if we should use director BVN
        $useDirectorBvn = empty($user->bvn);
        $this->line("Will use Director BVN: " . ($useDirectorBvn ? 'YES' : 'NO'));

        $this->newLine();
        if ($this->confirm('Do you want to create a virtual account now?', true)) {
            try {
                $this->info("Creating virtual account...");
                $account = $bellBankService->createVirtualAccount($user->id, $useDirectorBvn);
                
                $this->newLine();
                $this->info("✅ Virtual account created successfully!");
                $this->line("Account Number: {$account->account_number}");
                $this->line("Virtual Account ID: {$account->virtual_account_id}");
                $this->line("Bank: {$account->bank_name}");
                $this->line("Created by Director: " . ($account->created_by_director ? 'YES' : 'NO'));
                $this->line("Status: {$account->status}");
                
                return 0;
            } catch (\Exception $e) {
                $this->error("❌ Failed to create virtual account: " . $e->getMessage());
                $this->error("Stack trace: " . $e->getTraceAsString());
                return 1;
            }
        } else {
            $this->info("Virtual account creation cancelled");
            return 0;
        }
    }
}


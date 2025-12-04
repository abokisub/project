<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ShowVirtualAccount extends Command
{
    protected $signature = 'show:virtual-account {user_id?}';
    protected $description = 'Show virtual account details for a user';

    public function handle()
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

        $this->newLine();
        $this->info("=== Virtual Account Details ===");
        $account = $user->bellbankAccount;
        
        if ($account) {
            $this->line("Account Number: {$account->account_number}");
            $this->line("Virtual Account ID: {$account->virtual_account_id}");
            $this->line("Bank: {$account->bank_name}");
            $this->line("Created by Director: " . ($account->created_by_director ? 'YES ✅' : 'NO'));
            $this->line("Status: {$account->status}");
            
            if ($account->metadata) {
                $this->newLine();
                $this->info("=== Metadata ===");
                $this->line(json_encode($account->metadata, JSON_PRETTY_PRINT));
            }
        } else {
            $this->warn("No virtual account found for this user");
        }

        return 0;
    }
}


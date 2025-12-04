<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Wallet;

class SeedSampleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kobopoint:seed-sample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed sample data for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding sample data...');

        // Create sample users if they don't exist
        $users = [
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@kobopoint.com',
                'phone' => '+2348000000001',
                'password' => bcrypt('password'),
                'kyc_status' => 'approved',
                'user_tier' => 'tier5',
            ],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@kobopoint.com',
                'phone' => '+2348000000002',
                'password' => bcrypt('password'),
                'kyc_status' => 'approved',
                'user_tier' => 'tier1',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Create wallet if it doesn't exist
            if (!$user->wallet) {
                Wallet::create([
                    'user_id' => $user->id,
                    'currency' => 'NGN',
                    'balance' => 100000, // 1000 NGN in kobo
                    'status' => 'active',
                ]);
            }

            $this->info("Created/Updated user: {$user->email}");
        }

        $this->info('Sample data seeded successfully!');
        return 0;
    }
}


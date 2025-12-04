<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure admin role exists
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@kobopoint.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'phone' => '+2348000000001',
                'password' => Hash::make('admin123'),
                'kyc_status' => 'approved',
                'user_tier' => 'tier5',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                // api_key and app_key will be auto-generated
            ]
        );

        // Assign admin role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create wallet for admin if it doesn't exist
        if (!$admin->wallet) {
            Wallet::create([
                'user_id' => $admin->id,
                'currency' => 'NGN',
                'balance' => 0,
                'status' => 'active',
            ]);
        }

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@kobopoint.com');
        $this->command->info('Password: admin123');
        $this->command->info('Login URL: /secure/app');
    }
}


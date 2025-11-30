<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Wallet permissions
            'wallet.view',
            'wallet.fund',
            'wallet.transfer',
            'wallet.withdraw',
            
            // Thrift permissions
            'thrift.create',
            'thrift.join',
            'thrift.contribute',
            'thrift.manage',
            
            // Savings permissions
            'savings.create',
            'savings.deposit',
            'savings.withdraw',
            
            // Payment permissions
            'payment.send',
            'payment.receive',
            'payment.bank-transfer',
            'payment.qr',
            
            // KYC permissions
            'kyc.submit',
            'kyc.view',
            'kyc.approve',
            'kyc.reject',
            
            // Admin permissions
            'admin.users.view',
            'admin.users.manage',
            'admin.transactions.view',
            'admin.kyc.review',
            'admin.settings.manage',
            'admin.reports.view',
            
            // Merchant permissions
            'merchant.dashboard',
            'merchant.qr.generate',
            'merchant.settlement',
            'merchant.transactions.view',
            
            // Agent permissions
            'agent.dashboard',
            'agent.cash-in',
            'agent.cash-out',
            'agent.commissions.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin Role - Full access
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Tier 1 - Basic user (lowest tier)
        $tier1 = Role::firstOrCreate(['name' => 'tier1']);
        $tier1->givePermissionTo([
            'wallet.view',
            'wallet.fund',
            'wallet.transfer',
            'thrift.create',
            'thrift.join',
            'thrift.contribute',
            'savings.create',
            'savings.deposit',
            'savings.withdraw',
            'payment.send',
            'payment.receive',
            'payment.qr',
            'kyc.submit',
            'kyc.view',
        ]);

        // Tier 2 - Standard user
        $tier2 = Role::firstOrCreate(['name' => 'tier2']);
        $tier2->givePermissionTo([
            'wallet.view',
            'wallet.fund',
            'wallet.transfer',
            'wallet.withdraw',
            'thrift.create',
            'thrift.join',
            'thrift.contribute',
            'savings.create',
            'savings.deposit',
            'savings.withdraw',
            'payment.send',
            'payment.receive',
            'payment.bank-transfer',
            'payment.qr',
            'kyc.submit',
            'kyc.view',
        ]);

        // Tier 3 - Premium user
        $tier3 = Role::firstOrCreate(['name' => 'tier3']);
        $tier3->givePermissionTo([
            'wallet.view',
            'wallet.fund',
            'wallet.transfer',
            'wallet.withdraw',
            'thrift.create',
            'thrift.join',
            'thrift.contribute',
            'thrift.manage',
            'savings.create',
            'savings.deposit',
            'savings.withdraw',
            'payment.send',
            'payment.receive',
            'payment.bank-transfer',
            'payment.qr',
            'kyc.submit',
            'kyc.view',
        ]);

        // Tier 4 - Gold user
        $tier4 = Role::firstOrCreate(['name' => 'tier4']);
        $tier4->givePermissionTo([
            'wallet.view',
            'wallet.fund',
            'wallet.transfer',
            'wallet.withdraw',
            'thrift.create',
            'thrift.join',
            'thrift.contribute',
            'thrift.manage',
            'savings.create',
            'savings.deposit',
            'savings.withdraw',
            'payment.send',
            'payment.receive',
            'payment.bank-transfer',
            'payment.qr',
            'kyc.submit',
            'kyc.view',
        ]);

        // Tier 5 - Platinum user (highest tier)
        $tier5 = Role::firstOrCreate(['name' => 'tier5']);
        $tier5->givePermissionTo([
            'wallet.view',
            'wallet.fund',
            'wallet.transfer',
            'wallet.withdraw',
            'thrift.create',
            'thrift.join',
            'thrift.contribute',
            'thrift.manage',
            'savings.create',
            'savings.deposit',
            'savings.withdraw',
            'payment.send',
            'payment.receive',
            'payment.bank-transfer',
            'payment.qr',
            'kyc.submit',
            'kyc.view',
        ]);

        // Merchant Role
        $merchant = Role::firstOrCreate(['name' => 'merchant']);
        $merchant->givePermissionTo([
            'wallet.view',
            'wallet.fund',
            'wallet.transfer',
            'wallet.withdraw',
            'payment.receive',
            'payment.qr',
            'merchant.dashboard',
            'merchant.qr.generate',
            'merchant.settlement',
            'merchant.transactions.view',
            'kyc.submit',
            'kyc.view',
        ]);

        // Agent Role
        $agent = Role::firstOrCreate(['name' => 'agent']);
        $agent->givePermissionTo([
            'wallet.view',
            'wallet.fund',
            'wallet.transfer',
            'wallet.withdraw',
            'agent.dashboard',
            'agent.cash-in',
            'agent.cash-out',
            'agent.commissions.view',
            'thrift.create',
            'thrift.manage',
            'kyc.submit',
            'kyc.view',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}


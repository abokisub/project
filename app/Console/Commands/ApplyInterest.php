<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SavingsAccount;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApplyInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:apply';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply interest to savings accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Applying interest to savings accounts...');

        $accounts = SavingsAccount::where('status', 'active')
            ->where('interest_rate', '>', 0)
            ->get();

        $processed = 0;
        foreach ($accounts as $account) {
            try {
                DB::transaction(function () use ($account) {
                    $account->lockForUpdate();
                    $interest = ($account->current_amount * $account->interest_rate) / 100;
                    
                    if ($interest > 0) {
                        $account->current_amount += $interest;
                        $account->save();

                        // Create interest transaction
                        \App\Models\SavingsTransaction::create([
                            'savings_account_id' => $account->id,
                            'amount' => $interest,
                            'type' => 'interest',
                            'status' => 'completed',
                        ]);
                    }
                });
                $processed++;
            } catch (\Exception $e) {
                $this->error("Failed to apply interest to account {$account->id}: " . $e->getMessage());
            }
        }

        $this->info("Applied interest to {$processed} savings accounts.");
        return 0;
    }
}


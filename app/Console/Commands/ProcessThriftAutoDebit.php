<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ThriftGroup;
use App\Services\ThriftService;
use Carbon\Carbon;

class ProcessThriftAutoDebit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thrift:auto-debit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process auto-debit for active thrift groups';

    protected $thriftService;

    public function __construct(ThriftService $thriftService)
    {
        parent::__construct();
        $this->thriftService = $thriftService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing thrift auto-debits...');

        $today = Carbon::today();
        
        // Get active thrift groups
        $groups = ThriftGroup::where('status', 'active')
            ->where('start_date', '<=', $today)
            ->get();

        $processed = 0;
        foreach ($groups as $group) {
            try {
                // Check if it's time for contribution based on frequency
                $shouldProcess = false;
                
                if ($group->frequency === 'daily') {
                    $shouldProcess = true;
                } elseif ($group->frequency === 'weekly') {
                    $shouldProcess = $today->isDayOfWeek(1); // Monday
                } elseif ($group->frequency === 'monthly') {
                    $shouldProcess = $today->day === 1; // First day of month
                }

                if ($shouldProcess) {
                    $this->thriftService->processAutoDebit($group->id);
                    $processed++;
                    $this->info("Processed auto-debit for group: {$group->name}");
                }
            } catch (\Exception $e) {
                $this->error("Failed to process group {$group->id}: " . $e->getMessage());
            }
        }

        $this->info("Processed {$processed} thrift groups.");
        return 0;
    }
}


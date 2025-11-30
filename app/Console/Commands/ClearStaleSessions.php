<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClearStaleSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:stale-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear stale user sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing stale sessions...');

        $expired = DB::table('sessions')
            ->where('last_activity', '<', Carbon::now()->subDays(30)->timestamp)
            ->delete();

        $this->info("Cleared {$expired} stale sessions.");
        return 0;
    }
}


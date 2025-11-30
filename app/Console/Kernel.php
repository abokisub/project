<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Thrift auto-debit - run every minute (will check frequency internally)
        $schedule->command('thrift:auto-debit')->everyMinute();

        // Daily settlement run
        $schedule->command('settlement:run')->daily();

        // BellBank reconciliation - run every hour
        $schedule->command('reconcile:bellbank')->hourly();

        // Clear stale sessions - run daily
        $schedule->command('clear:stale-sessions')->daily();

        // Interest computation - run monthly
        $schedule->command('interest:apply')->monthly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}


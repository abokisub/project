<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Jobs\CreateVirtualAccountJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateVirtualAccountListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        // Dispatch job to create virtual account after OTP verification
        // Use director BVN by default for new users (they can upgrade KYC later)
        if (env('AUTO_CREATE_VIRTUAL_ACCOUNT', true)) {
            // Use director BVN if user doesn't have BVN yet
            $useDirectorBvn = empty($event->user->bvn);
            
            CreateVirtualAccountJob::dispatch($event->user->id, $useDirectorBvn)
                ->delay(now()->addMinutes(1));
        }
    }
}


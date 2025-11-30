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
        // This will be triggered when user verifies phone/email
        if (env('AUTO_CREATE_VIRTUAL_ACCOUNT', true)) {
            CreateVirtualAccountJob::dispatch($event->user->id, false)
                ->delay(now()->addMinutes(1));
        }
    }
}


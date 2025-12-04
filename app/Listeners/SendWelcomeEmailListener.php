<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Helpers\EmailHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        // Send welcome email after successful registration
        EmailHelper::sendWelcome($event->user);
    }
}

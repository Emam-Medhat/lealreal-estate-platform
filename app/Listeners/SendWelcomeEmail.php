<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Notifications\WelcomeNotification;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserRegistered $event): void
    {
        try {
            // Send notification which handles the email
            $event->user->notify(new WelcomeNotification($event->user));
        } catch (\Exception $e) {
            // Log error but don't stop registration process
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Listeners;

use App\Events\PasswordReset;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
// use App\Notifications\PasswordResetNotification; // We might need this, or standard Laravel reset

class SendPasswordResetEmail
{
    public function handle(PasswordReset $event): void
    {
        // Typically Laravel User model has sendPasswordResetNotification method
        // But if we want to hook into event:
        // $event->user->notify(new PasswordResetNotification($token)); 
        // Note: PasswordReset event in Laravel usually fires AFTER reset.
        // If we want to send the link, we usually do it in the Service.

        // Assuming this event is "Reset Requested" or "Reset Complete"?
        // If "Reset Complete", we send a confirmation email "Your password has been changed".

        $event->user->notify(new \App\Notifications\PasswordResetNotification());
    }
}

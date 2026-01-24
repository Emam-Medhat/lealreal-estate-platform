<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetupDefaultPreferences
{
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;
        $user->update([
            'notifications_preferences' => [
                'email' => true,
                'sms' => false,
                'security' => true,
                'marketing' => false
            ],
            // 'currency' => 'USD', // already default
            // 'language' => 'en', // already default
        ]);
    }
}

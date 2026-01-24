<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\NewDeviceLoginNotification;

class NotifyNewDevice
{
    public function handle(UserLoggedIn $event): void
    {
        // Logic to check if device is new
        $user = $event->user;
        $currentDevice = request()->userAgent();

        // If logic determines new device (simplified check here)
        if ($user->last_login_device && $user->last_login_device !== $currentDevice) {
            $user->notify(new NewDeviceLoginNotification($currentDevice, request()->ip()));
        }
    }
}

<?php

namespace App\Observers;

use App\Models\Developer;
use App\Models\User;
use App\Notifications\NewDeveloperNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class DeveloperObserver
{
    /**
     * Handle the Developer "created" event.
     */
    public function created(Developer $developer): void
    {
        try {
            $admins = User::whereIn('user_type', ['admin', 'super_admin'])->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewDeveloperNotification($developer));
            }
        } catch (\Exception $e) {
            Log::warning('Could not send developer creation notification: ' . $e->getMessage());
        }
    }
}

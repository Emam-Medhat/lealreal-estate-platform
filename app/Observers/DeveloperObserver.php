<?php

namespace App\Observers;

use App\Models\Developer;
use App\Models\User;
use App\Notifications\DeveloperCreated;
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
            $admins = User::whereIn('role', ['admin', 'manager'])->get();
            Notification::send($admins, new DeveloperCreated($developer));
        } catch (\Exception $e) {
            Log::warning('Could not send developer creation notification: ' . $e->getMessage());
        }
    }
}

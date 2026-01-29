<?php

namespace App\Observers;

use App\Models\AgentTask;
use App\Models\User;
use App\Notifications\TaskCreated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class AgentTaskObserver
{
    /**
     * Handle the AgentTask "created" event.
     */
    public function created(AgentTask $task): void
    {
        try {
            // Notify the assigned agent if different from creator
            if ($task->agent && $task->agent->user_id !== auth()->id()) {
                $task->agent->user->notify(new TaskCreated($task));
            }

            // Notify admins and managers
            $admins = User::whereIn('role', ['admin', 'manager'])->get();
            Notification::send($admins, new TaskCreated($task));
            
        } catch (\Exception $e) {
            Log::warning('Could not send task creation notification: ' . $e->getMessage());
        }
    }
}

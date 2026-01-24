<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Notifications\SuspiciousActivityNotification;
use Illuminate\Support\Facades\Notification;

class NotifyAdminSuspiciousActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function handle(): void
    {
        // Find admins
        $admins = User::where('user_type', 'admin')->get();

        Notification::send($admins, new SuspiciousActivityNotification('Admin Alert: ' . $this->details));
    }
}

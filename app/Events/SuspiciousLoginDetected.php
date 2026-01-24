<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SuspiciousLoginDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $ip;
    public $device;

    public function __construct(User $user, $ip, $device)
    {
        $this->user = $user;
        $this->ip = $ip;
        $this->device = $device;
    }
}

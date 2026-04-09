<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SocialAccountLinked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $provider;

    public function __construct(User $user, string $provider)
    {
        $this->user = $user;
        $provider = $provider;
    }
}

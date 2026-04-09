<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AvatarChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $oldAvatar;
    public $newAvatar;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, ?string $oldAvatar, string $newAvatar)
    {
        $this->user = $user;
        $this->oldAvatar = $oldAvatar;
        $this->newAvatar = $newAvatar;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->user->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'avatar.changed';
    }
}

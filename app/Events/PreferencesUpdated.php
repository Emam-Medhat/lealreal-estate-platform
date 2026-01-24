<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PreferencesUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $preferences;
    public $updatedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, array $preferences, ?User $updatedBy = null)
    {
        $this->user = $user;
        $this->preferences = $preferences;
        $this->updatedBy = $updatedBy;
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
        return 'preferences.updated';
    }
}

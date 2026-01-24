<?php

namespace App\Events;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AgentRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $agent;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(Agent $agent, User $user = null)
    {
        $this->agent = $agent;
        $this->user = $user ?? ($agent->user ?? null);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('agent.' . $this->agent->id),
            new PrivateChannel('user.' . $this->user->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'agent.registered';
    }
}

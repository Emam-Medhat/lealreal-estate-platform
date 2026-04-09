<?php

namespace App\Events;

use App\Models\Agent;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommissionEarned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $commission;
    public $agent;
    public $earnedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Commission $commission, Agent $agent, User $earnedBy = null)
    {
        $this->commission = $commission;
        $this->agent = $agent;
        $this->earnedBy = $earnedBy ?? ($commission->created_by ?? null);
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
            new PrivateChannel('user.' . $this->earnedBy->id),
            new PrivateChannel('commission.' . $this->commission->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'commission.earned';
    }
}

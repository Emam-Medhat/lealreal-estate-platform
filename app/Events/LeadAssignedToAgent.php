<?php

namespace App\Events;

use App\Models\Agent;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadAssignedToAgent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lead;
    public $agent;
    public $assignedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Lead $lead, Agent $agent, User $assignedBy = null)
    {
        $this->lead = $lead;
        $this->agent = $agent;
        $this->assignedBy = $assignedBy ?? ($lead->assigned_by ?? null);
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
            new PrivateChannel('user.' . $this->assignedBy->id),
            new PrivateChannel('lead.' . $this->lead->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'lead.assigned_to_agent';
    }
}

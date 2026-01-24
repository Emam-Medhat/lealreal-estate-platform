<?php

namespace App\Events;

use App\Models\Agent;
use App\Models\Lead;
use App\Models\Client;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadConvertedToClient
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lead;
    public $client;
    public $convertedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Lead $lead, Client $client, User $convertedBy = null)
    {
        $this->lead = $lead;
        $this->client = $client;
        $this->convertedBy = $convertedBy ?? ($lead->converted_by ?? null);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('agent.' . $this->lead->assigned_agent_id),
            new PrivateChannel('user.' . $this->convertedBy->id),
            new PrivateChannel('client.' . $this->client->id),
            new PrivateChannel('lead.' . $this->lead->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'lead.converted_to_client';
    }
}

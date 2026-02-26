<?php

namespace App\Events;

use App\Models\IntelligenceAlert;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IntelligenceAlertBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public IntelligenceAlert $alert;
    public string $broadcastQueue = 'intelligence';

    public function __construct(IntelligenceAlert $alert)
    {
        $this->alert = $alert;
    }

    public function broadcastOn(): Channel
    {
        $prefix = config('intelligence.realtime.channel_prefix', 'alerts.user.');
        return new PrivateChannel($prefix . $this->alert->user_id);
    }

    public function broadcastAs(): string
    {
        return 'IntelligenceAlertBroadcast';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->alert->id,
            'type' => $this->alert->type,
            'property_id' => $this->alert->property_id,
            'data' => $this->alert->data ?? [],
            'created_at' => $this->alert->created_at?->toISOString(),
        ];
    }
}

<?php

namespace App\Events;

use App\Models\Agent;
use App\Models\AgentReview;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AgentReviewReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $review;
    public $agent;
    public $reviewedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(AgentReview $review, Agent $agent, User $reviewedBy = null)
    {
        $this->review = $review;
        $this->agent = $agent;
        $this->reviewedBy = $reviewedBy ?? ($review->created_by ?? null);
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
            new PrivateChannel('user.' . $this->reviewedBy->id),
            new PrivateChannel('agent_review.' . $this->review->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'agent.review.received';
    }
}

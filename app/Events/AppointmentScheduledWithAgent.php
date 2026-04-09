<?php

namespace App\Events;

use App\Models\Agent;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentScheduledWithAgent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $appointment;
    public $agent;
    public $scheduledBy;

    /**
     * Create a new event instance.
     */
    public function __construct(Appointment $appointment, Agent $agent, User $scheduledBy = null)
    {
        $this->appointment = $appointment;
        $this->agent = $agent;
        $this->scheduledBy = $scheduledBy ?? ($appointment->created_by ?? null);
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
            new PrivateChannel('user.' . $this->scheduledBy->id),
            new PrivateChannel('appointment.' . $this->appointment->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'appointment.scheduled_with_agent';
    }
}

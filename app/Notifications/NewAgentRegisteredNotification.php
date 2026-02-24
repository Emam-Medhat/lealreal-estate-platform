<?php

namespace App\Notifications;

use App\Models\Agent;
use Illuminate\Bus\Queueable;
class NewAgentRegisteredNotification extends Notification
{
    use Queueable;

    public $agent;

    /**
     * Create a new notification instance.
     */
    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'agent_id' => $this->agent->id,
            'agent_name' => $this->agent->name,
            'license_number' => $this->agent->license_number,
            'created_at' => now(),
            'message' => 'وكيل جديد سجل في المنصة: ' . $this->agent->name,
            'url' => route('admin.agents.show', $this->agent->id)
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'agent_id' => $this->agent->id,
            'title' => 'وكيل جديد',
            'message' => 'وكيل جديد سجل في المنصة: ' . $this->agent->name,
            'url' => route('admin.agents.show', $this->agent->id),
            'time' => now()->diffForHumans(),
        ]);
    }
}

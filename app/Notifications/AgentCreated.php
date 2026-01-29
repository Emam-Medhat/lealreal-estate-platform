<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AgentCreated extends Notification
{
    use Queueable;

    public $agent;

    /**
     * Create a new notification instance.
     */
    public function __construct($agent)
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
            'title' => 'تم إضافة وكيل جديد',
            'message' => "تم إضافة الوكيل '{$this->agent->name}' إلى النظام",
            'type' => 'agent_created',
            'agent_id' => $this->agent->id,
            'agent_name' => $this->agent->name,
            'company_name' => $this->agent->company ? $this->agent->company->name : null,
            'created_by' => auth()->id(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function broadcastType(): string
    {
        return 'agent.created';
    }
}

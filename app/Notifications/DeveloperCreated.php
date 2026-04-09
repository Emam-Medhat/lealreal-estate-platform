<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeveloperCreated extends Notification
{
    use Queueable;

    public $developer;

    /**
     * Create a new notification instance.
     */
    public function __construct($developer)
    {
        $this->developer = $developer;
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
            'title' => 'تم إضافة مطور جديد',
            'message' => "تم إضافة المطور '{$this->developer->company_name}' إلى النظام",
            'type' => 'developer_created',
            'developer_id' => $this->developer->id,
            'company_name' => $this->developer->company_name,
            'created_by' => auth()->id(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function broadcastType(): string
    {
        return 'developer.created';
    }
}

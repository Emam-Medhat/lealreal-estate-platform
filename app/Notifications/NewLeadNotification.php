<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewLeadNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $lead;

    /**
     * Create a new notification instance.
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
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
            'lead_id' => $this->lead->id,
            'title' => 'عميل محتمل جديد',
            'message' => 'تمت إضافة عميل محتمل جديد: ' . $this->lead->name,
            'url' => route('admin.leads.show', $this->lead->id),
            'icon' => 'fa-user-tie',
            'color' => '#f39c12', // Orange color for leads
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'title' => 'عميل محتمل جديد',
            'message' => 'تمت إضافة عميل محتمل جديد: ' . $this->lead->name,
            'url' => route('admin.leads.show', $this->lead->id),
            'icon' => 'fa-user-tie',
            'color' => '#f39c12',
            'time' => now()->diffForHumans(),
        ]);
    }
}

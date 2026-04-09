<?php

namespace App\Notifications;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class LeadAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $lead;
    public $assigner;

    /**
     * Create a new notification instance.
     */
    public function __construct(Lead $lead, User $assigner)
    {
        $this->lead = $lead;
        $this->assigner = $assigner;
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
            'title' => 'تم إسناد عميل محتمل إليك',
            'message' => "قام {$this->assigner->name} بإسناد العميل المحتمل {$this->lead->name} إليك.",
            'url' => route('admin.leads.show', $this->lead->id),
            'icon' => 'fa-user-check',
            'color' => '#16a085', // Turquoise color for assignments
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'title' => 'تم إسناد عميل محتمل إليك',
            'message' => "قام {$this->assigner->name} بإسناد العميل المحتمل {$this->lead->name} إليك.",
            'url' => route('admin.leads.show', $this->lead->id),
            'icon' => 'fa-user-check',
            'color' => '#16a085',
            'time' => now()->diffForHumans(),
        ]);
    }
}

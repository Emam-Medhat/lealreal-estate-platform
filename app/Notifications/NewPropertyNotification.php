<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewPropertyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $property;

    /**
     * Create a new notification instance.
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
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
            'property_id' => $this->property->id,
            'title' => $this->property->title,
            'agent_name' => $this->property->agent ? $this->property->agent->name : 'Unknown Agent',
            'created_at' => now(),
            'message' => 'تم إضافة عقار جديد: ' . $this->property->title,
            'url' => route('admin.properties.show', $this->property->id)
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'property_id' => $this->property->id,
            'title' => $this->property->title,
            'message' => 'تم إضافة عقار جديد: ' . $this->property->title,
            'url' => route('admin.properties.show', $this->property->id),
            'time' => now()->diffForHumans(),
        ]);
    }
}

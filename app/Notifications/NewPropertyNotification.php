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
            'title' => 'عقار جديد أضيف',
            'message' => 'تمت إضافة عقار جديد بعنوان: ' . $this->property->title,
            'url' => route('admin.properties.show', $this->property->id),
            'icon' => 'fa-building',
            'color' => '#3498db', // Blue color for properties
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'title' => 'عقار جديد أضيف',
            'message' => 'تمت إضافة عقار جديد بعنوان: ' . $this->property->title,
            'url' => route('admin.properties.show', $this->property->id),
            'icon' => 'fa-building',
            'color' => '#3498db',
            'time' => now()->diffForHumans(),
        ]);
    }
}

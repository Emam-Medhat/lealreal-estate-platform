<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropertyCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $property;

    /**
     * Create a new notification instance.
     */
    public function __construct($property)
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
            'title' => 'تم إضافة عقار جديد',
            'message' => "تم إضافة العقار '{$this->property->title}' إلى النظام",
            'type' => 'property_created',
            'property_id' => $this->property->id,
            'property_title' => $this->property->title,
            'property_type' => $this->property->type,
            'property_price' => $this->property->price,
            'created_by' => auth()->id(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function broadcastType(): string
    {
        return 'property.created';
    }
}

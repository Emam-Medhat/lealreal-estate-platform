<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewInquiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $inquiry;

    /**
     * Create a new notification instance.
     */
    public function __construct(Inquiry $inquiry)
    {
        $this->inquiry = $inquiry;
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
            'inquiry_id' => $this->inquiry->id,
            'title' => 'استفسار جديد على عقار',
            'message' => "لديك استفسار جديد من {$this->inquiry->name} بخصوص العقار '{$this->inquiry->property->title}'.",
            'url' => route('admin.inquiries.show', $this->inquiry->id),
            'icon' => 'fa-question-circle',
            'color' => '#e67e22', // Orange for inquiries
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'title' => 'استفسار جديد على عقار',
            'message' => "لديك استفسار جديد من {$this->inquiry->name} بخصوص العقار '{$this->inquiry->property->title}'.",
            'url' => route('admin.inquiries.show', $this->inquiry->id),
            'icon' => 'fa-question-circle',
            'color' => '#e67e22',
            'time' => now()->diffForHumans(),
        ]);
    }
}

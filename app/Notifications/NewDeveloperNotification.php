<?php

namespace App\Notifications;

use App\Models\Developer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewDeveloperNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $developer;

    /**
     * Create a new notification instance.
     */
    public function __construct(Developer $developer)
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
            'developer_id' => $this->developer->id,
            'title' => 'مطور جديد انضم',
            'message' => 'تم تسجيل مطور جديد باسم: ' . $this->developer->name,
            'url' => route('admin.developers.show', $this->developer->id),
            'icon' => 'fa-code',
            'color' => '#2ecc71', // Green color for developers
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'title' => 'مطور جديد انضم',
            'message' => 'تم تسجيل مطور جديد باسم: ' . $this->developer->name,
            'url' => route('admin.developers.show', $this->developer->id),
            'icon' => 'fa-code',
            'color' => '#2ecc71',
            'time' => now()->diffForHumans(),
        ]);
    }
}

<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewUserRegisteredNotification extends Notification
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
            'user_id' => $this->user->id,
            'user_name' => $this->user->full_name ?? $this->user->name ?? $this->user->username,
            'user_email' => $this->user->email,
            'user_type' => $this->user->user_type,
            'created_at' => now(),
            'title' => 'مستخدم جديد',
            'message' => 'مستخدم جديد انضم للمنصة: ' . ($this->user->full_name ?? $this->user->name ?? $this->user->username),
            'url' => route('admin.users.show', $this->user->id),
            'icon' => 'user-plus',
            'color' => 'green'
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id, // Important for markAsRead
            'user_id' => $this->user->id,
            'title' => 'مستخدم جديد',
            'message' => 'مستخدم جديد انضم للمنصة: ' . ($this->user->full_name ?? $this->user->name ?? $this->user->username),
            'url' => route('admin.users.show', $this->user->id),
            'icon' => 'user-plus',
            'color' => 'green',
            'time' => now()->diffForHumans(),
        ]);
    }
}

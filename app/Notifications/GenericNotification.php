<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class GenericNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $notification;

    public function __construct(array $notification)
    {
        $this->notification = $notification;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage($this->notification);
    }

    public function toArray($notifiable): array
    {
        return $this->notification;
    }
}

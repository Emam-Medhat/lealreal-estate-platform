<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDeviceLoginNotification extends Notification
{
    use Queueable;

    public $device;
    public $ip;

    public function __construct($device, $ip)
    {
        $this->device = $device;
        $this->ip = $ip;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Login Detected')
            ->line('We detected a login from a new device.')
            ->line('Device: ' . $this->device)
            ->line('IP Address: ' . $this->ip)
            ->line('If this was you, you can ignore this email.')
            ->line('If this was not you, please secure your account immediately.');
    }
}

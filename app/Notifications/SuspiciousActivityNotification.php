<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuspiciousActivityNotification extends Notification
{
    use Queueable;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Suspicious Activity Detected')
            ->line('We detected suspicious activity on your account.')
            ->line('Details: ' . $this->details)
            ->action('Review Activity', url('/activity-log'))
            ->line('If this wasn\'t you, please change your password immediately.');
    }
}

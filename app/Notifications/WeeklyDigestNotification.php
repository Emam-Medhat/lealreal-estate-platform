<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WeeklyDigestNotification extends Notification
{
    use Queueable;

    protected $digestData;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $digestData)
    {
        $this->digestData = $digestData;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => '📊 ملخص نشاطك الأسبوعي',
            'message' => $this->getDigestMessage(),
            'type' => 'weekly_digest',
            'icon' => 'chart-bar',
            'color' => 'info',
            'data' => $this->digestData
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('📊 ملخص نشاطك الأسبوعي')
            ->view('emails.weekly-digest', [
                'user' => $notifiable,
                'digestData' => $this->digestData
            ]);
    }

    /**
     * Get digest message
     */
    private function getDigestMessage(): string
    {
        if (!$this->digestData['has_activity']) {
            return 'لم يكن لديك أي نشاط هذا الأسبوع';
        }
        
        $count = $this->digestData['activities_count'];
        
        if ($count <= 5) {
            return "لديك {$count} أنشطة هذا الأسبوع";
        } elseif ($count <= 20) {
            return "لديك {$count} نشاطًا هذا الأسبوع";
        } else {
            return "لديك {$count} نشاطًا هذا الأسبوع - نشاط رائع!";
        }
    }
}

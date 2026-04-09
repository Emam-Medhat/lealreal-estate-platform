<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProfileCompletedNotification extends Notification
{
    use Queueable;

    public $via;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->via = ['database', 'mail'];
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return $this->via;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => 'تهانينا! ملفك الشخصي مكتمل 100%',
            'message' => 'لقد أكملت جميع بيانات ملفك الشخصي. يمكنك الآن الاستفادة من جميع ميزات المنصة.',
            'type' => 'profile_completed',
            'icon' => 'check-circle',
            'color' => 'success',
            'data' => [
                'completion_percentage' => 100,
                'achievement_unlocked' => true,
                'next_steps' => [
                    'verify_kyc' => 'أكمل التحقق من الهوية',
                    'explore_features' => 'استكشف جميع ميزات المنصة',
                    'connect_agents' => 'تواصل مع الوكلاء'
                ]
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('🎉 ملفك الشخصي مكتمل بنسبة 100%')
            ->view('emails.profile-completed', [
                'user' => $notifiable,
                'completion_percentage' => 100,
                'next_steps' => [
                    'verify_kyc' => 'أكمل التحقق من الهوية',
                    'explore_features' => 'استكشف جميع ميزات المنصة',
                    'connect_agents' => 'تواصل مع الوكلاء'
                ]
            ]);
    }
}

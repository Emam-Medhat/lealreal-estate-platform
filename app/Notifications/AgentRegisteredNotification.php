<?php

namespace App\Notifications;

use App\Models\Agent;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AgentRegisteredNotification extends Notification
{
    use Queueable;

    protected $agent;
    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(Agent $agent, User $user = null)
    {
        $this->agent = $agent;
        $this->user = $user ?? ($agent->user ?? null);
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
            'title' => 'مرحباً بك في منصتنا',
            'message' => "تم تسجيلك كوكلاء في منصتنا. نحن سعداء بانضمامك لفريقنا.",
            'type' => 'agent_registered',
            'icon' => 'user-check',
            'color' => 'success',
            'data' => [
                'agent_id' => $this->agent->id,
                'agent_name' => $this->agent->name,
                'license_number' => $this->agent->license_number,
                'company_id' => $this->agent->company_id,
                'company_name' => $this->agent->company ? $this->agent->company->name : null,
                'registration_date' => $this->agent->created_at,
                'next_steps' => [
                    'complete_profile' => 'أكمل ملفك الشخصي',
                    'verify_license' => 'تحقق من رخصتك',
                    'add_portfolio' => 'أضف معرض أعمالك',
                    'attend_training' => 'حضور التدريب'
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
            ->subject('مرحباً بك في منصتنا')
            ->view('emails.agent-registered', [
                'agent' => $this->agent,
                'user' => $this->user
            ]);
    }
}

<?php

namespace App\Notifications;

use App\Models\Agent;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewLeadAssignedNotification extends Notification
{
    use Queueable;

    protected $lead;
    protected $agent;

    /**
     * Create a new notification instance.
     */
    public function __construct(Lead $lead, Agent $agent)
    {
        $this->lead = $lead;
        $this->agent = $agent;
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
            'title' => 'عميل جديد مخصص لك',
            'message' => "تم تخصيص العميل {$this->lead->title} لك. يرجى التواصل مع العميل في أقرب وقت.",
            'type' => 'new_lead_assigned',
            'icon' => 'user-plus',
            'color' => 'info',
            'data' => [
                'lead_id' => $this->lead->id,
                'lead_title' => $this->lead->title,
                'lead_value' => $this->lead->value,
                'lead_source' => $this->lead->source->name,
                'client_name' => $this->lead->client_name,
                'client_phone' => $this->lead->client_phone,
                'client_email' => $this->lead->client_email,
                'assigned_by' => $this->agent->name,
                'assigned_at' => now()
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('عميل جديد مخصص لك')
            ->view('emails.new-lead-assigned', [
                'agent' => $this->agent,
                'lead' => $this->lead
            ]);
    }
}

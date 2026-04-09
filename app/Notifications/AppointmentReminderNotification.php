<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification
{
    use Queueable;

    protected $appointment;
    protected $agent;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment, Agent $agent)
    {
        $this->appointment = $appointment;
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
            'title' => 'تذكير بموعد',
            'message' => "لديك موعد مع العميل {$this->appointment->client->name} في {$this->appointment->start_time->toDateTimeString()}",
            'type' => 'appointment_reminder',
            'icon' => 'calendar',
            'color' => 'warning',
            'data' => [
                'appointment_id' => $this->appointment->id,
                'client_name' => $this->appointment->client->name,
                'client_phone' => $this->appointment->client->phone,
                'appointment_time' => $this->appointment->start_time->toDateTimeString(),
                'appointment_location' => $this->appointment->location,
                'agent_name' => $this->agent->name,
                'reminder_sent_at' => now()
            ]
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('تذكير بموعد')
            ->view('emails.appointment-reminder', [
                'agent' => $this->agent,
                'appointment' => $this->appointment
            ]);
    }
}

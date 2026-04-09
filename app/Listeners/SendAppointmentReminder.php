<?php

namespace App\Listeners;

use App\Events\AppointmentScheduledWithAgent;
use App\Models\Appointment;
use App\Models\Agent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminder
{
    /**
     * Handle the event.
     */
    public function handle(AppointmentScheduledWithAgent $event): void
    {
        $appointment = $event->appointment;
        $agent = $event->agent;
        $scheduledBy = $event->scheduledBy;

        try {
            // Send reminder email to agent
            Mail::to($agent->email)->send(new \App\Mail\AppointmentReminderMail($appointment, $agent));

            // Create notification for agent
            $agent->notifications()->create([
                'title' => 'تذكير بموعد',
                'message' => "لديك موعد مع العميل {$appointment->client->name} في {$appointment->start_time->toDateTimeString()}",
                'type' => 'appointment_reminder',
                'data' => [
                    'appointment_id' => $appointment->id,
                    'client_name' => $appointment->client->name,
                    'client_phone' => $appointment->client->phone,
                    'appointment_time' => $appointment->start_time->toDateTimeString(),
                    'appointment_location' => $appointment->location,
                    'scheduled_by' => $scheduledBy->name,
                    'reminder_sent_at' => now()
                ]
            ]);

            Log::info('Appointment reminder sent to agent', [
                'agent_id' => $agent->id,
                'appointment_id' => $appointment->id,
                'client_id' => $appointment->client_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send appointment reminder', [
                'agent_id' => $agent->id,
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

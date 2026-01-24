<?php

namespace App\Jobs;

use App\Models\Agent;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminders implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 600;

    protected $agentId;
    protected $appointmentIds;

    /**
     * Create a new job instance.
     */
    public function __construct(int $agentId, array $appointmentIds = [])
    {
        $this->agentId = $agentId;
        $this->appointmentIds = $appointmentIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $agent = Agent::findOrFail($this->agentId);

            // Get upcoming appointments for the agent
            $appointments = $agent->appointments()
                ->whereIn('id', $this->appointmentIds)
                ->where('start_time', '>', now())
                ->where('status', 'scheduled')
                ->orderBy('start_time', 'asc')
                ->with(['client'])
                ->get();

            foreach ($appointments as $appointment) {
                // Send reminder email for appointment
                $this->sendAppointmentReminder($appointment, $agent);

                // Update reminder sent status
                $appointment->update([
                    'reminder_sent_at' => now()
                ]);
            }

            Log::info('Appointment reminders sent', [
                'agent_id' => $this->agentId,
                'appointment_count' => count($appointments),
                'sent_count' => count($appointments)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send appointment reminders', [
                'agent_id' => $this->agentId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Send reminder email for appointment
     */
    private function sendAppointmentReminder(Appointment $appointment, Agent $agent): void
    {
        try {
            Mail::to($agent->email)->send(new \App\Mail\AppointmentReminderMail($appointment, $agent));

            Log::info('Appointment reminder sent', [
                'agent_id' => $agent->id,
                'appointment_id' => $appointment->id,
                'client_id' => $appointment->client_id,
                'client_name' => $appointment->client->name,
                'appointment_time' => $appointment->start_time->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send appointment reminder', [
                'agent_id' => $agent->id,
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Appointment reminders job failed', [
            'agent_id' => $this->agentId,
            'error' => $exception->getMessage()
        ]);
    }
}

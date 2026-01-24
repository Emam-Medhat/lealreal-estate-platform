<?php

namespace App\Listeners;

use App\Events\LeadAssignedToAgent;
use App\Models\Agent;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class NotifyAgentNewLead
{
    /**
     * Handle the event.
     */
    public function handle(LeadAssignedToAgent $event): void
    {
        $lead = $event->lead;
        $agent = $event->agent;
        $assignedBy = $event->assignedBy;

        try {
            // Create notification for agent
            $agent->notifications()->create([
                'title' => 'عميل جديد مخصص لك',
                'message' => "تم تخصيص العميل {$lead->title} لك. يرجى التواصل مع العميل في أقرب وقت.",
                'type' => 'new_lead_assigned',
                'data' => [
                    'lead_id' => $lead->id,
                    'lead_title' => $lead->title,
                    'lead_value' => $lead->value,
                    'lead_source' => $lead->source->name,
                    'client_name' => $lead->client_name,
                    'client_phone' => $lead->client_phone,
                    'client_email' => $lead->client_email,
                    'assigned_by' => $assignedBy->name,
                    'assigned_at' => now()
                ]
            ]);

            // Send notification to lead if client exists
            if ($lead->client) {
                $lead->client->notifications()->create([
                    'title' => 'تم تخصيص عميلك لوكلاء',
                    'message' => "تم تخصيص عميلك للوكيل {$agent->name}. سيقوم بالتواصل معك قريباً.",
                    'type' => 'lead_assigned_to_agent',
                    'data' => [
                        'agent_id' => $agent->id,
                        'agent_name' => $agent->name,
                        'lead_id' => $lead->id
                    ]
                ]);
            }

            Log::info('Agent notified about new lead', [
                'agent_id' => $agent->id,
                'lead_id' => $lead->id,
                'assigned_by_id' => $assignedBy->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to notify agent about new lead', [
                'agent_id' => $agent->id,
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

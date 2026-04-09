<?php

namespace App\Listeners;

use App\Events\AgentRegistered;
use App\Models\Agent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendAgentWelcomeEmail
{
    /**
     * Handle the event.
     */
    public function handle(AgentRegistered $event): void
    {
        $agent = $event->agent;
        $user = $event->user;

        try {
            // Send welcome email to agent
            Mail::to($agent->email)->send(new \App\Mail\AgentWelcomeMail($agent, $user));

            // Create notification for agent
            $agent->notifications()->create([
                'title' => 'مرحباً بك في منصتنا',
                'message' => "تم تسجيلك كوكلاء في منصتنا. نحن سعداء بانضمامك لفريقنا.",
                'type' => 'agent_registered',
                'data' => [
                    'agent_id' => $agent->id,
                    'agent_name' => $agent->name,
                    'license_number' => $agent->license_number,
                    'company_id' => $agent->company_id,
                    'company_name' => $agent->company ? $agent->company->name : null,
                    'registration_date' => $agent->created_at,
                    'next_steps' => [
                        'complete_profile' => 'أكمل ملفك الشخصي',
                        'verify_license' => 'تحقق من رخصتك',
                        'add_portfolio' => 'أضف معرض أعمالك',
                        'attend_training' => 'حضور التدريب'
                    ]
                ]
            ]);

            Log::info('Agent welcome email sent', [
                'agent_id' => $agent->id,
                'email' => $agent->email,
                'user_id' => $user->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send agent welcome email', [
                'agent_id' => $agent->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

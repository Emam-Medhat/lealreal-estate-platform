<?php

namespace App\Listeners;

use App\Events\CompanyCreated;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendCompanyWelcomeEmail
{
    /**
     * Handle the event.
     */
    public function handle(CompanyCreated $event): void
    {
        $company = $event->company;
        $user = $event->user;

        try {
            // Send welcome email to company owner
            Mail::to($user->email)->send(new \App\Mail\CompanyWelcomeMail($company, $user));

            // Send notification to user
            $user->notifications()->create([
                'title' => 'مرحباً بإنشاء شركتك',
                'message' => "تم إنشاء شركة {$company->name} بنجاح. شكراً لثقتك بمنصتنا.",
                'type' => 'company_created',
                'data' => [
                    'company_id' => $company->id,
                    'company_name' => $company->name
                ]
            ]);

            // Log the event
            Log::info('Company welcome email sent', [
                'company_id' => $company->id,
                'user_id' => $user->id,
                'email' => $user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send company welcome email', [
                'company_id' => $company->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Listeners;

use App\Events\CompanyCreated;
use App\Models\Company;
use Illuminate\Support\Facades\Log;

class CreateCompanyDefaultSettings
{
    /**
     * Handle the event.
     */
    public function handle(CompanyCreated $event): void
    {
        $company = $event->company;
        $user = $event->user ?? ($company->creator ?? $company->owner);
        if (!$user)
            return; // Should not happen usually

        try {
            // Create default company settings
            $defaultSettings = [
                'notifications' => [
                    'email_notifications' => true,
                    'sms_notifications' => false,
                    'push_notifications' => true,
                    'property_alerts' => true,
                    'member_alerts' => true,
                    'report_alerts' => true
                ],
                'privacy' => [
                    'public_profile' => false,
                    'public_properties' => true,
                    'public_analytics' => false,
                    'allow_member_invites' => true
                ],
                'features' => [
                    'property_management' => true,
                    'member_management' => true,
                    'branch_management' => true,
                    'analytics' => true,
                    'reports' => true,
                    'document_management' => true,
                    'api_access' => false
                ],
                'branding' => [
                    'primary_color' => '#007bff',
                    'secondary_color' => '#6c757d',
                    'logo_url' => null,
                    'custom_css' => null
                ]
            ];

            // Save settings
            $company->settings()->create([
                'key' => 'default',
                'value' => json_encode($defaultSettings),
                'description' => 'الإعدادات الافتراضية',
                'created_by' => $user->id
            ]);

            Log::info('Default company settings created', [
                'company_id' => $company->id,
                'user_id' => $user->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create default company settings', [
                'company_id' => $company->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

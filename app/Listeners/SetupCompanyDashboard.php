<?php

namespace App\Listeners;

use App\Events\CompanyCreated;
use App\Models\User;
use App\Models\Company; // Added
use App\Models\CompanyMember;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SetupCompanyDashboard
{
    /**
     * Handle the event.
     */
    public function handle(CompanyCreated $event): void
    {
        $company = $event->company;
        $user = $event->user;

        try {
            // Create initial dashboard data
            $this->createInitialDashboardData($company);

            // Send welcome notification to owner
            if ($user) {
                $this->sendWelcomeNotification($company, $user);
            }

            Log::info('Company dashboard setup completed', [
                'company_id' => $company->id,
                'owner_id' => $user ? $user->id : null
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to setup company dashboard', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create initial dashboard data
     */
    private function createInitialDashboardData(Company $company): void
    {
        // Create default dashboard widgets
        $widgets = [
            [
                'type' => 'overview',
                'title' => 'نظرة عامة',
                'data' => [
                    'total_properties' => 0,
                    'total_members' => 1,
                    'active_members' => 1,
                    'total_branches' => 0,
                    'recent_activities' => []
                ]
            ],
            [
                'type' => 'properties',
                'title' => 'العقارات',
                'data' => [
                    'total' => 0,
                    'listed' => 0,
                    'sold' => 0,
                    'pending' => 0
                ]
            ],
            [
                'type' => 'members',
                'title' => 'الأعضاء',
                'data' => [
                    'total' => 1,
                    'active' => 1,
                    'recent' => [
                        [
                            'id' => $company->members()->first()->id,
                            'name' => $company->members()->first()->user->name,
                            'role' => $company->members()->first()->role,
                            'joined_at' => $company->members()->first()->created_at
                        ]
                    ]
                ]
            ]
        ];

        // Save widgets to company settings
        $company->settings()->create([
            'key' => 'dashboard_widgets',
            'value' => json_encode($widgets),
            'description' => 'ترتيب لوحة التحكم',
            'created_by' => $company->owner_id
        ]);
    }

    /**
     * Add member to existing dashboard
     */
    private function addMemberToDashboard(Company $company, CompanyMember $member): void
    {
        // Update dashboard widgets with new member
        $widgets = json_decode($company->settings()->where('key', 'dashboard_widgets')->first()->value ?? '[]', true);

        // Update members widget
        foreach ($widgets as &$widget) {
            if ($widget['type'] === 'members') {
                $widget['data']['total'] = $widget['data']['total'] + 1;
                $widget['data']['active'] = $widget['data']['active'] + 1;
                $widget['data']['recent'][] = [
                    'id' => $member->id,
                    'name' => $member->user->name,
                    'role' => $member->role,
                    'joined_at' => $member->created_at
                ];
                break;
            }
        }

        // Save updated widgets
        $company->settings()->where('key', 'dashboard_widgets')->update([
            'value' => json_encode($widgets)
        ]);

        // Send notification to member
        $member->user->notifications()->create([
            'title' => 'مرحباً بك في الشركة',
            'message' => "تم إضافتك كعضو في شركة {$company->name}",
            'type' => 'member_added',
            'data' => [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'role' => $member->role
            ]
        ]);
    }

    /**
     * Send welcome notification to company owner
     */
    private function sendWelcomeNotification(Company $company, User $user): void
    {
        $user->notifications()->create([
            'title' => 'تهانينا! تم إنشاء الشركة',
            'message' => "تم إنشاء شركة {$company->name} بنجاح. يمكنك الآن البدء في إدارة الأعضاء والتحكم في لوحة التحكم.",
            'type' => 'company_created',
            'data' => [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'next_steps' => [
                    'add_members' => 'إضافة الأعضاء',
                    'setup_branches' => 'إنشاء الفروع',
                    'configure_settings' => 'ضبط الإعدادات',
                    'upload_logo' => 'رفع شعار الشركة'
                ]
            ]
        ]);
    }
}

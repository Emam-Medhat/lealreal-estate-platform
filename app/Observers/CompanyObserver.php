<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\CompanyBranch;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CompanyObserver
{
    /**
     * Handle the Company "created" event.
     */
    public function created(Company $company): void
    {
        if (empty($company->slug)) {
            $company->slug = Str::slug($company->name) . '-' . Str::random(6);
            $company->saveQuietly();
        }

        try {
            // Create default settings
            $this->createDefaultSettings($company);

            // Create initial analytics record
            $this->createInitialAnalytics($company);

            // Setup initial dashboard
            $this->setupInitialDashboard($company);

            // Clear company cache
            Cache::tags(['company', 'company.' . $company->id])->flush();

            Log::info('Company created with default settings', [
                'company_id' => $company->id,
                'name' => $company->name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to setup company after creation', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle the Company "updated" event.
     */
    public function updated(Company $company): void
    {
        try {
            // Update cache
            Cache::tags(['company', 'company.' . $company->id])->flush();

            // Log changes
            $changes = $company->getDirty();

            if (!empty($changes)) {
                $company->activities()->create([
                    'activity_type' => 'company_update',
                    'action' => 'company_updated',
                    'data' => [
                        'changes' => $changes,
                        'updated_by' => auth()->id()
                    ],
                    'created_at' => now()
                ]);
            }

            Log::info('Company updated', [
                'company_id' => $company->id,
                'changes' => array_keys($changes)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle company update', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Company "deleting" event.
     */
    public function deleting(Company $company): void
    {
        try {
            // Archive company data before deletion
            $this->archiveCompanyData($company);

            // Cancel active subscriptions
            $this->cancelSubscriptions($company);

            // Notify members about company deletion
            $this->notifyMembersAboutDeletion($company);

            Log::info('Company deletion started', [
                'company_id' => $company->id,
                'name' => $company->name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle company deletion', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create default settings
     */
    private function createDefaultSettings(Company $company): void
    {
        $defaultSettings = [
            [
                'key' => 'notifications',
                'value' => json_encode([
                    'email_notifications' => true,
                    'sms_notifications' => false,
                    'push_notifications' => true,
                    'property_alerts' => true,
                    'member_alerts' => true,
                    'report_alerts' => true
                ])
            ],
            [
                'key' => 'privacy',
                'value' => json_encode([
                    'public_profile' => false,
                    'public_properties' => true,
                    'public_analytics' => false,
                    'allow_member_invites' => true
                ])
            ],
            [
                'key' => 'features',
                'value' => json_encode([
                    'property_management' => true,
                    'member_management' => true,
                    'branch_management' => true,
                    'analytics' => true,
                    'reports' => true,
                    'document_management' => true,
                    'api_access' => false
                ])
            ],
            [
                'key' => 'branding',
                'value' => json_encode([
                    'primary_color' => '#007bff',
                    'secondary_color' => '#6c757d',
                    'logo_url' => null,
                    'custom_css' => null
                ])
            ]
        ];

        $company->settings()->createMany($defaultSettings);
    }

    /**
     * Create initial analytics record
     */
    private function createInitialAnalytics(Company $company): void
    {
        $company->analytics()->create([
            'type' => 'initial',
            'data' => [
                'total_properties' => 0,
                'total_members' => 1,
                'active_members' => 1,
                'total_branches' => 0,
                'created_at' => now()
            ],
            'calculated_at' => now()
        ]);
    }

    /**
     * Setup initial dashboard
     */
    private function setupInitialDashboard(Company $company): void
    {
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

        $company->settings()->create([
            'key' => 'dashboard_widgets',
            'value' => json_encode($widgets),
            'description' => 'ترتيب لوحة التحكم',
            'created_by' => $company->owner_id
        ]);
    }

    /**
     * Archive company data
     */
    private function archiveCompanyData(Company $company): void
    {
        // This would integrate with your archiving system
        // Placeholder implementation

        Log::info('Company data archived', [
            'company_id' => $company->id,
            'archived_at' => now()
        ]);
    }

    /**
     * Cancel subscriptions
     */
    private function cancelSubscriptions(Company $company): void
    {
        $subscriptions = $company->subscriptions()->where('status', 'active')->get();

        foreach ($subscriptions as $subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => 'company_deletion'
            ]);
        }

        Log::info('Company subscriptions cancelled', [
            'company_id' => $company->id,
            'cancelled_count' => $subscriptions->count()
        ]);
    }

    /**
     * Notify members about company deletion
     */
    private function notifyMembersAboutDeletion(Company $company): void
    {
        $members = $company->members()->with('user')->get();

        foreach ($members as $member) {
            $member->user->notifications()->create([
                'title' => 'إشعار بإنهاء الشركة',
                'message' => "تم إنهاء شركة {$company->name}. جميع بياناتك سيتم أرشفتها.",
                'type' => 'company_deletion',
                'data' => [
                    'company_id' => $company->id,
                    'company_name' => $company->name,
                    'deletion_date' => now()
                ]
            ]);
        }

        Log::info('Company members notified about deletion', [
            'company_id' => $company->id,
            'notified_count' => $members->count()
        ]);
    }
}

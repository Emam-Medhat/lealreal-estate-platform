<?php

namespace App\Observers;

use App\Models\User;
use App\Services\ProfileService;

class UserProfileObserver
{
    protected $profileService;

    /**
     * Create a new observer instance.
     */
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Update profile completion percentage
        $completionPercentage = $this->profileService->calculateCompletionPercentage($user);
        
        if ($user->profile_completion_percentage !== $completionPercentage) {
            $user->update(['profile_completion_percentage' => $completionPercentage]);
        }
        
        // Log profile changes
        $this->logProfileChanges($user);
        
        // Update search index if needed
        $this->updateSearchIndex($user);
    }

    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        // Set default values
        $user->profile_completion_percentage = 0;
        $user->kyc_verified = false;
        $user->kyc_status = 'not_started';
        $user->status = 'active';
        $user->language = $user->language ?? 'ar';
        $user->timezone = $user->timezone ?? 'Asia/Riyadh';
        $user->currency = $user->currency ?? 'SAR';
        
        // Set default preferences
        $user->notification_preferences = $user->notification_preferences ?? [
            'email_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => true,
            'marketing_emails' => false,
            'property_alerts' => true,
            'price_changes' => true,
            'new_listings' => true,
            'message_notifications' => true
        ];
        
        // Set default search preferences
        $user->search_preferences = $user->search_preferences ?? [
            'per_page' => 20,
            'sort_by' => 'created_at',
            'sort_order' => 'desc',
            'map_view' => 'map',
            'grid_columns' => 3
        ];
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Create user profile
        if (!$user->profile) {
            $user->profile()->create([
                'bio' => null,
                'address' => null,
                'city' => null,
                'country' => null,
                'postal_code' => null,
                'profession' => null,
                'company' => null,
                'experience' => null,
                'education' => null,
                'interests' => null,
                'social_links' => null,
                'website' => null
            ]);
        }
        
        // Create user wallet
        if (!$user->wallet) {
            $user->wallet()->create([
                'balance' => 0,
                'available_balance' => 0,
                'frozen_balance' => 0,
                'currency' => 'SAR'
            ]);
        }
        
        // Send welcome notification
        $user->notifications()->create([
            'title' => 'مرحباً بك في منصتنا',
            'message' => 'شكراً لتسجيلك في منصة العقارات. ابدأ باستكشاف الميزات المتاحة.',
            'type' => 'welcome',
            'data' => [
                'next_steps' => [
                    'complete_profile' => 'أكمل ملفك الشخصي',
                    'verify_kyc' => 'أكمل التحقق من الهوية',
                    'browse_properties' => 'تصفح العقارات'
                ]
            ]
        ]);
    }

    /**
     * Log profile changes
     */
    private function logProfileChanges(User $user): void
    {
        $changes = $user->getDirty();
        
        if (empty($changes)) {
            return;
        }
        
        $logData = [];
        
        foreach ($changes as $field => $values) {
            $logData[] = [
                'field' => $field,
                'old_value' => $values[0],
                'new_value' => $values[1],
                'changed_at' => now()
            ];
        }
        
        // Store changes in user activity log
        $user->activities()->create([
            'activity_type' => 'profile_change',
            'action' => 'profile_updated',
            'data' => [
                'changes' => $logData,
                'completion_percentage' => $user->profile_completion_percentage
            ],
            'created_at' => now()
        ]);
    }

    /**
     * Update search index
     */
    private function updateSearchIndex(User $user): void
    {
        try {
            // Implementation depends on your search system
            // This is a placeholder for search index update
            
            $indexData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'profile_completion' => $user->profile_completion_percentage,
                'kyc_verified' => $user->kyc_verified,
                'kyc_level' => $user->kyc_level,
                'status' => $user->status,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ];
            
            // Example: Update Elasticsearch index
            // Elasticsearch::index([
            //     'index' => 'users',
            //     'id' => $user->id,
            //     'body' => $indexData
            // ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to update search index', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

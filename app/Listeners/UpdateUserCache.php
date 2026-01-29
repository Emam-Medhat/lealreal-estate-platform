<?php

namespace App\Listeners;

use App\Events\ProfileUpdated;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateUserCache
{
    /**
     * Handle the event.
     */
    public function handle(ProfileUpdated $event): void
    {
        $user = $event->user;
        
        // Update user cache
        try {
            if (config('cache.default') !== 'file' && config('cache.default') !== 'database') {
                Cache::tags(['user', 'user.' . $user->id])->flush();
            }
        } catch (\Exception $e) {
            // Ignore
        }
        
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'role' => $user->role,
            'profile_completion_percentage' => $user->profile_completion_percentage,
            'kyc_verified' => $user->kyc_verified,
            'kyc_level' => $user->kyc_level,
            'last_activity_at' => $user->last_activity_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];

        // Cache user profile data
        try {
            if (config('cache.default') !== 'file' && config('cache.default') !== 'database') {
                Cache::tags(['user', 'user.' . $user->id])->remember(
                    'user.profile.' . $user->id,
                    now()->addHours(24),
                    fn() => $userData
                );
            } else {
                Cache::put('user.profile.' . $user->id, $userData, now()->addHours(24));
            }
        } catch (\Exception $e) {
            Cache::put('user.profile.' . $user->id, $userData, now()->addHours(24));
        }
        
        // Update search index if needed
        $this->updateSearchIndex($user);
        
        Log::info('User cache updated', [
            'user_id' => $user->id,
            'changes' => $event->changes,
            'updated_by' => $event->updatedBy?->id
        ]);
    }
    
    /**
     * Update search index for user
     */
    private function updateSearchIndex($user): void
    {
        // Implementation depends on your search system (Elasticsearch, Algolia, etc.)
        // This is a placeholder for search index update
        try {
            // Example: Update Elasticsearch index
            // Elasticsearch::update([
            //     'index' => 'users',
            //     'id' => $user->id,
            //     'body' => [
            //         'name' => $user->name,
            //         'email' => $user->email,
            //         'phone' => $user->phone,
            //         'role' => $user->role,
            //         'profile_completion' => $user->profile_completion_percentage,
            //         'updated_at' => $user->updated_at
            //     ]
            // ]);
        } catch (\Exception $e) {
            Log::error('Failed to update search index', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

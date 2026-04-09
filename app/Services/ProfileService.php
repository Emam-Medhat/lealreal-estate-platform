<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    /**
     * Get user profile with necessary relationships.
     *
     * @param int $userId
     * @return User|null
     */
    public function getUserProfile(int $userId): ?User
    {
        $cacheKey = "user_profile_{$userId}";

        return Cache::remember($cacheKey, 1800, function () use ($userId) {
            return User::with(['profile', 'socialAccounts', 'subscriptionPlan'])
                ->find($userId);
        });
    }

    /**
     * Update user profile data.
     *
     * @param User $user
     * @param array $userData
     * @param array $profileData
     * @param mixed $avatarFile
     * @param string $ipAddress
     * @return bool
     */
    public function updateProfile(User $user, array $userData, array $profileData, $avatarFile = null, string $ipAddress = ''): bool
    {
        return DB::transaction(function () use ($user, $userData, $profileData, $avatarFile, $ipAddress) {
            // Update basic user info
            $user->update($userData);

            // Handle avatar upload
            if ($avatarFile) {
                if ($user->profile && $user->profile->avatar) {
                    Storage::disk('public')->delete($user->profile->avatar);
                }
                
                $path = $avatarFile->store('avatars', 'public');
                $profileData['avatar'] = $path;
                
                // Also update user avatar field if redundant
                $user->update(['avatar' => $path]);
            }

            // Update or create profile
            $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

            // Log activity
            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'updated_profile',
                'details' => 'Updated personal profile information via ProfileService',
                'ip_address' => $ipAddress,
            ]);

            // Invalidate cache
            Cache::forget("user_profile_{$user->id}");
            Cache::forget("user_profile_stats_{$user->id}");

            return true;
        });
    }

    /**
     * Calculate profile completion percentage.
     *
     * @param User $user
     * @return int
     */
    public function calculateCompletion(User $user): int
    {
        $cacheKey = "user_profile_stats_{$user->id}";

        return Cache::remember($cacheKey, 3600, function () use ($user) {
            $user->loadMissing('profile');
            $profile = $user->profile;
            
            $totalFields = 18; // Increased from 15 to include more fields
            $completedFields = 0;

            // Basic user fields
            if ($user->first_name) $completedFields++;
            if ($user->last_name) $completedFields++;
            if ($user->phone) $completedFields++;
            if ($user->email_verified_at) $completedFields++;
            if ($user->username) $completedFields++;

            // Profile fields
            if ($profile) {
                if ($profile->bio) $completedFields++;
                if ($profile->date_of_birth) $completedFields++;
                if ($profile->gender) $completedFields++;
                if ($profile->address) $completedFields++;
                if ($profile->city) $completedFields++;
                if ($profile->state) $completedFields++;
                if ($profile->country) $completedFields++;
                if ($profile->postal_code) $completedFields++;
                if ($profile->website) $completedFields++;
                if ($profile->social_links) $completedFields++;
                if ($profile->avatar) $completedFields++;
                if ($profile->cover_image) $completedFields++;
                if ($profile->skills) $completedFields++;
            }

            return (int) round(($completedFields / $totalFields) * 100);
        });
    }

    /**
     * Get public profile of a user.
     *
     * @param User $user
     * @return User
     */
    public function getPublicProfile(User $user): User
    {
        $cacheKey = "public_profile_{$user->id}";

        return Cache::remember($cacheKey, 1800, function () use ($user) {
            return $user->load(['profile', 'properties' => function ($query) {
                $query->where('status', 'published')->latest()->limit(6);
            }]);
        });
    }
}

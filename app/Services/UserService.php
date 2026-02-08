<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserWallet;
use App\Models\UserActivityLog;
use App\Services\CacheService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

use App\Repositories\Contracts\UserRepositoryInterface;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get dashboard data for the authenticated user.
     *
     * @param int $userId
     * @return array
     */
    public function getDashboardData(int $userId): array
    {
        return CacheService::rememberUsers("user_dashboard_{$userId}", function () use ($userId) {
            $user = $this->userRepository->findById($userId, ['*'], ['profile']);
            $stats = $this->userRepository->getDashboardStats($userId);
            $recentActivity = $this->getUserRecentActivity($userId);

            return [
                'user' => $user,
                'stats' => $stats,
                'recent_activity' => $recentActivity,
            ];
        }, 'short');
    }

    /**
     * Get recent activity for a user formatted for display.
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getUserRecentActivity(int $userId, int $limit = 5): array
    {
        $logs = $this->userRepository->getActivityLogs($userId, $limit);

        return $logs->map(function ($log) {
            return [
                'icon' => $this->getActivityIcon($log->action),
                'message' => $log->details,
                'time' => $log->created_at->diffForHumans(),
                'action' => $log->action,
            ];
        })->toArray();
    }

    /**
     * Map activity action to icon.
     *
     * @param string $action
     * @return string
     */
    protected function getActivityIcon(string $action): string
    {
        $icons = [
            'viewed_property' => 'eye',
            'searched_properties' => 'search',
            'saved_property' => 'heart',
            'updated_profile' => 'user-edit',
            'logged_in' => 'sign-in-alt',
            'logged_out' => 'sign-out-alt',
        ];

        return $icons[$action] ?? 'info-circle';
    }

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();
        try {
            // Generate username if not provided
            if (empty($data['username'])) {
                $baseUsername = explode('@', $data['email'])[0];
                $data['username'] = $baseUsername . '_' . time();
            }

            // Generate full_name if not provided
            if (empty($data['full_name']) && isset($data['first_name'], $data['last_name'])) {
                $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];
            }

            // Hash password if it's not hashed yet (though typically done before or via mutator, 
            // but service should ensure it)
            if (isset($data['password']) && !Hash::info($data['password'])['algo']) {
                $data['password'] = Hash::make($data['password']);
            }

            // Set default account status
            if (!isset($data['account_status'])) {
                $data['account_status'] = 'active';
            }

            // Create user
            $user = $this->userRepository->create($data);

            // Create profile
            $profileData = [
                'user_id' => $user->id,
                'first_name' => $data['first_name'] ?? null,
                'last_name' => $data['last_name'] ?? null,
                'bio' => $data['bio'] ?? null,
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
            ];

            // Handle profile creation if relation exists
            if (method_exists($user, 'profile')) {
                $user->profile()->create($profileData);
            }

            // Handle specific role logic
            $this->handleRoleSpecificSetup($user, $data);

            // Log activity
            $this->logActivity($user, 'account_created', [
                'created_at' => now(),
                'role' => $user->role ?? $user->user_type
            ]);

            DB::commit();

            // Clear cache
            $this->clearUserCaches();

            return $user;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user', [
                'email' => $data['email'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle setup specific to user roles (Agent, Company, etc.)
     */
    protected function handleRoleSpecificSetup(User $user, array $data): void
    {
        // Example: If user is an agent, create agent profile
        if (($user->role === 'agent' || $user->user_type === 'agent') && method_exists($user, 'agentProfile')) {
            // Logic to create agent profile if not exists
            // This might be handled by Observers, but Service can orchestrate it too.
        }
    }

    /**
     * Clear user related caches
     */
    protected function clearUserCaches(int $userId = null): void
    {
        CacheService::forget('users_paginated');
        CacheService::forget('users_filtered');
        CacheService::forget('user_stats');

        if ($userId) {
            CacheService::forget("user_dashboard_{$userId}");
            CacheService::forget("user_dashboard_stats_{$userId}");
            CacheService::forget("user_profile_{$userId}");
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(int $userId, array $data): User
    {
        $user = $this->userRepository->findById($userId);

        DB::beginTransaction();
        try {
            $oldData = $user->toArray();

            // Update user basic info
            $userData = $this->prepareUserData($data);
            $this->userRepository->update($user->id, $userData);

            // Update or create profile
            $profileData = $this->prepareProfileData($data);
            if ($user->profile) {
                $user->profile->update($profileData);
            } else {
                $user->profile()->create($profileData);
            }

            // Calculate and update profile completion
            $completionPercentage = $this->calculateProfileCompletion($user);
            $this->userRepository->update($user->id, ['profile_completion_percentage' => $completionPercentage]);

            // Log activity
            $this->logActivity($user, 'profile_updated', [
                'old_data' => $oldData,
                'new_data' => $userData,
                'completion_percentage' => $completionPercentage
            ]);

            DB::commit();

            // Fire event
            event(new \App\Events\ProfileUpdated($user, $this->getChanges($oldData, $userData)));

            return $user->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user profile', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(UploadedFile $file, int $userId): string
    {
        $user = $this->userRepository->findById($userId);

        // Delete old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Upload new avatar
        $path = $file->store('avatars', 'public');

        // Update user avatar
        $oldAvatar = $user->avatar;
        $this->userRepository->update($user->id, ['avatar' => $path]);

        // Fire event
        event(new \App\Events\AvatarChanged($user, $oldAvatar, $path));

        return $path;
    }

    /**
     * Delete user account
     */
    public function deleteUser(int $userId, bool $hardDelete = false): bool
    {
        $user = $this->userRepository->findById($userId);

        DB::beginTransaction();
        try {
            if ($hardDelete) {
                // Hard delete - remove all data
                $user->profile?->delete();
                $user->wallet?->delete();
                $user->kycVerification?->delete();
                $user->activities()->delete();
                $user->notifications()->delete();
                $user->sessions()->delete();

                // Delete avatar
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $this->userRepository->permanentlyDeleteById($userId);
            } else {
                // Soft delete
                $this->userRepository->deleteById($userId);
            }

            // Log activity
            $this->logActivity($user, 'account_deleted', [
                'hard_delete' => $hardDelete,
                'deleted_at' => now()
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Export user data
     */
    public function exportUserData(int $userId): array
    {
        $user = $this->userRepository->findById($userId, ['*'], [
            'profile',
            'wallet',
            'kycVerification',
            'activities',
            'notifications',
            'favorites',
            'comparisons'
        ]);

        return [
            'user' => $user->makeHidden(['password', 'remember_token']),
            'profile' => $user->profile,
            'wallet' => $user->wallet,
            'kyc_verification' => $user->kycVerification,
            'activities' => $user->activities->take(100)->toArray(),
            'notifications' => $user->notifications->take(50)->toArray(),
            'favorites' => $user->favorites->take(50)->toArray(),
            'comparisons' => $user->comparisons->take(50)->toArray(),
            'exported_at' => now()->toDateTimeString()
        ];
    }


    /**
     * Prepare user data for update
     */
    private function prepareUserData(array $data): array
    {
        $allowedFields = [
            'name',
            'email',
            'phone',
            'date_of_birth',
            'gender',
            'nationality',
            'language',
            'timezone'
        ];

        $userData = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $userData[$field] = $data[$field];
            }
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        return $userData;
    }

    /**
     * Prepare profile data for update
     */
    private function prepareProfileData(array $data): array
    {
        $allowedFields = [
            'bio',
            'address',
            'city',
            'country',
            'postal_code',
            'profession',
            'company',
            'experience',
            'education',
            'interests',
            'social_links',
            'website'
        ];

        $profileData = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $profileData[$field] = $data[$field];
            }
        }

        return $profileData;
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion(User $user): int
    {
        $fields = [
            'name' => $user->name ? 10 : 0,
            'email' => $user->email ? 10 : 0,
            'phone' => $user->phone ? 10 : 0,
            'avatar' => $user->avatar ? 10 : 0,
            'bio' => $user->profile?->bio ? 10 : 0,
            'address' => $user->profile?->address ? 10 : 0,
            'date_of_birth' => $user->date_of_birth ? 10 : 0,
            'profession' => $user->profile?->profession ? 10 : 0,
            'kyc_verified' => $user->kyc_verified ? 20 : 0
        ];

        $totalScore = array_sum($fields);
        $maxScore = 100;

        return min($totalScore, $maxScore);
    }

    /**
     * Get changes between old and new data
     */
    private function getChanges(array $oldData, array $newData): array
    {
        $changes = [];

        foreach ($newData as $key => $value) {
            if (!isset($oldData[$key]) || $oldData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldData[$key] ?? null,
                    'new' => $value
                ];
            }
        }

        return $changes;
    }

    /**
     * Log user activity
     */
    private function logActivity(User $user, string $action, array $data = []): void
    {
        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $action,
            'metadata' => json_encode($data),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}

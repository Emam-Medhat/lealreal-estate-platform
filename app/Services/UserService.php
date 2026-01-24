<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserWallet;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class UserService
{
    /**
     * Update user profile
     */
    public function updateProfile(int $userId, array $data): User
    {
        $user = User::findOrFail($userId);
        
        DB::beginTransaction();
        try {
            $oldData = $user->toArray();
            
            // Update user basic info
            $userData = $this->prepareUserData($data);
            $user->update($userData);
            
            // Update or create profile
            $profileData = $this->prepareProfileData($data);
            if ($user->profile) {
                $user->profile->update($profileData);
            } else {
                $user->profile()->create($profileData);
            }
            
            // Calculate and update profile completion
            $completionPercentage = $this->calculateProfileCompletion($user);
            $user->update(['profile_completion_percentage' => $completionPercentage]);
            
            // Log activity
            $this->logActivity($user, 'profile_updated', [
                'old_data' => $oldData,
                'new_data' => $userData,
                'completion_percentage' => $completionPercentage
            ]);
            
            DB::commit();
            
            // Fire event
            event(new \App\Events\ProfileUpdated($user, $this->getChanges($oldData, $userData)));
            
            return $user->refresh();
            
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
        $user = User::findOrFail($userId);
        
        // Delete old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Upload new avatar
        $path = $file->store('avatars', 'public');
        
        // Update user avatar
        $oldAvatar = $user->avatar;
        $user->update(['avatar' => $path]);
        
        // Fire event
        event(new \App\Events\AvatarChanged($user, $oldAvatar, $path));
        
        return $path;
    }
    
    /**
     * Delete user account
     */
    public function deleteUser(int $userId, bool $hardDelete = false): bool
    {
        $user = User::findOrFail($userId);
        
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
                
                $user->forceDelete();
            } else {
                // Soft delete
                $user->delete();
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
        $user = User::with([
            'profile',
            'wallet',
            'kycVerification',
            'activities',
            'notifications',
            'favorites',
            'comparisons'
        ])->findOrFail($userId);
        
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
            'name', 'email', 'phone', 'date_of_birth', 'gender',
            'nationality', 'language', 'timezone'
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
            'bio', 'address', 'city', 'country', 'postal_code',
            'profession', 'company', 'experience', 'education',
            'interests', 'social_links', 'website'
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
        UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'user_action',
            'action' => $action,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);
    }
}

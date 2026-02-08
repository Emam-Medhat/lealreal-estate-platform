<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserProfile;
use App\Services\CacheService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendVerificationEmail;
use App\Jobs\SendWelcomeEmail;
use App\Jobs\SendPasswordResetEmail;
use App\Notifications\UserAccountStatusChanged;
use App\Notifications\UserKycStatusChanged;
use App\Notifications\UserRoleChanged;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        try {
            // Hash password if needed
            if (Hash::needsRehash($user->password)) {
                $user->password = Hash::make($user->password);
            }

            // Generate UUID if not set
            if (!$user->uuid) {
                $user->uuid = (string) \Illuminate\Support\Str::uuid();
            }

            // Set default values
            $user->account_status = $user->account_status ?? 'active';
            $user->kyc_status = $user->kyc_status ?? 'pending';
            $user->two_factor_enabled = $user->two_factor_enabled ?? false;
            $user->marketing_consent = $user->marketing_consent ?? false;
            $user->newsletter_subscribed = $user->newsletter_subscribed ?? false;
            $user->is_first_time_buyer = $user->is_first_time_buyer ?? false;
            $user->is_look_to_rent = $user->is_look_to_rent ?? false;
            $user->is_look_to_buy = $user->is_look_to_buy ?? false;

            // Set default counters
            $user->properties_count = $user->properties_count ?? 0;
            $user->properties_views_count = $user->properties_views_count ?? 0;
            $user->leads_count = $user->leads_count ?? 0;
            $user->transactions_count = $user->transactions_count ?? 0;
            $user->reviews_count = $user->reviews_count ?? 0;
            $user->referral_count = $user->referral_count ?? 0;
            $user->saved_searches_count = $user->saved_searches_count ?? 0;
            $user->favorites_count = $user->favorites_count ?? 0;
            $user->login_count = $user->login_count ?? 0;

        } catch (\Exception $e) {
            Log::error('User observer creating event failed: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? null
            ]);
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        try {
            // Clear user-related caches
            $this->clearUserCaches();

            // Create user profile if not exists
            if (!$user->profile) {
                UserProfile::create([
                    'user_id' => $user->id,
                    'bio' => null,
                    'avatar' => null,
                    'avatar_thumbnail' => null,
                    'cover_image' => null,
                    'social_links' => [],
                    'preferences' => []
                ]);
            }

            // Create initial activity log
            $this->createActivityLog($user, 'created', 'User account created');

            // Send verification email
            SendVerificationEmail::dispatch($user);

            // Send welcome email
            SendWelcomeEmail::dispatch($user);

            // Log creation
            Log::info('User created successfully', [
                'user_id' => $user->id,
                'uuid' => $user->uuid,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'account_status' => $user->account_status
            ]);

            // Update analytics asynchronously
            dispatch(function () use ($user) {
                $this->updateUserAnalytics($user, 'created');
            });

        } catch (\Exception $e) {
            Log::error('User observer created event failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        try {
            // Track important changes before update
            $changes = $user->getDirty();
            
            // Log status changes for post-update processing
            if (isset($changes['account_status'])) {
                $user->_old_account_status = $user->getOriginal('account_status');
            }

            if (isset($changes['user_type'])) {
                $user->_old_user_type = $user->getOriginal('user_type');
            }

            if (isset($changes['kyc_status'])) {
                $user->_old_kyc_status = $user->getOriginal('kyc_status');
            }

            if (isset($changes['password'])) {
                $user->_password_changed = true;
            }

            // Hash password if being changed
            if (isset($changes['password']) && Hash::needsRehash($user->password)) {
                $user->password = Hash::make($user->password);
            }

        } catch (\Exception $e) {
            Log::error('User observer updating event failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        try {
            // Clear user-related caches
            $this->clearUserCaches();

            $changes = $user->getDirty();
            
            // Handle account status change
            if (isset($changes['account_status']) && isset($user->_old_account_status)) {
                $this->handleAccountStatusChange($user, $user->_old_account_status, $user->account_status);
            }

            // Handle user type change
            if (isset($changes['user_type']) && isset($user->_old_user_type)) {
                $this->handleUserTypeChange($user, $user->_old_user_type, $user->user_type);
            }

            // Handle KYC status change
            if (isset($changes['kyc_status']) && isset($user->_old_kyc_status)) {
                $this->handleKycStatusChange($user, $user->_old_kyc_status, $user->kyc_status);
            }

            // Handle password change
            if (isset($user->_password_changed) && $user->_password_changed) {
                $this->handlePasswordChange($user);
            }

            // Handle email change
            if (isset($changes['email'])) {
                $this->handleEmailChange($user, $user->getOriginal('email'), $user->email);
            }

            // Handle phone change
            if (isset($changes['phone'])) {
                $this->handlePhoneChange($user, $user->getOriginal('phone'), $user->phone);
            }

            // Create activity log for important changes
            $this->logImportantChanges($user, $changes);

            // Update analytics asynchronously
            dispatch(function () use ($user, $changes) {
                $this->updateUserAnalytics($user, 'updated', $changes);
            });

            Log::info('User updated successfully', [
                'user_id' => $user->id,
                'changes' => array_keys($changes),
                'updated_by' => auth()->id()
            ]);

        } catch (\Exception $e) {
            Log::error('User observer updated event failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        try {
            // Clear user-related caches
            $this->clearUserCaches();

            // Create activity log
            $this->createActivityLog($user, 'deleted', 'User account deleted');

            // Log deletion
            Log::info('User deleted', [
                'user_id' => $user->id,
                'email' => $user->email,
                'deleted_by' => auth()->id()
            ]);

            // Update analytics asynchronously
            dispatch(function () use ($user) {
                $this->updateUserAnalytics($user, 'deleted');
            });

        } catch (\Exception $e) {
            Log::error('User observer deleted event failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        try {
            // Clear user-related caches
            $this->clearUserCaches();

            // Create activity log
            $this->createActivityLog($user, 'restored', 'User account restored');

            // Log restoration
            Log::info('User restored', [
                'user_id' => $user->id,
                'email' => $user->email,
                'restored_by' => auth()->id()
            ]);

            // Update analytics asynchronously
            dispatch(function () use ($user) {
                $this->updateUserAnalytics($user, 'restored');
            });

        } catch (\Exception $e) {
            Log::error('User observer restored event failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        try {
            // Clear all caches
            $this->clearUserCaches();

            // Log force deletion
            Log::warning('User force deleted', [
                'user_id' => $user->id,
                'email' => $user->email,
                'deleted_by' => auth()->id()
            ]);

            // Update analytics asynchronously
            dispatch(function () use ($user) {
                $this->updateUserAnalytics($user, 'force_deleted');
            });

        } catch (\Exception $e) {
            Log::error('User observer force deleted event failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle account status change
     */
    private function handleAccountStatusChange(User $user, string $oldStatus, string $newStatus): void
    {
        // Create activity log
        $this->createActivityLog($user, 'status_changed', "Account status changed from {$oldStatus} to {$newStatus}");

        // Send notification if account is suspended or reactivated
        if (in_array($newStatus, ['suspended', 'banned', 'deactivated']) || 
            in_array($oldStatus, ['suspended', 'banned', 'deactivated']) && $newStatus === 'active') {
            dispatch(function () use ($user, $oldStatus, $newStatus) {
                $user->notify(new UserAccountStatusChanged($user, $oldStatus, $newStatus));
            });
        }

        // Update analytics
        dispatch(function () use ($user, $oldStatus, $newStatus) {
            $this->updateAccountStatusAnalytics($user, $oldStatus, $newStatus);
        });
    }

    /**
     * Handle user type change
     */
    private function handleUserTypeChange(User $user, string $oldType, string $newType): void
    {
        // Create activity log
        $this->createActivityLog($user, 'role_changed', "User role changed from {$oldType} to {$newType}");

        // Send notification
        dispatch(function () use ($user, $oldType, $newType) {
            $user->notify(new UserRoleChanged($user, $oldType, $newType));
        });

        // Update analytics
        dispatch(function () use ($user, $oldType, $newType) {
            $this->updateUserRoleAnalytics($user, $oldType, $newType);
        });
    }

    /**
     * Handle KYC status change
     */
    private function handleKycStatusChange(User $user, string $oldStatus, string $newStatus): void
    {
        // Create activity log
        $this->createActivityLog($user, 'kyc_changed', "KYC status changed from {$oldStatus} to {$newStatus}");

        // Send notification if KYC is verified
        if ($newStatus === 'verified') {
            dispatch(function () use ($user, $oldStatus, $newStatus) {
                $user->notify(new UserKycStatusChanged($user, $oldStatus, $newStatus));
            });
        }

        // Update analytics
        dispatch(function () use ($user, $oldStatus, $newStatus) {
            $this->updateKycStatusAnalytics($user, $oldStatus, $newStatus);
        });
    }

    /**
     * Handle password change
     */
    private function handlePasswordChange(User $user): void
    {
        // Create activity log
        $this->createActivityLog($user, 'password_changed', 'Password changed');

        // Invalidate all user sessions except current
        dispatch(function () use ($user) {
            $this->invalidateUserSessions($user);
        });

        // Send security notification
        dispatch(function () use ($user) {
            $this->sendSecurityNotification($user, 'password_changed');
        });
    }

    /**
     * Handle email change
     */
    private function handleEmailChange(User $user, ?string $oldEmail, string $newEmail): void
    {
        // Create activity log
        $this->createActivityLog($user, 'email_changed', "Email changed from {$oldEmail} to {$newEmail}");

        // Send verification email to new email
        dispatch(function () use ($user) {
            SendVerificationEmail::dispatch($user);
        });

        // Send security notification
        dispatch(function () use ($user, $oldEmail, $newEmail) {
            $this->sendSecurityNotification($user, 'email_changed', [
                'old_email' => $oldEmail,
                'new_email' => $newEmail
            ]);
        });
    }

    /**
     * Handle phone change
     */
    private function handlePhoneChange(User $user, ?string $oldPhone, string $newPhone): void
    {
        // Create activity log
        $this->createActivityLog($user, 'phone_changed', "Phone changed from {$oldPhone} to {$newPhone}");

        // Send security notification
        dispatch(function () use ($user, $oldPhone, $newPhone) {
            $this->sendSecurityNotification($user, 'phone_changed', [
                'old_phone' => $oldPhone,
                'new_phone' => $newPhone
            ]);
        });
    }

    /**
     * Log important changes
     */
    private function logImportantChanges(User $user, array $changes): void
    {
        $importantFields = [
            'first_name', 'last_name', 'full_name', 'phone', 'country', 'city',
            'is_agent', 'is_company', 'is_developer', 'is_investor',
            'agent_license_number', 'agent_company', 'company_id'
        ];

        foreach ($importantFields as $field) {
            if (isset($changes[$field])) {
                $this->createActivityLog($user, 'profile_updated', "{$field} updated");
            }
        }
    }

    /**
     * Create activity log
     */
    private function createActivityLog(User $user, string $action, string $description): void
    {
        try {
            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_by' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create user activity log: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'action' => $action
            ]);
        }
    }

    /**
     * Update user analytics
     */
    private function updateUserAnalytics(User $user, string $event, array $data = []): void
    {
        try {
            // Update user statistics
            \App\Models\UserAnalytics::updateOrCreate(
                ['user_id' => $user->id, 'date' => now()->toDateString()],
                [
                    'total_users' => \DB::raw('total_users + 1'),
                    'active_users' => $user->account_status === 'active' ? \DB::raw('active_users + 1') : 'active_users',
                    'kyc_verified_users' => $user->kyc_status === 'verified' ? \DB::raw('kyc_verified_users + 1') : 'kyc_verified_users',
                    'updated_at' => now()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to update user analytics: ' . $e->getMessage());
        }
    }

    /**
     * Update account status analytics
     */
    private function updateAccountStatusAnalytics(User $user, string $oldStatus, string $newStatus): void
    {
        try {
            \App\Models\UserStatusAnalytics::create([
                'user_id' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => now(),
                'changed_by' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update account status analytics: ' . $e->getMessage());
        }
    }

    /**
     * Update user role analytics
     */
    private function updateUserRoleAnalytics(User $user, string $oldType, string $newType): void
    {
        try {
            \App\Models\UserRoleAnalytics::create([
                'user_id' => $user->id,
                'old_role' => $oldType,
                'new_role' => $newType,
                'changed_at' => now(),
                'changed_by' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update user role analytics: ' . $e->getMessage());
        }
    }

    /**
     * Update KYC status analytics
     */
    private function updateKycStatusAnalytics(User $user, string $oldStatus, string $newStatus): void
    {
        try {
            \App\Models\UserKycAnalytics::create([
                'user_id' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => now(),
                'changed_by' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update KYC status analytics: ' . $e->getMessage());
        }
    }

    /**
     * Invalidate user sessions
     */
    private function invalidateUserSessions(User $user): void
    {
        try {
            $user->sessions()->where('id', '!=', session()->getId())->delete();
        } catch (\Exception $e) {
            Log::error('Failed to invalidate user sessions: ' . $e->getMessage());
        }
    }

    /**
     * Send security notification
     */
    private function sendSecurityNotification(User $user, string $type, array $data = []): void
    {
        try {
            // Implementation would depend on your notification system
            Log::info("Security notification sent: {$type}", [
                'user_id' => $user->id,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send security notification: ' . $e->getMessage());
        }
    }

    /**
     * Clear user-related caches
     */
    private function clearUserCaches(): void
    {
        try {
            $cacheKeys = [
                'user_dashboard_stats',
                'user_statistics',
                'total_users',
                'active_users',
                'recent_users',
                'user_types_stats',
                'kyc_stats'
            ];

            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }

            // Clear tagged caches if using CacheService
            if (class_exists(CacheService::class)) {
                app(CacheService::class)->clearTags(['users', 'dashboard', 'analytics']);
            }

        } catch (\Exception $e) {
            Log::error('Failed to clear user caches: ' . $e->getMessage());
        }
    }
}

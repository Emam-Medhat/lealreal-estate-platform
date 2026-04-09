<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CleanInactiveUsers implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 600;

    protected $daysThreshold;
    protected $action;

    /**
     * Create a new job instance.
     */
    public function __construct(int $daysThreshold = 90, string $action = 'notify')
    {
        $this->daysThreshold = $daysThreshold;
        $this->action = $action;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $inactiveUsers = $this->getInactiveUsers();
            
            foreach ($inactiveUsers as $user) {
                switch ($this->action) {
                    case 'notify':
                        $this->notifyInactiveUser($user);
                        break;
                    case 'warn':
                        $this->warnInactiveUser($user);
                        break;
                    case 'deactivate':
                        $this->deactivateUser($user);
                        break;
                    case 'delete':
                        $this->deleteUser($user);
                        break;
                }
            }
            
            Log::info('Inactive users processed', [
                'action' => $this->action,
                'days_threshold' => $this->daysThreshold,
                'users_count' => $inactiveUsers->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to clean inactive users', [
                'action' => $this->action,
                'days_threshold' => $this->daysThreshold,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Get inactive users
     */
    private function getInactiveUsers(): \Illuminate\Support\Collection
    {
        $thresholdDate = now()->subDays($this->daysThreshold);
        
        return User::where('last_activity_at', '<', $thresholdDate)
            ->where('status', 'active')
            ->where('role', '!=', 'admin') // Don't process admins
            ->get();
    }
    
    /**
     * Notify inactive user
     */
    private function notifyInactiveUser(User $user): void
    {
        $daysInactive = now()->diffInDays($user->last_activity_at);
        
        // Create notification
        $user->notifications()->create([
            'title' => 'لم نسجل دخولك منذ فترة',
            'message' => "لم نسجل دخولك منذ {$daysInactive} يوماً. نفتقدك!",
            'type' => 'inactivity_reminder',
            'data' => [
                'days_inactive' => $daysInactive,
                'last_login' => $user->last_activity_at
            ]
        ]);
        
        // Send email notification
        try {
            \Mail::to($user->email)->send(new \App\Mail\InactiveUserMail($user, $daysInactive));
        } catch (\Exception $e) {
            Log::error('Failed to send inactive user email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
        
        // Log activity
        UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'system_action',
            'action' => 'inactivity_notification_sent',
            'data' => [
                'days_inactive' => $daysInactive,
                'threshold' => $this->daysThreshold
            ],
            'created_at' => now()
        ]);
    }
    
    /**
     * Warn inactive user
     */
    private function warnInactiveUser(User $user): void
    {
        $daysInactive = now()->diffInDays($user->last_activity_at);
        
        // Create warning notification
        $user->notifications()->create([
            'title' => 'تحذير: حسابك معطل للغاية',
            'message' => "حسابك معطل منذ {$daysInactive} يوماً. سيتم إلغاء تنشيطه قريباً",
            'type' => 'inactivity_warning',
            'data' => [
                'days_inactive' => $daysInactive,
                'warning_level' => 'final',
                'deletion_date' => now()->addDays(7)->toDateString()
            ]
        ]);
        
        // Send warning email
        try {
            \Mail::to($user->email)->send(new \App\Mail\InactiveUserWarningMail($user, $daysInactive));
        } catch (\Exception $e) {
            Log::error('Failed to send inactive user warning email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
        
        // Log activity
        UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'system_action',
            'action' => 'inactivity_warning_sent',
            'data' => [
                'days_inactive' => $daysInactive,
                'warning_level' => 'final'
            ],
            'created_at' => now()
        ]);
    }
    
    /**
     * Deactivate user
     */
    private function deactivateUser(User $user): void
    {
        $daysInactive = now()->diffInDays($user->last_activity_at);
        
        // Update user status
        $user->update([
            'status' => 'inactive',
            'deactivated_at' => now(),
            'deactivation_reason' => 'inactivity'
        ]);
        
        // Create notification
        $user->notifications()->create([
            'title' => 'تم إلغاء تنشيط حسابك',
            'message' => "تم إلغاء تنشيط حسابك بسبب عدم النشاط لمدة {$daysInactive} يوماً",
            'type' => 'account_deactivated',
            'data' => [
                'reason' => 'inactivity',
                'days_inactive' => $daysInactive,
                'reactivation_url' => route('account.reactivate')
            ]
        ]);
        
        // Send deactivation email
        try {
            \Mail::to($user->email)->send(new \App\Mail\AccountDeactivatedMail($user, 'inactivity'));
        } catch (\Exception $e) {
            Log::error('Failed to send deactivation email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
        
        // Log activity
        UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'system_action',
            'action' => 'account_deactivated',
            'data' => [
                'reason' => 'inactivity',
                'days_inactive' => $daysInactive
            ],
            'created_at' => now()
        ]);
    }
    
    /**
     * Delete user
     */
    private function deleteUser(User $user): void
    {
        $daysInactive = now()->diffInDays($user->last_activity_at);
        
        // Soft delete user
        $user->delete();
        
        // Log activity before deletion
        UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'system_action',
            'action' => 'account_deleted',
            'data' => [
                'reason' => 'inactivity',
                'days_inactive' => $daysInactive,
                'deleted_at' => now()
            ],
            'created_at' => now()
        ]);
        
        // Send deletion notification email
        try {
            \Mail::to($user->email)->send(new \App\Mail\AccountDeletedMail($user, 'inactivity'));
        } catch (\Exception $e) {
            Log::error('Failed to send deletion email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
        
        Log::warning('User deleted due to inactivity', [
            'user_id' => $user->id,
            'email' => $user->email,
            'days_inactive' => $daysInactive
        ]);
    }
    
    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Clean inactive users job failed', [
            'action' => $this->action,
            'days_threshold' => $this->daysThreshold,
            'error' => $exception->getMessage()
        ]);
    }
}

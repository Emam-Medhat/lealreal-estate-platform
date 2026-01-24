<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_category',
        'action',
        'description',
        'details',
        'ip_address',
        'user_agent',
        'device_id',
        'session_id',
        'location_data',
        'request_data',
        'response_data',
        'metadata',
        'reference_id',
        'reference_type',
        'old_values',
        'new_values',
        'changes',
        'duration',
        'success',
        'error_code',
        'error_message',
        'warning_level',
        'security_level',
        'privacy_level',
        'compliance_flags',
        'audit_flags',
        'retention_period',
        'archived_at',
        'deleted_at',
        'purged_at',
        'restored_at',
        'exported_at',
        'imported_at',
        'synced_at',
        'backed_up_at',
        'restored_from_backup_at',
        'migrated_at',
    ];

    protected $casts = [
        'location_data' => 'json',
        'request_data' => 'json',
        'response_data' => 'json',
        'metadata' => 'json',
        'old_values' => 'json',
        'new_values' => 'json',
        'changes' => 'json',
        'duration' => 'integer',
        'success' => 'boolean',
        'compliance_flags' => 'json',
        'audit_flags' => 'json',
        'retention_period' => 'integer',
        'archived_at' => 'datetime',
        'deleted_at' => 'datetime',
        'purged_at' => 'datetime',
        'restored_at' => 'datetime',
        'exported_at' => 'datetime',
        'imported_at' => 'datetime',
        'synced_at' => 'datetime',
        'backed_up_at' => 'datetime',
        'restored_from_backup_at' => 'datetime',
        'migrated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(UserDevice::class, 'device_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSession::class, 'session_id');
    }

    public function getActivityTypeLabelAttribute(): string
    {
        return match($this->activity_type) {
            'login' => __('Login'),
            'logout' => __('Logout'),
            'register' => __('Registration'),
            'profile_update' => __('Profile Update'),
            'password_change' => __('Password Change'),
            'email_change' => __('Email Change'),
            'phone_change' => __('Phone Change'),
            'security_update' => __('Security Update'),
            'preference_update' => __('Preference Update'),
            'kyc_submission' => __('KYC Submission'),
            'kyc_verification' => __('KYC Verification'),
            'wallet_transaction' => __('Wallet Transaction'),
            'subscription_update' => __('Subscription Update'),
            'property_view' => __('Property View'),
            'property_search' => __('Property Search'),
            'property_favorite' => __('Property Favorite'),
            'property_comparison' => __('Property Comparison'),
            'property_inquiry' => __('Property Inquiry'),
            'property_booking' => __('Property Booking'),
            'property_review' => __('Property Review'),
            'property_rating' => __('Property Rating'),
            'agent_contact' => __('Agent Contact'),
            'message_send' => __('Message Send'),
            'message_receive' => __('Message Receive'),
            'notification_read' => __('Notification Read'),
            'notification_delete' => __('Notification Delete'),
            'file_upload' => __('File Upload'),
            'file_download' => __('File Download'),
            'file_delete' => __('File Delete'),
            'api_call' => __('API Call'),
            'system_access' => __('System Access'),
            'admin_action' => __('Admin Action'),
            'security_event' => __('Security Event'),
            'error_occurred' => __('Error Occurred'),
            'warning_triggered' => __('Warning Triggered'),
            'data_export' => __('Data Export'),
            'data_import' => __('Data Import'),
            'data_backup' => __('Data Backup'),
            'data_restore' => __('Data Restore'),
            'data_migrate' => __('Data Migration'),
            'data_archive' => __('Data Archive'),
            'data_purge' => __('Data Purge'),
            'audit_log' => __('Audit Log'),
            'compliance_check' => __('Compliance Check'),
            'performance_metric' => __('Performance Metric'),
            'usage_statistic' => __('Usage Statistic'),
            'system_health' => __('System Health'),
            'maintenance_task' => __('Maintenance Task'),
            'update_installed' => __('Update Installed'),
            'patch_applied' => __('Patch Applied'),
            'configuration_change' => __('Configuration Change'),
            'permission_change' => __('Permission Change'),
            'role_change' => __('Role Change'),
            'user_created' => __('User Created'),
            'user_updated' => __('User Updated'),
            'user_deleted' => __('User Deleted'),
            'user_suspended' => __('User Suspended'),
            'user_reactivated' => __('User Reactivated'),
            'user_banned' => __('User Banned'),
            'user_unbanned' => __('User Unbanned'),
            'login_attempt' => __('Login Attempt'),
            'login_success' => __('Login Success'),
            'login_failure' => __('Login Failure'),
            'logout_success' => __('Logout Success'),
            'logout_failure' => __('Logout Failure'),
            'password_reset' => __('Password Reset'),
            'password_reset_request' => __('Password Reset Request'),
            'email_verification' => __('Email Verification'),
            'phone_verification' => __('Phone Verification'),
            'two_factor_enabled' => __('Two Factor Enabled'),
            'two_factor_disabled' => __('Two Factor Disabled'),
            'two_factor_challenge' => __('Two Factor Challenge'),
            'session_created' => __('Session Created'),
            'session_destroyed' => __('Session Destroyed'),
            'device_registered' => __('Device Registered'),
            'device_unregistered' => __('Device Unregistered'),
            'social_login' => __('Social Login'),
            'social_logout' => __('Social Logout'),
            'social_disconnect' => __('Social Disconnect'),
            'api_key_generated' => __('API Key Generated'),
            'api_key_revoked' => __('API Key Revoked'),
            'token_generated' => __('Token Generated'),
            'token_revoked' => __('Token Revoked'),
            'token_refreshed' => __('Token Refreshed'),
            'rate_limit_hit' => __('Rate Limit Hit'),
            'rate_limit_reset' => __('Rate Limit Reset'),
            'cache_cleared' => __('Cache Cleared'),
            'cache_warmed' => __('Cache Warmed'),
            'index_rebuilt' => __('Index Rebuilt'),
            'search_performed' => __('Search Performed'),
            'filter_applied' => __('Filter Applied'),
            'sort_applied' => __('Sort Applied'),
            'pagination_used' => __('Pagination Used'),
            'export_started' => __('Export Started'),
            'export_completed' => __('Export Completed'),
            'export_failed' => __('Export Failed'),
            'import_started' => __('Import Started'),
            'import_completed' => __('Import Completed'),
            'import_failed' => __('Import Failed'),
            'backup_started' => __('Backup Started'),
            'backup_completed' => __('Backup Completed'),
            'backup_failed' => __('Backup Failed'),
            'restore_started' => __('Restore Started'),
            'restore_completed' => __('Restore Completed'),
            'restore_failed' => __('Restore Failed'),
            'migration_started' => __('Migration Started'),
            'migration_completed' => __('Migration Completed'),
            'migration_failed' => __('Migration Failed'),
            'archive_started' => __('Archive Started'),
            'archive_completed' => __('Archive Completed'),
            'archive_failed' => __('Archive Failed'),
            'purge_started' => __('Purge Started'),
            'purge_completed' => __('Purge Completed'),
            'purge_failed' => __('Purge Failed'),
            'audit_started' => __('Audit Started'),
            'audit_completed' => __('Audit Completed'),
            'audit_failed' => __('Audit Failed'),
            'compliance_started' => __('Compliance Started'),
            'compliance_completed' => __('Compliance Completed'),
            'compliance_failed' => __('Compliance Failed'),
            'performance_check' => __('Performance Check'),
            'health_check' => __('Health Check'),
            'maintenance_started' => __('Maintenance Started'),
            'maintenance_completed' => __('Maintenance Completed'),
            'update_started' => __('Update Started'),
            'update_completed' => __('Update Completed'),
            'update_failed' => __('Update Failed'),
            'patch_started' => __('Patch Started'),
            'patch_completed' => __('Patch Completed'),
            'patch_failed' => __('Patch Failed'),
            'configuration_updated' => __('Configuration Updated'),
            'permission_updated' => __('Permission Updated'),
            'role_updated' => __('Role Updated'),
            'user_created' => __('User Created'),
            'user_updated' => __('User Updated'),
            'user_deleted' => __('User Deleted'),
            'user_suspended' => __('User Suspended'),
            'user_reactivated' => __('User Reactivated'),
            'user_banned' => __('User Banned'),
            'user_unbanned' => __('User Unbanned'),
            default => __('Unknown')
        };
    }

    public function getActivityCategoryLabelAttribute(): string
    {
        return match($this->activity_category) {
            'authentication' => __('Authentication'),
            'authorization' => __('Authorization'),
            'profile' => __('Profile'),
            'security' => __('Security'),
            'privacy' => __('Privacy'),
            'compliance' => __('Compliance'),
            'audit' => __('Audit'),
            'system' => __('System'),
            'admin' => __('Admin'),
            'user' => __('User'),
            'property' => __('Property'),
            'agent' => __('Agent'),
            'message' => __('Message'),
            'notification' => __('Notification'),
            'file' => __('File'),
            'api' => __('API'),
            'data' => __('Data'),
            'backup' => __('Backup'),
            'restore' => __('Restore'),
            'migration' => __('Migration'),
            'archive' => __('Archive'),
            'purge' => __('Purge'),
            'export' => __('Export'),
            'import' => __('Import'),
            'search' => __('Search'),
            'filter' => __('Filter'),
            'sort' => __('Sort'),
            'pagination' => __('Pagination'),
            'cache' => __('Cache'),
            'index' => __('Index'),
            'performance' => __('Performance'),
            'health' => __('Health'),
            'maintenance' => __('Maintenance'),
            'update' => __('Update'),
            'patch' => __('Patch'),
            'configuration' => __('Configuration'),
            'permission' => __('Permission'),
            'role' => __('Role'),
            'device' => __('Device'),
            'session' => __('Session'),
            'social' => __('Social'),
            'token' => __('Token'),
            'key' => __('Key'),
            'rate_limit' => __('Rate Limit'),
            'error' => __('Error'),
            'warning' => __('Warning'),
            'info' => __('Info'),
            'debug' => __('Debug'),
            'critical' => __('Critical'),
            'emergency' => __('Emergency'),
            'alert' => __('Alert'),
            'notice' => __('Notice'),
            'log' => __('Log'),
            'metric' => __('Metric'),
            'statistic' => __('Statistic'),
            'report' => __('Report'),
            'dashboard' => __('Dashboard'),
            'analytics' => __('Analytics'),
            'monitoring' => __('Monitoring'),
            'tracking' => __('Tracking'),
            'logging' => __('Logging'),
            'recording' => __('Recording'),
            'capturing' => __('Capturing'),
            'collecting' => __('Collecting'),
            'gathering' => __('Gathering'),
            'aggregating' => __('Aggregating'),
            'summarizing' => __('Summarizing'),
            'processing' => __('Processing'),
            'handling' => __('Handling'),
            'managing' => __('Managing'),
            'controlling' => __('Controlling'),
            'overseeing' => __('Overseeing'),
            'supervising' => __('Supervising'),
            'monitoring' => __('Monitoring'),
            'tracking' => __('Tracking'),
            'logging' => __('Logging'),
            'recording' => __('Recording'),
            'capturing' => __('Capturing'),
            'collecting' => __('Collecting'),
            'gathering' => __('Gathering'),
            'aggregating' => __('Aggregating'),
            'summarizing' => __('Summarizing'),
            'processing' => __('Processing'),
            'handling' => __('Handling'),
            'managing' => __('Managing'),
            'controlling' => __('Controlling'),
            'overseeing' => __('Overseeing'),
            'supervising' => __('Supervising'),
            default => __('Unknown')
        };
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'create' => __('Create'),
            'read' => __('Read'),
            'update' => __('Update'),
            'delete' => __('Delete'),
            'view' => __('View'),
            'edit' => __('Edit'),
            'add' => __('Add'),
            'remove' => __('Remove'),
            'insert' => __('Insert'),
            'modify' => __('Modify'),
            'change' => __('Change'),
            'alter' => __('Alter'),
            'adjust' => __('Adjust'),
            'correct' => __('Correct'),
            'fix' => __('Fix'),
            'repair' => __('Repair'),
            'restore' => __('Restore'),
            'recover' => __('Recover'),
            'rescue' => __('Rescue'),
            'save' => __('Save'),
            'store' => __('Store'),
            'persist' => __('Persist'),
            'commit' => __('Commit'),
            'submit' => __('Submit'),
            'apply' => __('Apply'),
            'execute' => __('Execute'),
            'run' => __('Run'),
            'start' => __('Start'),
            'stop' => __('Stop'),
            'pause' => __('Pause'),
            'resume' => __('Resume'),
            'continue' => __('Continue'),
            'cancel' => __('Cancel'),
            'abort' => __('Abort'),
            'terminate' => __('Terminate'),
            'end' => __('End'),
            'finish' => __('Finish'),
            'complete' => __('Complete'),
            'done' => __('Done'),
            'success' => __('Success'),
            'failure' => __('Failure'),
            'error' => __('Error'),
            'warning' => __('Warning'),
            'info' => __('Info'),
            'debug' => __('Debug'),
            'critical' => __('Critical'),
            'emergency' => __('Emergency'),
            'alert' => __('Alert'),
            'notice' => __('Notice'),
            'log' => __('Log'),
            'metric' => __('Metric'),
            'statistic' => __('Statistic'),
            'report' => __('Report'),
            'dashboard' => __('Dashboard'),
            'analytics' => __('Analytics'),
            'monitoring' => __('Monitoring'),
            'tracking' => __('Tracking'),
            'logging' => __('Logging'),
            'recording' => __('Recording'),
            'capturing' => __('Capturing'),
            'collecting' => __('Collecting'),
            'gathering' => __('Gathering'),
            'aggregating' => __('Aggregating'),
            'summarizing' => __('Summarizing'),
            'processing' => __('Processing'),
            'handling' => __('Handling'),
            'managing' => __('Managing'),
            'controlling' => __('Controlling'),
            'overseeing' => __('Overseeing'),
            'supervising' => __('Supervising'),
            'monitoring' => __('Monitoring'),
            'tracking' => __('Tracking'),
            'logging' => __('Logging'),
            'recording' => __('Recording'),
            'capturing' => __('Capturing'),
            'collecting' => __('Collecting'),
            'gathering' => __('Gathering'),
            'aggregating' => __('Aggregating'),
            'summarizing' => __('Summarizing'),
            'processing' => __('Processing'),
            'handling' => __('Handling'),
            'managing' => __('Managing'),
            'controlling' => __('Controlling'),
            'overseeing' => __('Overseeing'),
            'supervising' => __('Supervising'),
            default => __('Unknown')
        };
    }

    public function getWarningLevelLabelAttribute(): string
    {
        return match($this->warning_level) {
            'low' => __('Low'),
            'medium' => __('Medium'),
            'high' => __('High'),
            'critical' => __('Critical'),
            'emergency' => __('Emergency'),
            default => __('None')
        };
    }

    public function getSecurityLevelLabelAttribute(): string
    {
        return match($this->security_level) {
            'public' => __('Public'),
            'internal' => __('Internal'),
            'confidential' => __('Confidential'),
            'secret' => __('Secret'),
            'top_secret' => __('Top Secret'),
            default => __('Unknown')
        };
    }

    public function getPrivacyLevelLabelAttribute(): string
    {
        return match($this->privacy_level) {
            'none' => __('None'),
            'basic' => __('Basic'),
            'standard' => __('Standard'),
            'enhanced' => __('Enhanced'),
            'maximum' => __('Maximum'),
            default => __('Unknown')
        };
    }

    public function isSuccess(): bool
    {
        return $this->success === true;
    }

    public function isFailure(): bool
    {
        return $this->success === false;
    }

    public function isWarning(): bool
    {
        return $this->warning_level !== null && $this->warning_level !== 'none';
    }

    public function isCritical(): bool
    {
        return $this->warning_level === 'critical' || $this->warning_level === 'emergency';
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    public function isDeleted(): bool
    {
        return $this->deleted_at !== null;
    }

    public function isPurged(): bool
    {
        return $this->purged_at !== null;
    }

    public function isRestored(): bool
    {
        return $this->restored_at !== null;
    }

    public function isExported(): bool
    {
        return $this->exported_at !== null;
    }

    public function isImported(): bool
    {
        return $this->imported_at !== null;
    }

    public function isSynced(): bool
    {
        return $this->synced_at !== null;
    }

    public function isBackedUp(): bool
    {
        return $this->backed_up_at !== null;
    }

    public function isRestoredFromBackup(): bool
    {
        return $this->restored_from_backup_at !== null;
    }

    public function isMigrated(): bool
    {
        return $this->migrated_at !== null;
    }

    public function archive(): void
    {
        $this->update(['archived_at' => now()]);
    }

    public function delete(): void
    {
        $this->update(['deleted_at' => now()]);
    }

    public function purge(): void
    {
        $this->update(['purged_at' => now()]);
    }

    public function restore(): void
    {
        $this->update([
            'restored_at' => now(),
            'deleted_at' => null,
            'purged_at' => null,
        ]);
    }

    public function export(): void
    {
        $this->update(['exported_at' => now()]);
    }

    public function import(): void
    {
        $this->update(['imported_at' => now()]);
    }

    public function sync(): void
    {
        $this->update(['synced_at' => now()]);
    }

    public function backup(): void
    {
        $this->update(['backed_up_at' => now()]);
    }

    public function restoreFromBackup(): void
    {
        $this->update(['restored_from_backup_at' => now()]);
    }

    public function migrate(): void
    {
        $this->update(['migrated_at' => now()]);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('activity_category', $category);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeWarning($query)
    {
        return $query->whereNotNull('warning_level')
                    ->where('warning_level', '!=', 'none');
    }

    public function scopeCritical($query)
    {
        return $query->whereIn('warning_level', ['critical', 'emergency']);
    }

    public function scopeBySecurityLevel($query, string $level)
    {
        return $query->where('security_level', $level);
    }

    public function scopeByPrivacyLevel($query, string $level)
    {
        return $query->where('privacy_level', $level);
    }

    public function scopeByDevice($query, int $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    public function scopeBySession($query, int $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeByIpAddress($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    public function scopeByReference($query, string $referenceType, int $referenceId)
    {
        return $query->where('reference_type', $referenceType)
                    ->where('reference_id', $referenceId);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    public function scopeThisYear($query)
    {
        return $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeDeleted($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    public function scopeNotDeleted($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopePurged($query)
    {
        return $query->whereNotNull('purged_at');
    }

    public function scopeNotPurged($query)
    {
        return $query->whereNull('purged_at');
    }

    public function scopeRestored($query)
    {
        return $query->whereNotNull('restored_at');
    }

    public function scopeNotRestored($query)
    {
        return $query->whereNull('restored_at');
    }

    public function scopeExported($query)
    {
        return $query->whereNotNull('exported_at');
    }

    public function scopeNotExported($query)
    {
        return $query->whereNull('exported_at');
    }

    public function scopeImported($query)
    {
        return $query->whereNotNull('imported_at');
    }

    public function scopeNotImported($query)
    {
        return $query->whereNull('imported_at');
    }

    public function scopeSynced($query)
    {
        return $query->whereNotNull('synced_at');
    }

    public function scopeNotSynced($query)
    {
        return $query->whereNull('synced_at');
    }

    public function scopeBackedUp($query)
    {
        return $query->whereNotNull('backed_up_at');
    }

    public function scopeNotBackedUp($query)
    {
        return $query->whereNull('backed_up_at');
    }

    public function scopeRestoredFromBackup($query)
    {
        return $query->whereNotNull('restored_from_backup_at');
    }

    public function scopeNotRestoredFromBackup($query)
    {
        return $query->whereNull('restored_from_backup_at');
    }

    public function scopeMigrated($query)
    {
        return $query->whereNotNull('migrated_at');
    }

    public function scopeNotMigrated($query)
    {
        return $query->whereNull('migrated_at');
    }
}

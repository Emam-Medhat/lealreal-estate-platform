<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

class ActivityLogService
{
    /**
     * Log user activity
     */
    public function logActivity(string $action, string $description, array $metadata = [], ?int $userId = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    /**
     * Log financial activity (high priority)
     */
    public function logFinancialActivity(string $action, string $description, array $metadata = [], ?int $userId = null): ActivityLog
    {
        return $this->logActivity($action, $description, array_merge($metadata, [
            'type' => 'financial',
            'priority' => 'high',
        ]), $userId);
    }

    /**
     * Log security activity (critical priority)
     */
    public function logSecurityActivity(string $action, string $description, array $metadata = [], ?int $userId = null): ActivityLog
    {
        return $this->logActivity($action, $description, array_merge($metadata, [
            'type' => 'security',
            'priority' => 'critical',
        ]), $userId);
    }

    /**
     * Log system activity
     */
    public function logSystemActivity(string $action, string $description, array $metadata = [], ?int $userId = null): ActivityLog
    {
        return $this->logActivity($action, $description, array_merge($metadata, [
            'type' => 'system',
            'priority' => 'medium',
        ]), $userId);
    }

    /**
     * Get activity logs with filtering
     */
    public function getActivityLogs(array $filters = [], int $perPage = 50)
    {
        $query = ActivityLog::with('user');

        // Filter by user
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter by action
        if (isset($filters['action'])) {
            $query->where('action', 'like', "%{$filters['action']}%");
        }

        // Filter by type
        if (isset($filters['type'])) {
            $query->whereJsonContains('metadata->type', $filters['type']);
        }

        // Filter by priority
        if (isset($filters['priority'])) {
            $query->whereJsonContains('metadata->priority', $filters['priority']);
        }

        // Filter by date range
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Search in description
        if (isset($filters['search'])) {
            $query->where('description', 'like', "%{$filters['search']}%");
        }

        return $query->latest('created_at')->paginate($perPage);
    }

    /**
     * Get user activity statistics
     */
    public function getUserActivityStats(int $userId = null, array $filters = []): array
    {
        $query = ActivityLog::when($userId, function ($q) use ($userId) {
            return $q->where('user_id', $userId);
        });

        // Apply date filters
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $total = $query->count();
        $financial = $query->clone()->whereJsonContains('metadata->type', 'financial')->count();
        $security = $query->clone()->whereJsonContains('metadata->type', 'security')->count();
        $system = $query->clone()->whereJsonContains('metadata->type', 'system')->count();

        // Get activity by day for the last 30 days
        $dailyActivity = $query->clone()
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get top actions
        $topActions = $query->clone()
            ->select('action', DB::raw('COUNT(*) as count'))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_activities' => $total,
            'financial_activities' => $financial,
            'security_activities' => $security,
            'system_activities' => $system,
            'daily_activity' => $dailyActivity,
            'top_actions' => $topActions,
        ];
    }

    /**
     * Get security audit trail
     */
    public function getSecurityAuditTrail(array $filters = []): array
    {
        $query = ActivityLog::whereJsonContains('metadata->type', 'security')
                            ->with('user');

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $criticalActivities = $query->clone()
            ->whereJsonContains('metadata->priority', 'critical')
            ->latest('created_at')
            ->limit(50)
            ->get();

        $allSecurityActivities = $query->latest('created_at')->paginate(50);

        // Get security statistics
        $stats = [
            'total_security_events' => $query->count(),
            'critical_events' => $query->clone()->whereJsonContains('metadata->priority', 'critical')->count(),
            'high_priority_events' => $query->clone()->whereJsonContains('metadata->priority', 'high')->count(),
            'failed_logins' => $query->clone()->where('action', 'login_failed')->count(),
            'successful_logins' => $query->clone()->where('action', 'login_success')->count(),
            'password_changes' => $query->clone()->where('action', 'password_changed')->count(),
            'permission_changes' => $query->clone()->where('action', 'permission_changed')->count(),
        ];

        return [
            'critical_activities' => $criticalActivities,
            'all_activities' => $allSecurityActivities,
            'statistics' => $stats,
        ];
    }

    /**
     * Get financial audit trail
     */
    public function getFinancialAuditTrail(array $filters = []): array
    {
        $query = ActivityLog::whereJsonContains('metadata->type', 'financial')
                            ->with('user');

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $financialActivities = $query->latest('created_at')->paginate(50);

        // Get financial statistics
        $stats = [
            'total_financial_activities' => $query->count(),
            'invoice_created' => $query->clone()->where('action', 'invoice_created')->count(),
            'invoice_updated' => $query->clone()->where('action', 'invoice_updated')->count(),
            'payment_processed' => $query->clone()->where('action', 'payment_processed')->count(),
            'payment_failed' => $query->clone()->where('action', 'payment_failed')->count(),
            'refund_processed' => $query->clone()->where('action', 'refund_processed')->count(),
        ];

        // Get financial summary by day
        $dailySummary = $query->clone()
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, action, COUNT(*) as count')
            ->groupBy('date', 'action')
            ->orderBy('date')
            ->get();

        return [
            'activities' => $financialActivities,
            'statistics' => $stats,
            'daily_summary' => $dailySummary,
        ];
    }

    /**
     * Clean old activity logs
     */
    public function cleanOldLogs(int $daysToKeep = 90): int
    {
        return ActivityLog::where('created_at', '<', now()->subDays($daysToKeep))
                           ->delete();
    }

    /**
     * Export activity logs
     */
    public function exportActivityLogs(array $filters = []): array
    {
        $query = ActivityLog::with('user');

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', 'like', "%{$filters['action']}%");
        }

        if (isset($filters['type'])) {
            $query->whereJsonContains('metadata->type', $filters['type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $logs = $query->latest('created_at')->get();

        return [
            'logs' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user->name ?? 'System',
                    'action' => $log->action,
                    'description' => $log->description,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                    'metadata' => $log->metadata,
                ];
            }),
            'exported_at' => now(),
            'total_count' => $logs->count(),
        ];
    }

    /**
     * Get real-time activity feed
     */
    public function getRealTimeActivityFeed(int $limit = 20): array
    {
        return ActivityLog::with('user')
                        ->latest('created_at')
                        ->limit($limit)
                        ->get()
                        ->map(function ($log) {
                            return [
                                'id' => $log->id,
                                'user' => $log->user->name ?? 'System',
                                'action' => $log->action,
                                'description' => $log->description,
                                'created_at' => $log->created_at->diffForHumans(),
                                'type' => $log->metadata['type'] ?? 'general',
                                'priority' => $log->metadata['priority'] ?? 'low',
                            ];
                        })
                        ->toArray();
    }
}

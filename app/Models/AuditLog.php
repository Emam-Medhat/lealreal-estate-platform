<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AuditLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'action',
        'details',
        'success',
        'risk_level',
        'ip_address',
        'user_agent',
        'location_data',
        'device_info',
        'additional_data',
        'session_id',
        'request_id',
        'response_time',
        'memory_usage',
        'processing_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'location_data' => 'array',
        'device_info' => 'array',
        'additional_data' => 'array',
        'memory_usage' => 'integer',
        'processing_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function fraudAlerts(): MorphMany
    {
        return $this->morphMany(FraudAlert::class, 'auditable');
    }

    public function complianceRecords(): MorphMany
    {
        return $this->morphMany(ComplianceRecord::class, 'auditable');
    }

    public function securityIncidents(): MorphMany
    {
        return $this->morphMany(SecurityIncident::class, 'auditable');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class, 'auditable');
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByAction($query, $action)
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

    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', $startDate, $endDate);
    }

    // Methods
    public static function logActivity($userId, $action, $details = [], $propertyId = null, $success = true, $riskLevel = 'low', $ipAddress = null, $userAgent = null, $locationData = [], $deviceInfo = []): void
    {
        $logEntry = [
            'user_id' => $userId,
            'property_id' => $propertyId,
            'action' => $action,
            'details' => json_encode($details),
            'success' => $success,
            'risk_level' => $riskLevel,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
            'location_data' => $locationData,
            'device_info' => $deviceInfo,
            'additional_data' => $additional_data,
            'session_id' => session()->getId(),
            'request_id' => request()->id(),
            'response_time' => microtime(true) - LARAVEL_START,
            'memory_usage' => memory_get_usage(true),
            'processing_time' => microtime(true) - LARAVEL_START,
            'created_at' => now(),
        ];

        try {
            \Log::info('Audit log created', $logEntry);
        } catch (\Exception $e) {
            \Log::error('Failed to create audit log', ['error' => $e->getMessage()]);
        }
    }

    public static function logSecurityEvent($userId, $event, $details = [], $propertyId = null, $severity = 'low'): void
    {
        self::logActivity($userId, $event, $details, $propertyId, true, $severity);
    }

    public static function logDataAccess($userId, $dataId, $action, $details = []): void
    {
        self::logActivity($userId, "data_access_{$action}", array_merge($details, ['data_id' => $dataId]), $propertyId, true, 'medium');
    }

    public static function logSystemEvent($event, $details = [], $severity = 'low'): void
    {
        self::logActivity(null, "system_{$event}", $details, null, true, $severity);
    }

    public static function logComplianceEvent($userId, $complianceType, $details = [], $propertyId = null): void
    {
        self::logActivity($userId, "compliance_{$complianceType}", $details, $propertyId, true, 'medium');
    }

    public static function logFraudEvent($userId, $fraudType, $details = [], $propertyId = null): void
    {
        self::logActivity($userId, "fraud_{$fraudType}", $details, $propertyId, false, 'high');
    }

    public static function logSecurityIncident($userId, $incidentType, $details = [], $propertyId = null): void
    {
        self::logActivity($userId, "security_incident_{$incidentType}", $details, $propertyId, false, 'critical');
    }

    public static function logDataModification($userId, $dataType, $recordId, $changes, $propertyId = null): void
    {
        self::logActivity($userId, "data_modification", [
            'data_type' => $dataType,
            'record_id' => $recordId,
            'changes' => $changes,
        ], $propertyId, true, 'medium');
    }

    public function getRecentActivity($userId, $days = 7, $limit = 50): array
    {
        return self::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'success' => $log->success,
                    'risk_level' => $log->risk_level,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'details' => json_decode($log->details),
                ];
            })
            ->toArray();
    }

    public function getActivityTrends($userId, $days = 30): array
    {
        return self::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getActionFrequency($userId, $days = 30): array
    {
        return self::where('user_id', $userId)
            ->selectRaw('action, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();
    }

    public function getRiskDistribution($userId, $days = 30): array
    {
        return self::where('user_id', $userId)
            ->selectRaw('risk_level, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('risk_level')
            ->orderBy('risk_level')
            ->get();
    }

    public function getFailedAttempts($userId, $days = 7): array
    {
        return self::where('user_id', $userId)
            ->where('success', false)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAccessPatterns($userId, $days = 30): array
    {
        return self::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('success', true)
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getDeviceAnalysis($userId, $days = 30): array
    {
        return [
            'device_distribution' => $this->getDeviceDistribution($userId),
            'unusual_devices' => $this->getUnusualDevices($userId),
            'device_anomalies' => $this->getDeviceAnomalies($userId),
            'browser_distribution' => $this->getBrowserDistribution($userId),
            'os_distribution' => $this->getOSDistribution($userId),
        ];
    }

    public function getLocationAnalysis($userId, $days = 30): array
    {
        return [
            'location_distribution' => $this->getLocationDistribution($userId),
            'unusual_locations' => $this->getUnusualLocations($userId),
            'location_anomalies' => $this->getLocationAnomalies($userId),
            'geographic_distribution' => $this->getGeographicDistribution($userId),
        ];
    }

    public function getComplianceMetrics($userId, $days = 30): array
    {
        return [
            'total_activities' => $this->where('user_id', $userId)->count(),
            'successful_activities' => $this->where('user_id', $userId)->where('success', true)->count(),
            'failed_activities' => $this->where('user_id', $userId)->where('success', false)->count(),
            'success_rate' => $this->calculateSuccessRate($userId),
            'high_risk_activities' => $this->where('user_id', $userId)->where('risk_level', 'high')->count(),
            'compliance_score' => $this->calculateComplianceScore($userId),
            'security_score' => $this->calculateSecurityScore($userId),
        ];
    }

    public function calculateSuccessRate($userId): float
    {
        $total = self::where('user_id', $userId)->count();
        $successful = self::where('user_id', $userId)->where('success', true)->count();

        return $total > 0 ? ($successful / $total) * 100 : 0;
    }

    public function calculateSecurityScore($userId): int
    {
        $totalScore = 0;
        $logs = self::where('user_id', $userId)->get();

        foreach ($logs as $log) {
            switch ($log->risk_level) {
                case 'critical':
                    $totalScore += 40;
                    break;
                case 'high':
                    $totalScore += 30;
                    break;
                case 'medium':
                    $totalScore += 20;
                    break;
                case 'low':
                    $totalScore += 10;
                    break;
            }

            // Additional scoring for failed attempts
            if (!$log->success) {
                $totalScore += 5;
            }
        }

        return min(100, $totalScore);
    }

    private function calculateComplianceScore($userId): int
    {
        $totalScore = 100;
        $logs = self::where('user_id', $userId)->get();

        foreach ($logs as $log) {
            // Check for compliance violations
            if ($log->action === 'compliance_violation') {
                $totalScore -= 10;
            }

            // Check for security violations
            if ($log->action === 'security_violation') {
                $totalScore -= 15;
            }
        }

        return max(0, $totalScore);
    }

    private function getDeviceDistribution($userId): array
    {
        $devices = AccessLog::where('user_id', $userId)
            ->whereNotNull('device_info->fingerprint')
            ->selectRaw('device_info->fingerprint, COUNT(*) as count')
            ->groupBy('device_info->fingerprint')
            ->orderBy('count', 'desc')
            ->get();

        return $devices->map(function ($device) {
            return [
                'fingerprint' => $device->{'device_info->fingerprint'},
                'count' => $device->count,
                'percentage' => ($device->count / $devices->sum('count')) * 100,
            ];
        })->toArray();
    }

    private function getUnusualDevices($userId): array
    {
        return AccessLog::where('user_id', $userId)
            ->whereNotNull('device_info->fingerprint')
            ->selectRaw('device_info->fingerprint, COUNT(*) as count')
            ->groupBy('device_info->fingerprint')
            ->havingRaw('COUNT(*) > 5')
            ->orderBy('count', 'desc')
            ->pluck('device_info->fingerprint');
    }

    private function getDeviceAnomalies($userId): array
    {
        return AccessLog::where('user_id', $userId)
            ->whereNotNull('device_info')
            ->selectRaw('device_info->fingerprint, device_info->os, device_info->browser, COUNT(*) as count')
            ->groupBy('device_info->os, device_info->browser')
            ->havingRaw('COUNT(*) > 3')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getBrowserDistribution($userId): array
    {
        return AccessLog::where('user_id', $userId)
            ->whereNotNull('device_info->browser')
            ->selectRaw('device_info->browser, COUNT(*) as count')
            ->groupBy('device_info->browser')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getOSDistribution($userId): array
    {
        return AccessLog::where('user_id', $userId)
            ->whereNotNull('device_info->os')
            ->selectRaw('device_info->os, COUNT(*) as count')
            ->groupBy('device_info->os')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getLocationDistribution($userId): array
    {
        return AccessLog::where('user_id', $userId)
            ->whereNotNull('location_data->location')
            ->selectRaw('location_data->location, COUNT(*) as count')
            ->groupBy('location_data->location')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getGeographicDistribution($userId): array
    {
        return AccessLog::where('user_id', $userId)
            ->whereNotNull('location_data->location')
            ->selectRaw('location_data->country, COUNT(*) as count')
            ->groupBy('location_data->country')
            ->orderBy('count', 'desc')
            ->get();
    }

    public function generateReport(array $filters = []): array
    {
        $query = self::query();

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['property_id'])) {
            $query->where('property_id', $filters['property_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', 'like', '%' . $filters['action'] . '%');
        }

        if (isset($filters['risk_level'])) {
            $query->where('risk_level', $filters['risk_level']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_range'])) {
            switch ($filters['date_range']) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'last_week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'last_month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
                case 'last_quarter':
                    $query->where('created_at', '>=', now()->subQuarter());
                    break;
                case 'last_year':
                    $query->where('created_at', '>=', now()->subYear());
                    break;
            }
        }

        if (isset($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        // Get results
        $results = $query->orderBy('created_at', 'desc')->get();

        return [
            'total_logs' => $results->count(),
            'success_rate' => $this->calculateSuccessRate($filters['user_id'] ?? null),
            'risk_distribution' => $this->getRiskDistribution($filters['user_id'] ?? null),
            'action_frequency' => $this->getActionFrequency($filters['user_id'] ?? null),
            'trends' => $this->getActivityTrends($filters['user_id'] ?? null),
            'compliance_metrics' => $this->getComplianceMetrics($filters['user_id'] ?? null),
            'security_metrics' => $this->getSecurityMetrics($filters['user_id'] ?? null),
            'device_analysis' => $this->getDeviceAnalysis($filters['user_id'] ?? null),
            'location_analysis' => $this->getLocationAnalysis($filters['user_id'] ?? null),
            'generated_at' => now(),
        ];
    }

    public function exportToCSV(array $filters = []): string
    {
        $data = $this->generateReport($filters);
        
        $filename = 'audit_logs_' . date('Y-m-d') . '. date('H-i-s') . '. date('s') . '. date('i-s');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'User ID', 'Property ID', 'Action', 'Success', 'Risk Level',
                'IP Address', 'User Agent', 'Created At'
            ]);

            // Data
            foreach ($data['logs'] as $log) {
                fputcsv($file, [
                    $log['id'],
                    $log['user_id'],
                    $log['property_id'],
                    $log['action'],
                    $log['success'] ? 'Yes' : 'No',
                    $log['risk_level'],
                    $log['ip_address'],
                    $log['user_agent'],
                    $log['created_at'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportToExcel(array $filters = []): string
    {
        // Implementation for Excel export
        return response()->download('audit_logs.xlsx');
    }

    public function exportToPDF(array $filters = []): string
    {
        // Implementation for PDF export
        return response()->download('audit_logs.pdf');
    }

    public function getMetrics(array $filters = []): array
    {
        $query = self::query();

        // Apply same filters as generateReport
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $results = $query->get();

        return [
            'total_logs' => $results->count(),
            'success_rate' => $this->calculateSuccessRate($filters['user_id'] ?? null),
            'failed_attempts' => $results->where('success', false)->count(),
            'high_risk_activities' => $results->where('risk_level', 'high')->count(),
            'average_processing_time' => $results->avg('processing_time'),
            'peak_hours' => $this->getPeakActivityHours($filters['user_id'] ?? null),
            'most_active_hour' => $this->getMostActiveHour($filters['user_id'] ?? null),
            'device_usage' => $this->getDeviceUsage($filters['user_id'] ?? null),
            'location_coverage' => $this->getLocationCoverage($filters['user_id'] ?? null),
        ];
    }

    private function getPeakActivityHours($userId): array
    {
        return AccessLog::where('user_id', $userId)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->where('success', true)
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->take(5)
            ->pluck('hour');
    }

    private function getMostActiveHour($userId): string
    {
        $peakHour = $this->getPeakActivityHours($userId);
        return $peakHour[0] ?? '00:00';
    }

    private function getDeviceUsage($userId): array
    {
        $devices = $this->getDeviceDistribution($userId);
        $totalUsage = array_sum(array_column($devices, 'percentage'));
        
        return [
            'total_devices' => count($devices),
            'total_usage' => $totalUsage,
            'most_used_device' => $devices[0]['fingerprint'] ?? 'unknown',
            'usage_distribution' => $devices,
            'high_usage_devices' => array_filter($devices, function ($device) {
                return $device['percentage'] > 50;
            }),
        ];
    }

    private function getLocationCoverage($userId): array
    {
        $locations = $this->getLocationDistribution($userId);
        $totalLocations = array_sum(array_column($locations, 'count'));
        
        return [
            'total_locations' => count($locations),
            'coverage_percentage' => $totalLocations > 0 ? ($totalLocations / count($locations)) * 100 : 0,
            'geographic_coverage' => $this->getGeographicCoverage($userId),
            'location_distribution' => $locations,
        ];
    }

    private function getGeographicCoverage($userId): array
    {
        $locations = $this->getGeographicDistribution($userId);
        $countries = array_unique(array_column($locations, 'country'));
        
        return [
            'countries_covered' => count($countries),
            'coverage_percentage' => count($countries) / count($locations) * 100,
            'countries' => $countries,
        ];
    }
}

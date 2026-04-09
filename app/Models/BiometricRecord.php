<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BiometricRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'biometric_type',
        'biometric_data_encrypted',
        'biometric_template',
        'biometric_hash',
        'quality_score',
        'enrollment_quality',
        'verification_threshold',
        'false_acceptance_rate',
        'false_rejection_rate',
        'device_id',
        'device_info',
        'capture_method',
        'capture_quality_metrics',
        'template_version',
        'algorithm_used',
        'processing_time',
        'storage_location',
        'backup_locations',
        'encryption_method',
        'key_identifier',
        'access_log',
        'verification_attempts',
        'successful_verifications',
        'failed_verifications',
        'last_used_at',
        'expiry_date',
        'revoked_at',
        'revocation_reason',
        'compliance_standards',
        'audit_trail',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'biometric_data_encrypted' => 'encrypted',
        'biometric_template' => 'array',
        'device_info' => 'array',
        'capture_quality_metrics' => 'array',
        'backup_locations' => 'array',
        'access_log' => 'array',
        'compliance_standards' => 'array',
        'audit_trail' => 'array',
        'metadata' => 'array',
        'quality_score' => 'decimal:2',
        'enrollment_quality' => 'decimal:2',
        'verification_threshold' => 'decimal:2',
        'false_acceptance_rate' => 'decimal:4',
        'false_rejection_rate' => 'decimal:4',
        'processing_time' => 'integer',
        'verification_attempts' => 'integer',
        'successful_verifications' => 'integer',
        'failed_verifications' => 'integer',
        'last_used_at' => 'datetime',
        'expiry_date' => 'datetime',
        'revoked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'last_used_at' => 'datetime',
        'expiry_date' => 'datetime',
        'revoked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'biometric_data_encrypted',
        'biometric_template',
        'key_identifier',
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

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
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

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('biometric_type', $type);
    }

    public function scopeByDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('revoked_at')
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>', now());
            });
    }

    public function scopeRevoked($query)
    {
        return $query->whereNotNull('revoked_at');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
            ->where('expiry_date', '>', now());
    }

    public function scopeHighQuality($query)
    {
        return $query->where('quality_score', '>=', 0.8);
    }

    public function scopeLowQuality($query)
    {
        return $query->where('quality_score', '<', 0.5);
    }

    public function scopeByAlgorithm($query, $algorithm)
    {
        return $query->where('algorithm_used', $algorithm);
    }

    // Methods
    public function verifyBiometric($inputData, $threshold = null): array
    {
        $this->verification_attempts++;
        $threshold = $threshold ?? $this->verification_threshold;
        
        $verification = [
            'biometric_id' => $this->id,
            'biometric_type' => $this->biometric_type,
            'verification_time' => now(),
            'device_id' => $this->device_id,
            'threshold_used' => $threshold,
            'match_score' => 0,
            'is_match' => false,
            'processing_time' => 0,
            'quality_score' => 0,
        ];
        
        try {
            $startTime = microtime(true);
            
            // Simulate biometric verification
            $matchScore = $this->calculateMatchScore($inputData);
            $qualityScore = $this->assessInputQuality($inputData);
            
            $verification['match_score'] = $matchScore;
            $verification['quality_score'] = $qualityScore;
            $verification['is_match'] = $matchScore >= $threshold && $qualityScore >= 0.5;
            $verification['processing_time'] = (microtime(true) - $startTime) * 1000; // in milliseconds
            
            if ($verification['is_match']) {
                $this->successful_verifications++;
                $this->last_used_at = now();
            } else {
                $this->failed_verifications++;
            }
            
            // Log access attempt
            $this->logAccessAttempt($verification);
            
        } catch (\Exception $e) {
            $verification['error'] = $e->getMessage();
            $this->failed_verifications++;
        }
        
        $this->save();
        
        return $verification;
    }

    private function calculateMatchScore($inputData): float
    {
        // Simulate biometric matching algorithm
        $baseScore = rand(0, 100) / 100;
        
        // Adjust based on quality
        $qualityMultiplier = $this->quality_score ?? 0.8;
        
        // Adjust based on algorithm
        $algorithmMultiplier = $this->getAlgorithmMultiplier();
        
        return min(1.0, $baseScore * $qualityMultiplier * $algorithmMultiplier);
    }

    private function assessInputQuality($inputData): float
    {
        // Simulate quality assessment
        return rand(50, 100) / 100;
    }

    private function getAlgorithmMultiplier(): float
    {
        $multipliers = [
            'minutiae' => 0.95,
            'pattern' => 0.85,
            'texture' => 0.90,
            'iris' => 0.98,
            'face' => 0.88,
            'voice' => 0.82,
        ];
        
        return $multipliers[$this->algorithm_used] ?? 0.85;
    }

    public function logAccessAttempt($verification): void
    {
        $logEntry = [
            'timestamp' => $verification['verification_time'],
            'device_id' => $verification['device_id'],
            'match_score' => $verification['match_score'],
            'quality_score' => $verification['quality_score'],
            'is_match' => $verification['is_match'],
            'processing_time' => $verification['processing_time'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];
        
        $this->access_log[] = $logEntry;
        
        $this->audit_trail[] = [
            'action' => 'biometric_verification',
            'result' => $verification['is_match'] ? 'success' : 'failure',
            'match_score' => $verification['match_score'],
            'timestamp' => now(),
            'ip_address' => request()->ip(),
        ];
    }

    public function updateTemplate($newData, $deviceInfo = []): array
    {
        $update = [
            'previous_template_version' => $this->template_version,
            'new_template_version' => $this->template_version + 1,
            'update_time' => now(),
            'device_info' => $deviceInfo,
            'quality_score' => 0,
            'success' => false,
        ];
        
        try {
            // Encrypt and store new biometric data
            $this->biometric_data_encrypted = $this->encryptBiometricData($newData);
            
            // Generate new template
            $this->biometric_template = $this->generateTemplate($newData);
            $this->template_version = $update['new_template_version'];
            
            // Update quality metrics
            $this->quality_score = $this->calculateQualityScore($newData);
            $update['quality_score'] = $this->quality_score;
            
            $update['success'] = true;
            
            $this->audit_trail[] = [
                'action' => 'template_updated',
                'old_version' => $update['previous_template_version'],
                'new_version' => $update['new_template_version'],
                'quality_score' => $this->quality_score,
                'timestamp' => now(),
                'user_id' => auth()->id(),
            ];
            
        } catch (\Exception $e) {
            $update['error'] = $e->getMessage();
        }
        
        $this->save();
        
        return $update;
    }

    private function encryptBiometricData($data): string
    {
        // Simulate biometric data encryption
        return 'encrypted_' . base64_encode(json_encode($data));
    }

    private function generateTemplate($data): array
    {
        // Simulate template generation
        return [
            'features' => array_fill(0, 100, rand(0, 255)),
            'minutiae' => array_fill(0, 50, ['x' => rand(0, 500), 'y' => rand(0, 500), 'angle' => rand(0, 360)]),
            'quality_metrics' => [
                'signal_to_noise' => rand(20, 40) / 10,
                'clarity' => rand(60, 100) / 100,
                'completeness' => rand(70, 100) / 100,
            ],
            'algorithm_version' => $this->algorithm_used,
            'generated_at' => now(),
        ];
    }

    private function calculateQualityScore($data): float
    {
        // Simulate quality score calculation
        return rand(60, 100) / 100;
    }

    public function revoke($reason = 'user_request'): void
    {
        $this->revoked_at = now();
        $this->revocation_reason = $reason;
        
        $this->audit_trail[] = [
            'action' => 'biometric_revoked',
            'reason' => $reason,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];
        
        $this->save();
    }

    public function extendExpiry($days): void
    {
        $this->expiry_date = $this->expiry_date->addDays($days);
        
        $this->audit_trail[] = [
            'action' => 'expiry_extended',
            'days_extended' => $days,
            'new_expiry_date' => $this->expiry_date,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];
        
        $this->save();
    }

    public function calculatePerformanceMetrics(): array
    {
        $totalAttempts = $this->verification_attempts;
        $successfulAttempts = $this->successful_verifications;
        $failedAttempts = $this->failed_verifications;
        
        return [
            'total_attempts' => $totalAttempts,
            'successful_verifications' => $successfulAttempts,
            'failed_verifications' => $failedAttempts,
            'success_rate' => $totalAttempts > 0 ? ($successfulAttempts / $totalAttempts) * 100 : 0,
            'failure_rate' => $totalAttempts > 0 ? ($failedAttempts / $totalAttempts) * 100 : 0,
            'average_processing_time' => $this->calculateAverageProcessingTime(),
            'quality_score' => $this->quality_score,
            'false_acceptance_rate' => $this->false_acceptance_rate,
            'false_rejection_rate' => $this->false_rejection_rate,
            'last_used' => $this->last_used_at,
            'days_since_last_use' => $this->last_used_at ? now()->diffInDays($this->last_used_at) : null,
        ];
    }

    private function calculateAverageProcessingTime(): float
    {
        if (empty($this->access_log)) {
            return 0;
        }
        
        $totalTime = 0;
        $count = 0;
        
        foreach ($this->access_log as $log) {
            if (isset($log['processing_time'])) {
                $totalTime += $log['processing_time'];
                $count++;
            }
        }
        
        return $count > 0 ? $totalTime / $count : 0;
    }

    public function detectAnomalies(): array
    {
        $anomalies = [];
        
        // Check for unusual access patterns
        $recentAttempts = $this->getRecentAttempts(24); // Last 24 hours
        if (count($recentAttempts) > 10) {
            $anomalies[] = [
                'type' => 'high_frequency_attempts',
                'count' => count($recentAttempts),
                'timeframe' => '24 hours',
                'severity' => 'medium',
            ];
        }
        
        // Check for low success rate
        $metrics = $this->calculatePerformanceMetrics();
        if ($metrics['success_rate'] < 50 && $metrics['total_attempts'] > 5) {
            $anomalies[] = [
                'type' => 'low_success_rate',
                'success_rate' => $metrics['success_rate'],
                'severity' => 'high',
            ];
        }
        
        // Check for unusual devices
        $devices = $this->getUniqueDevices();
        if (count($devices) > 3) {
            $anomalies[] = [
                'type' => 'multiple_devices',
                'device_count' => count($devices),
                'devices' => $devices,
                'severity' => 'medium',
            ];
        }
        
        // Check for quality degradation
        if ($this->quality_score < 0.6) {
            $anomalies[] = [
                'type' => 'quality_degradation',
                'quality_score' => $this->quality_score,
                'severity' => 'low',
            ];
        }
        
        return $anomalies;
    }

    private function getRecentAttempts($hours): array
    {
        $cutoff = now()->subHours($hours);
        $recent = [];
        
        foreach ($this->access_log as $log) {
            $timestamp = \Carbon\Carbon::parse($log['timestamp']);
            if ($timestamp->greaterThan($cutoff)) {
                $recent[] = $log;
            }
        }
        
        return $recent;
    }

    private function getUniqueDevices(): array
    {
        $devices = [];
        
        foreach ($this->access_log as $log) {
            if (isset($log['device_id'])) {
                $devices[] = $log['device_id'];
            }
        }
        
        return array_unique($devices);
    }

    public function generateComplianceReport(): array
    {
        return [
            'biometric_id' => $this->id,
            'user_id' => $this->user_id,
            'biometric_type' => $this->biometric_type,
            'compliance_standards' => $this->compliance_standards,
            'data_protection' => [
                'encryption_method' => $this->encryption_method,
                'storage_location' => $this->storage_location,
                'backup_locations' => $this->backup_locations,
                'key_management' => $this->key_identifier ? 'secure' : 'needs_improvement',
            ],
            'quality_metrics' => [
                'enrollment_quality' => $this->enrollment_quality,
                'current_quality' => $this->quality_score,
                'false_acceptance_rate' => $this->false_acceptance_rate,
                'false_rejection_rate' => $this->false_rejection_rate,
            ],
            'performance_metrics' => $this->calculatePerformanceMetrics(),
            'security_metrics' => [
                'revoked' => $this->revoked_at ? true : false,
                'expired' => $this->expiry_date && $this->expiry_date->isPast(),
                'anomalies_detected' => $this->detectAnomalies(),
                'access_patterns' => $this->analyzeAccessPatterns(),
            ],
            'audit_trail_summary' => $this->summarizeAuditTrail(),
            'recommendations' => $this->generateRecommendations(),
            'generated_at' => now(),
        ];
    }

    private function analyzeAccessPatterns(): array
    {
        if (empty($this->access_log)) {
            return ['status' => 'no_data'];
        }
        
        $patterns = [
            'peak_hours' => $this->getPeakAccessHours(),
            'average_daily_attempts' => $this->getAverageDailyAttempts(),
            'most_used_device' => $this->getMostUsedDevice(),
            'geographic_distribution' => $this->getGeographicDistribution(),
        ];
        
        return $patterns;
    }

    private function getPeakAccessHours(): array
    {
        $hourCounts = [];
        
        foreach ($this->access_log as $log) {
            $hour = \Carbon\Carbon::parse($log['timestamp'])->hour;
            $hourCounts[$hour] = ($hourCounts[$hour] ?? 0) + 1;
        }
        
        arsort($hourCounts);
        
        return array_slice($hourCounts, 0, 3, true);
    }

    private function getAverageDailyAttempts(): float
    {
        if (empty($this->access_log)) {
            return 0;
        }
        
        $days = \Carbon\Carbon::parse($this->created_at)->diffInDays(now()) + 1;
        return count($this->access_log) / $days;
    }

    private function getMostUsedDevice(): string
    {
        $deviceCounts = [];
        
        foreach ($this->access_log as $log) {
            $device = $log['device_id'] ?? 'unknown';
            $deviceCounts[$device] = ($deviceCounts[$device] ?? 0) + 1;
        }
        
        arsort($deviceCounts);
        
        return array_key_first($deviceCounts) ?? 'unknown';
    }

    private function getGeographicDistribution(): array
    {
        $locations = [];
        
        foreach ($this->access_log as $log) {
            $location = $log['ip_address'] ?? 'unknown';
            $locations[$location] = ($locations[$location] ?? 0) + 1;
        }
        
        return $locations;
    }

    private function summarizeAuditTrail(): array
    {
        $summary = [
            'total_events' => count($this->audit_trail),
            'events_by_type' => [],
            'recent_events' => [],
        ];
        
        foreach ($this->audit_trail as $event) {
            $action = $event['action'] ?? 'unknown';
            $summary['events_by_type'][$action] = ($summary['events_by_type'][$action] ?? 0) + 1;
        }
        
        $summary['recent_events'] = array_slice($this->audit_trail, -5);
        
        return $summary;
    }

    private function generateRecommendations(): array
    {
        $recommendations = [];
        $metrics = $this->calculatePerformanceMetrics();
        $anomalies = $this->detectAnomalies();
        
        if ($metrics['success_rate'] < 80) {
            $recommendations[] = 'Consider re-enrolling biometric data due to low success rate';
        }
        
        if ($this->quality_score < 0.7) {
            $recommendations[] = 'Biometric quality is below optimal threshold, consider recapture';
        }
        
        if (!empty($anomalies)) {
            $recommendations[] = 'Security anomalies detected, review access patterns';
        }
        
        if ($this->expiry_date && $this->expiry_date->diffInDays(now()) < 30) {
            $recommendations[] = 'Biometric record will expire soon, schedule renewal';
        }
        
        if ($metrics['average_processing_time'] > 1000) {
            $recommendations[] = 'Processing time is high, consider algorithm optimization';
        }
        
        return $recommendations;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isRevoked();
    }

    public static function getBiometricMetrics($filters = []): array
    {
        $query = self::query();
        
        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        if (isset($filters['biometric_type'])) {
            $query->where('biometric_type', $filters['biometric_type']);
        }
        
        $records = $query->get();
        
        return [
            'total_records' => $records->count(),
            'active_records' => $records->filter(function ($record) {
                return $record->isValid();
            })->count(),
            'revoked_records' => $records->filter(function ($record) {
                return $record->isRevoked();
            })->count(),
            'expired_records' => $records->filter(function ($record) {
                return $record->isExpired();
            })->count(),
            'average_quality_score' => $records->avg('quality_score'),
            'total_verifications' => $records->sum('verification_attempts'),
            'successful_verifications' => $records->sum('successful_verifications'),
            'failed_verifications' => $records->sum('failed_verifications'),
            'records_by_type' => $records->groupBy('biometric_type')->map->count(),
            'records_by_algorithm' => $records->groupBy('algorithm_used')->map->count(),
            'average_success_rate' => $records->map(function ($record) {
                $total = $record->verification_attempts;
                return $total > 0 ? ($record->successful_verifications / $total) * 100 : 0;
            })->avg(),
        ];
    }
}

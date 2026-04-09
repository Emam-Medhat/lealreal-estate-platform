<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AccessControl extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'access_level',
        'access_permissions',
        'time_restrictions',
        'ip_restrictions',
        'device_restrictions',
        'location_restrictions',
        'biometric_required',
        'two_factor_required',
        'session_timeout',
        'concurrent_sessions',
        'emergency_access',
        'emergency_contacts',
        'audit_frequency',
        'notification_settings',
        'access_code',
        'qr_code',
        'status',
        'last_access_attempt',
        'access_count',
        'failed_attempts',
        'last_access_at',
        'access_log',
        'security_policies',
        'compliance_flags',
        'risk_assessment',
        'access_patterns',
        'anomaly_detection',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'access_permissions' => 'array',
        'time_restrictions' => 'array',
        'ip_restrictions' => 'array',
        'device_restrictions' => 'array',
        'location_restrictions' => 'array',
        'emergency_contacts' => 'array',
        'notification_settings' => 'array',
        'biometric_required' => 'boolean',
        'two_factor_required' => 'boolean',
        'emergency_access' => 'boolean',
        'compliance_flags' => 'array',
        'risk_assessment' => 'array',
        'access_patterns' => 'array',
        'anomaly_detection' => 'array',
        'last_access_attempt' => 'datetime',
        'last_access_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'last_access_attempt' => 'datetime',
        'last_access_at' => 'datetime',
        'created_at' => 'time',
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

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class, 'access_control_id');
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByAccessLevel($query, $level)
    {
        return $query->where('access_level', $level);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeHighRisk($query)
    {
        return $query->where('risk_assessment->risk_level', 'high');
    }

    public function scopePendingReview($query)
    {
        return $query->where('compliance_flags->review_required', true);
    }

    public function scopeWithBiometric($query)
    {
        return $query->where('biometric_required', true);
    }

    public function scopeWithTwoFactor($query)
    {
        return $query->where('two_factor_required', true);
    }

    // Methods
    public function checkAccess($userId, string $action, array $context = []): array
    {
        $accessResult = [
            'granted' => false,
            'reason' => '',
            'access_level' => null,
            'expires_at' => null,
            'additional_requirements' => [],
            'risk_score' => 0,
        ];

        // Check if user has permission
        $userPermission = $this->getUserPermission($userId);
        if (!$userPermission) {
            $accessResult['reason'] = 'المستخدم ليس لديه صلاحية الوصول';
            return $accessResult;
        }

        // Check if permission level is sufficient for the requested action
        if (!$this->isPermissionSufficient($userPermission['permission_level'], $action)) {
            $accessResult['reason'] = 'مستوى الصلاحية غير كافي للإجراء المطلوب';
            return $accessResult;
        }

        // Check time restrictions
        if (!$this->checkTimeRestrictions()) {
            $accessResult['reason'] = 'الوصول مسموح في هذا الوقت';
            return $accessResult;
        }

        // Check IP restrictions
        if (!$this->checkIPRestrictions($context['ip_address'] ?? request()->ip())) {
            $accessResult['reason'] = 'عنوان IP غير مصرح به';
            return $accessResult;
        }

        // Check device restrictions
        if (!$this->checkDeviceRestrictions($context['device_info'] ?? [])) {
            $accessResult['reason'] 'الجهاز غير مصرح به';
            return $accessResult;
        }

        // Check location restrictions
        if (!$this->checkLocationRestrictions($context['location_data'] ?? [])) {
            $accessResult['reason'] = 'الموقع غير مصرح به';
            return $accessResult;
        }

        // Check biometric requirement
        if ($this->biometric_required) {
            $accessResult['additional_requirements'][] = 'التحقق البيومتري';
        }

        // Check two-factor requirement
        if ($this->two_factor_required) {
            $accessResult['additional_requirements'][] = 'المصادقة الثنائية';
        }

        // All checks passed
        $accessResult['granted'] = true;
        $accessResult['access_level'] = $userPermission['permission_level'];
        $accessResult['risk_score'] = $this->calculateAccessRisk($userId, $context);

        // Log access attempt
        $this->logAccessAttempt($userId, $action, $accessResult);

        return $accessResult;
    }

    public function grantAccess($userId, string $permissionLevel, string $expiresAt = null, string $purpose = ''): bool
    {
        $permissions = $this->access_permissions ?? [];
        
        // Check if user already has access
        $existingPermission = $this->getUserPermission($userId);
        if ($existingPermission) {
            // Update existing permission
            $this->updatePermission($userId, $permissionLevel, $expiresAt, $purpose);
            return true;
        }

        // Add new permission
        $permissions[] = [
            'user_id' => $userId,
            'permission_level' => $permissionLevel,
            'expires_at' => $expiresAt,
            'purpose' => $purpose,
            'granted_at' => now(),
            'granted_by' => auth()->id(),
            'status' => 'active',
        ];

        $this->update(['access_permissions' => $permissions]);

        // Log access grant
        $this->logAccessAttempt($userId, 'access_granted', [
            'permission_level' => $permissionLevel,
            'expires_at' => $expiresAt,
            'purpose' => $purpose,
        ]);

        return true;
    }

    public function revokeAccess($userId, string $reason = ''): bool
    {
        $permissions = $this->access_permissions ?? [];
        
        // Remove user permission
        $permissions = array_filter($permissions, function ($permission) use ($userId) {
            return $permission['user_id'] !== $userId;
        });

        $this->update(['access_permissions' => array_values($permissions)]);

        // Log access revocation
        $this->logAccessAttempt($userId, 'access_revoked', ['reason' => $reason]);

        return true;
    }

    public function updatePermission($userId, string $permissionLevel, ?string $expiresAt = null, string $purpose = ''): void
    {
        $permissions = $this->access_permissions ?? [];
        
        // Find and update existing permission
        foreach ($permissions as $key => $permission) {
            if ($permission['user_id'] === $userId) {
                $permissions[$key]['permission_level'] = $permissionLevel;
                $permissions[$key]['expires_at'] = $expiresAt;
                $permissions[$key]['purpose'] = $purpose;
                $permissions[$key]['updated_at'] = now();
                $permissions[$key]['updated_by'] = auth()->id();
                $permissions[$key]['status'] = 'active';
                break;
            }
        }

        $this->update(['access_permissions' => $permissions]);
    }

    public function grantEmergencyAccess(string $reason = '', int $duration = 24): array
    {
        $emergencyCode = $this->generateEmergencyCode();
        $expiresAt = now()->addHours($duration);

        $this->update([
            'emergency_access' => true,
            'emergency_access_code' => $emergencyCode,
            'emergency_access_expires' => $expiresAt,
            'emergency_reason' => $reason,
            'emergency_granted_at' => now(),
            'emergency_granted_by' => auth()->id(),
        ]);

        // Notify emergency contacts
        $this->notifyEmergencyContacts($reason, $duration);

        return [
            'success' => true,
            'emergency_code' => $emergency_code,
            'expires_at' => $expiresAt,
            'duration' => $duration,
        ];
    }

    public function revokeEmergencyAccess(): void
    {
        $this->update([
            'emergency_access' => false,
            'emergency_access_code' => null,
            'emergency_access_expires' => null,
            'emergency_reason' => null,
            'emergency_granted_at' => null,
            'emergency_granted_by' => null,
        ]);

        // Notify emergency contacts
        $this->notifyEmergencyContacts('Emergency access revoked', 0);
    }

    public function checkEmergencyAccess(string $code): bool
    {
        return $this->emergency_access &&
               $this->emergency_access_code === $code &&
               $this->emergency_access_expires &&
               $this->emergency_access_expires->isFuture();
    }

    public function updateSecurityScore(): void
    {
        $this->update(['risk_assessment' => $this->calculateRiskAssessment()]);
    }

    public function calculateAccessRisk($userId, array $context = []): int
    {
        $riskScore = 0;

        // Base risk by access level
        switch ($this->access_level) {
            case 'confidential':
                $riskScore += 40;
                break;
            case 'restricted':
                $riskScore += 30;
                break;
            case 'private':
                $riskScore += 20;
                break;
            case 'public':
                $riskScore += 10;
                break;
        }

        // Risk by failed attempts
        $failedAttempts = $this->failed_attempts;
        if ($failedAttempts > 5) {
            $riskScore += min(20, $failedAttempts * 4);
        }

        // Risk by unusual access patterns
        $recentAttempts = $this->getRecentFailedAttempts($userId);
        if ($recentAttempts > 3) {
            $riskScore += 15;
        }

        // Risk by location (if location data available)
        if (isset($context['location_data']['risk_level'])) {
            $riskScore += $context['location_data']['risk_level'] * 10;
        }

        return min(100, $riskScore);
    }

    public function generateAccessReport(): array
    {
        return [
            'access_control_id' => $this->id,
            'property_id' => $this->property_id,
            'access_level' => $this->access_level,
            'total_permissions' => count($this->access_permissions ?? []),
            'active_permissions' => count(array_filter($this->access_permissions ?? [], function ($p) {
            return $p['status'] === 'active' && (!$p['expires_at || $p['expires_at'] > now());
        })),
        'security_score' => $this->calculateAccessRisk($this->user_id),
        'access_patterns' => $this->getAccessPatterns(),
        'anomaly_detection' => $this->getAnomalyDetection(),
        'compliance_status' => $this->checkCompliance(),
        'last_access' => $this->last_access_at,
        'access_count' => $this->access_count,
        'failed_attempts' => $this->failed_attempts,
        'emergency_access' => $this->emergency_access,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at',
        'generated_at' => now(),
        'security_policies' => $this->security_policies,
        'notification_settings' => $this->notification_settings,
    ];
    }

    // Private methods
    private function getUserPermission($userId): ?array
    {
        $permissions = $this->access_permissions ?? [];
        
        foreach ($permissions as $permission) {
            if ($permission['user_id'] === $userId && $permission['status'] === 'active') {
                return $permission;
            }
        }

        return null;
    }

    private function isPermissionSufficient(string $userLevel, string $requiredAction): bool
    {
        $permissionLevels = [
            'read' => ['read', 'write', 'admin', 'full'],
            'write' => ['write', 'admin', 'full'],
            'admin' => ['admin', 'full'],
            'full' => ['full'],
        ];

        return in_array($requiredAction, $permissionLevels[$userLevel] ?? []);
    }

    private function checkTimeRestrictions(): bool
    {
        if (!$this->time_restrictions) {
            return true;
        }

        $currentTime = now();
        
        // Check time range
        if (isset($this->time_restrictions['start_time']) {
            $startTime = now()->setTimeFromTimeString($this->time_restrictions['start_time']);
            $endTime = now()->setTimeFromTimeString($this->time_restrictions['end_time']);
            
            if (!$currentTime->between($startTime, $endTime)) {
                return false;
            }
        }

        // Check allowed days
        if (isset($this->time_restrictions['allowed_days'])) {
            $currentDay = $currentTime->dayOfWeek;
            if (!in_array($currentDay, $this->time_restrictions['allowed_days'])) {
                return false;
            }
        }

        return true;
    }

    private function checkIPRestrictions(string $ipAddress): bool
    {
        if (!$this->ip_restrictions) {
            return true;
        }

        // Check blocked IPs
        if (isset($this->ip_restrictions['blocked_ips']) {
            if (in_array($ipAddress, $this->ip_restrictions['blocked_ips'])) {
                return false;
            }
        }

        // Check allowed IPs (if specified)
        if (isset($this->ip_restrictions['allowed_ips']) {
            return in_array($ipAddress, $this->ip_restrictions['allowed_ips']);
        }

        return true;
    }

    private function checkDeviceRestrictions(array $deviceInfo): bool
    {
        if (!$this->device_restrictions || empty($deviceInfo)) {
            return true;
        }

        // Check allowed devices
        if (isset($this->device_restrictions['allowed_devices'])) {
            $deviceFingerprint = $deviceInfo['fingerprint'] ?? '';
            if (!in_array($deviceFingerprint, $this->device_restrictions['allowed_devices'])) {
                return false;
            }
        }

        // Check device verification requirement
        if (isset($this->device_restrictions['require_device_verification'])) {
            if (!isset($deviceInfo['verified']) || !$deviceInfo['verified']) {
                return false;
            }
        }

        return true;
    }

    private function checkLocationRestrictions(array $locationData): bool
    {
        if (!$this->location_restrictions || empty($locationData)) {
            return true;
        }

        // Check allowed locations
        if (isset($this->location_restrictions['allowed_locations'])) {
            $currentLocation = $locationData['location'] ?? '';
            if (!in_array($currentLocation, $this->location_restrictions['allowed_locations'])) {
                return false;
            }
        }

        // Check distance restriction
        if (isset($this->location_restrictions['max_distance']) {
            $distance = $locationData['distance'] ?? 0;
            if ($distance > $this->location_restrictions['max_distance']) {
                return false;
            }
        }

        return true;
    }

    private function calculateRiskAssessment(): array
    {
        return [
            'risk_level' => $this->calculateRiskLevel(),
            'risk_score' => $this->calculateAccessRisk($this->user_id),
            'failed_attempts' => $this->failed_attempts,
            'unusual_patterns' => $this->detectUnusualPatterns(),
            'security_violations' => $this->detectSecurityViolations(),
            'compliance_issues' => $this->detectComplianceIssues(),
            'recommendations' => $this->generateSecurityRecommendations(),
        ];
    }

    private function calculateRiskLevel(): string
    {
        $riskScore = $this->calculateAccessRisk($this->user_id);
        
        if ($riskScore >= 80) {
            return 'critical';
        } elseif ($riskScore >= 60) {
            return 'high';
        } elseif ($riskScore >= 40) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    private function detectUnusualPatterns(): array
    {
        $patterns = [];
        
        // Detect rapid failed attempts
        $recentFailed = $this->getRecentFailedAttempts($this->user_id);
        if ($recentFailed > 3) {
            $patterns[] = 'محاولات فشل متكررة';
        }

        // Detect access from unusual locations
        $unusualLocations = $this->getUnusualLocations($this->user_id);
        if (!empty($unusualLocations)) {
            $patterns[] = 'وصول من مواقع غير معتاد';
        }

        // Detect unusual time patterns
        $unusualTimes = $this->getUnusualTimePatterns($this->user_id);
        if (!empty($unusualTimes)) {
            $patterns[] = 'وصول في أوقت غير عادي';
        }

        return $patterns;
    }

    private function detectSecurityViolations(): array
    {
        $violations = [];
        
        // Check for concurrent session violations
        if ($this->concurrent_sessions > $this->getAllowedConcurrentSessions()) {
            $violations[] = 'تجاوز في عدد الجلسات المتزامنة';
        }

        // Check for session timeout violations
        if ($this->session_timeout > $this->getRecommendedSessionTimeout()) {
            $violations[] = 'تجاوز في مدة الجلسة';
        }

        // Check for emergency access abuse
        if ($this->emergency_access && !$this->emergency_access_expires->isPast()) {
            $violations[] = 'استخدام الوصول الطارئ';
        }

        return $violations;
    }

    private function detectComplianceIssues(): array
    {
        $issues = [];
        
        // Check audit frequency compliance
        if ($this->audit_frequency === 'real_time' && !$this->hasRealTimeAuditCapability()) {
            $issues[] = 'المراجعة في الوقت الفعلي غير متاحة';
        }

        // Check biometric compliance
        if ($this->biometric_required && !$this->hasBiometricCapability()) {
            $issues[] = 'التحقق البيومتري مطلوب ولكن غير متاح';
        }

        // Check two-factor compliance
        if ($this->two_factor_required && !$this->hasTwoFactorCapability()) {
            $issues[] = 'المصادقة الثنائية مطلوبة ولكن غير متاحة';
        }

        return $issues;
    }

    private function generateSecurityRecommendations(): array
    {
        $recommendations = [];

        $riskAssessment = $this->risk_assessment;

        if ($riskAssessment['risk_level'] === 'critical') {
            $recommendations[] = 'اتخاذ إجراءات أمان فورية';
        } elseif ($riskAssessment['risk_level'] === 'high') {
            $recommendations[] = 'تعزيز إجراءات الأمان';
        }

        if ($riskAssessment['failed_attempts'] > 5) {
            $recommendations[] = 'مراجعة محاولات الفشل';
        }

        if (in_array('تجاوز في عدد الجلسات المتزامنة', $riskAssessment['security_violations'])) {
            $recommendations[] = 'تقييد عدد الجلسات المتزامنة';
        }

        if (in_array('استخدام الوصول الطارئ', $riskAssessment['security_violations'])) {
            $recommendations[] = 'مراجعة استخدام الوصول الطارئ';
        }

        return $recommendations;
    }

    private function getRecentFailedAttempts(int $userId): int
    {
        return AccessLog::where('user_id', $userId)
            ->where('success', false)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
    }

    private function getUnusualLocations(int $userId): array
    {
        return AccessLog::where('user_id', $userId)
            ->where('success', true)
            ->selectRaw('DISTINCT(location_data->location')
            ->whereNotNull('location_data->location')
            ->groupBy('location_data->location')
            ->havingRaw('COUNT(*) > 3')
            ->pluck('location_data->location')
            ->toArray();
    }

    private function getUnusualTimePatterns(int $userId): array
    {
        return AccessLog::where('user_id', $userId)
            ->where('success', true)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->havingRaw('COUNT(*) > 10')
            ->pluck('hour')
            ->toArray();
    }

    private function getAllowedConcurrentSessions(): int
    {
        return $this->concurrent_sessions;
    }

    private function getRecommendedSessionTimeout(): int
    {
        switch ($this->access_level) {
            case 'confidential':
                return 30; // 30 minutes
            case 'restricted':
                return 60; // 60 minutes
            case 'private':
                return 120; // 2 hours
            case 'public':
                return 240; // 4 hours
            default:
                return 60;
        }
    }

    private function hasRealTimeAuditCapability(): bool
    {
        // Check if system has real-time audit capability
        return config('security.real_time_audit_enabled', false);
    }

    private function hasBiometricCapability(): bool
    {
        // Check if system has biometric capability
        return config('security.biometric_enabled', false);
    }

    private function hasTwoFactorCapability(): bool
    {
        // Check if system has 2FA capability
        return config('security.two_factor_enabled', false);
    }

    private function logAccessAttempt(int $userId, string $action, array $result): void
    {
        AccessLog::create([
            'user_id' => $userId,
            'property_id' => $this->property_id,
            'access_control_id' => $this->id,
            'action' => $action,
            'success' => $result['granted'] ?? false,
            'reason' => $result['reason'] ?? '',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'location_data' => request()->header('X-Location') ?? [],
            'device_info' => request()->header('X-Device-Info') ?? [],
            'risk_score' => $result['risk_score'] ?? 0,
            'timestamp' => now(),
        ]);
    }

    private function notifyEmergencyContacts(string $reason, int $duration): void
    {
        $contacts = $this->emergency_contacts ?? [];
        
        foreach ($contacts as $contact) {
            // Send notification to emergency contact
            \Log::info('Emergency contact notified', [
                'contact_name' => $contact['name'],
                'contact_phone' => $contact['phone'],
                'contact_email' => $contact['email'],
                'reason' => $reason,
                'duration' => $duration,
            ]);
        }
    }

    private function generateEmergencyCode(): string
    {
        return 'EMG-' . Str::upper(Str::random(6)) . '-' . time();
    }

    private function getAccessPatterns(): array
    {
        return AccessLog::where('access_control_id', $this->id)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('success', true)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getAnomalyDetection(): array
    {
        return [
            'suspicious_ips' => $this->getSuspiciousIPs(),
            'unusual_times' => $this->getUnusualTimes(),
            'failed_patterns' => $this->getFailedPatterns(),
            'device_anomalies' => $this->getDeviceAnomalies(),
            'location_anomalies' => $this->getLocationAnomalies(),
        ];
    }

    private function getSuspiciousIPs(): array
    {
        return AccessLog::where('success', false)
            ->selectRaw('ip_address, COUNT(*) as count')
            ->groupBy('ip_address')
            ->havingRaw('COUNT(*) > 10')
            ->pluck('ip_address')
            ->toArray();
    }

    private function getFailedPatterns(): array
    {
        return [
            'rapid_attempts' => $this->getRapidAttempts(),
            'consecutive_failures' => $this->getConsecutiveFailures(),
            'multiple_locations' => $this->getMultipleLocations(),
            'unusual_devices' => $this->getUnusualDevices(),
        ];
    }

    private function getRapidAttempts(): array
    {
        return AccessLog::where('success', false)
            ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)')
            ->selectRaw('user_id, COUNT(*) as count')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) >= 5')
            ->pluck('user_id', 'count')
            ->toArray();
    }

    private function getConsecutiveFailures(): array
    {
        return AccessLog::where('success', false)
            ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) >= 3')
            ->pluck('user_id', 'count')
            ->toArray();
    }

    private function getMultipleLocations(): array
    {
        return AccessLog::where('success', true)
            ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)')
            ->selectRaw('user_id, COUNT(DISTINCT location_data->location) as locations')
            ->groupBy('user_id')
            ->havingRaw('COUNT(DISTINCT location_data->location) > 3)')
            ->pluck('user_id', 'locations')
            ->toArray();
    }

    private function getUnusualDevices(): array
    {
        return AccessLog::where('success', true)
            ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)')
            ->selectRaw('user_id, COUNT(DISTINCT device_info->fingerprint) as devices')
            ->groupBy('user_id')
            ->havingRaw('COUNT(DISTINCT device_info->fingerprint) > 5)')
            ->pluck('user_id', 'devices')
            ->toArray();
    }

    private function getLocationAnomalies(): array
    {
        return AccessLog::where('success', true)
            ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)')
            ->selectRaw('user_id, COUNT(DISTINCT location_data->location) as locations')
            ->groupBy('user_id')
            ->havingRaw('COUNT(DISTINCT location_data->location) > 5)')
            ->pluck('user_id', 'locations')
            ->toArray();
    }

    private function checkCompliance(): array
    {
        $complianceIssues = [];
        $complianceScore = 100;

        // Check audit frequency
        if ($this->audit_frequency === 'real_time' && !$this->hasRealTimeAuditCapability()) {
            $complianceIssues[] = 'المراجعة في الوقت الفعلي غير متاحة';
            $complianceScore -= 20;
        }

        // Check biometric compliance
        if ($this->biometric_required && !$this->hasBiometricCapability()) {
            $complianceIssues[] = 'التحقق البيومتري مطلوب ولكن غير متاح';
            $complianceScore -= 15;
        }

        // Check two-factor compliance
        if ($this->two_factor_required && !$this->hasTwoFactorCapability()) {
            $complianceIssues[] = 'المصادقة الثنائية مطلوبة ولكن غير متاحة';
            $complianceScore -= 15;
        }

        // Check session timeout
        if ($this->session_timeout > $this->getRecommendedSessionTimeout()) {
            $complianceIssues[] = 'مدة الجلسة طويلة طويلة';
            $complianceScore -= 10;
        }

        return [
            'compliant' => $complianceScore >= 80,
            'score' => $complianceScore,
            'issues' => $complianceIssues,
            'recommendations' => $this->generateSecurityRecommendations(),
        ];
    }
}

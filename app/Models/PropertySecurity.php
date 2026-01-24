<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PropertySecurity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'security_level',
        'access_permissions',
        'encryption_enabled',
        'biometric_required',
        'two_factor_enabled',
        'audit_frequency',
        'security_notes',
        'last_audit_date',
        'next_audit_date',
        'access_code',
        'qr_code',
        'status',
        'security_score',
        'risk_assessment',
        'compliance_status',
        'security_policies',
        'monitoring_settings',
        'alert_settings',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'access_permissions' => 'array',
        'encryption_enabled' => 'boolean',
        'biometric_required' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'last_audit_date' => 'datetime',
        'next_audit_date' => 'datetime',
        'security_policies' => 'array',
        'monitoring_settings' => 'array',
        'alert_settings' => 'array',
        'risk_assessment' => 'array',
        'compliance_status' => 'array',
    ];

    protected $dates = [
        'last_audit_date',
        'next_audit_date',
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'property_id', 'property_id');
    }

    public function securityIncidents(): HasMany
    {
        return $this->hasMany(SecurityIncident::class, 'property_id', 'property_id');
    }

    public function fraudAlerts(): HasMany
    {
        return $this->hasMany(FraudAlert::class, 'property_id', 'property_id');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class, 'property_id', 'property_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBySecurityLevel($query, $level)
    {
        return $query->where('security_level', $level);
    }

    public function scopeHighRisk($query)
    {
        return $query->where('security_score', '<', 50);
    }

    public function scopePendingAudit($query)
    {
        return $query->where('next_audit_date', '<=', now());
    }

    public function scopeCompliant($query)
    {
        return $query->whereJsonContains('compliance_status', ['status' => 'compliant']);
    }

    // Methods
    public function calculateSecurityScore(): int
    {
        $score = 0;

        // Base score by security level
        switch ($this->security_level) {
            case 'critical':
                $score += 40;
                break;
            case 'high':
                $score += 30;
                break;
            case 'medium':
                $score += 20;
                break;
            case 'low':
                $score += 10;
                break;
        }

        // Additional security features
        if ($this->encryption_enabled) {
            $score += 20;
        }

        if ($this->biometric_required) {
            $score += 15;
        }

        if ($this->two_factor_enabled) {
            $score += 15;
        }

        if ($this->access_permissions && count($this->access_permissions) > 0) {
            $score += 10;
        }

        return min(100, $score);
    }

    public function assessRisk(): array
    {
        $riskFactors = [];

        // Check security score
        if ($this->security_score < 50) {
            $riskFactors[] = 'نقطة أمان منخفضة';
        }

        // Check audit status
        if ($this->next_audit_date <= now()) {
            $riskFactors[] = 'تأخير في المراجعة الأمنية';
        }

        // Check recent incidents
        $recentIncidents = $this->securityIncidents()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        if ($recentIncidents > 5) {
            $riskFactors[] = 'حوادث أمنية متكررة';
        }

        // Check compliance status
        if (!$this->isCompliant()) {
            $riskFactors[] = 'عدم الامتثال';
        }

        return [
            'risk_level' => $this->determineRiskLevel($riskFactors),
            'risk_factors' => $riskFactors,
            'risk_score' => count($riskFactors) * 20,
            'recommendations' => $this->generateRiskRecommendations($riskFactors),
        ];
    }

    public function isCompliant(): bool
    {
        if (!$this->compliance_status) {
            return true;
        }

        return collect($this->compliance_status)
            ->filter(function ($status) {
                return isset($status['status']) && $status['status'] === 'compliant';
            })
            ->count() === count($this->compliance_status);
    }

    public function generateSecurityReport(): array
    {
        return [
            'property_id' => $this->property_id,
            'security_level' => $this->security_level,
            'security_score' => $this->calculateSecurityScore(),
            'compliance_status' => $this->isCompliant() ? 'compliant' : 'non_compliant',
            'last_audit_date' => $this->last_audit_date,
            'next_audit_date' => $this->next_audit_date,
            'security_features' => [
                'encryption_enabled' => $this->encryption_enabled,
                'biometric_required' => $this->biometric_required,
                'two_factor_enabled' => $this->two_factor_enabled,
                'access_permissions_count' => count($this->access_permissions ?? []),
            ],
            'risk_assessment' => $this->assessRisk(),
            'recent_incidents' => $this->securityIncidents()
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'compliance_details' => $this->compliance_status,
            'generated_at' => now(),
        ];
    }

    public function updateSecurityScore(): void
    {
        $this->update(['security_score' => $this->calculateSecurityScore()]);
    }

    public function scheduleNextAudit(): void
    {
        $nextAuditDate = $this->calculateNextAuditDate();
        $this->update(['next_audit_date' => $nextAuditDate]);
    }

    public function validateAccessPermissions(array $permissions): bool
    {
        // Validate access permissions structure
        foreach ($permissions as $permission) {
            if (!isset($permission['user_id']) || !isset($permission['permission_level'])) {
                return false;
            }

            if (!in_array($permission['permission_level'], ['read', 'write', 'admin', 'full'])) {
                return false;
            }
        }

        return true;
    }

    public function encryptSensitiveData(string $data): string
    {
        if (!$this->encryption_enabled) {
            return $data;
        }

        // Implement encryption logic
        return openssl_encrypt($data, 'aes-256-cbc', $this->getEncryptionKey(), 0, $this->getEncryptionIv());
    }

    public function decryptSensitiveData(string $encryptedData): string
    {
        if (!$this->encryption_enabled) {
            return $encryptedData;
        }

        // Implement decryption logic
        return openssl_decrypt($encryptedData, 'aes-256-cbc', $this->getEncryptionKey(), 0, $this->getEncryptionIv());
    }

    public function checkBiometricRequirement(array $userData): bool
    {
        if (!$this->biometric_required) {
            return true;
        }

        // Implement biometric verification logic
        return $this->verifyBiometricData($userData);
    }

    public function checkTwoFactorRequirement(string $code): bool
    {
        if (!$this->two_factor_enabled) {
            return true;
        }

        // Implement 2FA verification logic
        return $this->verifyTwoFactorCode($code);
    }

    public function logSecurityActivity(string $action, array $details = []): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'property_id' => $this->property_id,
            'action' => $action,
            'details' => json_encode($details),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'success' => true,
            'risk_level' => $this->determineActionRiskLevel($action),
        ]);
    }

    public function generateSecurityAlert(string $type, string $message, array $data = []): void
    {
        // Implement security alert generation
        Log::warning('Security alert generated', [
            'property_id' => $this->property_id,
            'type' => $type,
            'message' => $message,
            'data' => $data,
        ]);

        // Send notifications based on alert settings
        if ($this->shouldSendAlert($type)) {
            $this->sendSecurityNotification($type, $message, $data);
        }
    }

    // Private methods
    private function determineRiskLevel(array $riskFactors): string
    {
        $count = count($riskFactors);

        if ($count >= 4) {
            return 'critical';
        } elseif ($count >= 3) {
            return 'high';
        } elseif ($count >= 2) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    private function generateRiskRecommendations(array $riskFactors): array
    {
        $recommendations = [];

        foreach ($riskFactors as $factor) {
            switch ($factor) {
                case 'نقطة أمان منخفضة':
                    $recommendations[] = 'زيادة إجراءات الأمان وتحسين مستوى الحماية';
                    break;
                case 'تأخير في المراجعة الأمنية':
                    $recommendations[] = 'إجراء المراجعة الأمنية فوراً';
                    break;
                case 'حوادث أمنية متكررة':
                    $recommendations[] = 'تحليل الأسباب الجذرية وتعزيز الإجراءات الوقائية';
                    break;
                case 'عدم الامتثال':
                    $recommendations[] = 'معالجة مشاكل الامتثال وتطبيق المعايير المطلوبة';
                    break;
            }
        }

        return $recommendations;
    }

    private function calculateNextAuditDate(): \Carbon\Carbon
    {
        switch ($this->audit_frequency) {
            case 'daily':
                return now()->addDay();
            case 'weekly':
                return now()->addWeek();
            case 'monthly':
                return now()->addMonth();
            default:
                return now()->addMonth();
        }
    }

    private function getEncryptionKey(): string
    {
        // Generate or retrieve encryption key
        return config('app.encryption_key', 'default-key');
    }

    private function getEncryptionIv(): string
    {
        // Generate or retrieve encryption IV
        return config('app.encryption_iv', 'default-iv');
    }

    private function verifyBiometricData(array $userData): bool
    {
        // Implement biometric verification logic
        return true; // Placeholder
    }

    private function verifyTwoFactorCode(string $code): bool
    {
        // Implement 2FA verification logic
        return true; // Placeholder
    }

    private function determineActionRiskLevel(string $action): string
    {
        $highRiskActions = [
            'access_denied',
            'unauthorized_access_attempt',
            'security_breach',
            'data_compromise',
        ];

        if (in_array($action, $highRiskActions)) {
            return 'high';
        }

        return 'low';
    }

    private function shouldSendAlert(string $type): bool
    {
        if (!$this->alert_settings) {
            return false;
        }

        return collect($this->alert_settings)
            ->filter(function ($setting) use ($type) {
                return isset($setting['type']) && $setting['type'] === $type && $setting['enabled'];
            })
            ->isNotEmpty();
    }

    private function sendSecurityNotification(string $type, string $message, array $data): void
    {
        // Implement notification sending logic
        Log::info('Security notification sent', [
            'type' => $type,
            'message' => $message,
            'data' => $data,
        ]);
    }
}

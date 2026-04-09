<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Events\SecurityAlertTriggered;

class AuditLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'action',
        'auditable_id',
        'auditable_type',
        'old_values',
        'new_values',
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
        'old_values' => 'array',
        'new_values' => 'array',
        'memory_usage' => 'integer',
        'processing_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

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

    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    // Helper Methods
    public static function logActivity($userId, $action, $details = null, $propertyId = null, $success = true, $riskLevel = 'low')
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'details' => $details,
            'property_id' => $propertyId,
            'success' => $success,
            'risk_level' => $riskLevel,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'request_id' => request()->header('X-Request-ID'),
        ]);
    }

    public static function logComplianceCheck($userId, $complianceType, $details = [], $propertyId = null): void
    {
        self::logActivity($userId, "compliance_{$complianceType}", $details, $propertyId, true, 'medium');
    }

    public static function logFraudEvent($userId, $fraudType, $details = [], $propertyId = null): void
    {
        self::logActivity($userId, "fraud_{$fraudType}", $details, $propertyId, false, 'high');
    }

    public static function logSecurityIncident($userId, $incidentType, $details = []): void
    {
        $log = self::logActivity($userId, "security_{$incidentType}", $details, null, false, 'critical');
        
        // Trigger event
        event(new SecurityAlertTriggered($log));
    }
}

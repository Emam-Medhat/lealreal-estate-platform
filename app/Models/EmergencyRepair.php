<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes; // Temporarily disabled until table structure is confirmed

class EmergencyRepair extends Model
{
    use HasFactory; // , SoftDeletes; // Temporarily disabled until table structure is confirmed

    protected $fillable = [
        'repair_number',
        'property_id',
        'maintenance_request_id',
        'title',
        'description',
        'emergency_type',
        'severity',
        'priority',
        'location_details',
        'reported_by',
        'contact_phone',
        'assigned_team_id',
        'assigned_provider_id',
        'estimated_cost',
        'actual_cost',
        'status',
        'reported_at',
        'assigned_at',
        'assigned_by',
        'started_at',
        'paused_at',
        'resumed_at',
        'completed_at',
        'completion_notes',
        'resolution_details',
        'preventive_measures',
        'reported_by_user_id',
        'attachments',
        'notes',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'reported_at' => 'datetime',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'completed_at' => 'datetime',
        'attachments' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function assignedTeam()
    {
        return $this->belongsTo(MaintenanceTeam::class, 'assigned_team_id');
    }

    public function assignedProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'assigned_provider_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function reportedByUser()
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(EmergencyRepairTimeLog::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByEmergencyType($query, $type)
    {
        return $query->where('emergency_type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeReported($query)
    {
        return $query->where('status', 'reported');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'emergency']);
    }

    public function getEmergencyTypeLabelAttribute()
    {
        $labels = [
            'water_damage' => 'ضرر مائي',
            'electrical_fire' => 'حريق كهربائي',
            'gas_leak' => 'تسرب غاز',
            'structural_damage' => 'ضرر إنشائي',
            'security_breach' => 'اختراق أمني',
            'other' => 'أخرى',
        ];

        return $labels[$this->emergency_type] ?? $this->emergency_type;
    }

    public function getSeverityLabelAttribute()
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'critical' => 'حرج',
        ];

        return $labels[$this->severity] ?? $this->severity;
    }

    public function getPriorityLabelAttribute()
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'emergency' => 'طوارئ',
        ];

        return $labels[$this->priority] ?? $this->priority;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'reported' => 'تم الإبلاغ',
            'assigned' => 'مكلف',
            'in_progress' => 'قيد التنفيذ',
            'paused' => 'موقوف',
            'completed' => 'مكتمل',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getSeverityColorAttribute()
    {
        $colors = [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
        ];

        return $colors[$this->severity] ?? 'gray';
    }

    public function getPriorityColorAttribute()
    {
        $colors = [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'emergency' => 'red',
        ];

        return $colors[$this->priority] ?? 'gray';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'reported' => 'gray',
            'assigned' => 'blue',
            'in_progress' => 'yellow',
            'paused' => 'orange',
            'completed' => 'green',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function isCritical()
    {
        return $this->severity === 'critical';
    }

    public function isHighPriority()
    {
        return in_array($this->priority, ['high', 'emergency']);
    }

    public function canBeAssigned()
    {
        return $this->status === 'reported';
    }

    public function canBeStarted()
    {
        return $this->status === 'assigned';
    }

    public function canBePaused()
    {
        return $this->status === 'in_progress';
    }

    public function canBeResumed()
    {
        return $this->status === 'paused';
    }

    public function canBeCompleted()
    {
        return $this->status === 'in_progress';
    }

    public function isOverdue()
    {
        if ($this->severity === 'critical') {
            $responseTime = 30; // 30 minutes for critical
        } elseif ($this->severity === 'high') {
            $responseTime = 60; // 1 hour for high
        } else {
            $responseTime = 120; // 2 hours for medium/low
        }

        return $this->status !== 'completed' && 
               $this->reported_at && 
               $this->reported_at->diffInMinutes(now()) > $responseTime;
    }

    public function getResponseTime()
    {
        if ($this->reported_at && $this->started_at) {
            return $this->reported_at->diffInMinutes($this->started_at);
        }

        return null;
    }

    public function getResolutionTime()
    {
        if ($this->reported_at && $this->completed_at) {
            return $this->reported_at->diffInMinutes($this->completed_at);
        }

        return null;
    }

    public function getHandlingTime()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }

        return null;
    }

    public function getCostDifference()
    {
        if ($this->estimated_cost && $this->actual_cost) {
            return $this->actual_cost - $this->estimated_cost;
        }

        return null;
    }

    public function getCostVariancePercentage()
    {
        if ($this->estimated_cost && $this->actual_cost) {
            return (($this->actual_cost - $this->estimated_cost) / $this->estimated_cost) * 100;
        }

        return null;
    }

    public function addTimeLog($description, $duration, $userId = null)
    {
        return $this->timeLogs()->create([
            'description' => $description,
            'duration' => $duration,
            'user_id' => $userId ?? auth()->id(),
            'log_time' => now(),
        ]);
    }

    public function getTotalLoggedTime()
    {
        return $this->timeLogs()->sum('duration');
    }

    public function getLatestTimeLog()
    {
        return $this->timeLogs()->latest()->first();
    }

    public function getTimeLogCount()
    {
        return $this->timeLogs()->count();
    }

    public function getAttachmentCount()
    {
        return count($this->attachments ?? []);
    }

    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    public function assign($teamId = null, $providerId = null, $assignedBy, $notes = null)
    {
        $this->update([
            'assigned_team_id' => $teamId,
            'assigned_provider_id' => $providerId,
            'status' => 'assigned',
            'assigned_at' => now(),
            'assigned_by' => $assignedBy,
            'assignment_notes' => $notes,
        ]);

        return $this;
    }

    public function start()
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return $this;
    }

    public function pause($reason)
    {
        $this->update([
            'status' => 'paused',
            'paused_at' => now(),
            'pause_reason' => $reason,
        ]);

        return $this;
    }

    public function resume()
    {
        $this->update([
            'status' => 'in_progress',
            'resumed_at' => now(),
        ]);

        return $this;
    }

    public function complete($completionNotes, $resolutionDetails, $actualCost, $preventiveMeasures = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $completionNotes,
            'resolution_details' => $resolutionDetails,
            'actual_cost' => $actualCost,
            'preventive_measures' => $preventiveMeasures,
        ]);

        return $this;
    }

    public function getUrgencyLevel()
    {
        if ($this->severity === 'critical') {
            return 'critical';
        } elseif ($this->severity === 'high') {
            return 'high';
        } elseif ($this->isOverdue()) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function getExpectedResponseTime()
    {
        $responseTimes = [
            'critical' => 30, // 30 minutes
            'high' => 60, // 1 hour
            'medium' => 120, // 2 hours
            'low' => 240, // 4 hours
        ];

        return $responseTimes[$this->severity] ?? 120;
    }

    public function getExpectedResolutionTime()
    {
        $resolutionTimes = [
            'critical' => 180, // 3 hours
            'high' => 360, // 6 hours
            'medium' => 720, // 12 hours
            'low' => 1440, // 24 hours
        ];

        return $resolutionTimes[$this->severity] ?? 720;
    }

    public function isWithinExpectedResponseTime()
    {
        $responseTime = $this->getResponseTime();
        $expectedTime = $this->getExpectedResponseTime();

        return $responseTime !== null && $responseTime <= $expectedTime;
    }

    public function isWithinExpectedResolutionTime()
    {
        $resolutionTime = $this->getResolutionTime();
        $expectedTime = $this->getExpectedResolutionTime();

        return $resolutionTime !== null && $resolutionTime <= $expectedTime;
    }

    public function getPerformanceMetrics()
    {
        return [
            'response_time' => $this->getResponseTime(),
            'resolution_time' => $this->getResolutionTime(),
            'handling_time' => $this->getHandlingTime(),
            'cost_variance' => $this->getCostVariancePercentage(),
            'within_response_time' => $this->isWithinExpectedResponseTime(),
            'within_resolution_time' => $this->isWithinExpectedResolutionTime(),
            'is_overdue' => $this->isOverdue(),
            'urgency_level' => $this->getUrgencyLevel(),
        ];
    }

    public function generateReport()
    {
        return [
            'repair_number' => $this->repair_number,
            'property' => $this->property->title ?? 'N/A',
            'emergency_type' => $this->emergency_type_label,
            'severity' => $this->severity_label,
            'priority' => $this->priority_label,
            'status' => $this->status_label,
            'reported_at' => $this->reported_at,
            'completed_at' => $this->completed_at,
            'response_time' => $this->getResponseTime(),
            'resolution_time' => $this->getResolutionTime(),
            'estimated_cost' => $this->estimated_cost,
            'actual_cost' => $this->actual_cost,
            'cost_variance' => $this->getCostVariancePercentage(),
            'assigned_team' => $this->assignedTeam->name ?? 'N/A',
            'assigned_provider' => $this->assignedProvider->name ?? 'N/A',
            'resolution_details' => $this->resolution_details,
            'preventive_measures' => $this->preventive_measures,
        ];
    }
}

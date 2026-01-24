<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'schedule_number',
        'title',
        'description',
        'maintenance_type',
        'scheduled_date',
        'estimated_duration',
        'priority',
        'status',
        'estimated_cost',
        'actual_cost',
        'maintenance_team_id',
        'service_provider_id',
        'assigned_at',
        'assigned_by',
        'started_at',
        'completed_at',
        'rescheduled_at',
        'rescheduled_by',
        'reschedule_reason',
        'cancelled_at',
        'cancellation_reason',
        'completion_notes',
        'attachments',
        'preventive_maintenance_id',
        'maintenance_request_id',
        'created_by',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'estimated_duration' => 'integer',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'rescheduled_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'attachments' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function maintenanceTeam()
    {
        return $this->belongsTo(MaintenanceTeam::class);
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function rescheduledBy()
    {
        return $this->belongsTo(User::class, 'rescheduled_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function preventiveMaintenance()
    {
        return $this->belongsTo(PreventiveMaintenance::class);
    }

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function timeLogs()
    {
        return $this->hasMany(MaintenanceTimeLog::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('maintenance_type', $type);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_date', '<', now())
                    ->where('status', '!=', 'completed')
                    ->where('status', '!=', 'cancelled');
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('scheduled_date', '>=', now())
                    ->where('scheduled_date', '<=', now()->addDays($days))
                    ->where('status', 'scheduled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', today())
                    ->where('status', 'scheduled');
    }

    public function getMaintenanceTypeLabelAttribute()
    {
        $labels = [
            'inspection' => 'فحص',
            'cleaning' => 'تنظيف',
            'service' => 'خدمة',
            'replacement' => 'استبدال',
            'testing' => 'اختبار',
            'repair' => 'إصلاح',
            'maintenance' => 'صيانة',
            'other' => 'أخرى',
        ];

        return $labels[$this->maintenance_type] ?? $this->maintenance_type;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'scheduled' => 'مجدول',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'rescheduled' => 'تم إعادة جدولته',
        ];

        return $labels[$this->status] ?? $this->status;
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

    public function getStatusColorAttribute()
    {
        $colors = [
            'scheduled' => 'blue',
            'in_progress' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
            'rescheduled' => 'orange',
        ];

        return $colors[$this->status] ?? 'gray';
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

    public function isOverdue()
    {
        return $this->scheduled_date && $this->scheduled_date < now() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }

    public function isToday()
    {
        return $this->scheduled_date && $this->scheduled_date->isToday();
    }

    public function isUpcoming($days = 7)
    {
        return $this->scheduled_date && 
               $this->scheduled_date >= now() && 
               $this->scheduled_date <= now()->addDays($days);
    }

    public function canBeStarted()
    {
        return $this->status === 'scheduled' && $this->scheduled_date <= now();
    }

    public function canBeCompleted()
    {
        return $this->status === 'in_progress';
    }

    public function canBeRescheduled()
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    public function canBeCancelled()
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getDurationInMinutes()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }
        
        if ($this->started_at) {
            return $this->started_at->diffInMinutes(now());
        }
        
        return $this->estimated_duration;
    }

    public function getCostDifference()
    {
        if ($this->estimated_cost && $this->actual_cost) {
            return $this->actual_cost - $this->estimated_cost;
        }
        
        return null;
    }

    public function getEfficiencyScore()
    {
        if (!$this->estimated_duration || !$this->started_at || !$this->completed_at) {
            return null;
        }

        $actualDuration = $this->started_at->diffInMinutes($this->completed_at);
        $efficiency = ($this->estimated_duration / $actualDuration) * 100;
        
        return min(100, max(0, $efficiency));
    }

    public function addTimeLog($description, $duration, $notes = null)
    {
        return $this->timeLogs()->create([
            'description' => $description,
            'duration' => $duration,
            'notes' => $notes,
            'user_id' => auth()->id(),
            'log_time' => now(),
        ]);
    }

    public function getTotalLoggedTime()
    {
        return $this->timeLogs()->sum('duration');
    }

    public function isPreventive()
    {
        return $this->preventiveMaintenance()->exists();
    }

    public function isReactive()
    {
        return $this->maintenanceRequest()->exists();
    }
}

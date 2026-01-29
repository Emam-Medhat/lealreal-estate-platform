<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'user_id',
        'title',
        'description',
        'priority',
        'category',
        'status',
        'estimated_cost',
        'actual_cost',
        'scheduled_date',
        'completed_date',
        'assigned_to',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'attachments' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedTeam()
    {
        return $this->belongsTo(MaintenanceTeam::class, 'assigned_team_id');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function tickets()
    {
        return $this->hasMany(MaintenanceTicket::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function schedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function logs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function emergencyRepair()
    {
        return $this->belongsTo(EmergencyRepair::class);
    }

    public function invoices()
    {
        return $this->hasMany(MaintenanceInvoice::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed')
                    ->where('status', '!=', 'cancelled');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
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
            'pending' => 'في انتظار',
            'assigned' => 'مكلف',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getCategoryLabelAttribute()
    {
        $labels = [
            'plumbing' => 'سباكة',
            'electrical' => 'كهرباء',
            'hvac' => 'تكييف',
            'structural' => 'إنشائي',
            'general' => 'عام',
            'cosmetic' => 'تجميلي',
            'safety' => 'سلامة',
            'other' => 'أخرى',
        ];

        return $labels[$this->category] ?? $this->category;
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
            'pending' => 'gray',
            'assigned' => 'blue',
            'in_progress' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date < now() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }

    public function canBeAssigned()
    {
        return $this->status === 'pending';
    }

    public function canBeStarted()
    {
        return $this->status === 'assigned';
    }

    public function canBeCompleted()
    {
        return $this->status === 'in_progress';
    }

    public function canBeCancelled()
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getDurationInDays()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInDays($this->completed_at);
        }
        
        if ($this->started_at) {
            return $this->started_at->diffInDays(now());
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

    public function addLog($action, $description, $oldValues = null, $newValues = null)
    {
        return $this->logs()->create([
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'user_id' => auth()->id(),
        ]);
    }
}

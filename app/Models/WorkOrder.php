<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'work_order_number',
        'maintenance_request_id',
        'title',
        'description',
        'priority',
        'status',
        'estimated_cost',
        'actual_cost',
        'estimated_duration',
        'actual_duration',
        'assigned_to',
        'assigned_team_id',
        'assigned_at',
        'assigned_by',
        'started_at',
        'paused_at',
        'resumed_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'completion_notes',
        'location',
        'work_type',
        'materials_needed',
        'tools_needed',
        'safety_requirements',
        'attachments',
        'created_by',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'estimated_duration' => 'integer',
        'actual_duration' => 'integer',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'materials_needed' => 'array',
        'tools_needed' => 'array',
        'safety_requirements' => 'array',
        'attachments' => 'array',
    ];

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedTeam()
    {
        return $this->belongsTo(MaintenanceTeam::class, 'assigned_team_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function timeLogs()
    {
        return $this->hasMany(WorkOrderTimeLog::class);
    }

    public function items()
    {
        return $this->hasMany(WorkOrderItem::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByWorkType($query, $workType)
    {
        return $query->where('work_type', $workType);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
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

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
                    ->where('status', '!=', 'cancelled')
                    ->whereHas('maintenanceRequest', function ($q) {
                        $q->where('due_date', '<', now());
                    });
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
            'paused' => 'موقوف',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getWorkTypeLabelAttribute()
    {
        $labels = [
            'repair' => 'إصلاح',
            'installation' => 'تركيب',
            'maintenance' => 'صيانة',
            'inspection' => 'فحص',
            'replacement' => 'استبدال',
            'upgrade' => 'ترقية',
            'other' => 'أخرى',
        ];

        return $labels[$this->work_type] ?? $this->work_type;
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
            'paused' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function canBeAssigned()
    {
        return $this->status === 'pending';
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

    public function canBeCancelled()
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    public function isOverdue()
    {
        return $this->maintenanceRequest && 
               $this->maintenanceRequest->due_date && 
               $this->maintenanceRequest->due_date < now() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getDurationInMinutes()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        } elseif ($this->started_at) {
            return $this->started_at->diffInMinutes(now());
        }

        return null;
    }

    public function getActualDurationInMinutes()
    {
        if (!$this->actual_duration) {
            return $this->getDurationInMinutes();
        }

        return $this->actual_duration;
    }

    public function getEfficiencyScore()
    {
        if (!$this->estimated_duration || !$this->actual_duration) {
            return null;
        }

        $efficiency = ($this->estimated_duration / $this->actual_duration) * 100;
        return min(100, max(0, $efficiency));
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

    public function getTotalLoggedTime()
    {
        return $this->timeLogs()->sum('duration');
    }

    public function getTotalMaterialCost()
    {
        return $this->items()->sum('total_cost');
    }

    public function getLaborCost()
    {
        if ($this->actual_cost && $this->getTotalMaterialCost()) {
            return $this->actual_cost - $this->getTotalMaterialCost();
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

    public function addItem($itemId, $quantity, $unitCost, $notes = null)
    {
        return $this->items()->create([
            'inventory_id' => $itemId,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $quantity * $unitCost,
            'notes' => $notes,
        ]);
    }

    public function removeItem($itemId)
    {
        return $this->items()->where('inventory_id', $itemId)->delete();
    }

    public function updateItemQuantity($itemId, $quantity)
    {
        $item = $this->items()->where('inventory_id', $itemId)->first();
        
        if ($item) {
            $item->quantity = $quantity;
            $item->total_cost = $quantity * $item->unit_cost;
            $item->save();
        }

        return $item;
    }

    public function getTotalItems()
    {
        return $this->items()->sum('quantity');
    }

    public function getItemCount()
    {
        return $this->items()->count();
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

    public function getMaterialsNeededCount()
    {
        return count($this->materials_needed ?? []);
    }

    public function getToolsNeededCount()
    {
        return count($this->tools_needed ?? []);
    }

    public function getSafetyRequirementsCount()
    {
        return count($this->safety_requirements ?? []);
    }

    public function isPaused()
    {
        return $this->status === 'paused';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function getWorkSummary()
    {
        return [
            'duration' => $this->getDurationInMinutes(),
            'efficiency' => $this->getEfficiencyScore(),
            'cost_variance' => $this->getCostVariancePercentage(),
            'material_cost' => $this->getTotalMaterialCost(),
            'labor_cost' => $this->getLaborCost(),
            'items_used' => $this->getTotalItems(),
            'time_logs' => $this->getTimeLogCount(),
        ];
    }

    public function calculateActualCost()
    {
        $materialCost = $this->getTotalMaterialCost();
        $laborCost = $this->getLaborCost() ?? 0;
        
        $totalCost = $materialCost + $laborCost;
        
        $this->update(['actual_cost' => $totalCost]);
        
        return $totalCost;
    }

    public function markAsCompleted($completionNotes = null, $actualCost = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $completionNotes,
            'actual_cost' => $actualCost ?? $this->calculateActualCost(),
            'actual_duration' => $this->getDurationInMinutes(),
        ]);

        // Update related maintenance request
        if ($this->maintenanceRequest) {
            $this->maintenanceRequest->update([
                'status' => 'completed',
                'completed_at' => now(),
                'actual_cost' => $this->actual_cost,
            ]);
        }

        return $this;
    }

    public function markAsCancelled($reason)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        // Update related maintenance request
        if ($this->maintenanceRequest) {
            $this->maintenanceRequest->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);
        }

        return $this;
    }
}

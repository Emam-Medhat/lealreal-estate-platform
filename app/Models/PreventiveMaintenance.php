<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreventiveMaintenance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'plan_number',
        'property_id',
        'title',
        'description',
        'frequency',
        'maintenance_type',
        'priority',
        'estimated_duration',
        'estimated_cost',
        'maintenance_team_id',
        'start_date',
        'end_date',
        'checklist_items',
        'materials_needed',
        'status',
        'activated_at',
        'activated_by',
        'deactivated_at',
        'deactivated_by',
        'deactivation_reason',
        'completion_notes',
        'total_cost',
        'completed_at',
        'completed_by',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'estimated_duration' => 'integer',
        'estimated_cost' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'checklist_items' => 'array',
        'materials_needed' => 'array',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'total_cost' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function maintenanceTeam()
    {
        return $this->belongsTo(MaintenanceTeam::class);
    }

    public function activatedBy()
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function deactivatedBy()
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function schedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByFrequency($query, $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('maintenance_type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function getMaintenanceTypeLabelAttribute()
    {
        $labels = [
            'inspection' => 'فحص',
            'cleaning' => 'تنظيف',
            'service' => 'خدمة',
            'replacement' => 'استبدال',
            'testing' => 'اختبار',
            'maintenance' => 'صيانة',
        ];

        return $labels[$this->maintenance_type] ?? $this->maintenance_type;
    }

    public function getFrequencyLabelAttribute()
    {
        $labels = [
            'daily' => 'يومي',
            'weekly' => 'أسبوعي',
            'monthly' => 'شهري',
            'quarterly' => 'ربع سنوي',
            'yearly' => 'سنوي',
        ];

        return $labels[$this->frequency] ?? $this->frequency;
    }

    public function getPriorityLabelAttribute()
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
        ];

        return $labels[$this->priority] ?? $this->priority;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'completed' => 'مكتمل',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'active' => 'green',
            'inactive' => 'gray',
            'completed' => 'blue',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getPriorityColorAttribute()
    {
        $colors = [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
        ];

        return $colors[$this->priority] ?? 'gray';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    public function canBeActivated()
    {
        return $this->status === 'inactive';
    }

    public function canBeDeactivated()
    {
        return $this->status === 'active';
    }

    public function canBeCompleted()
    {
        return $this->status === 'active';
    }

    public function isExpired()
    {
        return $this->end_date && $this->end_date < now();
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->end_date && 
               $this->end_date > now() && 
               $this->end_date <= now()->addDays($days);
    }

    public function getDaysRemaining()
    {
        if (!$this->end_date) {
            return null;
        }

        return $this->end_date->diffInDays(now(), false);
    }

    public function getTotalSchedules()
    {
        return $this->schedules()->count();
    }

    public function getCompletedSchedules()
    {
        return $this->schedules()->where('status', 'completed')->count();
    }

    public function getPendingSchedules()
    {
        return $this->schedules()->where('status', 'scheduled')->count();
    }

    public function getOverdueSchedules()
    {
        return $this->schedules()
            ->where('scheduled_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();
    }

    public function getInProgressSchedules()
    {
        return $this->schedules()->where('status', 'in_progress')->count();
    }

    public function getCompletionRate()
    {
        $totalSchedules = $this->getTotalSchedules();
        
        if ($totalSchedules === 0) {
            return 0;
        }

        return ($this->getCompletedSchedules() / $totalSchedules) * 100;
    }

    public function getNextScheduledDate()
    {
        return $this->schedules()
            ->where('scheduled_date', '>=', now())
            ->where('status', 'scheduled')
            ->min('scheduled_date');
    }

    public function getLastCompletedDate()
    {
        return $this->schedules()
            ->where('status', 'completed')
            ->max('completed_at');
    }

    public function getAverageCompletionTime()
    {
        $completedSchedules = $this->schedules()
            ->where('status', 'completed')
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at');

        if ($completedSchedules->count() === 0) {
            return null;
        }

        $totalTime = $completedSchedules
            ->get()
            ->sum(function ($schedule) {
                return $schedule->started_at->diffInMinutes($schedule->completed_at);
            });

        return $totalTime / $completedSchedules->count();
    }

    public function getTotalActualCost()
    {
        return $this->schedules()
            ->where('status', 'completed')
            ->whereNotNull('actual_cost')
            ->sum('actual_cost');
    }

    public function getCostVariance()
    {
        if ($this->estimated_cost && $this->total_cost) {
            return $this->total_cost - $this->estimated_cost;
        }

        return null;
    }

    public function getCostVariancePercentage()
    {
        if ($this->estimated_cost && $this->total_cost) {
            return (($this->total_cost - $this->estimated_cost) / $this->estimated_cost) * 100;
        }

        return null;
    }

    public function getUpcomingSchedules($days = 30)
    {
        return $this->schedules()
            ->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays($days))
            ->where('status', 'scheduled')
            ->with('property')
            ->orderBy('scheduled_date')
            ->get();
    }

    public function getOverdueSchedulesList()
    {
        return $this->schedules()
            ->where('scheduled_date', '<', now())
            ->where('status', '!=', 'completed')
            ->with('property')
            ->orderBy('scheduled_date')
            ->get();
    }

    public function getChecklistItemsCount()
    {
        return count($this->checklist_items ?? []);
    }

    public function getMaterialsNeededCount()
    {
        return count($this->materials_needed ?? []);
    }

    public function addChecklistItem($item)
    {
        $items = $this->checklist_items ?? [];
        
        if (!in_array($item, $items)) {
            $items[] = $item;
            $this->update(['checklist_items' => $items]);
        }
    }

    public function removeChecklistItem($item)
    {
        $items = $this->checklist_items ?? [];
        
        if (($key = array_search($item, $items)) !== false) {
            unset($items[$key]);
            $this->update(['checklist_items' => array_values($items)]);
        }
    }

    public function addMaterial($material)
    {
        $materials = $this->materials_needed ?? [];
        
        if (!in_array($material, $materials)) {
            $materials[] = $material;
            $this->update(['materials_needed' => $materials]);
        }
    }

    public function removeMaterial($material)
    {
        $materials = $this->materials_needed ?? [];
        
        if (($key = array_search($material, $materials)) !== false) {
            unset($materials[$key]);
            $this->update(['materials_needed' => array_values($materials)]);
        }
    }

    public function activate($activatedBy)
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now(),
            'activated_by' => $activatedBy,
        ]);

        // Generate initial schedules
        $this->generateSchedules();
    }

    public function deactivate($deactivatedBy, $reason)
    {
        $this->update([
            'status' => 'inactive',
            'deactivated_at' => now(),
            'deactivated_by' => $deactivatedBy,
            'deactivation_reason' => $reason,
        ]);
    }

    public function complete($completedBy, $completionNotes, $totalCost)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => $completedBy,
            'completion_notes' => $completionNotes,
            'total_cost' => $totalCost,
        ]);
    }

    public function generateSchedules()
    {
        $startDate = $this->start_date;
        $endDate = $this->end_date ?? $this->start_date->copy()->addYear();
        $interval = $this->getFrequencyInterval();

        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $this->schedules()->create([
                'property_id' => $this->property_id,
                'title' => $this->title,
                'description' => $this->description,
                'maintenance_type' => $this->maintenance_type,
                'scheduled_date' => $currentDate,
                'estimated_duration' => $this->estimated_duration,
                'priority' => $this->priority,
                'maintenance_team_id' => $this->maintenance_team_id,
                'estimated_cost' => $this->estimated_cost,
                'preventive_maintenance_id' => $this->id,
                'status' => 'scheduled',
                'created_by' => $this->created_by,
            ]);

            $currentDate->add($interval);
        }
    }

    private function getFrequencyInterval()
    {
        $intervals = [
            'daily' => '1 day',
            'weekly' => '1 week',
            'monthly' => '1 month',
            'quarterly' => '3 months',
            'yearly' => '1 year',
        ];

        return $intervals[$this->frequency] ?? '1 month';
    }

    public function getPerformanceMetrics()
    {
        return [
            'total_schedules' => $this->getTotalSchedules(),
            'completed_schedules' => $this->getCompletedSchedules(),
            'completion_rate' => $this->getCompletionRate(),
            'overdue_schedules' => $this->getOverdueSchedules(),
            'average_completion_time' => $this->getAverageCompletionTime(),
            'total_cost' => $this->total_cost,
            'cost_variance' => $this->getCostVariance(),
            'next_scheduled_date' => $this->getNextScheduledDate(),
            'days_remaining' => $this->getDaysRemaining(),
        ];
    }
}

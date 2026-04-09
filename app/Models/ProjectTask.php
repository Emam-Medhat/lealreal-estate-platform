<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ProjectTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'phase_id',
        'assigned_to',
        'created_by',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'estimated_hours',
        'actual_hours',
        'progress',
        'tags',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'estimated_hours' => 'integer',
        'actual_hours' => 'integer',
        'progress' => 'decimal:2',
        'tags' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class, 'phase_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(ProjectTask::class, 'project_task_dependencies', 'task_id', 'dependency_task_id');
    }

    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(ProjectTask::class, 'project_task_dependencies', 'dependency_task_id', 'task_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProjectTaskComment::class, 'task_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProjectTaskAttachment::class, 'task_id');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(ProjectTaskTimeLog::class, 'task_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(ProjectTaskChecklist::class, 'task_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByAssignee($query, $assigneeId)
    {
        return $query->where('assignee_id', $assigneeId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'completed');
    }

    public function scopeDueSoon($query, $days = 3)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>', now())
            ->where('status', '!=', 'completed');
    }

    public function getDaysRemaining()
    {
        return Carbon::now()->diffInDays($this->due_date, false);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isOverdue()
    {
        return $this->due_date < now() && !$this->isCompleted();
    }

    public function isDueSoon()
    {
        return $this->due_date <= now()->addDays(3) && $this->due_date > now() && !$this->isCompleted();
    }

    public function getCompletionPercentage()
    {
        if ($this->checklists->count() > 0) {
            $totalItems = $this->checklists()->withCount('items')->get()->sum('items_count');
            $completedItems = $this->checklists()->withCount(['items' => fn($q) => $q->where('completed', true)])->get()->sum('items_count');
            
            if ($totalItems > 0) {
                return round(($completedItems / $totalItems) * 100, 2);
            }
        }
        
        return $this->progress_percentage;
    }

    public function getTotalTimeLogged()
    {
        return $this->timeLogs()->sum('hours');
    }

    public function getRemainingHours()
    {
        return max(0, $this->estimated_hours - $this->actual_hours);
    }

    public function canBeCompleted()
    {
        // Check if all dependencies are completed
        foreach ($this->dependencies as $dependency) {
            if (!$dependency->isCompleted()) {
                return false;
            }
        }
        
        return true;
    }
}

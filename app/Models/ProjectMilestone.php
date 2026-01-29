<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ProjectMilestone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'due_date',
        'status',
        'progress_percentage',
        'budget_allocated',
        'budget_spent',
        'dependencies',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'progress_percentage' => 'integer',
        'budget_allocated' => 'decimal:2',
        'budget_spent' => 'decimal:2',
        'dependencies' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    
    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(ProjectMilestone::class, 'project_milestone_dependencies', 'milestone_id', 'dependency_milestone_id');
    }

    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(ProjectMilestone::class, 'project_milestone_dependencies', 'dependency_milestone_id', 'milestone_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(ProjectMilestoneDeliverable::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'completed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getDaysRemaining()
    {
        return Carbon::now()->diffInDays($this->due_date, false);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isOverdue()
    {
        return $this->due_date < now() && !$this->isCompleted();
    }

    public function getCompletionPercentage()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) return $this->completion_percentage;

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return round(($completedTasks / $totalTasks) * 100, 2);
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

    public function markAsCompleted($notes = null, $completedBy = null)
    {
        $this->update([
            'status' => 'completed',
            'completion_percentage' => 100,
            'completion_notes' => $notes,
            'completed_at' => now(),
            'completed_by' => $completedBy,
        ]);
    }
}

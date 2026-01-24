<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ProjectPhase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'order',
        'start_date',
        'end_date',
        'duration_days',
        'status',
        'progress_percentage',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'progress_percentage' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByOrder($query)
    {
        return $query->orderBy('order');
    }

    public function getProgressPercentage()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) return $this->progress_percentage;

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    public function getDaysRemaining()
    {
        return Carbon::now()->diffInDays($this->end_date, false);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isActive()
    {
        return $this->status === 'in_progress';
    }

    public function isOverdue()
    {
        return $this->end_date < now() && !$this->isCompleted();
    }

    public function updateProgress()
    {
        $this->update(['progress_percentage' => $this->getProgressPercentage()]);
    }
}

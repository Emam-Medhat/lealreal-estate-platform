<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ProjectMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'team_id',
        'user_id',
        'role',
        'status',
        'hourly_rate',
        'start_date',
        'end_date',
        'joined_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'joined_at' => 'datetime',
        'hourly_rate' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(ProjectTeam::class, 'team_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'assignee_id', 'user_id');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(ProjectTaskTimeLog::class, 'user_id', 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getTotalHoursWorked()
    {
        return $this->timeLogs()->sum('hours');
    }

    public function getTotalEarnings()
    {
        return $this->getTotalHoursWorked() * $this->hourly_rate;
    }

    public function getDaysOnProject()
    {
        return Carbon::parse($this->joined_at)->diffInDays(now());
    }

    public function getCompletedTasksCount()
    {
        return $this->tasks()->where('status', 'completed')->count();
    }

    public function getAssignedTasksCount()
    {
        return $this->tasks()->count();
    }

    public function getTaskCompletionRate()
    {
        $totalTasks = $this->getAssignedTasksCount();
        if ($totalTasks === 0) return 0;

        $completedTasks = $this->getCompletedTasksCount();
        return round(($completedTasks / $totalTasks) * 100, 2);
    }
}

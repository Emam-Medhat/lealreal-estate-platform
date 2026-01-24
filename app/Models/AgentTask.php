<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AgentTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'title',
        'description',
        'type',
        'priority',
        'status',
        'due_date',
        'property_id',
        'lead_id',
        'client_id',
        'estimated_hours',
        'actual_hours',
        'tags',
        'checklist',
        'attachments',
        'notes',
        'assigned_by',
        'assigned_at',
        'completed_at',
        'status_updated_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'status_updated_at' => 'datetime',
        'tags' => 'array',
        'checklist' => 'array',
        'attachments' => 'array',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(AgentLead::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(AgentClient::class);
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(AgentSubtask::class);
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(AgentTaskTimeLog::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(AgentTaskNote::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(UserActivityLog::class, 'subject');
    }

    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
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
        return $query->where('type', $type);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed');
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today())
                    ->where('status', '!=', 'completed');
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
                    ->where('due_date', '>', now())
                    ->where('status', '!=', 'completed');
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && $this->status !== 'completed';
    }

    public function isDueToday(): bool
    {
        return $this->due_date->isToday() && $this->status !== 'completed';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getProgressPercentage(): float
    {
        if (empty($this->checklist)) {
            return $this->status === 'completed' ? 100 : 0;
        }

        $totalItems = count($this->checklist);
        $completedItems = collect($this->checklist)->where('completed', true)->count();

        return $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 2) : 0;
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'status_updated_at' => now(),
        ]);
    }

    public function updateStatus(string $status): void
    {
        $this->update([
            'status' => $status,
            'status_updated_at' => now(),
        ]);

        if ($status === 'completed') {
            $this->update(['completed_at' => now()]);
        }
    }
}

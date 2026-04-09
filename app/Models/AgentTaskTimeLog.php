<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentTaskTimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'agent_id',
        'hours',
        'notes',
        'logged_at',
    ];

    protected $casts = [
        'hours' => 'decimal:2',
        'logged_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(AgentTask::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('logged_at', [$startDate, $endDate]);
    }

    public function getFormattedHours(): string
    {
        return number_format($this->hours, 2) . ' hours';
    }
}

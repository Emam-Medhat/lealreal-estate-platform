<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentClientCommunication extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'agent_id',
        'type',
        'direction',
        'subject',
        'content',
        'duration_minutes',
        'created_at',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'created_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(AgentClient::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDirection($query, $direction)
    {
        return $query->where('direction', $direction);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}

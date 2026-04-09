<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'name',
        'email',
        'phone',
        'whatsapp',
        'company',
        'client_type',
        'status',
        'status_updated_at',
        'source',
        'referral_source',
        'budget_min',
        'budget_max',
        'preferred_areas',
        'preferred_property_types',
        'requirements',
        'timeline',
        'financing_status',
        'pre_approved_amount',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'notes',
        'tags',
        'custom_fields',
        'last_contact_date',
        'next_follow_up',
        'follow_up_type',
        'follow_up_notes',
    ];

    protected $casts = [
        'status_updated_at' => 'datetime',
        'last_contact_date' => 'datetime',
        'next_follow_up' => 'datetime',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'pre_approved_amount' => 'decimal:2',
        'preferred_areas' => 'array',
        'preferred_property_types' => 'array',
        'requirements' => 'array',
        'tags' => 'array',
        'custom_fields' => 'array',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(AgentAppointment::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(AgentClientCommunication::class);
    }

    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('client_type', $type);
    }

    public function scopeNeedingFollowUp($query)
    {
        return $query->whereNotNull('next_follow_up')
                    ->where('next_follow_up', '<=', now())
                    ->where('status', '!=', 'closed');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function needsFollowUp(): bool
    {
        return $this->next_follow_up && $this->next_follow_up->isPast() && !$this->isClosed();
    }

    public function getBudgetRange(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return '$' . number_format($this->budget_min) . ' - $' . number_format($this->budget_max);
        }
        
        if ($this->budget_min) {
            return '$' . number_format($this->budget_min) . '+';
        }
        
        if ($this->budget_max) {
            return 'Up to $' . number_format($this->budget_max);
        }
        
        return 'Not specified';
    }
}

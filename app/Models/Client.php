<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'agent_id',
        'company_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'company_name',
        'type', // individual, company, investor
        'status', // active, inactive, prospect
        'source', // website, referral, advertising, social_media
        'budget_min',
        'budget_max',
        'preferred_property_types',
        'preferred_locations',
        'notes',
        'last_contact_date',
        'next_follow_up_date',
        'total_purchases',
        'total_spent',
        'loyalty_level',
        'is_vip',
        'created_by',
    ];

    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'total_purchases' => 'integer',
        'total_spent' => 'decimal:2',
        'preferred_property_types' => 'array',
        'preferred_locations' => 'array',
        'last_contact_date' => 'datetime',
        'next_follow_up_date' => 'datetime',
        'is_vip' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Core Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Financial Relationships
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Property Relationships
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'client_property')
            ->withPivot('type', 'status', 'amount', 'created_at')
            ->withTimestamps();
    }

    public function ownedProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    // Activity Relationships
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(AgentClientCommunication::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeProspects($query)
    {
        return $query->where('status', 'prospect');
    }

    public function scopeVip($query)
    {
        return $query->where('is_vip', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Methods
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getBudgetRangeAttribute(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return number_format($this->budget_min, 2) . ' - ' . number_format($this->budget_max, 2);
        }
        return 'Not specified';
    }

    public function getTotalInvoicesAmountAttribute(): float
    {
        return $this->invoices()->sum('total') ?? 0;
    }

    public function getTotalPaidAmountAttribute(): float
    {
        return $this->payments()->where('status', 'completed')->sum('amount') ?? 0;
    }

    public function getOutstandingBalanceAttribute(): float
    {
        return $this->getTotalInvoicesAmountAttribute() - $this->getTotalPaidAmountAttribute();
    }

    public function isHighValueClient(): bool
    {
        return $this->total_spent >= 1000000; // 1M+ considered high value
    }

    public function updateSpending(float $amount): void
    {
        $this->increment('total_spent', $amount);
        $this->increment('total_purchases');
        
        // Update loyalty level based on spending
        if ($this->total_spent >= 5000000) {
            $this->loyalty_level = 'platinum';
        } elseif ($this->total_spent >= 1000000) {
            $this->loyalty_level = 'gold';
        } elseif ($this->total_spent >= 100000) {
            $this->loyalty_level = 'silver';
        } else {
            $this->loyalty_level = 'bronze';
        }
        
        $this->save();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentOpportunityInvestment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'opportunity_id',
        'investor_id',
        'investment_amount',
        'status',
        'investment_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'investment_amount' => 'decimal:15,2',
        'investment_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(InvestmentOpportunity::class);
    }

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('investment_date', [$startDate, $endDate]);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getInvestmentAmountFormattedAttribute(): string
    {
        return number_format($this->investment_amount, 2);
    }

    public function getInvestmentDateFormattedAttribute(): string
    {
        return $this->investment_date->format('Y-m-d H:i:s');
    }

    public function getStatusAttribute(): string
    {
        return $this->status ?? 'pending';
    }

    public function getNotesAttribute(): string
    {
        return $this->notes ?? '';
    }
}

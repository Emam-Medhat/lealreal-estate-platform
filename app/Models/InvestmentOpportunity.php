<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvestmentOpportunity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'investment_type',
        'sector',
        'min_investment',
        'max_investment',
        'expected_return',
        'risk_level',
        'funding_goal',
        'total_invested',
        'investor_count',
        'status',
        'published_at',
        'deadline',
        'documents',
        'images',
        'tags',
        'location',
        'team_members',
        'milestones',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'documents' => 'array',
        'images' => 'array',
        'tags' => 'array',
        'team_members' => 'array',
        'milestones' => 'array',
        'min_investment' => 'decimal:15,2',
        'max_investment' => 'decimal:15,2',
        'expected_return' => 'decimal:8,4',
        'funding_goal' => 'decimal:15,2',
        'total_invested' => 'decimal:15,2',
        'published_at' => 'datetime',
        'deadline' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function investments(): HasMany
    {
        return $this->hasMany(InvestmentOpportunityInvestment::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('investment_type', $type);
    }

    public function scopeBySector($query, $sector)
    {
        return $query->where('sector', $sector);
    }

    public function scopeByRiskLevel($query, $risk)
    {
        return $query->where('risk_level', $risk);
    }

    // Helper methods
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isFunded(): bool
    {
        return $this->status === 'funded';
    }

    public function isExpired(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }

    public function getFundingProgressAttribute(): float
    {
        return $this->funding_goal > 0 ? ($this->total_invested / $this->funding_goal) * 100 : 0;
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->funding_goal - $this->total_invested;
    }

    public function getDaysRemainingAttribute(): int
    {
        return $this->deadline ? max(0, now()->diffInDays($this->deadline)) : 0;
    }

    public function getMinInvestmentFormattedAttribute(): string
    {
        return number_format($this->min_investment, 2);
    }

    public function getMaxInvestmentFormattedAttribute(): string
    {
        return number_format($this->max_investment, 2);
    }

    public function getExpectedReturnFormattedAttribute(): string
    {
        return number_format($this->expected_return, 2) . '%';
    }

    public function getFundingGoalFormattedAttribute(): string
    {
        return number_format($this->funding_goal, 2);
    }

    public function getTotalInvestedFormattedAttribute(): string
    {
        return number_format($this->total_invested, 2);
    }

    public function getRemainingAmountFormattedAttribute(): string
    {
        return number_format($this->getRemainingAmountAttribute(), 2);
    }

    public function getDocumentsCountAttribute(): int
    {
        return count($this->documents ?? []);
    }

    public function getImagesCountAttribute(): int
    {
        return count($this->images ?? []);
    }

    public function getTagsCountAttribute(): int
    {
        return count($this->tags ?? []);
    }

    public function getTeamMembersCountAttribute(): int
    {
        return count($this->team_members ?? []);
    }

    public function getMilestonesCountAttribute(): int
    {
        return count($this->milestones ?? []);
    }

    public function getPublishedAtFormattedAttribute(): string
    {
        return $this->published_at ? $this->published_at->format('Y-m-d H:i:s') : '';
    }

    public function getDeadlineFormattedAttribute(): string
    {
        return $this->deadline ? $this->deadline->format('Y-m-d H:i:s') : '';
    }

    public function getInvestmentTypeAttribute(): string
    {
        return $this->investment_type ?? 'stocks';
    }

    public function getSectorAttribute(): string
    {
        return $this->sector ?? 'technology';
    }

    public function getRiskLevelAttribute(): string
    {
        return $this->risk_level ?? 'medium';
    }

    public function getStatusAttribute(): string
    {
        return $this->status ?? 'draft';
    }

    public function getLocationAttribute(): string
    {
        return $this->location ?? 'Global';
    }

    public function getTitleAttribute(): string
    {
        return $this->title ?? '';
    }

    public function getDescriptionAttribute(): string
    {
        return $this->description ?? '';
    }

    public function getTagsAttribute(): array
    {
        return $this->tags ?? [];
    }

    public function getTeamMembersAttribute(): array
    {
        return $this->team_members ?? [];
    }

    public function getMilestonesAttribute(): array
    {
        return $this->milestones ?? [];
    }

    public function getDocumentsAttribute(): array
    {
        return $this->documents ?? [];
    }

    public function getImagesAttribute(): array
    {
        return $this->images ?? [];
    }
}

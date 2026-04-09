<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvestmentCrowdfunding extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_name',
        'description',
        'category',
        'funding_goal',
        'total_raised',
        'investor_count',
        'minimum_investment',
        'maximum_investment',
        'equity_offered',
        'projected_return_rate',
        'risk_level',
        'status',
        'start_date',
        'end_date',
        'published_at',
        'documents',
        'images',
        'updates',
        'team_members',
        'milestones',
        'location',
        'tags',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'documents' => 'array',
        'images' => 'array',
        'updates' => 'array',
        'team_members' => 'array',
        'milestones' => 'array',
        'tags' => 'array',
        'funding_goal' => 'decimal:15,2',
        'total_raised' => 'decimal:15,2',
        'minimum_investment' => 'decimal:15,2',
        'maximum_investment' => 'decimal:15,2',
        'equity_offered' => 'decimal:8,4',
        'projected_return_rate' => 'decimal:8,4',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function investments(): HasMany
    {
        return $this->hasMany(InvestmentCrowdfundingInvestment::class);
    }

    public function investors(): HasMany
    {
        return $this->hasMany(InvestmentCrowdfundingInvestment::class, 'investor_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(CrowdfundingUpdate::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByRiskLevel($query, $risk)
    {
        return $query->where('risk_level', $risk);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
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

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function getFundingProgressAttribute(): float
    {
        return $this->funding_goal > 0 ? ($this->total_raised / $this->funding_goal) * 100 : 0;
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->funding_goal - $this->total_raised;
    }

    public function getDaysRemainingAttribute(): int
    {
        return $this->end_date ? max(0, now()->diffInDays($this->end_date)) : 0;
    }

    public function getFundingGoalFormattedAttribute(): string
    {
        return number_format($this->funding_goal, 2);
    }

    public function getTotalRaisedFormattedAttribute(): string
    {
        return number_format($this->total_raised, 2);
    }

    public function getRemainingAmountFormattedAttribute(): string
    {
        return number_format($this->getRemainingAmountAttribute(), 2);
    }

    public function getMinimumInvestmentFormattedAttribute(): string
    {
        return number_format($this->minimum_investment, 2);
    }

    public function getMaximumInvestmentFormattedAttribute(): string
    {
        return number_format($this->maximum_investment, 2);
    }

    public function getEquityOfferedFormattedAttribute(): string
    {
        return number_format($this->equity_offered, 4) . '%';
    }

    public function getProjectedReturnRateFormattedAttribute(): string
    {
        return number_format($this->projected_return_rate, 2) . '%';
    }

    public function getStartDateFormattedAttribute(): string
    {
        return $this->start_date ? $this->start_date->format('Y-m-d H:i:s') : '';
    }

    public function getEndDateFormattedAttribute(): string
    {
        return $this->end_date ? $this->end_date->format('Y-m-d H:i:s') : '';
    }

    public function getPublishedAtFormattedAttribute(): string
    {
        return $this->published_at ? $this->published_at->format('Y-m-d H:i:s') : '';
    }

    public function getDocumentsCountAttribute(): int
    {
        return count($this->documents ?? []);
    }

    public function getImagesCountAttribute(): int
    {
        return count($this->images ?? []);
    }

    public function getUpdatesCountAttribute(): int
    {
        return count($this->updates ?? []);
    }

    public function getTeamMembersCountAttribute(): int
    {
        return count($this->team_members ?? []);
    }

    public function getMilestonesCountAttribute(): int
    {
        return count($this->milestones ?? []);
    }

    public function getTagsCountAttribute(): int
    {
        return count($this->tags ?? []);
    }

    public function getCampaignNameAttribute(): string
    {
        return $this->campaign_name ?? '';
    }

    public function getDescriptionAttribute(): string
    {
        return $this->description ?? '';
    }

    public function getCategoryAttribute(): string
    {
        return $this->category ?? 'technology';
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

    public function getTagsAttribute(): array
    {
        return $this->tags ?? [];
    }

    public function getDocumentsAttribute(): array
    {
        return $this->documents ?? [];
    }

    public function getImagesAttribute(): array
    {
        return $this->images ?? [];
    }

    public function getUpdatesAttribute(): array
    {
        return $this->updates ?? [];
    }

    public function getTeamMembersAttribute(): array
    {
        return $this->team_members ?? [];
    }

    public function getMilestonesAttribute(): array
    {
        return $this->milestones ?? [];
    }

    public function getFundingStatusAttribute(): string
    {
        $progress = $this->getFundingProgressAttribute();
        if ($progress >= 100) return 'Fully Funded';
        if ($progress >= 75) return 'Nearly Funded';
        if ($progress >= 50) return 'Halfway There';
        if ($progress >= 25) return 'Good Progress';
        return 'Just Started';
    }

    public function getUrgencyAttribute(): string
    {
        $daysRemaining = $this->getDaysRemainingAttribute();
        if ($daysRemaining <= 7) return 'Critical';
        if ($daysRemaining <= 30) return 'High';
        if ($daysRemaining <= 60) return 'Medium';
        return 'Low';
    }
}

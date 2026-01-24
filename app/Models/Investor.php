<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Investor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_name',
        'investor_type',
        'status',
        'total_invested',
        'total_returns',
        'risk_tolerance',
        'investment_goals',
        'preferred_sectors',
        'experience_years',
        'accredited_investor',
        'verification_status',
        'address',
        'social_links',
        'bio',
        'profile_picture',
        'watchlist',
        'crowdfunding_watchlist',
        'created_by',
        'updated_by',
        'verified_at',
    ];

    protected $casts = [
        'address' => 'array',
        'social_links' => 'array',
        'investment_goals' => 'array',
        'preferred_sectors' => 'array',
        'watchlist' => 'array',
        'crowdfunding_watchlist' => 'array',
        'total_invested' => 'decimal:15,2',
        'total_returns' => 'decimal:15,2',
        'accredited_investor' => 'boolean',
        'verified_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profile(): HasMany
    {
        return $this->hasMany(InvestorProfile::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(InvestorPortfolio::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InvestorTransaction::class);
    }

    public function roiCalculations(): HasMany
    {
        return $this->hasMany(InvestorRoiCalculation::class);
    }

    public function riskAssessments(): HasMany
    {
        return $this->hasMany(InvestorRiskAssessment::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(InvestmentOpportunityInvestment::class);
    }

    public function fundInvestments(): HasMany
    {
        return $this->hasMany(InvestmentFundInvestment::class);
    }

    public function crowdfundingInvestments(): HasMany
    {
        return $this->hasMany(InvestmentCrowdfundingInvestment::class);
    }

    public function defiLoans(): HasMany
    {
        return $this->hasMany(DefiLoan::class);
    }

    public function defiStaking(): HasMany
    {
        return $this->hasMany(DefiStaking::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('investor_type', $type);
    }

    public function scopeByRiskTolerance($query, $tolerance)
    {
        return $query->where('risk_tolerance', $tolerance);
    }

    // Helper methods
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function isAccredited(): bool
    {
        return $this->accredited_investor;
    }

    public function getNetInvestedAttribute(): float
    {
        return $this->total_invested - $this->total_returns;
    }

    public function getRoiAttribute(): float
    {
        return $this->total_invested > 0 ? (($this->total_returns / $this->total_invested) * 100) : 0;
    }

    public function getProfilePictureUrlAttribute(): string
    {
        return $this->profile_picture ? asset('storage/' . $this->profile_picture) : '';
    }

    public function getInvestmentGoalsCountAttribute(): int
    {
        return count($this->investment_goals ?? []);
    }

    public function getPreferredSectorsCountAttribute(): int
    {
        return count($this->preferred_sectors ?? []);
    }

    public function getSocialLinksCountAttribute(): int
    {
        return count($this->social_links ?? []);
    }

    public function getWatchlistCountAttribute(): int
    {
        return count($this->watchlist ?? []);
    }

    public function getExperienceLevelAttribute(): string
    {
        if ($this->experience_years < 2) return 'Beginner';
        if ($this->experience_years < 5) return 'Intermediate';
        if ($this->experience_years < 10) return 'Advanced';
        return 'Expert';
    }

    public function getRiskProfileAttribute(): array
    {
        return [
            'tolerance' => $this->risk_tolerance,
            'level' => $this->getRiskLevel(),
            'description' => $this->getRiskDescription(),
        ];
    }

    private function getRiskLevel(): string
    {
        $levels = [
            'conservative' => 'Low',
            'moderate' => 'Medium',
            'aggressive' => 'High',
            'very_aggressive' => 'Very High',
        ];

        return $levels[$this->risk_tolerance] ?? 'Medium';
    }

    private function getRiskDescription(): string
    {
        $descriptions = [
            'conservative' => 'Prefers stable, low-risk investments with predictable returns',
            'moderate' => 'Balanced approach between risk and return',
            'aggressive' => 'Willing to take higher risks for potentially higher returns',
            'very_aggressive' => 'Seeks maximum returns even with very high risk',
        ];

        return $descriptions[$this->risk_tolerance] ?? 'Balanced investment approach';
    }
}

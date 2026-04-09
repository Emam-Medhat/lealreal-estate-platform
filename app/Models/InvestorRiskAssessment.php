<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestorRiskAssessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'investor_id',
        'portfolio_id',
        'assessment_name',
        'risk_type',
        'assessment_methodology',
        'market_risk',
        'credit_risk',
        'liquidity_risk',
        'operational_risk',
        'concentration_risk',
        'currency_risk',
        'regulatory_risk',
        'overall_risk_score',
        'overall_risk_level',
        'risk_factors',
        'mitigation_strategies',
        'risk_tolerance_comparison',
        'stress_test_results',
        'scenario_analysis',
        'recommendations',
        'next_review_date',
        'assessor_notes',
        'assessed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'risk_factors' => 'array',
        'mitigation_strategies' => 'array',
        'risk_tolerance_comparison' => 'array',
        'stress_test_results' => 'array',
        'scenario_analysis' => 'array',
        'recommendations' => 'array',
        'market_risk' => 'decimal:5,2',
        'credit_risk' => 'decimal:5,2',
        'liquidity_risk' => 'decimal:5,2',
        'operational_risk' => 'decimal:5,2',
        'concentration_risk' => 'decimal:5,2',
        'currency_risk' => 'decimal:5,2',
        'regulatory_risk' => 'decimal:5,2',
        'overall_risk_score' => 'decimal:5,2',
        'assessed_at' => 'datetime',
        'next_review_date' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(InvestorPortfolio::class);
    }

    // Scopes
    public function scopeByRiskType($query, $type)
    {
        return $query->where('risk_type', $type);
    }

    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('overall_risk_level', $level);
    }

    public function scopeByMethodology($query, $methodology)
    {
        return $query->where('assessment_methodology', $methodology);
    }

    public function scopeUpcomingReview($query)
    {
        return $query->where('next_review_date', '<=', now()->addDays(30))
                   ->where('next_review_date', '>', now());
    }

    // Helper methods
    public function isHighRisk(): bool
    {
        return in_array($this->overall_risk_level, ['high', 'critical']);
    }

    public function isLowRisk(): bool
    {
        return $this->overall_risk_level === 'low';
    }

    public function isOverdue(): bool
    {
        return $this->next_review_date && $this->next_review_date->isPast();
    }

    public function getOverallRiskScoreFormattedAttribute(): string
    {
        return number_format($this->overall_risk_score, 2);
    }

    public function getRiskLevelAttribute(): string
    {
        return $this->overall_risk_level ?? 'medium';
    }

    public function getRiskTypeAttribute(): string
    {
        return $this->risk_type ?? 'portfolio';
    }

    public function getAssessmentMethodologyAttribute(): string
    {
        return $this->assessment_methodology ?? 'quantitative';
    }

    public function getAssessedAtFormattedAttribute(): string
    {
        return $this->assessed_at->format('Y-m-d H:i:s');
    }

    public function getNextReviewDateFormattedAttribute(): string
    {
        return $this->next_review_date ? $this->next_review_date->format('Y-m-d') : 'N/A';
    }

    public function getDaysUntilReviewAttribute(): int
    {
        return $this->next_review_date ? now()->diffInDays($this->next_review_date) : 0;
    }

    public function getRiskBreakdownAttribute(): array
    {
        return [
            'market_risk' => $this->market_risk,
            'credit_risk' => $this->credit_risk,
            'liquidity_risk' => $this->liquidity_risk,
            'operational_risk' => $this->operational_risk,
            'concentration_risk' => $this->concentration_risk,
            'currency_risk' => $this->currency_risk,
            'regulatory_risk' => $this->regulatory_risk,
        ];
    }

    public function getRiskFactorsCountAttribute(): int
    {
        return count($this->risk_factors ?? []);
    }

    public function getMitigationStrategiesCountAttribute(): int
    {
        return count($this->mitigation_strategies ?? []);
    }

    public function getRecommendationsCountAttribute(): int
    {
        return count($this->recommendations ?? []);
    }

    public function getStressTestResultsCountAttribute(): int
    {
        return count($this->stress_test_results ?? []);
    }

    public function getScenarioAnalysisCountAttribute(): int
    {
        return count($this->scenario_analysis ?? []);
    }

    public function getRiskToleranceComparisonAttribute(): array
    {
        return $this->risk_tolerance_comparison ?? [];
    }

    public function getRiskScoreColorAttribute(): string
    {
        $score = $this->overall_risk_score;
        if ($score <= 20) return 'green';
        if ($score <= 40) return 'yellow';
        if ($score <= 60) return 'orange';
        return 'red';
    }

    public function getRiskLevelDescriptionAttribute(): string
    {
        $descriptions = [
            'low' => 'Minimal risk with stable returns expected',
            'medium' => 'Moderate risk with balanced risk-return profile',
            'high' => 'High risk with potential for significant losses',
            'critical' => 'Very high risk with potential for substantial losses',
        ];

        return $descriptions[$this->overall_risk_level] ?? 'Moderate risk level';
    }

    public function getTopRiskFactorsAttribute(): array
    {
        $factors = $this->risk_factors ?? [];
        return collect($factors)
            ->sortByDesc('probability')
            ->take(5)
            ->values()
            ->toArray();
    }

    public function getTopMitigationStrategiesAttribute(): array
    {
        $strategies = $this->mitigation_strategies ?? [];
        return collect($strategies)
            ->where('effectiveness', 'high')
            ->take(3)
            ->values()
            ->toArray();
    }
}

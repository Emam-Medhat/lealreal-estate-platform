<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'period',
        'period_start',
        'period_end',
        'total_leads',
        'new_leads',
        'contacted_leads',
        'qualified_leads',
        'converted_leads',
        'lost_leads',
        'total_appointments',
        'completed_appointments',
        'cancelled_appointments',
        'no_show_appointments',
        'total_properties',
        'sold_properties',
        'listed_properties',
        'active_properties',
        'total_sales_value',
        'total_commissions',
        'average_deal_size',
        'conversion_rate',
        'appointment_show_rate',
        'lead_to_appointment_rate',
        'appointment_to_conversion_rate',
        'average_response_time',
        'total_calls',
        'total_emails',
        'total_meetings',
        'customer_satisfaction_score',
        'average_rating',
        'total_reviews',
        'productivity_score',
        'efficiency_score',
        'quality_score',
        'overall_score',
        'rank_in_agency',
        'rank_in_territory',
        'rank_overall',
        'goals_achieved',
        'goals_total',
        'bonus_earned',
        'target_sales',
        'target_leads',
        'target_appointments',
        'target_commissions',
        'sales_achievement_percentage',
        'leads_achievement_percentage',
        'appointments_achievement_percentage',
        'commissions_achievement_percentage',
        'overall_achievement_percentage',
        'performance_grade',
        'strengths',
        'weaknesses',
        'improvement_areas',
        'notes',
        'reviewed_by',
        'reviewed_at',
        'custom_metrics',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_sales_value' => 'decimal:15,2',
        'total_commissions' => 'decimal:15,2',
        'average_deal_size' => 'decimal:15,2',
        'conversion_rate' => 'decimal:5,2',
        'appointment_show_rate' => 'decimal:5,2',
        'lead_to_appointment_rate' => 'decimal:5,2',
        'appointment_to_conversion_rate' => 'decimal:5,2',
        'average_response_time' => 'integer',
        'customer_satisfaction_score' => 'decimal:3,2',
        'average_rating' => 'decimal:3,2',
        'productivity_score' => 'decimal:5,2',
        'efficiency_score' => 'decimal:5,2',
        'quality_score' => 'decimal:5,2',
        'overall_score' => 'decimal:5,2',
        'rank_in_agency' => 'integer',
        'rank_in_territory' => 'integer',
        'rank_overall' => 'integer',
        'bonus_earned' => 'decimal:15,2',
        'target_sales' => 'decimal:15,2',
        'target_leads' => 'integer',
        'target_appointments' => 'integer',
        'target_commissions' => 'decimal:15,2',
        'sales_achievement_percentage' => 'decimal:5,2',
        'leads_achievement_percentage' => 'decimal:5,2',
        'appointments_achievement_percentage' => 'decimal:5,2',
        'commissions_achievement_percentage' => 'decimal:5,2',
        'overall_achievement_percentage' => 'decimal:5,2',
        'reviewed_at' => 'datetime',
        'strengths' => 'json',
        'weaknesses' => 'json',
        'improvement_areas' => 'json',
        'custom_metrics' => 'json',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where('period_start', '>=', $startDate)
                    ->where('period_end', '<=', $endDate);
    }

    public function scopeMonthly($query)
    {
        return $query->where('period', 'monthly');
    }

    public function scopeQuarterly($query)
    {
        return $query->where('period', 'quarterly');
    }

    public function scopeYearly($query)
    {
        return $query->where('period', 'yearly');
    }

    public function scopeWeekly($query)
    {
        return $query->where('period', 'weekly');
    }

    public function scopeTopPerformers($query, $limit = 10)
    {
        return $query->orderBy('overall_score', 'desc')->limit($limit);
    }

    public function scopeByRank($query, $rank)
    {
        return $query->where('rank_overall', $rank);
    }

    public function scopeByGrade($query, $grade)
    {
        return $query->where('performance_grade', $grade);
    }

    public function scopeAboveTarget($query)
    {
        return $query->where('overall_achievement_percentage', '>=', 100);
    }

    public function scopeAtTarget($query)
    {
        return $query->where('overall_achievement_percentage', '>=', 90)
                    ->where('overall_achievement_percentage', '<', 100);
    }

    public function scopeBelowTarget($query)
    {
        return $query->where('overall_achievement_percentage', '<', 90);
    }

    public function scopeWithMinimumScore($query, $minScore)
    {
        return $query->where('overall_score', '>=', $minScore);
    }

    public function scopeWithMinimumSales($query, $minSales)
    {
        return $query->where('total_sales_value', '>=', $minSales);
    }

    public function scopeWithMinimumConversions($query, $minConversions)
    {
        return $query->where('converted_leads', '>=', $minConversions);
    }

    // Helper Methods
    public function getFormattedTotalSalesValueAttribute(): string
    {
        return number_format($this->total_sales_value, 2) . ' SAR';
    }

    public function getFormattedTotalCommissionsAttribute(): string
    {
        return number_format($this->total_commissions, 2) . ' SAR';
    }

    public function getFormattedAverageDealSizeAttribute(): string
    {
        return number_format($this->average_deal_size, 2) . ' SAR';
    }

    public function getFormattedBonusEarnedAttribute(): string
    {
        return number_format($this->bonus_earned, 2) . ' SAR';
    }

    public function getFormattedConversionRateAttribute(): string
    {
        return number_format($this->conversion_rate, 1) . '%';
    }

    public function getFormattedAppointmentShowRateAttribute(): string
    {
        return number_format($this->appointment_show_rate, 1) . '%';
    }

    public function getFormattedLeadToAppointmentRateAttribute(): string
    {
        return number_format($this->lead_to_appointment_rate, 1) . '%';
    }

    public function getFormattedAppointmentToConversionRateAttribute(): string
    {
        return number_format($this->appointment_to_conversion_rate, 1) . '%';
    }

    public function getFormattedCustomerSatisfactionScoreAttribute(): string
    {
        return number_format($this->customer_satisfaction_score, 1) . '/5';
    }

    public function getFormattedAverageRatingAttribute(): string
    {
        return number_format($this->average_rating, 1) . '/5';
    }

    public function getFormattedOverallScoreAttribute(): string
    {
        return number_format($this->overall_score, 1);
    }

    public function getFormattedSalesAchievementPercentageAttribute(): string
    {
        return number_format($this->sales_achievement_percentage, 1) . '%';
    }

    public function getFormattedLeadsAchievementPercentageAttribute(): string
    {
        return number_format($this->leads_achievement_percentage, 1) . '%';
    }

    public function getFormattedAppointmentsAchievementPercentageAttribute(): string
    {
        return number_format($this->appointments_achievement_percentage, 1) . '%';
    }

    public function getFormattedCommissionsAchievementPercentageAttribute(): string
    {
        return number_format($this->commissions_achievement_percentage, 1) . '%';
    }

    public function getFormattedOverallAchievementPercentageAttribute(): string
    {
        return number_format($this->overall_achievement_percentage, 1) . '%';
    }

    public function getLeadConversionEfficiencyAttribute(): float
    {
        return $this->total_leads > 0 ? ($this->converted_leads / $this->total_leads) * 100 : 0;
    }

    public function getAppointmentEfficiencyAttribute(): float
    {
        return $this->total_appointments > 0 ? ($this->completed_appointments / $this->total_appointments) * 100 : 0;
    }

    public function getSalesEfficiencyAttribute(): float
    {
        return $this->listed_properties > 0 ? ($this->sold_properties / $this->listed_properties) * 100 : 0;
    }

    public function getResponseTimeRatingAttribute(): string
    {
        if ($this->average_response_time <= 60) {
            return 'excellent';
        } elseif ($this->average_response_time <= 180) {
            return 'good';
        } elseif ($this->average_response_time <= 360) {
            return 'average';
        } else {
            return 'poor';
        }
    }

    public function getPerformanceLevelAttribute(): string
    {
        if ($this->overall_score >= 90) {
            return 'excellent';
        } elseif ($this->overall_score >= 80) {
            return 'very_good';
        } elseif ($this->overall_score >= 70) {
            return 'good';
        } elseif ($this->overall_score >= 60) {
            return 'average';
        } elseif ($this->overall_score >= 50) {
            return 'below_average';
        } else {
            return 'poor';
        }
    }

    public function getPerformanceColorAttribute(): string
    {
        switch ($this->performance_level) {
            case 'excellent':
                return 'green';
            case 'very_good':
                return 'blue';
            case 'good':
                return 'cyan';
            case 'average':
                return 'yellow';
            case 'below_average':
                return 'orange';
            case 'poor':
                return 'red';
            default:
                return 'gray';
        }
    }

    public function getGradeColorAttribute(): string
    {
        switch ($this->performance_grade) {
            case 'A+':
            case 'A':
                return 'green';
            case 'B+':
            case 'B':
                return 'blue';
            case 'C+':
            case 'C':
                return 'yellow';
            case 'D+':
            case 'D':
                return 'orange';
            case 'F':
                return 'red';
            default:
                return 'gray';
        }
    }

    public function getStrengthsListAttribute(): array
    {
        return $this->strengths ?? [];
    }

    public function getWeaknessesListAttribute(): array
    {
        return $this->weaknesses ?? [];
    }

    public function getImprovementAreasListAttribute(): array
    {
        return $this->improvement_areas ?? [];
    }

    public function getCustomMetricsListAttribute(): array
    {
        return $this->custom_metrics ?? [];
    }

    public function getPeriodDisplayAttribute(): string
    {
        switch ($this->period) {
            case 'weekly':
                return 'Week of ' . $this->period_start->format('M d, Y');
            case 'monthly':
                return $this->period_start->format('F Y');
            case 'quarterly':
                return 'Q' . ceil($this->period_start->month / 3) . ' ' . $this->period_start->format('Y');
            case 'yearly':
                return $this->period_start->format('Y');
            default:
                return $this->period_start->format('M d, Y') . ' - ' . $this->period_end->format('M d, Y');
        }
    }

    public function getDaysInPeriodAttribute(): int
    {
        return $this->period_start->diffInDays($this->period_end) + 1;
    }

    public function getLeadsPerDayAttribute(): float
    {
        return $this->days_in_period > 0 ? $this->total_leads / $this->days_in_period : 0;
    }

    public function getAppointmentsPerDayAttribute(): float
    {
        return $this->days_in_period > 0 ? $this->total_appointments / $this->days_in_period : 0;
    }

    public function getSalesPerDayAttribute(): float
    {
        return $this->days_in_period > 0 ? $this->sold_properties / $this->days_in_period : 0;
    }

    public function getCommissionsPerDayAttribute(): float
    {
        return $this->days_in_period > 0 ? $this->total_commissions / $this->days_in_period : 0;
    }

    public function getCallToLeadRatioAttribute(): float
    {
        return $this->total_calls > 0 ? ($this->total_leads / $this->total_calls) * 100 : 0;
    }

    public function getEmailToLeadRatioAttribute(): float
    {
        return $this->total_emails > 0 ? ($this->total_leads / $this->total_emails) * 100 : 0;
    }

    public function getMeetingToConversionRatioAttribute(): float
    {
        return $this->total_meetings > 0 ? ($this->converted_leads / $this->total_meetings) * 100 : 0;
    }

    public function getGoalAchievementRateAttribute(): float
    {
        return $this->goals_total > 0 ? ($this->goals_achieved / $this->goals_total) * 100 : 0;
    }

    public function isExceedingTargets(): bool
    {
        return $this->overall_achievement_percentage >= 100;
    }

    public function isMeetingTargets(): bool
    {
        return $this->overall_achievement_percentage >= 90;
    }

    public function isBelowTargets(): bool
    {
        return $this->overall_achievement_percentage < 90;
    }

    public function hasImprovedFromPrevious($previousPerformance): bool
    {
        return $previousPerformance && $this->overall_score > $previousPerformance->overall_score;
    }

    public function hasDeclinedFromPrevious($previousPerformance): bool
    {
        return $previousPerformance && $this->overall_score < $previousPerformance->overall_score;
    }

    public function getPerformanceTrendAttribute(): string
    {
        // This would typically compare with previous period
        return 'stable'; // Can be 'improving', 'declining', or 'stable'
    }

    public function getTrendColorAttribute(): string
    {
        switch ($this->performance_trend) {
            case 'improving':
                return 'green';
            case 'declining':
                return 'red';
            case 'stable':
                return 'blue';
            default:
                return 'gray';
        }
    }

    public function calculateOverallScore(): void
    {
        $weights = [
            'sales' => 0.3,
            'leads' => 0.2,
            'appointments' => 0.15,
            'conversion' => 0.15,
            'satisfaction' => 0.1,
            'efficiency' => 0.1,
        ];

        $salesScore = min(($this->sales_achievement_percentage / 100) * 100, 100);
        $leadsScore = min(($this->leads_achievement_percentage / 100) * 100, 100);
        $appointmentsScore = min(($this->appointments_achievement_percentage / 100) * 100, 100);
        $conversionScore = min($this->conversion_rate * 2, 100); // Assuming 50% conversion = 100 points
        $satisfactionScore = min(($this->customer_satisfaction_score / 5) * 100, 100);
        $efficiencyScore = min($this->productivity_score, 100);

        $overallScore = 
            ($salesScore * $weights['sales']) +
            ($leadsScore * $weights['leads']) +
            ($appointmentsScore * $weights['appointments']) +
            ($conversionScore * $weights['conversion']) +
            ($satisfactionScore * $weights['satisfaction']) +
            ($efficiencyScore * $weights['efficiency']);

        $this->update(['overall_score' => $overallScore]);
    }

    public function assignGrade(): void
    {
        $score = $this->overall_score;

        if ($score >= 95) {
            $grade = 'A+';
        } elseif ($score >= 90) {
            $grade = 'A';
        } elseif ($score >= 85) {
            $grade = 'B+';
        } elseif ($score >= 80) {
            $grade = 'B';
        } elseif ($score >= 75) {
            $grade = 'C+';
        } elseif ($score >= 70) {
            $grade = 'C';
        } elseif ($score >= 65) {
            $grade = 'D+';
        } elseif ($score >= 60) {
            $grade = 'D';
        } else {
            $grade = 'F';
        }

        $this->update(['performance_grade' => $grade]);
    }

    public function calculateRanks($totalAgents, $agencyAgents = null, $territoryAgents = null): void
    {
        // This would typically be calculated by comparing with other agents
        // For now, we'll set placeholder values
        $this->update([
            'rank_overall' => $this->calculateRank($totalAgents),
            'rank_in_agency' => $agencyAgents ? $this->calculateRank($agencyAgents) : null,
            'rank_in_territory' => $territoryAgents ? $this->calculateRank($territoryAgents) : null,
        ]);
    }

    private function calculateRank($totalAgents): int
    {
        // Simplified rank calculation based on overall score
        // In reality, this would query all agents and sort by score
        return max(1, ceil($totalAgents * (1 - ($this->overall_score / 100))));
    }

    public function addStrength(string $strength): void
    {
        $strengths = $this->strengths ?? [];
        
        if (!in_array($strength, $strengths)) {
            $strengths[] = $strength;
            $this->update(['strengths' => $strengths]);
        }
    }

    public function addWeakness(string $weakness): void
    {
        $weaknesses = $this->weaknesses ?? [];
        
        if (!in_array($weakness, $weaknesses)) {
            $weaknesses[] = $weakness;
            $this->update(['weaknesses' => $weaknesses]);
        }
    }

    public function addImprovementArea(string $area): void
    {
        $areas = $this->improvement_areas ?? [];
        
        if (!in_array($area, $areas)) {
            $areas[] = $area;
            $this->update(['improvement_areas' => $areas]);
        }
    }

    public function setCustomMetric(string $key, $value): void
    {
        $customMetrics = $this->custom_metrics ?? [];
        $customMetrics[$key] = $value;
        $this->update(['custom_metrics' => $customMetrics]);
    }

    public function getCustomMetric(string $key, $default = null)
    {
        $customMetrics = $this->custom_metrics ?? [];
        return $customMetrics[$key] ?? $default;
    }

    public function review($reviewedBy, $notes = null): void
    {
        $this->update([
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
            'notes' => $notes,
        ]);
    }

    public static function generatePerformanceData($agentId, $period, $startDate, $endDate): self
    {
        $agent = Agent::find($agentId);
        
        if (!$agent) {
            throw new \Exception('Agent not found');
        }

        // Calculate performance metrics based on actual data
        $totalLeads = $agent->leads()->whereBetween('created_at', [$startDate, $endDate])->count();
        $newLeads = $agent->leads()->where('status', 'new')->whereBetween('created_at', [$startDate, $endDate])->count();
        $convertedLeads = $agent->leads()->where('status', 'converted')->whereBetween('created_at', [$startDate, $endDate])->count();
        
        $totalAppointments = $agent->appointments()->whereBetween('appointment_date', [$startDate, $endDate])->count();
        $completedAppointments = $agent->appointments()->where('status', 'completed')->whereBetween('appointment_date', [$startDate, $endDate])->count();
        
        $soldProperties = $agent->properties()->where('status', 'sold')->whereBetween('updated_at', [$startDate, $endDate])->count();
        $totalSalesValue = $agent->properties()->where('status', 'sold')->whereBetween('updated_at', [$startDate, $endDate])->sum('price');
        
        $totalCommissions = $agent->commissions()->whereBetween('commission_date', [$startDate, $endDate])->sum('amount');
        
        $averageRating = $agent->reviews()->whereBetween('created_at', [$startDate, $endDate])->avg('rating') ?? 0;
        $totalReviews = $agent->reviews()->whereBetween('created_at', [$startDate, $endDate])->count();

        return self::create([
            'agent_id' => $agentId,
            'period' => $period,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'total_leads' => $totalLeads,
            'new_leads' => $newLeads,
            'converted_leads' => $convertedLeads,
            'total_appointments' => $totalAppointments,
            'completed_appointments' => $completedAppointments,
            'sold_properties' => $soldProperties,
            'total_sales_value' => $totalSalesValue,
            'total_commissions' => $totalCommissions,
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews,
            'conversion_rate' => $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0,
            'appointment_show_rate' => $totalAppointments > 0 ? ($completedAppointments / $totalAppointments) * 100 : 0,
            'customer_satisfaction_score' => $averageRating,
        ]);
    }
}

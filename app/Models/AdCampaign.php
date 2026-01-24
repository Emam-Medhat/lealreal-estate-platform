<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class AdCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'objective',
        'status',
        'start_date',
        'end_date',
        'launched_at',
        'paused_at',
        'completed_at',
        'total_budget',
        'daily_budget',
        'target_audience_size',
        'estimated_reach',
        'actual_reach',
        'total_impressions',
        'total_clicks',
        'total_conversions',
        'total_spent',
        'average_ctr',
        'average_cpc',
        'average_cpa',
        'conversion_rate',
        'roi'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'launched_at' => 'datetime',
        'paused_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_budget' => 'decimal:2',
        'daily_budget' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'average_ctr' => 'decimal:2',
        'average_cpc' => 'decimal:2',
        'average_cpa' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'roi' => 'decimal:2'
    ];

    protected $appends = [
        'is_active',
        'days_remaining',
        'budget_utilization',
        'performance_score'
    ];

    // Campaign Objectives
    const OBJECTIVES = [
        'awareness' => 'زيادة الوعي',
        'traffic' => 'زيادة الزيارات',
        'conversions' => 'زيادة التحويلات',
        'engagement' => 'زيادة التفاعل'
    ];

    // Campaign Statuses
    const STATUSES = [
        'draft' => 'مسودة',
        'active' => 'نشط',
        'paused' => 'موقف',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ads()
    {
        return $this->hasMany(Advertisement::class);
    }

    public function budget()
    {
        return $this->hasOne(AdBudget::class);
    }

    public function targeting()
    {
        return $this->hasOne(AdTargeting::class);
    }

    public function placements()
    {
        return $this->hasManyThrough(
            AdPlacement::class,
            Advertisement::class,
            'campaign_id',
            'id',
            'id',
            'id'
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByObjective($query, $objective)
    {
        return $query->where('objective', $objective);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' 
               && $this->start_date <= now()
               && $this->end_date >= now();
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->end_date) {
            return max(0, $this->end_date->diffInDays(now()));
        }
        return 0;
    }

    public function getBudgetUtilizationAttribute()
    {
        if ($this->total_budget > 0) {
            return ($this->total_spent / $this->total_budget) * 100;
        }
        return 0;
    }

    public function getPerformanceScoreAttribute()
    {
        $score = 0;
        
        // CTR contribution (30%)
        if ($this->average_ctr > 2) $score += 30;
        elseif ($this->average_ctr > 1) $score += 20;
        elseif ($this->average_ctr > 0.5) $score += 10;
        
        // Conversion rate contribution (30%)
        if ($this->conversion_rate > 5) $score += 30;
        elseif ($this->conversion_rate > 3) $score += 20;
        elseif ($this->conversion_rate > 1) $score += 10;
        
        // ROI contribution (40%)
        if ($this->roi > 200) $score += 40;
        elseif ($this->roi > 100) $score += 30;
        elseif ($this->roi > 50) $score += 20;
        elseif ($this->roi > 0) $score += 10;
        
        return $score;
    }

    // Methods
    public function launch()
    {
        if ($this->ads->count() === 0) {
            throw new \Exception('لا يمكن إطلاق حملة بدون إعلانات');
        }

        $this->update([
            'status' => 'active',
            'launched_at' => now()
        ]);

        // Activate approved ads
        $this->ads()->where('approval_status', 'approved')->update(['status' => 'active']);
    }

    public function pause()
    {
        $this->update([
            'status' => 'paused',
            'paused_at' => now()
        ]);

        // Pause all ads
        $this->ads()->update(['status' => 'paused']);
    }

    public function resume()
    {
        $this->update(['status' => 'active']);

        // Resume approved ads
        $this->ads()->where('approval_status', 'approved')->update(['status' => 'active']);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Deactivate all ads
        $this->ads()->update(['status' => 'inactive']);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason
        ]);

        // Deactivate all ads
        $this->ads()->update(['status' => 'inactive']);
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'paused']);
    }

    public function canBeLaunched()
    {
        return $this->status === 'draft' && $this->ads->count() > 0;
    }

    public function canBePaused()
    {
        return $this->status === 'active';
    }

    public function canBeResumed()
    {
        return $this->status === 'paused';
    }

    public function updatePerformance()
    {
        $ads = $this->ads;
        
        $totalImpressions = $ads->sum('impressions_count');
        $totalClicks = $ads->sum('clicks_count');
        $totalConversions = $ads->sum('conversions_count');
        $totalSpent = $ads->sum('total_spent');

        $this->update([
            'total_impressions' => $totalImpressions,
            'total_clicks' => $totalClicks,
            'total_conversions' => $totalConversions,
            'total_spent' => $totalSpent,
            'average_ctr' => $totalImpressions > 0 ? ($totalClicks / $totalImpressions) * 100 : 0,
            'average_cpc' => $totalClicks > 0 ? $totalSpent / $totalClicks : 0,
            'average_cpa' => $totalConversions > 0 ? $totalSpent / $totalConversions : 0,
            'conversion_rate' => $totalClicks > 0 ? ($totalConversions / $totalClicks) * 100 : 0,
            'actual_reach' => $this->calculateActualReach()
        ]);
    }

    public function calculateActualReach()
    {
        // This would typically be calculated from unique user views
        // For now, we'll estimate based on impressions
        return $this->total_impressions * 0.7; // Assume 70% are unique views
    }

    public function getPerformanceMetrics()
    {
        return [
            'total_impressions' => $this->total_impressions,
            'total_clicks' => $this->total_clicks,
            'total_conversions' => $this->total_conversions,
            'total_spent' => $this->total_spent,
            'average_ctr' => $this->average_ctr,
            'average_cpc' => $this->average_cpc,
            'average_cpa' => $this->average_cpa,
            'conversion_rate' => $this->conversion_rate,
            'roi' => $this->roi,
            'estimated_reach' => $this->estimated_reach,
            'actual_reach' => $this->actual_reach,
            'reach_rate' => $this->estimated_reach > 0 ? ($this->actual_reach / $this->estimated_reach) * 100 : 0,
            'performance_score' => $this->performance_score
        ];
    }

    public function getDailyPerformance($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return $this->ads()
            ->join('ad_impressions', 'advertisements.id', '=', 'ad_impressions.advertisement_id')
            ->selectRaw('DATE(ad_impressions.viewed_at) as date, COUNT(*) as impressions')
            ->where('ad_impressions.viewed_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getTopPerformingAds($limit = 5)
    {
        return $this->ads()
            ->orderBy('impressions_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getBudgetStatus()
    {
        if (!$this->budget) {
            return [
                'status' => 'no_budget',
                'remaining' => 0,
                'utilization' => 0,
                'daily_remaining' => 0
            ];
        }

        $budget = $this->budget;
        $utilization = $budget->total_budget > 0 ? ($budget->spent_amount / $budget->total_budget) * 100 : 0;
        
        return [
            'status' => $budget->status,
            'total_budget' => $budget->total_budget,
            'spent_amount' => $budget->spent_amount,
            'remaining' => $budget->remaining_budget,
            'utilization' => $utilization,
            'daily_budget' => $budget->daily_budget,
            'daily_spent' => $budget->daily_spent,
            'daily_remaining' => $budget->daily_remaining
        ];
    }

    public function getObjectiveLabel()
    {
        return self::OBJECTIVES[$this->objective] ?? $this->objective;
    }

    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getProgressPercentage()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $totalDays = $this->start_date->diffInDays($this->end_date);
        $elapsedDays = $this->start_date->diffInDays(now());
        
        return min(100, max(0, ($elapsedDays / $totalDays) * 100));
    }

    public function isOverBudget()
    {
        if ($this->budget) {
            return $this->budget->spent_amount >= $this->budget->total_budget;
        }
        return false;
    }

    public function isTimeExceeded()
    {
        return $this->end_date && $this->end_date < now();
    }

    public function shouldAutoComplete()
    {
        return $this->isOverBudget() || $this->isTimeExceeded();
    }
}

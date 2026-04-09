<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class AdBudget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'total_budget',
        'daily_budget',
        'remaining_budget',
        'daily_remaining',
        'spent_amount',
        'daily_spent',
        'budget_type',
        'delivery_method',
        'auto_renew',
        'renewal_amount',
        'renewal_trigger',
        'spending_limit',
        'limit_type',
        'alert_threshold',
        'status',
        'paused_at',
        'exhausted_at',
        'renewed_at',
        'last_reset_at'
    ];

    protected $casts = [
        'total_budget' => 'decimal:2',
        'daily_budget' => 'decimal:2',
        'remaining_budget' => 'decimal:2',
        'daily_remaining' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'daily_spent' => 'decimal:2',
        'auto_renew' => 'boolean',
        'renewal_amount' => 'decimal:2',
        'spending_limit' => 'decimal:2',
        'alert_threshold' => 'decimal:2',
        'paused_at' => 'datetime',
        'exhausted_at' => 'datetime',
        'renewed_at' => 'datetime',
        'last_reset_at' => 'datetime'
    ];

    protected $appends = [
        'utilization_percentage',
        'daily_utilization_percentage',
        'remaining_percentage',
        'days_until_exhaustion',
        'is_exhausted',
        'is_near_limit',
        'needs_alert'
    ];

    // Budget Types
    const BUDGET_TYPES = [
        'standard' => 'قياسي',
        'accelerated' => 'معجل',
        'limited' => 'محدود'
    ];

    // Delivery Methods
    const DELIVERY_METHODS = [
        'standard' => 'قياسي',
        'accelerated' => 'معجل'
    ];

    // Limit Types
    const LIMIT_TYPES = [
        'daily' => 'يومي',
        'weekly' => 'أسبوعي',
        'monthly' => 'شهري',
        'total' => 'إجمالي'
    ];

    // Renewal Triggers
    const RENEWAL_TRIGGERS = [
        'exhausted' => 'عند النفاد',
        'below_threshold' => 'أقل من الحد'
    ];

    // Statuses
    const STATUSES = [
        'active' => 'نشط',
        'paused' => 'موقف',
        'exhausted' => 'منتهي',
        'cancelled' => 'ملغي'
    ];

    // Relationships
    public function campaign()
    {
        return $this->belongsTo(AdCampaign::class);
    }

    public function transactions()
    {
        return $this->hasMany(BudgetTransaction::class);
    }

    public function adjustments()
    {
        return $this->hasMany(BudgetAdjustment::class);
    }

    public function spendingLogs()
    {
        return $this->hasMany(BudgetSpendingLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExhausted($query)
    {
        return $query->where('status', 'exhausted');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeNearLimit($query)
    {
        return $query->whereRaw('(remaining_budget / total_budget) * 100 <= 20');
    }

    // Accessors
    public function getUtilizationPercentageAttribute()
    {
        if ($this->total_budget > 0) {
            return ($this->spent_amount / $this->total_budget) * 100;
        }
        return 0;
    }

    public function getDailyUtilizationPercentageAttribute()
    {
        if ($this->daily_budget > 0) {
            return ($this->daily_spent / $this->daily_budget) * 100;
        }
        return 0;
    }

    public function getRemainingPercentageAttribute()
    {
        if ($this->total_budget > 0) {
            return ($this->remaining_budget / $this->total_budget) * 100;
        }
        return 0;
    }

    public function getDaysUntilExhaustionAttribute()
    {
        if ($this->daily_spent > 0) {
            return floor($this->remaining_budget / $this->daily_spent);
        }
        return null;
    }

    public function getIsExhaustedAttribute()
    {
        return $this->remaining_budget <= 0 || $this->status === 'exhausted';
    }

    public function getIsNearLimitAttribute()
    {
        return $this->utilization_percentage >= 80;
    }

    public function getNeedsAlertAttribute()
    {
        if ($this->alert_threshold > 0) {
            return $this->utilization_percentage >= $this->alert_threshold;
        }
        return false;
    }

    // Methods
    public function spend($amount, $adId = null, $reason = null)
    {
        if ($this->remaining_budget < $amount) {
            throw new \Exception('الميزانية غير كافية');
        }

        if ($this->daily_remaining < $amount) {
            throw new \Exception('الميزانية اليومية غير كافية');
        }

        // Update budget
        $this->decrement('remaining_budget', $amount);
        $this->decrement('daily_remaining', $amount);
        $this->increment('spent_amount', $amount);
        $this->increment('daily_spent', $amount);

        // Log spending
        $this->spendingLogs()->create([
            'amount' => $amount,
            'advertisement_id' => $adId,
            'reason' => $reason,
            'balance_before' => $this->remaining_budget + $amount,
            'balance_after' => $this->remaining_budget
        ]);

        // Check if budget is exhausted
        if ($this->remaining_budget <= 0) {
            $this->exhaust();
        }

        // Check if daily budget is exhausted
        if ($this->daily_remaining <= 0) {
            $this->exhaustDaily();
        }

        return true;
    }

    public function addFunds($amount, $paymentMethod = null, $transactionId = null)
    {
        $this->increment('total_budget', $amount);
        $this->increment('remaining_budget', $amount);

        // Create transaction
        $this->transactions()->create([
            'type' => 'add_funds',
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
            'balance_before' => $this->remaining_budget - $amount,
            'balance_after' => $this->remaining_budget
        ]);

        return true;
    }

    public function pause()
    {
        $this->update([
            'status' => 'paused',
            'paused_at' => now()
        ]);

        // Pause campaign
        if ($this->campaign) {
            $this->campaign->pause();
        }
    }

    public function resume()
    {
        $this->update(['status' => 'active']);

        // Resume campaign
        if ($this->campaign) {
            $this->campaign->resume();
        }
    }

    public function exhaust()
    {
        $this->update([
            'status' => 'exhausted',
            'exhausted_at' => now()
        ]);

        // Handle auto-renewal
        if ($this->auto_renew) {
            $this->autoRenew();
        } else {
            // Complete campaign
            if ($this->campaign) {
                $this->campaign->complete();
            }
        }
    }

    public function exhaustDaily()
    {
        // Daily budget will be reset at midnight
        // Campaign might continue with remaining total budget
    }

    public function resetDaily()
    {
        $this->update([
            'daily_spent' => 0,
            'daily_remaining' => $this->daily_budget,
            'last_reset_at' => now()
        ]);
    }

    public function autoRenew()
    {
        if (!$this->auto_renew || !$this->renewal_amount) {
            return false;
        }

        // Add renewal funds
        $this->addFunds($this->renewal_amount, 'auto_renewal');

        // Update renewal date
        $this->update([
            'renewed_at' => now(),
            'status' => 'active'
        ]);

        // Resume campaign
        if ($this->campaign) {
            $this->campaign->resume();
        }

        return true;
    }

    public function adjustDailyBudget($newBudget, $effectiveDate = null)
    {
        $oldBudget = $this->daily_budget;

        $this->update([
            'daily_budget' => $newBudget,
            'daily_remaining' => $newBudget - $this->daily_spent
        ]);

        // Create adjustment record
        $this->adjustments()->create([
            'adjustment_type' => 'daily_budget',
            'old_value' => $oldBudget,
            'new_value' => $newBudget,
            'effective_date' => $effectiveDate ?? now()
        ]);

        return true;
    }

    public function setSpendingLimit($limit, $limitType)
    {
        $this->update([
            'spending_limit' => $limit,
            'limit_type' => $limitType
        ]);

        return true;
    }

    public function checkSpendingLimit($currentSpend)
    {
        if (!$this->spending_limit) {
            return true;
        }

        switch ($this->limit_type) {
            case 'daily':
                return $this->daily_spent < $this->spending_limit;
            case 'weekly':
                $weeklySpend = $this->getWeeklySpending();
                return $weeklySpend < $this->spending_limit;
            case 'monthly':
                $monthlySpend = $this->getMonthlySpending();
                return $monthlySpend < $this->spending_limit;
            case 'total':
                return $this->spent_amount < $this->spending_limit;
            default:
                return true;
        }
    }

    public function getWeeklySpending()
    {
        return $this->spendingLogs()
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->sum('amount');
    }

    public function getMonthlySpending()
    {
        return $this->spendingLogs()
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->sum('amount');
    }

    public function getSpendingTrends($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return $this->spendingLogs()
            ->selectRaw('DATE(created_at) as date, SUM(amount) as spent')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getBudgetForecast($days = 30)
    {
        $dailyAverage = $this->daily_spent;
        $projectedSpending = $dailyAverage * $days;
        
        return [
            'daily_average' => $dailyAverage,
            'projected_spending' => $projectedSpending,
            'projected_remaining' => max(0, $this->remaining_budget - $projectedSpending),
            'days_until_exhaustion' => $this->days_until_exhaustion,
            'exhaustion_date' => $this->days_until_exhaustion ? 
                Carbon::now()->addDays($this->days_until_exhaustion) : null
        ];
    }

    public function getOptimizationSuggestions()
    {
        $suggestions = [];

        // Budget utilization
        if ($this->utilization_percentage < 50) {
            $suggestions[] = [
                'type' => 'increase_budget',
                'priority' => 'medium',
                'message' => 'استخدام الميزانية منخفض، يمكن زيادة الميزانية اليومية',
                'action' => 'increase_daily_budget'
            ];
        }

        // Daily budget exhaustion
        if ($this->daily_utilization_percentage > 90) {
            $suggestions[] = [
                'type' => 'adjust_daily_budget',
                'priority' => 'high',
                'message' => 'الميزانية اليومية تنفد بسرعة',
                'action' => 'increase_daily_budget'
            ];
        }

        // Spending limit
        if ($this->spending_limit && $this->checkSpendingLimit($this->spent_amount) === false) {
            $suggestions[] = [
                'type' => 'adjust_spending_limit',
                'priority' => 'high',
                'message' => 'تم الوصول إلى حد الإنفاق',
                'action' => 'increase_spending_limit'
            ];
        }

        // Auto-renewal
        if ($this->is_exhausted && !$this->auto_renew) {
            $suggestions[] = [
                'type' => 'enable_auto_renewal',
                'priority' => 'medium',
                'message' => 'الميزانية نفدت، يمكن تفعيل التجديد التلقائي',
                'action' => 'enable_auto_renew'
            ];
        }

        return $suggestions;
    }

    public function getBudgetSummary()
    {
        return [
            'total_budget' => $this->total_budget,
            'spent_amount' => $this->spent_amount,
            'remaining_budget' => $this->remaining_budget,
            'daily_budget' => $this->daily_budget,
            'daily_spent' => $this->daily_spent,
            'daily_remaining' => $this->daily_remaining,
            'utilization_percentage' => $this->utilization_percentage,
            'daily_utilization_percentage' => $this->daily_utilization_percentage,
            'remaining_percentage' => $this->remaining_percentage,
            'status' => $this->status,
            'is_exhausted' => $this->is_exhausted,
            'is_near_limit' => $this->is_near_limit,
            'days_until_exhaustion' => $this->days_until_exhaustion,
            'auto_renew' => $this->auto_renew,
            'spending_limit' => $this->spending_limit,
            'forecast' => $this->getBudgetForecast()
        ];
    }

    public function getTypeLabel()
    {
        return self::BUDGET_TYPES[$this->budget_type] ?? $this->budget_type;
    }

    public function getDeliveryMethodLabel()
    {
        return self::DELIVERY_METHODS[$this->delivery_method] ?? $this->delivery_method;
    }

    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}

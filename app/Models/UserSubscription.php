<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'plan_name',
        'plan_type',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'renewal_at',
        'price',
        'currency',
        'billing_cycle',
        'auto_renew',
        'payment_method',
        'payment_method_id',
        'last_payment_at',
        'next_payment_at',
        'trial_ends_at',
        'grace_period_ends_at',
        'subscription_data',
        'features',
        'usage_limits',
        'current_usage',
        'metadata',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'renewal_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'next_payment_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'grace_period_ends_at' => 'datetime',
        'price' => 'decimal:2',
        'auto_renew' => 'boolean',
        'subscription_data' => 'json',
        'features' => 'json',
        'usage_limits' => 'json',
        'current_usage' => 'json',
        'metadata' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'trial' => 'blue',
            'expired' => 'red',
            'cancelled' => 'gray',
            'suspended' => 'yellow',
            'pending' => 'orange',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => __('Active'),
            'trial' => __('Trial'),
            'expired' => __('Expired'),
            'cancelled' => __('Cancelled'),
            'suspended' => __('Suspended'),
            'pending' => __('Pending'),
            default => __('Unknown')
        };
    }

    public function getPlanTypeLabelAttribute(): string
    {
        return match($this->plan_type) {
            'basic' => __('Basic'),
            'premium' => __('Premium'),
            'pro' => __('Professional'),
            'enterprise' => __('Enterprise'),
            'custom' => __('Custom'),
            default => __('Unknown')
        };
    }

    public function getBillingCycleLabelAttribute(): string
    {
        return match($this->billing_cycle) {
            'monthly' => __('Monthly'),
            'quarterly' => __('Quarterly'),
            'yearly' => __('Yearly'),
            'lifetime' => __('Lifetime'),
            default => __('Unknown')
        };
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at && $this->ends_at->isFuture();
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->ends_at && $this->ends_at->isPast());
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled' || $this->cancelled_at !== null;
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInGracePeriod(): bool
    {
        return $this->grace_period_ends_at && $this->grace_period_ends_at->isFuture();
    }

    public function canBeRenewed(): bool
    {
        return $this->auto_renew && !$this->isCancelled() && ($this->isActive() || $this->isInGracePeriod());
    }

    public function canBeCancelled(): bool
    {
        return $this->isActive() || $this->isTrial();
    }

    public function canBeUpgraded(): bool
    {
        return $this->isActive() && !$this->isCancelled();
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->ends_at) {
            return 0;
        }
        
        return max(0, $this->ends_at->diffInDays(now()));
    }

    public function getTrialDaysRemainingAttribute(): int
    {
        if (!$this->trial_ends_at) {
            return 0;
        }
        
        return max(0, $this->trial_ends_at->diffInDays(now()));
    }

    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        return in_array($feature, $features);
    }

    public function getUsageLimit(string $limit): mixed
    {
        $usageLimits = $this->usage_limits ?? [];
        return $usageLimits[$limit] ?? null;
    }

    public function getCurrentUsage(string $metric): mixed
    {
        $currentUsage = $this->current_usage ?? [];
        return $currentUsage[$metric] ?? 0;
    }

    public function updateUsage(string $metric, int $amount): void
    {
        $currentUsage = $this->current_usage ?? [];
        $currentUsage[$metric] = ($currentUsage[$metric] ?? 0) + $amount;
        $this->update(['current_usage' => $currentUsage]);
    }

    public function isWithinLimit(string $limit): bool
    {
        $usageLimit = $this->getUsageLimit($limit);
        $currentUsage = $this->getCurrentUsage($limit);
        
        if ($usageLimit === null) {
            return true; // No limit set
        }
        
        return $currentUsage < $usageLimit;
    }

    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => $this->calculateEndDate(),
            'next_payment_at' => $this->calculateNextPaymentDate(),
        ]);
    }

    public function startTrial(int $trialDays = 14): void
    {
        $this->update([
            'status' => 'trial',
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays($trialDays),
            'ends_at' => now()->addDays($trialDays),
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
        ]);
    }

    public function suspend(): void
    {
        $this->update([
            'status' => 'suspended',
        ]);
    }

    public function reactivate(): void
    {
        $this->update([
            'status' => 'active',
            'cancelled_at' => null,
            'auto_renew' => true,
        ]);
    }

    public function renew(): void
    {
        $this->update([
            'status' => 'active',
            'starts_at' => $this->ends_at,
            'ends_at' => $this->calculateEndDate($this->ends_at),
            'last_payment_at' => now(),
            'next_payment_at' => $this->calculateNextPaymentDate(),
            'cancelled_at' => null,
        ]);
    }

    public function upgrade(string $newPlanType, float $newPrice): void
    {
        $this->update([
            'plan_type' => $newPlanType,
            'price' => $newPrice,
            'ends_at' => $this->calculateEndDate(),
            'next_payment_at' => $this->calculateNextPaymentDate(),
        ]);
    }

    private function calculateEndDate(?\DateTime $startDate = null): \DateTime
    {
        $startDate = $startDate ?? now();
        
        return match($this->billing_cycle) {
            'monthly' => $startDate->addMonth(),
            'quarterly' => $startDate->addMonths(3),
            'yearly' => $startDate->addYear(),
            'lifetime' => $startDate->addYears(100),
            default => $startDate->addMonth()
        };
    }

    private function calculateNextPaymentDate(): \DateTime
    {
        return $this->ends_at;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('ends_at', '>', now());
    }

    public function scopeTrial($query)
    {
        return $query->where('status', 'trial')
                    ->where('trial_ends_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere('ends_at', '<', now());
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled')
                    ->orWhereNotNull('cancelled_at');
    }

    public function scopeByPlan($query, string $planType)
    {
        return $query->where('plan_type', $planType);
    }

    public function scopeByBillingCycle($query, string $billingCycle)
    {
        return $query->where('billing_cycle', $billingCycle);
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('ends_at', '<=', now()->addDays($days))
                    ->where('ends_at', '>', now())
                    ->where('auto_renew', false);
    }
}

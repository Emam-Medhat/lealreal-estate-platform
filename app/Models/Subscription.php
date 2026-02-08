<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\SubscriptionPlan;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'enterprise_id',
        'plan_id',
        'plan',
        'status',
        'starts_at',
        'ends_at',
        'next_billing_at',
        'amount',
        'price', // For compatibility with EnterpriseService
        'currency',
        'billing_cycle',
        'features',
        'auto_renew',
        'payment_method',
        'payment_status',
        'activated_at',
        'cancelled_at',
        'upgraded_at',
        'last_renewed_at',
        'proration_amount',
        'proration_reason',
        'notes'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'next_billing_at' => 'datetime',
        'activated_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'upgraded_at' => 'datetime',
        'last_renewed_at' => 'datetime',
        'amount' => 'decimal:2',
        'price' => 'decimal:2',
        'proration_amount' => 'decimal:2',
        'auto_renew' => 'boolean',
        'features' => 'json'
    ];

    public function enterprise()
    {
        return $this->belongsTo(EnterpriseAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    public function usage()
    {
        return $this->hasMany(SubscriptionUsage::class);
    }

    public function renewals()
    {
        return $this->hasMany(SubscriptionRenewal::class);
    }

    public function upgrades()
    {
        return $this->hasMany(SubscriptionUpgrade::class);
    }

    public function cancellation()
    {
        return $this->hasOne(SubscriptionCancellation::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->ends_at > now();
    }

    public function isExpired()
    {
        return $this->ends_at < now();
    }

    public function needsRenewal()
    {
        return $this->auto_renew && $this->ends_at <= now()->addDays(7);
    }

    public function isRefundEligible()
    {
        $daysUsed = $this->created_at->diffInDays(now());
        return $daysUsed <= 30; // 30-day refund policy
    }

    public function calculateRefundAmount()
    {
        if (!$this->isRefundEligible()) {
            return 0;
        }

        $totalDays = $this->created_at->diffInDays($this->ends_at);
        $daysUsed = $this->created_at->diffInDays(now());
        $remainingDays = max(0, $totalDays - $daysUsed);
        
        $dailyRate = $totalDays > 0 ? $this->amount / $totalDays : 0;
        return $dailyRate * $remainingDays * 0.8; // 80% refund
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('ends_at', '<', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

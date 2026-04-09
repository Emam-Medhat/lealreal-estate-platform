<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionUpgrade extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'old_plan_id',
        'new_plan_id',
        'old_price',
        'new_price',
        'proration_amount',
        'upgrade_type',
        'status',
        'effective_date',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'reason',
        'payment_method'
    ];

    protected $casts = [
        'effective_date' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'proration_amount' => 'decimal:2'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function oldPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'old_plan_id');
    }

    public function newPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'new_plan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}

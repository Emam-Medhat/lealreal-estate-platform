<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionRenewal extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'plan_id',
        'old_ends_at',
        'new_ends_at',
        'amount',
        'currency',
        'discount_amount',
        'final_amount',
        'renewal_type',
        'auto_renewal',
        'status',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'old_ends_at' => 'datetime',
        'new_ends_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'auto_renewal' => 'boolean'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
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

    public function scopeAuto($query)
    {
        return $query->where('renewal_type', 'auto');
    }

    public function scopeManual($query)
    {
        return $query->where('renewal_type', 'manual');
    }
}

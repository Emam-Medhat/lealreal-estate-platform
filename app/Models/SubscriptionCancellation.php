<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionCancellation extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'reason',
        'cancellation_type',
        'effective_date',
        'refund_eligible',
        'refund_amount',
        'refund_method',
        'refund_status',
        'refund_processed_at',
        'refund_transaction_id',
        'feedback',
        'would_recommend',
        'alternative_solution',
        'status',
        'reactivated_at'
    ];

    protected $casts = [
        'effective_date' => 'datetime',
        'refund_processed_at' => 'datetime',
        'reactivated_at' => 'datetime',
        'refund_eligible' => 'boolean',
        'refund_amount' => 'decimal:2',
        'would_recommend' => 'boolean'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeProcessed($query)
    {
        return $query->where('refund_status', 'processed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeReactivated($query)
    {
        return $query->where('status', 'reactivated');
    }
}

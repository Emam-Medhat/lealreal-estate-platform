<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionUsage extends Model
{
    protected $fillable = [
        'subscription_id',
        'user_id',
        'feature_id',
        'usage_amount',
        'usage_unit',
        'tracked_at',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'tracked_at' => 'datetime',
        'usage_amount' => 'decimal:2',
        'metadata' => 'array'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feature()
    {
        return $this->belongsTo(SubscriptionFeature::class);
    }

    public function scopeForFeature($query, $featureId)
    {
        return $query->where('feature_id', $featureId);
    }

    public function scopeForPeriod($query, $fromDate, $toDate)
    {
        return $query->whereBetween('tracked_at', [$fromDate, $toDate]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tracked_at', now()->month)
            ->whereYear('tracked_at', now()->year);
    }
}

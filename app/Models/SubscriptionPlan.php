<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'description',
        'tier_id',
        'price',
        'currency',
        'billing_cycle',
        'billing_cycle_unit',
        'trial_days',
        'setup_fee',
        'max_users',
        'storage_limit',
        'bandwidth_limit',
        'api_calls_limit',
        'is_popular',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'max_users' => 'integer',
        'storage_limit' => 'integer',
        'bandwidth_limit' => 'integer',
        'api_calls_limit' => 'integer',
        'trial_days' => 'integer',
        'billing_cycle' => 'integer',
        'sort_order' => 'integer'
    ];

    public function tier()
    {
        return $this->belongsTo(SubscriptionTier::class);
    }

    public function features()
    {
        return $this->belongsToMany(SubscriptionFeature::class, 'subscription_plan_features')
            ->withPivot(['limit', 'included'])
            ->withTimestamps();
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(SubscriptionInvoice::class, Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeOrderByPrice($query, $direction = 'asc')
    {
        return $query->orderBy('price', $direction);
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function getFormattedSetupFeeAttribute()
    {
        return number_format($this->setup_fee, 2) . ' ' . $this->currency;
    }
}

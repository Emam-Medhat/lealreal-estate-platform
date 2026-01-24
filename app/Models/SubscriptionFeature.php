<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionFeature extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'icon',
        'type',
        'unit',
        'default_value',
        'is_included_in_free',
        'is_required',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'default_value' => 'decimal:2',
        'is_included_in_free' => 'boolean',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function plans()
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'subscription_plan_features')
            ->withPivot(['limit', 'included'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrderBySort($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

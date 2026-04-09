<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionTier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'level',
        'color',
        'is_active'
    ];

    protected $casts = [
        'level' => 'integer',
        'is_active' => 'boolean'
    ];

    public function plans()
    {
        return $this->hasMany(SubscriptionPlan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderByLevel($query, $direction = 'asc')
    {
        return $query->orderBy('level', $direction);
    }
}

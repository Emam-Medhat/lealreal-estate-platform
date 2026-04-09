<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'bedrooms',
        'bathrooms',
        'area',
        'area_unit',
        'year_built',
        'parking_spaces',
        'floors',
        'condition',
        'interior_features',
        'exterior_features',
        'materials',
        'specifications',
    ];

    protected $casts = [
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'area' => 'decimal:2',
        'year_built' => 'integer',
        'parking_spaces' => 'integer',
        'floors' => 'integer',
        'interior_features' => 'array',
        'exterior_features' => 'array',
        'materials' => 'array',
        'specifications' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getAreaWithUnitAttribute(): string
    {
        $units = [
            '1' => 'متر مربع',
            '2' => 'قدم مربع',
            '3' => 'ياردة مربعة',
            '4' => 'فدان',
            '5' => 'هكتار',
        ];
        
        $unit = $units[$this->area_unit] ?? 'متر مربع';
        return $this->area . ' ' . $unit;
    }

    public function getConditionLabelAttribute(): string
    {
        $conditions = [
            'new' => 'جديد',
            'excellent' => 'ممتاز',
            'good' => 'جيد جداً',
            'fair' => 'جيد',
            'poor' => 'متوسط',
            'needs_renovation' => 'يحتاج تجديد',
        ];
        
        return $conditions[$this->condition] ?? $this->condition;
    }

    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    public function scopeByBedrooms($query, $bedrooms)
    {
        return $query->where('bedrooms', '>=', $bedrooms);
    }

    public function scopeByBathrooms($query, $bathrooms)
    {
        return $query->where('bathrooms', '>=', $bathrooms);
    }

    public function scopeByArea($query, $minArea, $maxArea = null)
    {
        $query->where('area', '>=', $minArea);
        if ($maxArea) {
            $query->where('area', '<=', $maxArea);
        }
        return $query;
    }
}

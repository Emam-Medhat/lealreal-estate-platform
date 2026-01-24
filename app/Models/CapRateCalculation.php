<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class CapRateCalculation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_financial_analysis_id',
        'calculation_date',
        'net_operating_income',
        'property_value',
        'capitalization_rate',
        'market_cap_rate',
        'comparable_properties_count',
        'market_trend',
        'location_factor',
        'property_condition_factor',
        'age_factor',
        'size_factor',
        'amenities_factor',
        'risk_adjusted_cap_rate',
        'projected_cap_rate',
        'historical_cap_rates',
        'market_segment',
        'property_class',
        'neighborhood_quality',
        'rent_growth_rate',
        'expense_growth_rate',
        'vacancy_trend',
        'calculation_method',
        'data_sources',
        'confidence_level',
        'notes'
    ];

    protected $casts = [
        'calculation_date' => 'date',
        'net_operating_income' => 'decimal:2',
        'property_value' => 'decimal:2',
        'capitalization_rate' => 'decimal:4',
        'market_cap_rate' => 'decimal:4',
        'comparable_properties_count' => 'integer',
        'market_trend' => 'string',
        'location_factor' => 'decimal:3',
        'property_condition_factor' => 'decimal:3',
        'age_factor' => 'decimal:3',
        'size_factor' => 'decimal:3',
        'amenities_factor' => 'decimal:3',
        'risk_adjusted_cap_rate' => 'decimal:4',
        'projected_cap_rate' => 'decimal:4',
        'historical_cap_rates' => 'json',
        'market_segment' => 'string',
        'property_class' => 'string',
        'neighborhood_quality' => 'string',
        'rent_growth_rate' => 'decimal:3',
        'expense_growth_rate' => 'decimal:3',
        'vacancy_trend' => 'decimal:3',
        'data_sources' => 'json',
        'confidence_level' => 'integer',
        'notes' => 'text'
    ];

    public function propertyFinancialAnalysis(): BelongsTo
    {
        return $this->belongsTo(PropertyFinancialAnalysis::class);
    }

    public function scopeByAnalysis(Builder $query, $analysisId): Builder
    {
        return $query->where('property_financial_analysis_id', $analysisId);
    }

    public function scopeByMarketSegment(Builder $query, $segment): Builder
    {
        return $query->where('market_segment', $segment);
    }

    public function scopeByPropertyClass(Builder $query, $class): Builder
    {
        return $query->where('property_class', $class);
    }

    public function calculateAdjustedCapRate(): float
    {
        $baseRate = $this->capitalization_rate;
        
        $adjustment = 1.0 
            + $this->location_factor 
            + $this->property_condition_factor 
            + $this->age_factor 
            + $this->size_factor 
            + $this->amenities_factor;
        
        return $baseRate * $adjustment;
    }

    public function calculateMarketPremium(): float
    {
        return $this->market_cap_rate > 0 
            ? (($this->capitalization_rate - $this->market_cap_rate) / $this->market_cap_rate) * 100 
            : 0;
    }

    public function assessMarketPosition(): string
    {
        $premium = $this->calculateMarketPremium();
        
        if ($premium > 10) {
            return 'premium';
        } elseif ($premium > -5) {
            return 'market';
        } elseif ($premium > -15) {
            return 'discount';
        } else {
            return 'deep_discount';
        }
    }

    public function calculateProjectedValue(): float
    {
        if ($this->projected_cap_rate <= 0) {
            return $this->property_value;
        }

        return $this->net_operating_income / ($this->projected_cap_rate / 100);
    }

    public function getValueProjection(): array
    {
        return [
            'current_value' => $this->property_value,
            'projected_value' => $this->calculateProjectedValue(),
            'value_change' => $this->calculateProjectedValue() - $this->property_value,
            'value_change_percentage' => $this->property_value > 0 
                ? (($this->calculateProjectedValue() - $this->property_value) / $this->property_value) * 100 
                : 0,
            'current_cap_rate' => $this->capitalization_rate,
            'projected_cap_rate' => $this->projected_cap_rate,
            'market_cap_rate' => $this->market_cap_rate
        ];
    }

    public function getCapRateAnalysis(): array
    {
        return [
            'calculated_cap_rate' => $this->capitalization_rate,
            'market_cap_rate' => $this->market_cap_rate,
            'risk_adjusted_cap_rate' => $this->risk_adjusted_cap_rate,
            'projected_cap_rate' => $this->projected_cap_rate,
            'adjusted_cap_rate' => $this->calculateAdjustedCapRate(),
            'market_premium_percentage' => $this->calculateMarketPremium(),
            'market_position' => $this->assessMarketPosition(),
            'confidence_level' => $this->confidence_level,
            'comparable_properties_count' => $this->comparable_properties_count,
            'market_trend' => $this->market_trend,
            'property_class' => $this->property_class,
            'market_segment' => $this->market_segment
        ];
    }

    public function assessDataQuality(): string
    {
        if ($this->comparable_properties_count >= 10 && $this->confidence_level >= 80) {
            return 'high';
        } elseif ($this->comparable_properties_count >= 5 && $this->confidence_level >= 60) {
            return 'medium';
        } else {
            return 'low';
        }
    }
}

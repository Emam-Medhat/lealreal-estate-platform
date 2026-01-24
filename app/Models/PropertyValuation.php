<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class PropertyValuation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_financial_analysis_id',
        'valuation_date',
        'valuation_method',
        'property_value',
        'comparable_sales_value',
        'income_approach_value',
        'cost_approach_value',
        'residual_method_value',
        'automated_valuation_value',
        'final_valuation',
        'confidence_score',
        'valuation_range_low',
        'valuation_range_high',
        'adjustment_factors',
        'comparable_properties',
        'market_conditions',
        'property_condition',
        'location_quality',
        'economic_factors',
        'zoning_restrictions',
        'development_potential',
        'highest_and_best_use',
        'replacement_cost',
        'depreciation',
        'land_value',
        'improvement_value',
        'capitalization_rate_used',
        'discount_rate_used',
        'terminal_growth_rate',
        'valuation_notes',
        'assumptions',
        'data_sources',
        'valuer_name',
        'valuation_purpose'
    ];

    protected $casts = [
        'valuation_date' => 'date',
        'property_value' => 'decimal:2',
        'comparable_sales_value' => 'decimal:2',
        'income_approach_value' => 'decimal:2',
        'cost_approach_value' => 'decimal:2',
        'residual_method_value' => 'decimal:2',
        'automated_valuation_value' => 'decimal:2',
        'final_valuation' => 'decimal:2',
        'confidence_score' => 'decimal:3',
        'valuation_range_low' => 'decimal:2',
        'valuation_range_high' => 'decimal:2',
        'adjustment_factors' => 'json',
        'comparable_properties' => 'json',
        'market_conditions' => 'json',
        'economic_factors' => 'json',
        'development_potential' => 'decimal:2',
        'replacement_cost' => 'decimal:2',
        'depreciation' => 'decimal:2',
        'land_value' => 'decimal:2',
        'improvement_value' => 'decimal:2',
        'capitalization_rate_used' => 'decimal:4',
        'discount_rate_used' => 'decimal:4',
        'terminal_growth_rate' => 'decimal:4',
        'valuation_notes' => 'text',
        'assumptions' => 'json',
        'data_sources' => 'json'
    ];

    public function propertyFinancialAnalysis(): BelongsTo
    {
        return $this->belongsTo(PropertyFinancialAnalysis::class);
    }

    public function scopeByAnalysis(Builder $query, $analysisId): Builder
    {
        return $query->where('property_financial_analysis_id', $analysisId);
    }

    public function scopeByMethod(Builder $query, $method): Builder
    {
        return $query->where('valuation_method', $method);
    }

    public function scopeByPurpose(Builder $query, $purpose): Builder
    {
        return $query->where('valuation_purpose', $purpose);
    }

    public function calculateValuationSpread(): float
    {
        return $this->valuation_range_high - $this->valuation_range_low;
    }

    public function calculateValuationAccuracy(): float
    {
        $spread = $this->calculateValuationSpread();
        $midpoint = ($this->valuation_range_high + $this->valuation_range_low) / 2;
        
        return $midpoint > 0 ? ($spread / $midpoint) * 100 : 0;
    }

    public function assessValuationReliability(): string
    {
        $accuracy = $this->calculateValuationAccuracy();
        $confidence = $this->confidence_score;
        
        if ($accuracy <= 10 && $confidence >= 0.8) {
            return 'high';
        } elseif ($accuracy <= 20 && $confidence >= 0.6) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function getWeightedValuation(): float
    {
        $weights = [
            'comparable_sales' => 0.4,
            'income_approach' => 0.35,
            'cost_approach' => 0.25
        ];

        $weightedValue = 0;
        $totalWeight = 0;

        if ($this->comparable_sales_value > 0) {
            $weightedValue += $this->comparable_sales_value * $weights['comparable_sales'];
            $totalWeight += $weights['comparable_sales'];
        }

        if ($this->income_approach_value > 0) {
            $weightedValue += $this->income_approach_value * $weights['income_approach'];
            $totalWeight += $weights['income_approach'];
        }

        if ($this->cost_approach_value > 0) {
            $weightedValue += $this->cost_approach_value * $weights['cost_approach'];
            $totalWeight += $weights['cost_approach'];
        }

        return $totalWeight > 0 ? $weightedValue / $totalWeight : $this->final_valuation;
    }

    public function getValuationSummary(): array
    {
        return [
            'final_valuation' => $this->final_valuation,
            'weighted_valuation' => $this->getWeightedValuation(),
            'valuation_range_low' => $this->valuation_range_low,
            'valuation_range_high' => $this->valuation_range_high,
            'valuation_spread' => $this->calculateValuationSpread(),
            'valuation_accuracy' => $this->calculateValuationAccuracy(),
            'confidence_score' => $this->confidence_score,
            'reliability' => $this->assessValuationReliability(),
            'valuation_method' => $this->valuation_method,
            'valuation_purpose' => $this->valuation_purpose,
            'valuation_date' => $this->valuation_date
        ];
    }

    public function getMethodComparison(): array
    {
        return [
            'comparable_sales_value' => $this->comparable_sales_value,
            'income_approach_value' => $this->income_approach_value,
            'cost_approach_value' => $this->cost_approach_value,
            'residual_method_value' => $this->residual_method_value,
            'automated_valuation_value' => $this->automated_valuation_value,
            'final_valuation' => $this->final_valuation,
            'method_differences' => $this->calculateMethodDifferences(),
            'primary_method' => $this->identifyPrimaryMethod()
        ];
    }

    private function calculateMethodDifferences(): array
    {
        $final = $this->final_valuation;
        $differences = [];

        if ($this->comparable_sales_value > 0) {
            $differences['comparable_sales'] = [
                'value' => $this->comparable_sales_value,
                'difference' => $this->comparable_sales_value - $final,
                'percentage' => $final > 0 ? (($this->comparable_sales_value - $final) / $final) * 100 : 0
            ];
        }

        if ($this->income_approach_value > 0) {
            $differences['income_approach'] = [
                'value' => $this->income_approach_value,
                'difference' => $this->income_approach_value - $final,
                'percentage' => $final > 0 ? (($this->income_approach_value - $final) / $final) * 100 : 0
            ];
        }

        if ($this->cost_approach_value > 0) {
            $differences['cost_approach'] = [
                'value' => $this->cost_approach_value,
                'difference' => $this->cost_approach_value - $final,
                'percentage' => $final > 0 ? (($this->cost_approach_value - $final) / $final) * 100 : 0
            ];
        }

        return $differences;
    }

    private function identifyPrimaryMethod(): string
    {
        $methods = [
            'comparable_sales' => $this->comparable_sales_value,
            'income_approach' => $this->income_approach_value,
            'cost_approach' => $this->cost_approach_value
        ];

        $maxValue = max($methods);
        $primaryMethod = array_search($maxValue, $methods);

        return $primaryMethod ?: 'automated_valuation';
    }
}

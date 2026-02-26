<?php

namespace App\Services\Intelligence;

use App\Models\Property;
use Illuminate\Support\Facades\Log;

/**
 * Computes investment scores for properties using configurable weights.
 */
class InvestmentScoringService
{
    /**
     * Calculate investment metrics and final score for a property.
     *
     * @param Property $property
     * @param array $inputs [
     *   'historical_trend' => float, // %
     *   'rental_avg' => float, // monthly rent
     *   'listing_velocity' => float, // days on market
     *   'search_frequency' => int, // relative demand
     *   'neighborhood_growth' => float // %
     * ]
     * @return array{
     *  roi: float,
     *  cash_flow: float,
     *  liquidity: float,
     *  risk: float,
     *  demand: float,
     *  investment_score: float,
     *  expected_appreciation: float,
     *  risk_level: string
     * }
     */
    public function score(Property $property, array $inputs): array
    {
        $weights = config('intelligence.investment.weights');
        $scales = config('intelligence.investment.scales');

        $price = (float) $property->price;
        $rent = (float) ($inputs['rental_avg'] ?? 0);
        $expenses = max(0.1 * $rent, 500); // simple expense estimate

        $roi = $price > 0 ? (($rent * 12) / $price) * 100 : 0;
        $cashFlow = max($rent - $expenses, 0);
        $liquidityDays = (float) ($inputs['listing_velocity'] ?? 0);
        $demandSignal = $this->normalize((float) ($inputs['search_frequency'] ?? 0), 0, $scales['demand_max']);
        $riskBase = $this->riskFromVariance(
            (float) ($inputs['historical_trend'] ?? 0),
            (float) ($inputs['neighborhood_growth'] ?? 0)
        );

        $liquidity = 100 * (1 - min($liquidityDays / max($scales['liquidity_max_days'], 1), 1)); // faster sell => higher
        $risk = max(0, min(100, $riskBase));
        $demand = 100 * $demandSignal;
        $expectedAppreciation = (float) ($inputs['historical_trend'] ?? 0) * 0.6 + (float) ($inputs['neighborhood_growth'] ?? 0) * 0.4;

        $roiNorm = min($roi / max($scales['roi_max'], 1), 1) * 100;
        $cashNorm = min($cashFlow / max($scales['cash_flow_max'], 1), 1) * 100;
        $riskNorm = 100 - $risk; // lower risk => higher contribution

        $score =
            $roiNorm * $weights['roi'] +
            $cashNorm * $weights['cash_flow'] +
            $liquidity * $weights['liquidity'] +
            $riskNorm * $weights['risk'] +
            $demand * $weights['demand'];

        $riskLevel = $risk < 35 ? 'Low' : ($risk < 65 ? 'Medium' : 'High');

        return [
            'roi' => round($roi, 2),
            'cash_flow' => round($cashFlow, 2),
            'liquidity' => round($liquidity, 2),
            'risk' => round($risk, 2),
            'demand' => round($demand, 2),
            'investment_score' => round($score / 10, 2), // map 0-100 to 0-10
            'expected_appreciation' => round($expectedAppreciation, 2),
            'risk_level' => $riskLevel,
        ];
    }

    /**
     * Persist computed metrics on the property.
     *
     * @param Property $property
     * @param array $metrics
     */
    public function persist(Property $property, array $metrics): void
    {
        $property->fill([
            'investment_score' => $metrics['investment_score'],
            'roi_percentage' => $metrics['roi'],
            'risk_score' => $metrics['risk'],
            'liquidity_score' => $metrics['liquidity'],
            'demand_score' => $metrics['demand'],
            'expected_appreciation' => $metrics['expected_appreciation'],
        ])->save();

        Log::channel('intelligence')->info('Investment scoring updated', [
            'property_id' => $property->id,
            'investment_score' => $metrics['investment_score'],
        ]);
    }

    /**
     * Simple normalization helper.
     */
    private function normalize(float $value, float $min, float $max): float
    {
        if ($max - $min == 0) {
            return 0.0;
        }
        return max(0.0, min(1.0, ($value - $min) / ($max - $min)));
    }

    /**
     * Derive a basic risk score from volatility proxies.
     */
    private function riskFromVariance(float $trend, float $growth): float
    {
        $volatility = abs($trend - $growth);
        $base = 20 + $volatility * 2; // 0..100 scale
        return min(100, max(0, $base));
    }
}


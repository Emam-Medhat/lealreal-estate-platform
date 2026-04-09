<?php

namespace App\Http\Controllers;

use App\Models\PropertyValuation;
use App\Models\PropertyFinancialAnalysis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyValuationModelController extends Controller
{
    public function index(): View
    {
        return view('financial.valuation.index');
    }

    public function create(): View
    {
        return view('financial.valuation.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_financial_analysis_id' => 'required|exists:property_financial_analyses,id',
            'valuation_method' => 'required|in:comparative,income,cost,residual',
            'valuation_amount' => 'required|numeric|min:0',
            'valuation_date' => 'required|date',
            'valuation_data' => 'required|array',
            'assumptions' => 'nullable|array',
            'adjustments' => 'nullable|array',
            'confidence_level' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        $valuation = PropertyValuation::create($validated);

        return redirect()
            ->route('financial.valuation.show', $valuation)
            ->with('success', 'تم إنشاء تقييم العقار بنجاح');
    }

    public function show(PropertyValuation $valuation): View
    {
        $valuation->load(['propertyFinancialAnalysis.property']);
        
        return view('financial.valuation.show', compact('valuation'));
    }

    public function edit(PropertyValuation $valuation): View
    {
        return view('financial.valuation.edit', compact('valuation'));
    }

    public function update(Request $request, PropertyValuation $valuation)
    {
        $validated = $request->validate([
            'valuation_amount' => 'required|numeric|min:0',
            'valuation_date' => 'required|date',
            'valuation_data' => 'required|array',
            'assumptions' => 'nullable|array',
            'adjustments' => 'nullable|array',
            'confidence_level' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        $valuation->update($validated);

        return redirect()
            ->route('financial.valuation.show', $valuation)
            ->with('success', 'تم تحديث تقييم العقار بنجاح');
    }

    public function destroy(PropertyValuation $valuation)
    {
        $valuation->delete();
        return redirect()
            ->route('financial.valuation.index')
            ->with('success', 'تم حذف تقييم العقار بنجاح');
    }

    public function comparativeAnalysis(): View
    {
        return view('financial.valuation.comparative');
    }

    public function performComparativeAnalysis(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject_property' => 'required|array',
            'comparable_properties' => 'required|array|min:3',
            'adjustment_factors' => 'required|array'
        ]);

        $analysisResult = $this->performComparativeValuation($validated);

        return response()->json([
            'success' => true,
            'data' => $analysisResult
        ]);
    }

    public function incomeApproach(): View
    {
        return view('financial.valuation.income-approach');
    }

    public function performIncomeApproach(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'net_operating_income' => 'required|numeric|min:0',
            'capitalization_rate' => 'required|numeric|min:0|max:100',
            'growth_rate' => 'nullable|numeric|min:0|max:100',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'holding_period' => 'nullable|integer|min:1|max:30'
        ]);

        $valuationResult = $this->performIncomeValuation($validated);

        return response()->json([
            'success' => true,
            'data' => $valuationResult
        ]);
    }

    public function costApproach(): View
    {
        return view('financial.valuation.cost-approach');
    }

    public function performCostApproach(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'land_value' => 'required|numeric|min:0',
            'construction_cost' => 'required|numeric|min:0',
            'depreciation' => 'nullable|numeric|min:0|max:100',
            'entrepreneurial_profit' => 'nullable|numeric|min:0|max:100',
            'improvements_cost' => 'nullable|array'
        ]);

        $valuationResult = $this->performCostValuation($validated);

        return response()->json([
            'success' => true,
            'data' => $valuationResult
        ]);
    }

    public function residualMethod(): View
    {
        return view('financial.valuation.residual');
    }

    public function performResidualMethod(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'gross_development_value' => 'required|numeric|min:0',
            'construction_costs' => 'required|numeric|min:0',
            'professional_fees' => 'nullable|numeric|min:0',
            'marketing_costs' => 'nullable|numeric|min:0',
            'finance_costs' => 'nullable|numeric|min:0',
            'developer_profit' => 'nullable|numeric|min:0|max:100'
        ]);

        $valuationResult = $this->performResidualValuation($validated);

        return response()->json([
            'success' => true,
            'data' => $valuationResult
        ]);
    }

    public function automatedValuation(PropertyFinancialAnalysis $analysis): JsonResponse
    {
        $automatedValuation = $this->generateAutomatedValuation($analysis);

        return response()->json([
            'success' => true,
            'data' => $automatedValuation
        ]);
    }

    public function valuationHistory(PropertyFinancialAnalysis $analysis): View
    {
        $valuations = $analysis->propertyValuations()
            ->orderBy('valuation_date')
            ->get();

        return view('financial.valuation.history', compact('analysis', 'valuations'));
    }

    public function export(PropertyValuation $valuation)
    {
        return response()->json([
            'valuation' => $valuation->load(['propertyFinancialAnalysis.property']),
            'exported_at' => now()
        ]);
    }

    private function performComparativeValuation(array $data): array
    {
        $subjectProperty = $data['subject_property'];
        $comparableProperties = $data['comparable_properties'];
        $adjustmentFactors = $data['adjustment_factors'];

        $adjustedValues = [];

        foreach ($comparableProperties as $index => $comp) {
            $basePrice = $comp['price'];
            $totalAdjustment = 0;

            // Apply adjustments based on differences
            foreach ($adjustmentFactors as $factor => $percentage) {
                if (isset($subjectProperty[$factor]) && isset($comp[$factor])) {
                    $difference = $subjectProperty[$factor] - $comp[$factor];
                    $adjustment = $basePrice * ($difference * $percentage / 100);
                    $totalAdjustment += $adjustment;
                }
            }

            $adjustedPrice = $basePrice + $totalAdjustment;
            $pricePerSqm = $comp['area'] > 0 ? $adjustedPrice / $comp['area'] : 0;

            $adjustedValues[] = [
                'comparable_id' => $index + 1,
                'original_price' => $basePrice,
                'total_adjustment' => $totalAdjustment,
                'adjusted_price' => $adjustedPrice,
                'price_per_sqm' => $pricePerSqm,
                'adjustment_percentage' => $basePrice > 0 ? ($totalAdjustment / $basePrice) * 100 : 0
            ];
        }

        // Calculate weighted average
        $totalWeightedValue = 0;
        $totalWeight = 0;

        foreach ($adjustedValues as $value) {
            // Weight based on similarity (inverse of adjustment percentage)
            $weight = max(0.1, 1 - abs($value['adjustment_percentage']) / 100);
            $totalWeightedValue += $value['adjusted_price'] * $weight;
            $totalWeight += $weight;
        }

        $finalValuation = $totalWeight > 0 ? $totalWeightedValue / $totalWeight : 0;

        return [
            'subject_property' => $subjectProperty,
            'comparable_analysis' => $adjustedValues,
            'final_valuation' => $finalValuation,
            'valuation_range' => [
                'min' => collect($adjustedValues)->min('adjusted_price'),
                'max' => collect($adjustedValues)->max('adjusted_price'),
                'average' => collect($adjustedValues)->avg('adjusted_price')
            ],
            'confidence_level' => $this->calculateComparativeConfidence($adjustedValues)
        ];
    }

    private function performIncomeValuation(array $data): array
    {
        $noi = $data['net_operating_income'];
        $capRate = $data['capitalization_rate'] / 100;
        $growthRate = ($data['growth_rate'] ?? 0) / 100;
        $discountRate = ($data['discount_rate'] ?? 0) / 100;
        $holdingPeriod = $data['holding_period'] ?? 10;

        // Direct capitalization
        $directCapitalizationValue = $capRate > 0 ? $noi / $capRate : 0;

        // Discounted cash flow (if parameters provided)
        $dcfValue = 0;
        if ($discountRate > 0 && $holdingPeriod > 0) {
            for ($year = 1; $year <= $holdingPeriod; $year++) {
                $projectedNoi = $noi * pow(1 + $growthRate, $year - 1);
                $presentValue = $projectedNoi / pow(1 + $discountRate, $year);
                $dcfValue += $presentValue;
            }

            // Add terminal value
            $terminalNoi = $noi * pow(1 + $growthRate, $holdingPeriod);
            $terminalValue = $terminalNoi / $capRate;
            $terminalPresentValue = $terminalValue / pow(1 + $discountRate, $holdingPeriod);
            $dcfValue += $terminalPresentValue;
        }

        return [
            'income_approach' => [
                'direct_capitalization' => [
                    'net_operating_income' => $noi,
                    'capitalization_rate' => $data['capitalization_rate'],
                    'property_value' => $directCapitalizationValue
                ],
                'discounted_cash_flow' => [
                    'net_operating_income' => $noi,
                    'growth_rate' => $data['growth_rate'] ?? 0,
                    'discount_rate' => $data['discount_rate'] ?? 0,
                    'holding_period' => $holdingPeriod,
                    'property_value' => $dcfValue
                ]
            ],
            'recommended_value' => $dcfValue > 0 ? $dcfValue : $directCapitalizationValue,
            'sensitivity_analysis' => $this->performIncomeSensitivity($noi, $capRate, $growthRate, $discountRate)
        ];
    }

    private function performCostValuation(array $data): array
    {
        $landValue = $data['land_value'];
        $constructionCost = $data['construction_cost'];
        $depreciation = ($data['depreciation'] ?? 0) / 100;
        $entrepreneurialProfit = ($data['entrepreneurial_profit'] ?? 0) / 100;
        $improvementsCost = collect($data['improvements_cost'] ?? [])->sum();

        $totalReplacementCost = $constructionCost + $improvementsCost;
        $depreciationAmount = $totalReplacementCost * $depreciation;
        $depreciatedCost = $totalReplacementCost - $depreciationAmount;
        $profitAmount = $depreciatedCost * $entrepreneurialProfit;

        $propertyValue = $landValue + $depreciatedCost + $profitAmount;

        return [
            'cost_approach' => [
                'land_value' => $landValue,
                'replacement_cost' => [
                    'construction_cost' => $constructionCost,
                    'improvements_cost' => $improvementsCost,
                    'total_replacement_cost' => $totalReplacementCost
                ],
                'depreciation' => [
                    'rate' => $data['depreciation'] ?? 0,
                    'amount' => $depreciationAmount,
                    'depreciated_cost' => $depreciatedCost
                ],
                'entrepreneurial_profit' => [
                    'rate' => $data['entrepreneurial_profit'] ?? 0,
                    'amount' => $profitAmount
                ]
            ],
            'final_property_value' => $propertyValue,
            'cost_breakdown' => [
                'land_percentage' => $propertyValue > 0 ? ($landValue / $propertyValue) * 100 : 0,
                'improvements_percentage' => $propertyValue > 0 ? ($depreciatedCost / $propertyValue) * 100 : 0,
                'profit_percentage' => $propertyValue > 0 ? ($profitAmount / $propertyValue) * 100 : 0
            ]
        ];
    }

    private function performResidualValuation(array $data): array
    {
        $gdv = $data['gross_development_value'];
        $constructionCosts = $data['construction_costs'];
        $professionalFees = $data['professional_fees'] ?? 0;
        $marketingCosts = $data['marketing_costs'] ?? 0;
        $financeCosts = $data['finance_costs'] ?? 0;
        $developerProfitRate = ($data['developer_profit'] ?? 20) / 100;

        $totalCosts = $constructionCosts + $professionalFees + $marketingCosts + $financeCosts;
        $developerProfit = $gdv * $developerProfitRate;
        $totalDeductions = $totalCosts + $developerProfit;
        $residualLandValue = $gdv - $totalDeductions;

        return [
            'residual_method' => [
                'gross_development_value' => $gdv,
                'development_costs' => [
                    'construction_costs' => $constructionCosts,
                    'professional_fees' => $professionalFees,
                    'marketing_costs' => $marketingCosts,
                    'finance_costs' => $financeCosts,
                    'total_costs' => $totalCosts
                ],
                'developer_profit' => [
                    'rate' => $data['developer_profit'] ?? 20,
                    'amount' => $developerProfit
                ],
                'residual_land_value' => $residualLandValue
            ],
            'profitability_analysis' => [
                'total_deductions' => $totalDeductions,
                'profit_margin' => $gdv > 0 ? (($gdv - $totalDeductions) / $gdv) * 100 : 0,
                'land_value_percentage' => $gdv > 0 ? ($residualLandValue / $gdv) * 100 : 0
            ]
        ];
    }

    private function generateAutomatedValuation(PropertyFinancialAnalysis $analysis): array
    {
        // Generate valuation using multiple methods
        $valuations = [];

        // Income approach
        $noi = $analysis->rental_income * 12 * (1 - $analysis->vacancy_rate / 100) - ($analysis->operating_expenses * 12);
        $capRate = 8.5; // Market average
        $incomeValue = $noi / ($capRate / 100);
        $valuations['income_approach'] = $incomeValue;

        // Cost approach (simplified)
        $costValue = $analysis->purchase_price * 1.1; // Assuming 10% appreciation
        $valuations['cost_approach'] = $costValue;

        // Market approach (using current value as proxy)
        $marketValue = $analysis->current_value;
        $valuations['market_approach'] = $marketValue;

        // Calculate weighted average
        $weights = ['income_approach' => 0.4, 'cost_approach' => 0.3, 'market_approach' => 0.3];
        $weightedValue = 0;

        foreach ($valuations as $method => $value) {
            $weightedValue += $value * $weights[$method];
        }

        return [
            'automated_valuation' => $weightedValue,
            'method_results' => $valuations,
            'weights_used' => $weights,
            'confidence_level' => $this->calculateAutomatedConfidence($valuations),
            'valuation_range' => [
                'min' => min($valuations),
                'max' => max($valuations),
                'spread' => max($valuations) - min($valuations),
                'spread_percentage' => min($valuations) > 0 ? ((max($valuations) - min($valuations)) / min($valuations)) * 100 : 0
            ]
        ];
    }

    private function calculateComparativeConfidence(array $adjustedValues): int
    {
        if (empty($adjustedValues)) {
            return 0;
        }

        $values = collect($adjustedValues)->pluck('adjusted_price');
        $mean = $values->avg();
        $variance = $values->sum(function($value) use ($mean) {
            return pow($value - $mean, 2);
        }) / $values->count();
        $stdDev = sqrt($variance);

        // Confidence based on standard deviation relative to mean
        $coefficientOfVariation = $mean > 0 ? ($stdDev / $mean) * 100 : 100;

        if ($coefficientOfVariation < 5) {
            return 90;
        } elseif ($coefficientOfVariation < 10) {
            return 80;
        } elseif ($coefficientOfVariation < 15) {
            return 70;
        } elseif ($coefficientOfVariation < 20) {
            return 60;
        } else {
            return 50;
        }
    }

    private function calculateAutomatedConfidence(array $valuations): int
    {
        if (count($valuations) < 2) {
            return 50;
        }

        $values = array_values($valuations);
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $values)) / count($values);
        $stdDev = sqrt($variance);

        $coefficientOfVariation = $mean > 0 ? ($stdDev / $mean) * 100 : 100;

        if ($coefficientOfVariation < 10) {
            return 85;
        } elseif ($coefficientOfVariation < 20) {
            return 75;
        } elseif ($coefficientOfVariation < 30) {
            return 65;
        } else {
            return 55;
        }
    }

    private function performIncomeSensitivity(float $noi, float $capRate, float $growthRate, float $discountRate): array
    {
        $sensitivity = [];
        $variations = [-20, -10, 0, 10, 20];

        foreach ($variations as $variation) {
            $adjustedCapRate = $capRate * (1 + $variation / 100);
            $value = $adjustedCapRate > 0 ? $noi / $adjustedCapRate : 0;
            
            $sensitivity[] = [
                'cap_rate_variation' => $variation,
                'adjusted_cap_rate' => $adjustedCapRate * 100,
                'property_value' => $value,
                'value_change' => $value - ($noi / $capRate),
                'percentage_change' => $variation
            ];
        }

        return $sensitivity;
    }
}

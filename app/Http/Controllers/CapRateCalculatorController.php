<?php

namespace App\Http\Controllers;

use App\Models\CapRateCalculation;
use App\Models\PropertyFinancialAnalysis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class CapRateCalculatorController extends Controller
{
    public function index(): View
    {
        return view('financial.cap-rate-calculator.index');
    }

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_value' => 'required|numeric|min:0',
            'annual_rental_income' => 'required|numeric|min:0',
            'other_income' => 'nullable|numeric|min:0',
            'operating_expenses' => 'required|array',
            'operating_expenses.*.name' => 'required|string',
            'operating_expenses.*.amount' => 'required|numeric|min:0',
            'vacancy_rate' => 'required|numeric|min:0|max:100',
            'capitalization_rate' => 'nullable|numeric|min:0|max:100',
            'market_cap_rate' => 'nullable|numeric|min:0|max:100'
        ]);

        $capRateData = $this->performCapRateCalculation($validated);

        return response()->json([
            'success' => true,
            'data' => $capRateData
        ]);
    }

    public function advanced(): View
    {
        return view('financial.cap-rate-calculator.advanced');
    }

    public function compare(): View
    {
        return view('financial.cap-rate-calculator.compare');
    }

    public function saveCalculation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_financial_analysis_id' => 'required|exists:property_financial_analyses,id',
            'property_value' => 'required|numeric|min:0',
            'net_operating_income' => 'required|numeric',
            'cap_rate_percentage' => 'required|numeric',
            'calculation_details' => 'required|array'
        ]);

        $capRateCalculation = CapRateCalculation::create([
            'property_financial_analysis_id' => $validated['property_financial_analysis_id'],
            'property_value' => $validated['property_value'],
            'gross_operating_income' => $validated['calculation_details']['gross_operating_income'],
            'operating_expenses' => $validated['calculation_details']['total_operating_expenses'],
            'net_operating_income' => $validated['net_operating_income'],
            'cap_rate_percentage' => $validated['cap_rate_percentage'],
            'calculated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ حساب معدل الرسملة بنجاح',
            'cap_rate_calculation' => $capRateCalculation
        ]);
    }

    public function marketAnalysis(): View
    {
        return view('financial.cap-rate-calculator.market-analysis');
    }

    public function scenarios(PropertyFinancialAnalysis $analysis): View
    {
        $scenarios = $this->generateCapRateScenarios($analysis);
        
        return view('financial.cap-rate-calculator.scenarios', compact('analysis', 'scenarios'));
    }

    public function export(PropertyFinancialAnalysis $analysis)
    {
        $capRateCalculations = $analysis->capRateCalculations()->latest()->get();
        
        return response()->json([
            'analysis_id' => $analysis->id,
            'cap_rate_calculations' => $capRateCalculations,
            'exported_at' => now()
        ]);
    }

    private function performCapRateCalculation(array $data): array
    {
        $propertyValue = $data['property_value'];
        $annualRentalIncome = $data['annual_rental_income'];
        $otherIncome = $data['other_income'] ?? 0;
        $operatingExpenses = collect($data['operating_expenses']);
        $vacancyRate = $data['vacancy_rate'] / 100;
        $marketCapRate = $data['market_cap_rate'] ?? null;

        // Calculate Effective Gross Income (EGI)
        $potentialGrossIncome = $annualRentalIncome + $otherIncome;
        $vacancyLoss = $potentialGrossIncome * $vacancyRate;
        $effectiveGrossIncome = $potentialGrossIncome - $vacancyLoss;

        // Calculate Total Operating Expenses
        $totalOperatingExpenses = $operatingExpenses->sum('amount');

        // Calculate Net Operating Income (NOI)
        $netOperatingIncome = $effectiveGrossIncome - $totalOperatingExpenses;

        // Calculate Capitalization Rate
        $capRate = $propertyValue > 0 ? ($netOperatingIncome / $propertyValue) * 100 : 0;

        // Calculate property value based on market cap rate
        $imPropertyValue = $marketCapRate && $marketCapRate > 0 ? 
            ($netOperatingIncome / ($marketCapRate / 100)) : null;

        // Calculate expense ratios
        $expenseRatios = $operatingExpenses->mapWithKeys(function ($expense) use ($totalOperatingExpenses) {
            $ratio = $totalOperatingExpenses > 0 ? ($expense['amount'] / $totalOperatingExpenses) * 100 : 0;
            return [$expense['name'] => [
                'amount' => $expense['amount'],
                'ratio' => $ratio
            ]];
        });

        // Calculate operating efficiency ratio
        $operatingEfficiency = $effectiveGrossIncome > 0 ? 
            ($netOperatingIncome / $effectiveGrossIncome) * 100 : 0;

        // Calculate break-even occupancy rate
        $breakEvenOccupancy = $annualRentalIncome > 0 ? 
            (($totalOperatingExpenses + $netOperatingIncome) / $annualRentalIncome) * 100 : 0;

        return [
            'income_analysis' => [
                'potential_gross_income' => $potentialGrossIncome,
                'vacancy_loss' => $vacancyLoss,
                'effective_gross_income' => $effectiveGrossIncome,
                'other_income' => $otherIncome
            ],
            'expense_analysis' => [
                'total_operating_expenses' => $totalOperatingExpenses,
                'expense_breakdown' => $expenseRatios,
                'operating_efficiency' => $operatingEfficiency
            ],
            'cap_rate_analysis' => [
                'net_operating_income' => $netOperatingIncome,
                'property_value' => $propertyValue,
                'cap_rate_percentage' => $capRate,
                'market_cap_rate' => $marketCapRate,
                'implied_property_value' => $imPropertyValue,
                'value_difference' => $imPropertyValue ? ($imPropertyValue - $propertyValue) : null,
                'value_difference_percentage' => $imPropertyValue && $propertyValue > 0 ? 
                    (($imPropertyValue - $propertyValue) / $propertyValue) * 100 : null
            ],
            'performance_metrics' => [
                'break_even_occupancy_rate' => $breakEvenOccupancy,
                'noi_margin' => $effectiveGrossIncome > 0 ? ($netOperatingIncome / $effectiveGrossIncome) * 100 : 0,
                'expense_ratio' => $effectiveGrossIncome > 0 ? ($totalOperatingExpenses / $effectiveGrossIncome) * 100 : 0,
                'vacancy_impact' => $annualRentalIncome * $vacancyRate
            ],
            'recommendations' => $this->generateCapRateRecommendations($capRate, $marketCapRate, $operatingEfficiency)
        ];
    }

    private function generateCapRateRecommendations(float $capRate, ?float $marketCapRate, float $operatingEfficiency): array
    {
        $recommendations = [];

        // Cap rate comparison
        if ($marketCapRate) {
            if ($capRate < $marketCapRate) {
                $recommendations[] = [
                    'type' => 'warning',
                    'message' => 'معدل الرسملة أقل من متوسط السوق. قد يكون العقار مبالغ في تقييمه.',
                    'action' => 'فكر في إعادة التفاوض على السعر أو تحسين الدخل التشغيلي'
                ];
            } elseif ($capRate > $marketCapRate) {
                $recommendations[] = [
                    'type' => 'positive',
                    'message' => 'معدل الرسملة أعلى من متوسط السوق. استثمار جيد محتمل.',
                    'action' => 'استمر في مراقبة أداء العقار وصيانة المستوى الجيد'
                ];
            }
        }

        // Operating efficiency
        if ($operatingEfficiency < 70) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'كفاءة التشغيل منخفضة. المصاريف التشغيلية عالية.',
                'action' => 'مراجعة وتقليل المصاريف التشغيلية غير الضرورية'
            ];
        } elseif ($operatingEfficiency > 85) {
            $recommendations[] = [
                'type' => 'positive',
                'message' => 'كفاءة تشغيل ممتازة. إدارة جيدة للمصاريف.',
                'action' => 'الحفاظ على مستوى الإدارة الحالي'
            ];
        }

        // General recommendations
        if ($capRate < 5) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'معدل رسملة منخفض جداً. عائد استثمار ضعيف.',
                'action' => 'ابحث عن فرص لزيادة الدخل أو تقليل المصاريف'
            ];
        } elseif ($capRate > 12) {
            $recommendations[] = [
                'type' => 'caution',
                'message' => 'معدل رسملة مرتفع جداً. قد يشير لمخاطر عالية.',
                'action' => 'تحقق من أسباب الارتفاع وتقييم المخاطر المحتملة'
            ];
        }

        return $recommendations;
    }

    private function generateCapRateScenarios(PropertyFinancialAnalysis $analysis): array
    {
        $baseData = [
            'property_value' => $analysis->current_value,
            'annual_rental_income' => $analysis->rental_income * 12,
            'operating_expenses' => [
                ['name' => 'المصاريف التشغيلية', 'amount' => $analysis->operating_expenses * 12]
            ],
            'vacancy_rate' => $analysis->vacancy_rate
        ];

        $scenarios = [];

        // Best case scenario
        $bestCase = $baseData;
        $bestCase['annual_rental_income'] *= 1.2; // 20% higher rent
        $bestCase['operating_expenses'][0]['amount'] *= 0.9; // 10% lower expenses
        $bestCase['vacancy_rate'] *= 0.5; // 50% lower vacancy
        $scenarios['best_case'] = $this->performCapRateCalculation($bestCase);

        // Worst case scenario
        $worstCase = $baseData;
        $worstCase['annual_rental_income'] *= 0.8; // 20% lower rent
        $worstCase['operating_expenses'][0]['amount'] *= 1.2; // 20% higher expenses
        $worstCase['vacancy_rate'] *= 1.5; // 50% higher vacancy
        $scenarios['worst_case'] = $this->performCapRateCalculation($worstCase);

        // Conservative scenario
        $conservativeCase = $baseData;
        $conservativeCase['annual_rental_income'] *= 1.05; // 5% higher rent
        $conservativeCase['operating_expenses'][0]['amount'] *= 1.05; // 5% higher expenses
        $conservativeCase['vacancy_rate'] *= 1.1; // 10% higher vacancy
        $scenarios['conservative'] = $this->performCapRateCalculation($conservativeCase);

        // Market adjustment scenario
        $marketCase = $baseData;
        $marketCase['market_cap_rate'] = 8.5; // Average market cap rate
        $scenarios['market_adjustment'] = $this->performCapRateCalculation($marketCase);

        return $scenarios;
    }

    public function calculateFromNoi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'net_operating_income' => 'required|numeric|min:0',
            'target_cap_rate' => 'required|numeric|min:0|max:100'
        ]);

        $noi = $validated['net_operating_income'];
        $targetCapRate = $validated['target_cap_rate'] / 100;

        $propertyValue = $targetCapRate > 0 ? $noi / $targetCapRate : 0;

        return response()->json([
            'success' => true,
            'property_value' => $propertyValue,
            'net_operating_income' => $noi,
            'target_cap_rate' => $validated['target_cap_rate']
        ]);
    }

    public function calculateNoiFromValue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_value' => 'required|numeric|min:0',
            'cap_rate' => 'required|numeric|min:0|max:100'
        ]);

        $propertyValue = $validated['property_value'];
        $capRate = $validated['cap_rate'] / 100;

        $netOperatingIncome = $propertyValue * $capRate;

        return response()->json([
            'success' => true,
            'net_operating_income' => $netOperatingIncome,
            'property_value' => $propertyValue,
            'cap_rate' => $validated['cap_rate']
        ]);
    }

    public function marketTrends(): View
    {
        // This would typically fetch market data from external sources
        $marketData = [
            'average_cap_rates' => [
                'residential' => 7.5,
                'commercial' => 8.2,
                'industrial' => 9.1,
                'retail' => 8.8
            ],
            'historical_trends' => [
                '2020' => 8.2,
                '2021' => 7.9,
                '2022' => 7.6,
                '2023' => 7.8,
                '2024' => 8.1
            ],
            'regional_variations' => [
                'north' => 8.5,
                'south' => 7.8,
                'east' => 8.2,
                'west' => 7.9,
                'central' => 8.0
            ]
        ];

        return view('financial.cap-rate-calculator.market-trends', compact('marketData'));
    }
}

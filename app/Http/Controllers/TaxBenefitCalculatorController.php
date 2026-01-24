<?php

namespace App\Http\Controllers;

use App\Models\TaxBenefit;
use App\Models\PropertyFinancialAnalysis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class TaxBenefitCalculatorController extends Controller
{
    public function index(): View
    {
        return view('financial.tax-benefit-calculator.index');
    }

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_value' => 'required|numeric|min:0',
            'annual_income' => 'required|numeric|min:0',
            'operating_expenses' => 'required|numeric|min:0',
            'depreciation_period' => 'required|integer|min:1|max:40',
            'loan_interest' => 'nullable|numeric|min:0',
            'property_tax' => 'nullable|numeric|min:0',
            'income_tax_rate' => 'required|numeric|min:0|max:100',
            'capital_gains_tax_rate' => 'required|numeric|min:0|max:100',
            'holding_period' => 'required|integer|min:1|max:30',
            'tax_deductions' => 'nullable|array',
            'tax_credits' => 'nullable|array'
        ]);

        $taxBenefitData = $this->performTaxBenefitCalculation($validated);

        return response()->json([
            'success' => true,
            'data' => $taxBenefitData
        ]);
    }

    public function detailed(): View
    {
        return view('financial.tax-benefit-calculator.detailed');
    }

    public function compare(): View
    {
        return view('financial.tax-benefit-calculator.compare');
    }

    public function saveCalculation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_financial_analysis_id' => 'required|exists:property_financial_analyses,id',
            'tax_year' => 'required|integer|min:2020|max:2030',
            'total_tax_benefit' => 'required|numeric',
            'deduction_details' => 'required|array',
            'credit_details' => 'required|array',
            'calculation_assumptions' => 'required|array'
        ]);

        $taxBenefit = TaxBenefit::create([
            'property_financial_analysis_id' => $validated['property_financial_analysis_id'],
            'tax_year' => $validated['tax_year'],
            'depreciation_benefit' => $validated['deduction_details']['depreciation'] ?? 0,
            'interest_deduction' => $validated['deduction_details']['interest'] ?? 0,
            'property_tax_deduction' => $validated['deduction_details']['property_tax'] ?? 0,
            'operating_expense_deduction' => $validated['deduction_details']['operating_expenses'] ?? 0,
            'tax_credits' => array_sum($validated['credit_details'] ?? []),
            'total_tax_benefit' => $validated['total_tax_benefit'],
            'after_tax_cash_flow' => $validated['calculation_assumptions']['after_tax_cash_flow'] ?? 0,
            'calculated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ حساب المزايا الضريبية بنجاح',
            'tax_benefit' => $taxBenefit
        ]);
    }

    public function depreciationSchedule(): View
    {
        return view('financial.tax-benefit-calculator.depreciation');
    }

    public function generateDepreciationSchedule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_value' => 'required|numeric|min:0',
            'land_value' => 'required|numeric|min:0',
            'depreciation_method' => 'required|in:straight_line,declining_balance,accelerated',
            'useful_life' => 'required|integer|min:1|max:40',
            'placed_in_service_date' => 'required|date'
        ]);

        $depreciationSchedule = $this->calculateDepreciationSchedule($validated);

        return response()->json([
            'success' => true,
            'data' => $depreciationSchedule
        ]);
    }

    public function scenarios(PropertyFinancialAnalysis $analysis): View
    {
        $scenarios = $this->generateTaxScenarios($analysis);
        
        return view('financial.tax-benefit-calculator.scenarios', compact('analysis', 'scenarios'));
    }

    public function taxOptimization(): View
    {
        return view('financial.tax-benefit-calculator.optimization');
    }

    public function generateOptimization(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_tax_situation' => 'required|array',
            'financial_goals' => 'required|array',
            'available_strategies' => 'required|array'
        ]);

        $optimizationStrategies = $this->generateTaxOptimizationStrategies($validated);

        return response()->json([
            'success' => true,
            'data' => $optimizationStrategies
        ]);
    }

    public function export(PropertyFinancialAnalysis $analysis)
    {
        $taxBenefits = $analysis->taxBenefits()->latest()->get();
        
        return response()->json([
            'analysis_id' => $analysis->id,
            'tax_benefits' => $taxBenefits,
            'exported_at' => now()
        ]);
    }

    private function performTaxBenefitCalculation(array $data): array
    {
        $propertyValue = $data['property_value'];
        $annualIncome = $data['annual_income'];
        $operatingExpenses = $data['operating_expenses'];
        $depreciationPeriod = $data['depreciation_period'];
        $loanInterest = $data['loan_interest'] ?? 0;
        $propertyTax = $data['property_tax'] ?? 0;
        $incomeTaxRate = $data['income_tax_rate'] / 100;
        $capitalGainsTaxRate = $data['capital_gains_tax_rate'] / 100;
        $holdingPeriod = $data['holding_period'];
        $taxDeductions = $data['tax_deductions'] ?? [];
        $taxCredits = $data['tax_credits'] ?? [];

        // Calculate annual depreciation
        $annualDepreciation = $propertyValue / $depreciationPeriod;

        // Calculate taxable income
        $totalDeductions = $operatingExpenses + $annualDepreciation + $loanInterest + $propertyTax;
        $totalDeductions += array_sum($taxDeductions);
        
        $taxableIncome = max(0, $annualIncome - $totalDeductions);
        $incomeTax = $taxableIncome * $incomeTaxRate;
        $afterTaxIncome = $annualIncome - $incomeTax;

        // Calculate after-tax cash flow
        $afterTaxCashFlow = $afterTaxIncome - $operatingExpenses - $loanInterest - $propertyTax;

        // Calculate total tax benefits
        $depreciationBenefit = $annualDepreciation * $incomeTaxRate;
        $interestBenefit = $loanInterest * $incomeTaxRate;
        $propertyTaxBenefit = $propertyTax * $incomeTaxRate;
        $operatingExpenseBenefit = $operatingExpenses * $incomeTaxRate;
        $totalTaxCredits = array_sum($taxCredits);

        $totalTaxBenefit = $depreciationBenefit + $interestBenefit + $propertyTaxBenefit + 
                          $operatingExpenseBenefit + $totalTaxCredits;

        // Calculate capital gains tax on sale
        $appreciatedValue = $propertyValue * 1.5; // Assuming 50% appreciation
        $capitalGain = $appreciatedValue - $propertyValue;
        $depreciationRecapture = $annualDepreciation * $holdingPeriod;
        $totalGain = $capitalGain + $depreciationRecapture;
        $capitalGainsTax = $totalGain * $capitalGainsTaxRate;

        // Calculate after-tax sale proceeds
        $afterTaxSaleProceeds = $appreciatedValue - $capitalGainsTax;

        // Calculate tax-advantaged ROI
        $preTaxROI = ($annualIncome - $operatingExpenses) / $propertyValue * 100;
        $afterTaxROI = $afterTaxCashFlow / $propertyValue * 100;
        $taxAdvantage = $preTaxROI - $afterTaxROI;

        return [
            'income_analysis' => [
                'gross_income' => $annualIncome,
                'total_deductions' => $totalDeductions,
                'taxable_income' => $taxableIncome,
                'income_tax' => $incomeTax,
                'after_tax_income' => $afterTaxIncome
            ],
            'tax_benefits' => [
                'depreciation_benefit' => $depreciationBenefit,
                'interest_deduction_benefit' => $interestBenefit,
                'property_tax_benefit' => $propertyTaxBenefit,
                'operating_expense_benefit' => $operatingExpenseBenefit,
                'tax_credits' => $totalTaxCredits,
                'total_tax_benefit' => $totalTaxBenefit
            ],
            'cash_flow_analysis' => [
                'pre_tax_cash_flow' => $annualIncome - $operatingExpenses - $loanInterest - $propertyTax,
                'after_tax_cash_flow' => $afterTaxCashFlow,
                'tax_cash_flow_impact' => $totalTaxBenefit
            ],
            'roi_analysis' => [
                'pre_tax_roi' => $preTaxROI,
                'after_tax_roi' => $afterTaxROI,
                'tax_advantage' => $taxAdvantage,
                'effective_tax_rate' => $annualIncome > 0 ? ($incomeTax / $annualIncome) * 100 : 0
            ],
            'sale_analysis' => [
                'assumed_sale_value' => $appreciatedValue,
                'capital_gain' => $capitalGain,
                'depreciation_recapture' => $depreciationRecapture,
                'capital_gains_tax' => $capitalGainsTax,
                'after_tax_proceeds' => $afterTaxSaleProceeds
            ],
            'projections' => $this->generateTaxProjections($data, $holdingPeriod)
        ];
    }

    private function generateTaxProjections(array $data, int $holdingPeriod): array
    {
        $projections = [];
        $propertyValue = $data['property_value'];
        $annualIncome = $data['annual_income'];
        $operatingExpenses = $data['operating_expenses'];
        $depreciationPeriod = $data['depreciation_period'];
        $incomeTaxRate = $data['income_tax_rate'] / 100;
        $annualDepreciation = $propertyValue / $depreciationPeriod;

        for ($year = 1; $year <= $holdingPeriod; $year++) {
            // Assume 3% annual income growth
            $projectedIncome = $annualIncome * pow(1.03, $year - 1);
            $projectedExpenses = $operatingExpenses * pow(1.02, $year - 1); // 2% expense growth
            
            $remainingDepreciation = $year <= $depreciationPeriod ? $annualDepreciation : 0;
            $taxableIncome = max(0, $projectedIncome - $projectedExpenses - $remainingDepreciation);
            $incomeTax = $taxableIncome * $incomeTaxRate;
            $afterTaxCashFlow = $projectedIncome - $projectedExpenses - $incomeTax;
            
            $projections[] = [
                'year' => $year,
                'projected_income' => $projectedIncome,
                'projected_expenses' => $projectedExpenses,
                'depreciation' => $remainingDepreciation,
                'taxable_income' => $taxableIncome,
                'income_tax' => $incomeTax,
                'after_tax_cash_flow' => $afterTaxCashFlow,
                'cumulative_tax_benefit' => $remainingDepreciation * $incomeTaxRate * $year
            ];
        }

        return $projections;
    }

    private function calculateDepreciationSchedule(array $data): array
    {
        $propertyValue = $data['property_value'];
        $landValue = $data['land_value'];
        $depreciableBasis = $propertyValue - $landValue;
        $method = $data['depreciation_method'];
        $usefulLife = $data['useful_life'];
        $placedInService = Carbon::parse($data['placed_in_service_date']);

        $schedule = [];
        $remainingBasis = $depreciableBasis;

        for ($year = 1; $year <= $usefulLife; $year++) {
            $yearlyDepreciation = 0;

            switch ($method) {
                case 'straight_line':
                    $yearlyDepreciation = $depreciableBasis / $usefulLife;
                    break;
                
                case 'declining_balance':
                    $rate = 2 / $usefulLife; // Double declining balance
                    $yearlyDepreciation = min($remainingBasis * $rate, $remainingBasis);
                    break;
                
                case 'accelerated':
                    // MACRS general depreciation system (simplified)
                    if ($year <= 5) {
                        $yearlyDepreciation = $depreciableBasis * 0.2;
                    } elseif ($year <= 10) {
                        $yearlyDepreciation = $depreciableBasis * 0.1;
                    } else {
                        $yearlyDepreciation = $remainingBasis / ($usefulLife - $year + 1);
                    }
                    break;
            }

            $remainingBasis -= $yearlyDepreciation;

            $schedule[] = [
                'year' => $year,
                'beginning_basis' => $remainingBasis + $yearlyDepreciation,
                'depreciation' => $yearlyDepreciation,
                'ending_basis' => $remainingBasis,
                'cumulative_depreciation' => $depreciableBasis - $remainingBasis,
                'percentage_depreciated' => ($depreciableBasis - $remainingBasis) / $depreciableBasis * 100
            ];
        }

        return [
            'depreciation_schedule' => $schedule,
            'depreciable_basis' => $depreciableBasis,
            'method_used' => $method,
            'useful_life_years' => $usefulLife,
            'total_depreciation' => $depreciableBasis,
            'placed_in_service' => $placedInService->format('Y-m-d')
        ];
    }

    private function generateTaxScenarios(PropertyFinancialAnalysis $analysis): array
    {
        $baseData = [
            'property_value' => $analysis->current_value,
            'annual_income' => $analysis->rental_income * 12,
            'operating_expenses' => $analysis->operating_expenses * 12,
            'depreciation_period' => 27.5, // Residential property standard
            'income_tax_rate' => 25,
            'capital_gains_tax_rate' => 15,
            'holding_period' => 10
        ];

        $scenarios = [];

        // Standard scenario
        $scenarios['standard'] = $this->performTaxBenefitCalculation($baseData);

        // High income tax scenario
        $highTaxScenario = $baseData;
        $highTaxScenario['income_tax_rate'] = 35;
        $scenarios['high_tax'] = $this->performTaxBenefitCalculation($highTaxScenario);

        // Low income tax scenario
        $lowTaxScenario = $baseData;
        $lowTaxScenario['income_tax_rate'] = 15;
        $scenarios['low_tax'] = $this->performTaxBenefitCalculation($lowTaxScenario);

        // Short holding period scenario
        $shortHoldScenario = $baseData;
        $shortHoldScenario['holding_period'] = 3;
        $scenarios['short_hold'] = $this->performTaxBenefitCalculation($shortHoldScenario);

        // Long holding period scenario
        $longHoldScenario = $baseData;
        $longHoldScenario['holding_period'] = 20;
        $scenarios['long_hold'] = $this->performTaxBenefitCalculation($longHoldScenario);

        return $scenarios;
    }

    private function generateTaxOptimizationStrategies(array $data): array
    {
        $currentSituation = $data['current_tax_situation'];
        $goals = $data['financial_goals'];
        $strategies = $data['available_strategies'];

        $optimizationStrategies = [];

        // Cost segregation analysis
        if (in_array('cost_segregation', $strategies)) {
            $optimizationStrategies[] = [
                'strategy' => 'Cost Segregation Study',
                'description' => 'Accelerate depreciation by reclassifying property components',
                'potential_savings' => '$50,000 - $100,000 over 5 years',
                'implementation_cost' => '$10,000 - $30,000',
                'payback_period' => '2-3 years',
                'suitability' => $this->assessCostSegregationSuitability($currentSituation)
            ];
        }

        // 1031 exchange
        if (in_array('1031_exchange', $strategies)) {
            $optimizationStrategies[] = [
                'strategy' => '1031 Exchange',
                'description' => 'Defer capital gains tax by reinvesting in similar property',
                'potential_savings' => 'Defer $100,000+ in capital gains tax',
                'implementation_cost' => '$5,000 - $15,000',
                'payback_period' => 'Immediate tax deferral',
                'suitability' => $this->assess1031ExchangeSuitability($currentSituation, $goals)
            ];
        }

        // Opportunity Zone investment
        if (in_array('opportunity_zone', $strategies)) {
            $optimizationStrategies[] = [
                'strategy' => 'Opportunity Zone Investment',
                'description' => 'Tax benefits for investing in designated opportunity zones',
                'potential_savings' => 'Capital gains tax elimination + 10% step-up',
                'implementation_cost' => 'Due diligence and legal fees',
                'payback_period' => '10 years for full benefits',
                'suitability' => $this->assessOpportunityZoneSuitability($currentSituation)
            ];
        }

        // Energy efficiency upgrades
        if (in_array('energy_efficiency', $strategies)) {
            $optimizationStrategies[] = [
                'strategy' => 'Energy Efficiency Upgrades',
                'description' => 'Tax credits for energy-efficient improvements',
                'potential_savings' => '$5,000 - $20,000 in tax credits',
                'implementation_cost' => '$20,000 - $100,000',
                'payback_period' => '3-7 years',
                'suitability' => $this->assessEnergyEfficiencySuitability($currentSituation)
            ];
        }

        // Real estate professional status
        if (in_array('real_estate_professional', $strategies)) {
            $optimizationStrategies[] = [
                'strategy' => 'Real Estate Professional Status',
                'description' => 'Deduct rental losses against ordinary income',
                'potential_savings' => '$20,000+ in annual tax savings',
                'implementation_cost' => 'Time and compliance requirements',
                'payback_period' => '1 year',
                'suitability' => $this->assessProfessionalStatusSuitability($currentSituation)
            ];
        }

        return [
            'strategies' => $optimizationStrategies,
            'prioritized_recommendations' => $this->prioritizeStrategies($optimizationStrategies, $goals),
            'implementation_roadmap' => $this->createImplementationRoadmap($optimizationStrategies),
            'expected_total_savings' => $this->calculateTotalSavings($optimizationStrategies)
        ];
    }

    private function assessCostSegregationSuitability(array $situation): string
    {
        $propertyValue = $situation['property_value'] ?? 0;
        
        if ($propertyValue > 1000000) {
            return 'highly_recommended';
        } elseif ($propertyValue > 500000) {
            return 'recommended';
        } else {
            return 'consider';
        }
    }

    private function assess1031ExchangeSuitability(array $situation, array $goals): string
    {
        $hasCapitalGains = ($situation['capital_gains'] ?? 0) > 0;
        $wantsToReinvest = in_array('portfolio_growth', $goals);
        
        if ($hasCapitalGains && $wantsToReinvest) {
            return 'highly_recommended';
        } elseif ($hasCapitalGains) {
            return 'recommended';
        } else {
            return 'not_applicable';
        }
    }

    private function assessOpportunityZoneSuitability(array $situation): string
    {
        $capitalGains = $situation['capital_gains'] ?? 0;
        $riskTolerance = $situation['risk_tolerance'] ?? 'medium';
        
        if ($capitalGains > 100000 && $riskTolerance === 'high') {
            return 'recommended';
        } elseif ($capitalGains > 50000) {
            return 'consider';
        } else {
            return 'not_applicable';
        }
    }

    private function assessEnergyEfficiencySuitability(array $situation): string
    {
        $propertyAge = $situation['property_age'] ?? 10;
        $currentUtilities = $situation['annual_utilities'] ?? 0;
        
        if ($propertyAge > 20 && $currentUtilities > 20000) {
            return 'highly_recommended';
        } elseif ($propertyAge > 10) {
            return 'recommended';
        } else {
            return 'consider';
        }
    }

    private function assessProfessionalStatusSuitability(array $situation): string
    {
        $rentalLosses = $situation['rental_losses'] ?? 0;
        $otherIncome = $situation['other_income'] ?? 0;
        $timeAvailable = $situation['time_available'] ?? 0;
        
        if ($rentalLosses > 25000 && $otherIncome > 100000 && $timeAvailable > 750) {
            return 'highly_recommended';
        } elseif ($rentalLosses > 0 && $otherIncome > 50000) {
            return 'consider';
        } else {
            return 'not_applicable';
        }
    }

    private function prioritizeStrategies(array $strategies, array $goals): array
    {
        $prioritized = $strategies;
        
        // Sort by potential savings and suitability
        usort($prioritized, function($a, $b) {
            $scoreA = $this->calculateStrategyScore($a);
            $scoreB = $this->calculateStrategyScore($b);
            return $scoreB <=> $scoreA;
        });
        
        return $prioritized;
    }

    private function calculateStrategyScore(array $strategy): float
    {
        $suitabilityScore = $this->getSuitabilityScore($strategy['suitability']);
        $savingsScore = $this->estimateSavingsScore($strategy['potential_savings']);
        $paybackScore = $this->getPaybackScore($strategy['payback_period']);
        
        return ($suitabilityScore * 0.4) + ($savingsScore * 0.4) + ($paybackScore * 0.2);
    }

    private function getSuitabilityScore(string $suitability): float
    {
        $scores = [
            'highly_recommended' => 10,
            'recommended' => 8,
            'consider' => 6,
            'not_applicable' => 0
        ];
        
        return $scores[$suitability] ?? 0;
    }

    private function estimateSavingsScore(string $savings): float
    {
        // Extract numeric value from savings string
        preg_match('/\$?([\d,]+)/', $savings, $matches);
        if (isset($matches[1])) {
            $value = (int)str_replace(',', '', $matches[1]);
            return min(10, $value / 20000); // Max score for $200k+ savings
        }
        
        return 5; // Default score
    }

    private function getPaybackScore(string $payback): float
    {
        if (strpos($payback, 'Immediate') !== false || strpos($payback, '1 year') !== false) {
            return 10;
        } elseif (strpos($payback, '2-3') !== false) {
            return 8;
        } elseif (strpos($payback, '3-7') !== false) {
            return 6;
        } else {
            return 4;
        }
    }

    private function createImplementationRoadmap(array $strategies): array
    {
        $roadmap = [];
        $currentQuarter = Carbon::now()->quarter;
        
        foreach ($strategies as $index => $strategy) {
            $quarter = ($currentQuarter + $index) % 4 + 1;
            $year = Carbon::now()->year + floor(($currentQuarter + $index) / 4);
            
            $roadmap[] = [
                'strategy' => $strategy['strategy'],
                'timeline' => "Q{$quarter} {$year}",
                'priority' => $index < 2 ? 'high' : ($index < 4 ? 'medium' : 'low'),
                'dependencies' => $this->getStrategyDependencies($strategy['strategy']),
                'estimated_duration' => $this->getImplementationDuration($strategy['strategy'])
            ];
        }
        
        return $roadmap;
    }

    private function getStrategyDependencies(string $strategy): array
    {
        $dependencies = [
            'Cost Segregation Study' => ['Property appraisal', 'Engineering report'],
            '1031 Exchange' => ['Property identification', 'Qualified intermediary'],
            'Opportunity Zone Investment' => ['Capital gains identification', 'Zone research'],
            'Energy Efficiency Upgrades' => ['Energy audit', 'Contractor selection'],
            'Real Estate Professional Status' => ['Time tracking', 'Documentation']
        ];
        
        return $dependencies[$strategy] ?? [];
    }

    private function getImplementationDuration(string $strategy): string
    {
        $durations = [
            'Cost Segregation Study' => '2-3 months',
            '1031 Exchange' => '6 months (identification period)',
            'Opportunity Zone Investment' => '3-6 months',
            'Energy Efficiency Upgrades' => '1-3 months',
            'Real Estate Professional Status' => 'Ongoing'
        ];
        
        return $durations[$strategy] ?? 'Varies';
    }

    private function calculateTotalSavings(array $strategies): array
    {
        $totalSavings = 0;
        $totalCosts = 0;
        
        foreach ($strategies as $strategy) {
            $savings = $this->extractNumericSavings($strategy['potential_savings']);
            $costs = $this->extractNumericCost($strategy['implementation_cost']);
            
            $totalSavings += $savings;
            $totalCosts += $costs;
        }
        
        return [
            'total_potential_savings' => $totalSavings,
            'total_implementation_costs' => $totalCosts,
            'net_savings' => $totalSavings - $totalCosts,
            'roi_on_optimization' => $totalCosts > 0 ? (($totalSavings - $totalCosts) / $totalCosts) * 100 : 0
        ];
    }

    private function extractNumericSavings(string $savings): float
    {
        preg_match('/\$?([\d,]+)/', $savings, $matches);
        if (isset($matches[1])) {
            return (float)str_replace(',', '', $matches[1]);
        }
        return 0;
    }

    private function extractNumericCost(string $cost): float
    {
        preg_match('/\$?([\d,]+)/', $cost, $matches);
        if (isset($matches[1])) {
            return (float)str_replace(',', '', $matches[1]);
        }
        return 0;
    }
}

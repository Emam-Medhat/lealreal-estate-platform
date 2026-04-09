<?php

namespace App\Http\Controllers;

use App\Models\RoiCalculation;
use App\Models\PropertyFinancialAnalysis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class RoiCalculatorController extends Controller
{
    public function index(): View
    {
        return view('financial.roi-calculator.index');
    }

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'purchase_price' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0',
            'loan_amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'loan_term' => 'required|integer|min:1|max:30',
            'monthly_rent' => 'required|numeric|min:0',
            'vacancy_rate' => 'required|numeric|min:0|max:100',
            'monthly_expenses' => 'required|numeric|min:0',
            'property_appreciation' => 'required|numeric|min:0|max:100',
            'selling_costs' => 'required|numeric|min:0|max:100',
            'holding_period' => 'required|integer|min:1|max:30',
            'income_tax_rate' => 'required|numeric|min:0|max:100',
            'capital_gains_tax' => 'required|numeric|min:0|max:100'
        ]);

        $roiData = $this->performRoiCalculation($validated);

        return response()->json([
            'success' => true,
            'data' => $roiData
        ]);
    }

    public function advanced(): View
    {
        return view('financial.roi-calculator.advanced');
    }

    public function compare(): View
    {
        return view('financial.roi-calculator.compare');
    }

    public function saveCalculation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_financial_analysis_id' => 'required|exists:property_financial_analyses,id',
            'calculation_data' => 'required|array',
            'roi_percentage' => 'required|numeric',
            'type' => 'required|in:initial,projected,actual'
        ]);

        $roiCalculation = RoiCalculation::create([
            'property_financial_analysis_id' => $validated['property_financial_analysis_id'],
            'total_investment' => $validated['calculation_data']['total_investment'],
            'annual_income' => $validated['calculation_data']['annual_income'],
            'annual_expenses' => $validated['calculation_data']['annual_expenses'],
            'net_income' => $validated['calculation_data']['net_income'],
            'roi_percentage' => $validated['roi_percentage'],
            'type' => $validated['type'],
            'calculated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ حساب العائد على الاستثمار بنجاح',
            'roi_calculation' => $roiCalculation
        ]);
    }

    public function scenarios(PropertyFinancialAnalysis $analysis): View
    {
        $scenarios = $this->generateRoiScenarios($analysis);
        
        return view('financial.roi-calculator.scenarios', compact('analysis', 'scenarios'));
    }

    public function export(PropertyFinancialAnalysis $analysis)
    {
        $roiCalculations = $analysis->roiCalculations()->latest()->get();
        
        return response()->json([
            'analysis_id' => $analysis->id,
            'roi_calculations' => $roiCalculations,
            'exported_at' => now()
        ]);
    }

    private function performRoiCalculation(array $data): array
    {
        $purchasePrice = $data['purchase_price'];
        $downPayment = $data['down_payment'];
        $loanAmount = $data['loan_amount'];
        $interestRate = $data['interest_rate'] / 100 / 12; // Monthly rate
        $loanTermMonths = $data['loan_term'] * 12;
        $monthlyRent = $data['monthly_rent'];
        $vacancyRate = $data['vacancy_rate'] / 100;
        $monthlyExpenses = $data['monthly_expenses'];
        $appreciationRate = $data['property_appreciation'] / 100;
        $sellingCosts = $data['selling_costs'] / 100;
        $holdingPeriod = $data['holding_period'];
        $incomeTaxRate = $data['income_tax_rate'] / 100;
        $capitalGainsTax = $data['capital_gains_tax'] / 100;

        // Calculate monthly mortgage payment
        $monthlyPayment = $this->calculateMortgagePayment($loanAmount, $interestRate, $loanTermMonths);

        // Calculate annual cash flows
        $annualCashFlows = [];
        $cumulativeCashFlow = 0;
        $propertyValue = $purchasePrice;

        for ($year = 1; $year <= $holdingPeriod; $year++) {
            $effectiveRent = $monthlyRent * 12 * (1 - $vacancyRate);
            $totalExpenses = ($monthlyExpenses + $monthlyPayment) * 12;
            $netOperatingIncome = $effectiveRent - $totalExpenses;
            
            // Apply income tax
            $taxableIncome = max(0, $netOperatingIncome);
            $incomeTax = $taxableIncome * $incomeTaxRate;
            $afterTaxCashFlow = $netOperatingIncome - $incomeTax;
            
            $cumulativeCashFlow += $afterTaxCashFlow;
            
            // Update property value
            $propertyValue *= (1 + $appreciationRate);
            
            $annualCashFlows[] = [
                'year' => $year,
                'gross_income' => $effectiveRent,
                'total_expenses' => $totalExpenses,
                'net_income' => $netOperatingIncome,
                'income_tax' => $incomeTax,
                'after_tax_cash_flow' => $afterTaxCashFlow,
                'cumulative_cash_flow' => $cumulativeCashFlow,
                'property_value' => $propertyValue
            ];
        }

        // Calculate sale proceeds
        $salePrice = $propertyValue;
        $sellingCostsAmount = $salePrice * $sellingCosts;
        $netSalePrice = $salePrice - $sellingCostsAmount;
        
        // Calculate capital gains
        $capitalGain = $netSalePrice - $purchasePrice;
        $capitalGainsTaxAmount = max(0, $capitalGain) * $capitalGainsTax;
        $afterTaxSaleProceeds = $netSalePrice - $capitalGainsTaxAmount;

        // Total ROI calculation
        $totalInvestment = $downPayment + $cumulativeCashFlow;
        $totalReturn = $afterTaxSaleProceeds + $cumulativeCashFlow;
        $totalRoi = $totalInvestment > 0 ? (($totalReturn - $totalInvestment) / $totalInvestment) * 100 : 0;
        
        // Annualized ROI
        $annualizedRoi = $holdingPeriod > 0 ? (pow(1 + ($totalRoi / 100), 1 / $holdingPeriod) - 1) * 100 : 0;

        // Cash on Cash ROI
        $cashOnCashRoi = $downPayment > 0 ? ($annualCashFlows[0]['after_tax_cash_flow'] / $downPayment) * 100 : 0;

        return [
            'input_data' => $data,
            'monthly_payment' => $monthlyPayment,
            'annual_cash_flows' => $annualCashFlows,
            'sale_analysis' => [
                'sale_price' => $salePrice,
                'selling_costs' => $sellingCostsAmount,
                'net_sale_price' => $netSalePrice,
                'capital_gain' => $capitalGain,
                'capital_gains_tax' => $capitalGainsTaxAmount,
                'after_tax_proceeds' => $afterTaxSaleProceeds
            ],
            'roi_metrics' => [
                'total_investment' => $totalInvestment,
                'total_return' => $totalReturn,
                'total_roi_percentage' => $totalRoi,
                'annualized_roi' => $annualizedRoi,
                'cash_on_cash_roi' => $cashOnCashRoi,
                'payback_period_years' => $this->calculatePaybackPeriod($annualCashFlows, $downPayment)
            ],
            'profitability_metrics' => [
                'net_present_value' => $this->calculateNPV($annualCashFlows, 0.08), // 8% discount rate
                'internal_rate_of_return' => $this->calculateIRR($annualCashFlows, $downPayment),
                'profitability_index' => $this->calculateProfitabilityIndex($annualCashFlows, $downPayment)
            ]
        ];
    }

    private function calculateMortgagePayment(float $loanAmount, float $monthlyRate, int $termMonths): float
    {
        if ($monthlyRate == 0) {
            return $loanAmount / $termMonths;
        }
        
        return $loanAmount * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / 
               (pow(1 + $monthlyRate, $termMonths) - 1);
    }

    private function calculatePaybackPeriod(array $cashFlows, float $investment): ?float
    {
        $cumulativeCashFlow = 0;
        
        foreach ($cashFlows as $index => $cashFlow) {
            $cumulativeCashFlow += $cashFlow['after_tax_cash_flow'];
            
            if ($cumulativeCashFlow >= $investment) {
                // Linear interpolation for exact payback period
                $previousCumulative = $cumulativeCashFlow - $cashFlow['after_tax_cash_flow'];
                $remaining = $investment - $previousCumulative;
                $fraction = $remaining / $cashFlow['after_tax_cash_flow'];
                
                return $index + 1 + $fraction;
            }
        }
        
        return null; // Investment never recovered
    }

    private function calculateNPV(array $cashFlows, float $discountRate): float
    {
        $npv = 0;
        
        foreach ($cashFlows as $index => $cashFlow) {
            $year = $index + 1;
            $npv += $cashFlow['after_tax_cash_flow'] / pow(1 + $discountRate, $year);
        }
        
        return $npv;
    }

    private function calculateIRR(array $cashFlows, float $initialInvestment): float
    {
        // Simplified IRR calculation using Newton-Raphson method
        $rate = 0.1; // Initial guess
        $iterations = 0;
        $maxIterations = 100;
        $tolerance = 0.0001;
        
        while ($iterations < $maxIterations) {
            $npv = -$initialInvestment;
            $dnpv = 0;
            
            foreach ($cashFlows as $index => $cashFlow) {
                $year = $index + 1;
                $factor = pow(1 + $rate, $year);
                $npv += $cashFlow['after_tax_cash_flow'] / $factor;
                $dnpv -= $year * $cashFlow['after_tax_cash_flow'] / ($factor * (1 + $rate));
            }
            
            if (abs($npv) < $tolerance) {
                break;
            }
            
            $rate = $rate - $npv / $dnpv;
            $iterations++;
        }
        
        return $rate * 100; // Return as percentage
    }

    private function calculateProfitabilityIndex(array $cashFlows, float $initialInvestment): float
    {
        $npv = $this->calculateNPV($cashFlows, 0.08);
        return $initialInvestment > 0 ? ($npv + $initialInvestment) / $initialInvestment : 0;
    }

    private function generateRoiScenarios(PropertyFinancialAnalysis $analysis): array
    {
        $baseData = [
            'purchase_price' => $analysis->purchase_price,
            'monthly_rent' => $analysis->rental_income,
            'monthly_expenses' => $analysis->operating_expenses,
            'vacancy_rate' => $analysis->vacancy_rate,
            'property_appreciation' => $analysis->appreciation_rate
        ];

        $scenarios = [];

        // Best case scenario
        $bestCase = $baseData;
        $bestCase['monthly_rent'] *= 1.2; // 20% higher rent
        $bestCase['monthly_expenses'] *= 0.9; // 10% lower expenses
        $bestCase['vacancy_rate'] *= 0.5; // 50% lower vacancy
        $bestCase['property_appreciation'] *= 1.5; // 50% higher appreciation
        $scenarios['best_case'] = $this->performRoiCalculation($bestCase);

        // Worst case scenario
        $worstCase = $baseData;
        $worstCase['monthly_rent'] *= 0.8; // 20% lower rent
        $worstCase['monthly_expenses'] *= 1.2; // 20% higher expenses
        $worstCase['vacancy_rate'] *= 1.5; // 50% higher vacancy
        $worstCase['property_appreciation'] *= 0.5; // 50% lower appreciation
        $scenarios['worst_case'] = $this->performRoiCalculation($worstCase);

        // Conservative scenario
        $conservativeCase = $baseData;
        $conservativeCase['monthly_rent'] *= 1.05; // 5% higher rent
        $conservativeCase['monthly_expenses'] *= 1.05; // 5% higher expenses
        $conservativeCase['vacancy_rate'] *= 1.1; // 10% higher vacancy
        $conservativeCase['property_appreciation'] *= 0.8; // 20% lower appreciation
        $scenarios['conservative'] = $this->performRoiCalculation($conservativeCase);

        return $scenarios;
    }
}

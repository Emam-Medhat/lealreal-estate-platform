<?php

namespace App\Http\Controllers;

use App\Models\CashFlowProjection;
use App\Models\PropertyFinancialAnalysis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class CashFlowAnalysisController extends Controller
{
    public function index(): View
    {
        return view('financial.cash-flow-analysis.index');
    }

    public function analyze(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'purchase_price' => 'required|numeric|min:0',
            'monthly_rent' => 'required|numeric|min:0',
            'vacancy_rate' => 'required|numeric|min:0|max:100',
            'operating_expenses' => 'required|array',
            'operating_expenses.*.name' => 'required|string',
            'operating_expenses.*.amount' => 'required|numeric|min:0',
            'capital_expenditures' => 'required|array',
            'capital_expenditures.*.year' => 'required|integer|min:1',
            'capital_expenditures.*.amount' => 'required|numeric|min:0',
            'rent_increase_rate' => 'required|numeric|min:0|max:100',
            'expense_increase_rate' => 'required|numeric|min:0|max:100',
            'analysis_period' => 'required|integer|min:1|max:30',
            'include_mortgage' => 'boolean',
            'loan_amount' => 'required_if:include_mortgage,true|numeric|min:0',
            'interest_rate' => 'required_if:include_mortgage,true|numeric|min:0|max:100',
            'loan_term' => 'required_if:include_mortgage,true|integer|min:1|max:30'
        ]);

        $cashFlowData = $this->performCashFlowAnalysis($validated);

        return response()->json([
            'success' => true,
            'data' => $cashFlowData
        ]);
    }

    public function detailed(PropertyFinancialAnalysis $analysis): View
    {
        $projections = $analysis->cashFlowProjections()
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->groupBy('year');

        $summary = $this->generateCashFlowSummary($analysis);
        $metrics = $this->calculateCashFlowMetrics($analysis);

        return view('financial.cash-flow-analysis.detailed', compact(
            'analysis',
            'projections',
            'summary',
            'metrics'
        ));
    }

    public function compare(): View
    {
        return view('financial.cash-flow-analysis.compare');
    }

    public function saveProjection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_financial_analysis_id' => 'required|exists:property_financial_analyses,id',
            'year' => 'required|integer|min:1|max:30',
            'month' => 'required|integer|min:1|max:12',
            'projected_income' => 'required|numeric',
            'projected_expenses' => 'required|numeric',
            'net_cash_flow' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);

        $projection = CashFlowProjection::updateOrCreate(
            [
                'property_financial_analysis_id' => $validated['property_financial_analysis_id'],
                'year' => $validated['year'],
                'month' => $validated['month']
            ],
            [
                'projected_income' => $validated['projected_income'],
                'projected_expenses' => $validated['projected_expenses'],
                'net_cash_flow' => $validated['net_cash_flow'],
                'notes' => $validated['notes']
            ]
        );

        // Update cumulative cash flow
        $this->updateCumulativeCashFlow($projection);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ توقع التدفق النقدي بنجاح',
            'projection' => $projection
        ]);
    }

    public function sensitivity(PropertyFinancialAnalysis $analysis): View
    {
        $sensitivityData = $this->performSensitivityAnalysis($analysis);
        
        return view('financial.cash-flow-analysis.sensitivity', compact(
            'analysis',
            'sensitivityData'
        ));
    }

    public function export(PropertyFinancialAnalysis $analysis)
    {
        $projections = $analysis->cashFlowProjections()
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return response()->json([
            'analysis_id' => $analysis->id,
            'projections' => $projections,
            'summary' => $this->generateCashFlowSummary($analysis),
            'exported_at' => now()
        ]);
    }

    private function performCashFlowAnalysis(array $data): array
    {
        $purchasePrice = $data['purchase_price'];
        $monthlyRent = $data['monthly_rent'];
        $vacancyRate = $data['vacancy_rate'] / 100;
        $operatingExpenses = collect($data['operating_expenses']);
        $capitalExpenditures = collect($data['capital_expenditures']);
        $rentIncreaseRate = $data['rent_increase_rate'] / 100;
        $expenseIncreaseRate = $data['expense_increase_rate'] / 100;
        $analysisPeriod = $data['analysis_period'];
        $includeMortgage = $data['include_mortgage'] ?? false;

        $monthlyOperatingExpenses = $operatingExpenses->sum('amount');
        $monthlyMortgagePayment = 0;

        if ($includeMortgage) {
            $loanAmount = $data['loan_amount'];
            $interestRate = $data['interest_rate'] / 100 / 12;
            $loanTermMonths = $data['loan_term'] * 12;
            $monthlyMortgagePayment = $this->calculateMortgagePayment($loanAmount, $interestRate, $loanTermMonths);
        }

        $monthlyProjections = [];
        $annualSummaries = [];
        $cumulativeCashFlow = 0;

        for ($year = 1; $year <= $analysisPeriod; $year++) {
            $yearlyIncome = 0;
            $yearlyExpenses = 0;
            $yearlyNetCashFlow = 0;
            $yearlyCapitalExpenditures = 0;

            for ($month = 1; $month <= 12; $month++) {
                $totalMonths = ($year - 1) * 12 + $month - 1;
                
                // Calculate projected rent with increases
                $projectedRent = $monthlyRent * pow(1 + $rentIncreaseRate, $totalMonths / 12);
                $effectiveRent = $projectedRent * (1 - $vacancyRate);
                
                // Calculate projected expenses with increases
                $projectedOperatingExpenses = $monthlyOperatingExpenses * pow(1 + $expenseIncreaseRate, $totalMonths / 12);
                
                // Calculate capital expenditures for this month
                $monthlyCapEx = $this->getMonthlyCapitalExpenditure($capitalExpenditures, $year, $month);
                
                // Total monthly expenses
                $totalMonthlyExpenses = $projectedOperatingExpenses + $monthlyMortgagePayment + $monthlyCapEx;
                
                // Net monthly cash flow
                $netMonthlyCashFlow = $effectiveRent - $totalMonthlyExpenses;
                $cumulativeCashFlow += $netMonthlyCashFlow;

                $monthlyProjections[] = [
                    'year' => $year,
                    'month' => $month,
                    'gross_rent' => $projectedRent,
                    'effective_rent' => $effectiveRent,
                    'operating_expenses' => $projectedOperatingExpenses,
                    'mortgage_payment' => $monthlyMortgagePayment,
                    'capital_expenditure' => $monthlyCapEx,
                    'total_expenses' => $totalMonthlyExpenses,
                    'net_cash_flow' => $netMonthlyCashFlow,
                    'cumulative_cash_flow' => $cumulativeCashFlow
                ];

                $yearlyIncome += $effectiveRent;
                $yearlyExpenses += $totalMonthlyExpenses;
                $yearlyNetCashFlow += $netMonthlyCashFlow;
                $yearlyCapitalExpenditures += $monthlyCapEx;
            }

            $annualSummaries[] = [
                'year' => $year,
                'total_income' => $yearlyIncome,
                'total_expenses' => $yearlyExpenses,
                'net_cash_flow' => $yearlyNetCashFlow,
                'capital_expenditures' => $yearlyCapitalExpenditures,
                'cash_on_cash_return' => $purchasePrice > 0 ? ($yearlyNetCashFlow / $purchasePrice) * 100 : 0,
                'cumulative_cash_flow' => $cumulativeCashFlow
            ];
        }

        return [
            'monthly_projections' => $monthlyProjections,
            'annual_summaries' => $annualSummaries,
            'summary_metrics' => [
                'total_cash_flow' => $cumulativeCashFlow,
                'average_monthly_cash_flow' => $cumulativeCashFlow / ($analysisPeriod * 12),
                'average_annual_cash_flow' => $cumulativeCashFlow / $analysisPeriod,
                'best_year' => collect($annualSummaries)->max('net_cash_flow'),
                'worst_year' => collect($annualSummaries)->min('net_cash_flow'),
                'payback_period' => $this->calculatePaybackPeriod($annualSummaries, $purchasePrice),
                'total_return_on_investment' => $purchasePrice > 0 ? ($cumulativeCashFlow / $purchasePrice) * 100 : 0
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

    private function getMonthlyCapitalExpenditure($capitalExpenditures, int $year, int $month): float
    {
        $capEx = $capitalExpenditures->firstWhere('year', $year);
        return $capEx ? $capEx['amount'] / 12 : 0; // Spread annual capex evenly across months
    }

    private function calculatePaybackPeriod(array $annualSummaries, float $investment): ?float
    {
        $cumulativeCashFlow = 0;
        
        foreach ($annualSummaries as $index => $summary) {
            $cumulativeCashFlow += $summary['net_cash_flow'];
            
            if ($cumulativeCashFlow >= $investment) {
                return $index + 1; // Years to payback
            }
        }
        
        return null; // Investment never recovered
    }

    private function generateCashFlowSummary(PropertyFinancialAnalysis $analysis): array
    {
        $projections = $analysis->cashFlowProjections;
        
        return [
            'total_months' => $projections->count(),
            'total_cash_flow' => $projections->sum('net_cash_flow'),
            'average_monthly_cash_flow' => $projections->avg('net_cash_flow'),
            'positive_months' => $projections->where('net_cash_flow', '>', 0)->count(),
            'negative_months' => $projections->where('net_cash_flow', '<', 0)->count(),
            'best_month' => $projections->max('net_cash_flow'),
            'worst_month' => $projections->min('net_cash_flow'),
            'total_income' => $projections->sum('projected_income'),
            'total_expenses' => $projections->sum('projected_expenses'),
            'net_present_value' => $this->calculateNPV($projections, 0.08),
            'internal_rate_of_return' => $this->calculateIRR($projections)
        ];
    }

    private function calculateCashFlowMetrics(PropertyFinancialAnalysis $analysis): array
    {
        $projections = $analysis->cashFlowProjections;
        $annualProjections = $projections->groupBy('year');
        
        $metrics = [];
        
        foreach ($annualProjections as $year => $yearProjections) {
            $yearlyIncome = $yearProjections->sum('projected_income');
            $yearlyExpenses = $yearProjections->sum('projected_expenses');
            $yearlyNetCashFlow = $yearProjections->sum('net_cash_flow');
            
            $metrics[] = [
                'year' => $year,
                'income' => $yearlyIncome,
                'expenses' => $yearlyExpenses,
                'net_cash_flow' => $yearlyNetCashFlow,
                'cash_on_cash_return' => $analysis->purchase_price > 0 ? ($yearlyNetCashFlow / $analysis->purchase_price) * 100 : 0,
                'expense_ratio' => $yearlyIncome > 0 ? ($yearlyExpenses / $yearlyIncome) * 100 : 0,
                'operating_efficiency' => $yearlyExpenses > 0 ? ($yearlyIncome / $yearlyExpenses) : 0
            ];
        }
        
        return $metrics;
    }

    private function performSensitivityAnalysis(PropertyFinancialAnalysis $analysis): array
    {
        $baseData = [
            'purchase_price' => $analysis->purchase_price,
            'monthly_rent' => $analysis->rental_income,
            'operating_expenses' => $analysis->operating_expenses,
            'vacancy_rate' => $analysis->vacancy_rate,
            'appreciation_rate' => $analysis->appreciation_rate
        ];

        $scenarios = [];
        $variables = ['monthly_rent', 'operating_expenses', 'vacancy_rate'];
        $variations = [-20, -10, 0, 10, 20]; // Percentage variations

        foreach ($variables as $variable) {
            $scenarioResults = [];
            
            foreach ($variations as $variation) {
                $scenarioData = $baseData;
                $scenarioData[$variable] = $baseData[$variable] * (1 + $variation / 100);
                
                $result = $this->performCashFlowAnalysis($scenarioData);
                $scenarioResults[] = [
                    'variation' => $variation,
                    'total_cash_flow' => $result['summary_metrics']['total_cash_flow'],
                    'average_monthly_cash_flow' => $result['summary_metrics']['average_monthly_cash_flow'],
                    'total_roi' => $result['summary_metrics']['total_return_on_investment']
                ];
            }
            
            $scenarios[$variable] = $scenarioResults;
        }

        return $scenarios;
    }

    private function calculateNPV($projections, float $discountRate): float
    {
        $npv = 0;
        
        foreach ($projections as $index => $projection) {
            $month = $index + 1;
            $npv += $projection->net_cash_flow / pow(1 + ($discountRate / 12), $month);
        }
        
        return $npv;
    }

    private function calculateIRR($projections): float
    {
        // Simplified IRR calculation
        $rate = 0.1; // Initial guess
        $iterations = 0;
        $maxIterations = 100;
        $tolerance = 0.0001;
        
        while ($iterations < $maxIterations) {
            $npv = 0;
            $dnpv = 0;
            
            foreach ($projections as $index => $projection) {
                $month = $index + 1;
                $factor = pow(1 + $rate, $month / 12);
                $npv += $projection->net_cash_flow / $factor;
                $dnpv -= ($month / 12) * $projection->net_cash_flow / ($factor * (1 + $rate));
            }
            
            if (abs($npv) < $tolerance) {
                break;
            }
            
            $rate = $rate - $npv / $dnpv;
            $iterations++;
        }
        
        return $rate * 100; // Return as percentage
    }

    private function updateCumulativeCashFlow(CashFlowProjection $projection): void
    {
        $analysis = $projection->propertyFinancialAnalysis;
        $previousProjections = $analysis->cashFlowProjections()
            ->where(function($query) use ($projection) {
                $query->where('year', '<', $projection->year)
                      ->orWhere(function($q) use ($projection) {
                          $q->where('year', $projection->year)->where('month', '<', $projection->month);
                      });
            })
            ->sum('net_cash_flow');

        $projection->update([
            'cumulative_cash_flow' => $previousProjections + $projection->net_cash_flow
        ]);
    }
}

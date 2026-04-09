<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MortgageCalculatorController extends Controller
{
    public function index()
    {
        return view('payments.calculator.index');
    }

    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'property_price' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0|max:property_price',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'loan_term' => 'required|integer|min:1|max:40',
            'property_tax' => 'nullable|numeric|min:0',
            'insurance' => 'nullable|numeric|min:0',
            'hoa_fees' => 'nullable|numeric|min:0',
            'pmi' => 'nullable|numeric|min:0',
            'loan_type' => 'required|in:fixed,variable,adjustable',
            'amortization_type' => 'required|in:standard,interest_only,balloon',
        ]);

        try {
            $propertyPrice = $request->property_price;
            $downPayment = $request->down_payment;
            $loanAmount = $propertyPrice - $downPayment;
            $interestRate = $request->interest_rate;
            $loanTermYears = $request->loan_term;
            $loanTermMonths = $loanTermYears * 12;
            
            // Calculate monthly interest rate
            $monthlyRate = $interestRate / 100 / 12;
            
            // Calculate monthly payment
            if ($request->amortization_type === 'interest_only') {
                $monthlyPayment = $loanAmount * $monthlyRate;
            } elseif ($request->amortization_type === 'balloon') {
                // Interest only with balloon payment
                $monthlyPayment = $loanAmount * $monthlyRate;
                $balloonPayment = $loanAmount; // Full principal at end
            } else {
                // Standard amortization
                if ($monthlyRate == 0) {
                    $monthlyPayment = $loanAmount / $loanTermMonths;
                } else {
                    $monthlyPayment = $loanAmount * ($monthlyRate * pow(1 + $monthlyRate, $loanTermMonths)) / 
                                   (pow(1 + $monthlyRate, $loanTermMonths) - 1);
                }
            }
            
            // Calculate PMI if down payment < 20%
            $pmiMonthly = 0;
            $downPaymentPercentage = ($downPayment / $propertyPrice) * 100;
            if ($downPaymentPercentage < 20) {
                $pmiRate = 0.005; // 0.5% annually
                $pmiMonthly = ($loanAmount * $pmiRate) / 12;
            }
            
            // Total monthly payment
            $totalMonthlyPayment = $monthlyPayment + 
                                 ($request->property_tax ?? 0) + 
                                 ($request->insurance ?? 0) + 
                                 ($request->hoa_fees ?? 0) + 
                                 $pmiMonthly;
            
            // Calculate totals
            $totalPayment = $totalMonthlyPayment * $loanTermMonths;
            $totalInterest = $totalPayment - $loanAmount;
            $totalPropertyTax = ($request->property_tax ?? 0) * $loanTermMonths;
            $totalInsurance = ($request->insurance ?? 0) * $loanTermMonths;
            $totalHOA = ($request->hoa_fees ?? 0) * $loanTermMonths;
            $totalPMI = $pmiMonthly * $loanTermMonths;
            
            // Generate amortization schedule
            $amortizationSchedule = $this->generateAmortizationSchedule(
                $loanAmount, $monthlyRate, $monthlyPayment, $loanTermMonths, $request->amortization_type
            );
            
            $results = [
                'loan_details' => [
                    'property_price' => $propertyPrice,
                    'down_payment' => $downPayment,
                    'down_payment_percentage' => round($downPaymentPercentage, 2),
                    'loan_amount' => round($loanAmount, 2),
                    'loan_to_value' => round(($loanAmount / $propertyPrice) * 100, 2),
                    'interest_rate' => $interestRate,
                    'loan_term_years' => $loanTermYears,
                    'loan_term_months' => $loanTermMonths,
                    'loan_type' => $request->loan_type,
                    'amortization_type' => $request->amortization_type,
                ],
                'monthly_payments' => [
                    'principal_and_interest' => round($monthlyPayment, 2),
                    'property_tax' => round($request->property_tax ?? 0, 2),
                    'insurance' => round($request->insurance ?? 0, 2),
                    'hoa_fees' => round($request->hoa_fees ?? 0, 2),
                    'pmi' => round($pmiMonthly, 2),
                    'total_monthly_payment' => round($totalMonthlyPayment, 2),
                ],
                'total_costs' => [
                    'total_principal' => round($loanAmount, 2),
                    'total_interest' => round($totalInterest, 2),
                    'total_property_tax' => round($totalPropertyTax, 2),
                    'total_insurance' => round($totalInsurance, 2),
                    'total_hoa' => round($totalHOA, 2),
                    'total_pmi' => round($totalPMI, 2),
                    'total_payment' => round($totalPayment, 2),
                ],
                'amortization_schedule' => $amortizationSchedule,
            ];
            
            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => 'Mortgage calculation completed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating mortgage: ' . $e->getMessage()
            ], 500);
        }
    }

    public function compareLoans(Request $request): JsonResponse
    {
        $request->validate([
            'scenarios' => 'required|array|min:2|max:5',
            'scenarios.*.name' => 'required|string|max:100',
            'scenarios.*.property_price' => 'required|numeric|min:0',
            'scenarios.*.down_payment' => 'required|numeric|min:0|max:scenarios.*.property_price',
            'scenarios.*.interest_rate' => 'required|numeric|min:0|max:100',
            'scenarios.*.loan_term' => 'required|integer|min:1|max:40',
        ]);

        try {
            $comparisons = [];
            
            foreach ($request->scenarios as $index => $scenario) {
                $propertyPrice = $scenario['property_price'];
                $downPayment = $scenario['down_payment'];
                $loanAmount = $propertyPrice - $downPayment;
                $monthlyRate = $scenario['interest_rate'] / 100 / 12;
                $loanTermMonths = $scenario['loan_term'] * 12;
                
                // Calculate monthly payment
                if ($monthlyRate == 0) {
                    $monthlyPayment = $loanAmount / $loanTermMonths;
                } else {
                    $monthlyPayment = $loanAmount * ($monthlyRate * pow(1 + $monthlyRate, $loanTermMonths)) / 
                                   (pow(1 + $monthlyRate, $loanTermMonths) - 1);
                }
                
                $totalPayment = $monthlyPayment * $loanTermMonths;
                $totalInterest = $totalPayment - $loanAmount;
                
                $comparisons[] = [
                    'name' => $scenario['name'],
                    'loan_amount' => round($loanAmount, 2),
                    'monthly_payment' => round($monthlyPayment, 2),
                    'total_interest' => round($totalInterest, 2),
                    'total_payment' => round($totalPayment, 2),
                    'interest_rate' => $scenario['interest_rate'],
                    'loan_term' => $scenario['loan_term'],
                    'down_payment_percentage' => round(($downPayment / $propertyPrice) * 100, 2),
                ];
            }
            
            // Sort by monthly payment
            usort($comparisons, function ($a, $b) {
                return $a['monthly_payment'] <=> $b['monthly_payment'];
            });
            
            return response()->json([
                'success' => true,
                'comparisons' => $comparisons,
                'best_monthly_payment' => $comparisons[0],
                'message' => 'Loan comparison completed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error comparing loans: ' . $e->getMessage()
            ], 500);
        }
    }

    public function refinanceCalculator(Request $request): JsonResponse
    {
        $request->validate([
            'current_loan_balance' => 'required|numeric|min:0',
            'current_interest_rate' => 'required|numeric|min:0|max:100',
            'current_monthly_payment' => 'required|numeric|min:0',
            'new_interest_rate' => 'required|numeric|min:0|max:100',
            'new_loan_term' => 'required|integer|min:1|max:40',
            'closing_costs' => 'nullable|numeric|min:0',
        ]);

        try {
            $currentBalance = $request->current_loan_balance;
            $currentRate = $request->current_interest_rate;
            $currentPayment = $request->current_monthly_payment;
            $newRate = $request->new_interest_rate;
            $newTerm = $request->new_loan_term * 12;
            $closingCosts = $request->closing_costs ?? 0;
            
            // Calculate new monthly payment
            $newMonthlyRate = $newRate / 100 / 12;
            if ($newMonthlyRate == 0) {
                $newMonthlyPayment = ($currentBalance + $closingCosts) / $newTerm;
            } else {
                $newMonthlyPayment = ($currentBalance + $closingCosts) * ($newMonthlyRate * pow(1 + $newMonthlyRate, $newTerm)) / 
                                   (pow(1 + $newMonthlyRate, $newTerm) - 1);
            }
            
            // Calculate remaining payments on current loan
            $currentMonthlyRate = $currentRate / 100 / 12;
            $remainingMonths = ceil(log(($currentPayment / ($currentPayment - $currentBalance * $currentMonthlyRate))) / log(1 + $currentMonthlyRate));
            
            $remainingPayments = $currentPayment * $remainingMonths;
            $remainingInterest = $remainingPayments - $currentBalance;
            
            // Calculate total payments for new loan
            $newTotalPayments = $newMonthlyPayment * $newTerm;
            $newTotalInterest = $newTotalPayments - ($currentBalance + $closingCosts);
            
            // Calculate savings
            $monthlySavings = $currentPayment - $newMonthlyPayment;
            $totalSavings = $remainingPayments - $newTotalPayments;
            $breakEvenMonths = $closingCosts > 0 ? ceil($closingCosts / $monthlySavings) : 0;
            
            $results = [
                'current_loan' => [
                    'balance' => $currentBalance,
                    'interest_rate' => $currentRate,
                    'monthly_payment' => $currentPayment,
                    'remaining_months' => $remainingMonths,
                    'remaining_payments' => round($remainingPayments, 2),
                    'remaining_interest' => round($remainingInterest, 2),
                ],
                'new_loan' => [
                    'balance' => $currentBalance + $closingCosts,
                    'interest_rate' => $newRate,
                    'loan_term' => $request->new_loan_term,
                    'monthly_payment' => round($newMonthlyPayment, 2),
                    'total_payments' => round($newTotalPayments, 2),
                    'total_interest' => round($newTotalInterest, 2),
                    'closing_costs' => $closingCosts,
                ],
                'savings' => [
                    'monthly_savings' => round($monthlySavings, 2),
                    'total_savings' => round($totalSavings, 2),
                    'break_even_months' => $breakEvenMonths,
                    'is_worthwhile' => $totalSavings > 0,
                ],
            ];
            
            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => 'Refinance calculation completed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating refinance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function affordabilityAnalysis(Request $request): JsonResponse
    {
        $request->validate([
            'annual_income' => 'required|numeric|min:0',
            'monthly_debts' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'property_tax_rate' => 'nullable|numeric|min:0|max:5',
            'insurance_rate' => 'nullable|numeric|min:0|max:5',
        ]);

        try {
            $annualIncome = $request->annual_income;
            $monthlyIncome = $annualIncome / 12;
            $monthlyDebts = $request->monthly_debts;
            $downPayment = $request->down_payment;
            $interestRate = $request->interest_rate;
            $propertyTaxRate = $request->property_tax_rate ?? 1.2; // Default 1.2%
            $insuranceRate = $request->insurance_rate ?? 0.5; // Default 0.5%
            
            // Calculate maximum affordable payment (28% DTI for housing)
            $maxHousingPayment = $monthlyIncome * 0.28;
            
            // Calculate maximum affordable payment including debts (43% total DTI)
            $maxTotalPayment = $monthlyIncome * 0.43;
            $maxHousingPaymentWithDebts = $maxTotalPayment - $monthlyDebts;
            
            // Use the more conservative figure
            $maxAffordablePayment = min($maxHousingPayment, $maxHousingPaymentWithDebts);
            
            // Calculate maximum loan amount
            $monthlyRate = $interestRate / 100 / 12;
            $loanTerm = 30; // Assume 30-year fixed rate for this calculation
            $numPayments = $loanTerm * 12;
            
            if ($monthlyRate > 0) {
                $maxLoanAmount = $maxAffordablePayment * (1 - pow(1 + $monthlyRate, -$numPayments)) / $monthlyRate;
            } else {
                $maxLoanAmount = $maxAffordablePayment * $numPayments;
            }
            
            // Calculate maximum property price
            $maxPropertyPrice = $maxLoanAmount + $downPayment;
            
            // Calculate estimated taxes and insurance
            $estimatedPropertyTax = ($maxPropertyPrice * $propertyTaxRate) / 12;
            $estimatedInsurance = ($maxPropertyPrice * $insuranceRate) / 12;
            
            // Recalculate with taxes and insurance
            $totalHousingCosts = $maxAffordablePayment + $estimatedPropertyTax + $estimatedInsurance;
            $actualDTI = ($totalHousingCosts + $monthlyDebts) / $monthlyIncome * 100;
            
            $analysis = [
                'income_analysis' => [
                    'annual_income' => $annualIncome,
                    'monthly_income' => round($monthlyIncome, 2),
                    'monthly_debts' => $monthlyDebts,
                    'available_for_housing' => round($maxHousingPaymentWithDebts, 2),
                ],
                'affordability' => [
                    'max_monthly_payment' => round($maxAffordablePayment, 2),
                    'max_loan_amount' => round($maxLoanAmount, 2),
                    'max_property_price' => round($maxPropertyPrice, 2),
                    'down_payment_percentage' => round(($downPayment / $maxPropertyPrice) * 100, 2),
                ],
                'costs_analysis' => [
                    'estimated_monthly_tax' => round($estimatedPropertyTax, 2),
                    'estimated_monthly_insurance' => round($estimatedInsurance, 2),
                    'total_monthly_housing' => round($totalHousingCosts, 2),
                    'dti_ratio' => round($actualDTI, 2),
                ],
                'recommendations' => [
                    'is_affordable' => $actualDTI <= 43,
                    'recommended_down_payment' => round($maxPropertyPrice * 0.20, 2), // 20% down payment
                    'recommended_property_price' => round($maxPropertyPrice * 0.85, 2), // 15% buffer
                ],
            ];
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'message' => 'Affordability analysis completed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing affordability: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateAmortizationSchedule($loanAmount, $monthlyRate, $monthlyPayment, $numPayments, $amortizationType)
    {
        $schedule = [];
        $remainingBalance = $loanAmount;
        
        for ($month = 1; $month <= $numPayments; $month++) {
            if ($remainingBalance <= 0) break;
            
            $interestPayment = $remainingBalance * $monthlyRate;
            $principalPayment = min($monthlyPayment - $interestPayment, $remainingBalance);
            
            if ($amortizationType === 'interest_only' && $month < $numPayments) {
                $principalPayment = 0;
            }
            
            $remainingBalance -= $principalPayment;
            
            $schedule[] = [
                'month' => $month,
                'payment' => round($monthlyPayment, 2),
                'principal' => round($principalPayment, 2),
                'interest' => round($interestPayment, 2),
                'balance' => round($remainingBalance, 2),
                'cumulative_interest' => round($monthlyPayment * $month - ($loanAmount - $remainingBalance), 2),
            ];
        }
        
        return $schedule;
    }
}

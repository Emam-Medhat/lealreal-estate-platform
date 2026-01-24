<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use App\Models\Property;
use Illuminate\Http\Request;

class TaxCalculatorController extends Controller
{
    public function __construct()
    {
        // Add middleware as needed
    }

    public function index()
    {
        return view('taxes.calculator.index');
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'property_value' => 'required|numeric|min:0',
            'property_type' => 'required|string',
            'location' => 'required|string',
            'ownership_type' => 'required|string',
        ]);

        $taxRate = $this->getApplicableTaxRate($request->property_type, $request->location);
        
        $baseTax = ($request->property_value * $taxRate->rate) / 100;
        
        $exemptions = $this->calculateExemptions($request);
        $totalExemption = array_sum($exemptions);
        
        $taxableAmount = max(0, $request->property_value - $totalExemption);
        $finalTax = ($taxableAmount * $taxRate->rate) / 100;

        return response()->json([
            'property_value' => $request->property_value,
            'tax_rate' => $taxRate->rate,
            'base_tax' => $baseTax,
            'exemptions' => $exemptions,
            'total_exemption' => $totalExemption,
            'taxable_amount' => $taxableAmount,
            'final_tax' => $finalTax,
            'payment_schedule' => $this->generatePaymentSchedule($finalTax),
        ]);
    }

    public function propertyTaxCalculator()
    {
        $properties = Property::all();
        $taxRates = TaxRate::all();

        return view('taxes.calculator.property', compact('properties', 'taxRates'));
    }

    public function capitalGainsCalculator()
    {
        return view('taxes.calculator.capital-gains');
    }

    public function vatCalculator()
    {
        return view('taxes.calculator.vat');
    }

    private function getApplicableTaxRate($propertyType, $location)
    {
        return TaxRate::where('property_type', $propertyType)
            ->where('location', $location)
            ->where('is_active', true)
            ->firstOrFail();
    }

    private function calculateExemptions($request)
    {
        $exemptions = [];

        // Primary residence exemption
        if ($request->ownership_type === 'primary_residence') {
            $exemptions['primary_residence'] = min(500000, $request->property_value * 0.3);
        }

        // Senior citizen exemption
        if ($request->has('is_senior_citizen') && $request->is_senior_citizen) {
            $exemptions['senior_citizen'] = $request->property_value * 0.1;
        }

        // Disability exemption
        if ($request->has('is_disabled') && $request->is_disabled) {
            $exemptions['disability'] = $request->property_value * 0.15;
        }

        return $exemptions;
    }

    private function generatePaymentSchedule($taxAmount)
    {
        $schedule = [];
        $installmentAmount = $taxAmount / 4; // Quarterly payments

        for ($i = 1; $i <= 4; $i++) {
            $dueDate = now()->addMonths($i * 3);
            $schedule[] = [
                'installment' => $i,
                'amount' => $installmentAmount,
                'due_date' => $dueDate->format('Y-m-d'),
                'status' => 'pending',
            ];
        }

        return $schedule;
    }
}

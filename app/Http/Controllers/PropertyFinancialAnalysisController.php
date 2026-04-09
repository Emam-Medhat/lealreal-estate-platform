<?php

namespace App\Http\Controllers;

use App\Models\PropertyFinancialAnalysis;
use App\Models\RoiCalculation;
use App\Models\CashFlowProjection;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class PropertyFinancialAnalysisController extends Controller
{
    public function dashboard(): View
    {
        $analyses = PropertyFinancialAnalysis::with(['property', 'roiCalculations', 'cashFlowProjections'])
            ->latest()
            ->paginate(10);

        $totalProperties = Property::count();
        $totalAnalyses = PropertyFinancialAnalysis::count();
        $avgRoi = RoiCalculation::avg('roi_percentage') ?? 0;
        $totalCashFlow = CashFlowProjection::sum('total_cash_flow') ?? 0;

        return view('financial.analysis-dashboard', compact(
            'analyses',
            'totalProperties',
            'totalAnalyses',
            'avgRoi',
            'totalCashFlow'
        ));
    }

    public function index(): View
    {
        $analyses = PropertyFinancialAnalysis::with(['property'])
            ->latest()
            ->paginate(15);

        return view('financial.analysis.index', compact('analyses'));
    }

    public function create(): View
    {
        $properties = Property::where('status', 'available')->get();
        return view('financial.analysis.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'purchase_price' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'rental_income' => 'required|numeric|min:0',
            'operating_expenses' => 'required|numeric|min:0',
            'vacancy_rate' => 'required|numeric|min:0|max:100',
            'appreciation_rate' => 'required|numeric|min:0|max:100',
            'analysis_period' => 'required|integer|min:1|max:30',
            'notes' => 'nullable|string'
        ]);

        $analysis = PropertyFinancialAnalysis::create($validated);

        // Calculate initial ROI
        $this->calculateInitialRoi($analysis);

        // Generate cash flow projections
        $this->generateCashFlowProjections($analysis);

        return redirect()
            ->route('financial.analysis.show', $analysis)
            ->with('success', 'تم إنشاء التحليل المالي بنجاح');
    }

    public function show(PropertyFinancialAnalysis $analysis): View
    {
        $analysis->load([
            'property',
            'roiCalculations' => function($query) {
                $query->latest();
            },
            'cashFlowProjections' => function($query) {
                $query->orderBy('year')->orderBy('month');
            },
            'propertyValuations' => function($query) {
                $query->latest();
            }
        ]);

        $currentRoi = $analysis->roiCalculations()->first();
        $cashFlowSummary = $this->calculateCashFlowSummary($analysis);
        $valuationTrend = $this->calculateValuationTrend($analysis);

        return view('financial.analysis.show', compact(
            'analysis',
            'currentRoi',
            'cashFlowSummary',
            'valuationTrend'
        ));
    }

    public function edit(PropertyFinancialAnalysis $analysis): View
    {
        $properties = Property::where('status', 'available')
            ->orWhere('id', $analysis->property_id)
            ->get();

        return view('financial.analysis.edit', compact('analysis', 'properties'));
    }

    public function update(Request $request, PropertyFinancialAnalysis $analysis)
    {
        $validated = $request->validate([
            'purchase_price' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'rental_income' => 'required|numeric|min:0',
            'operating_expenses' => 'required|numeric|min:0',
            'vacancy_rate' => 'required|numeric|min:0|max:100',
            'appreciation_rate' => 'required|numeric|min:0|max:100',
            'analysis_period' => 'required|integer|min:1|max:30',
            'notes' => 'nullable|string'
        ]);

        $analysis->update($validated);

        // Recalculate ROI and cash flow
        $this->calculateInitialRoi($analysis);
        $this->generateCashFlowProjections($analysis);

        return redirect()
            ->route('financial.analysis.show', $analysis)
            ->with('success', 'تم تحديث التحليل المالي بنجاح');
    }

    public function destroy(PropertyFinancialAnalysis $analysis)
    {
        $analysis->delete();
        return redirect()
            ->route('financial.analysis.index')
            ->with('success', 'تم حذف التحليل المالي بنجاح');
    }

    public function recalculate(PropertyFinancialAnalysis $analysis): JsonResponse
    {
        $this->calculateInitialRoi($analysis);
        $this->generateCashFlowProjections($analysis);

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة الحساب بنجاح',
            'roi' => $analysis->roiCalculations()->first(),
            'cash_flow' => $analysis->cashFlowProjections()->get()
        ]);
    }

    public function export(PropertyFinancialAnalysis $analysis)
    {
        // Export analysis to PDF or Excel
        $data = [
            'analysis' => $analysis->load(['property', 'roiCalculations', 'cashFlowProjections']),
            'generated_at' => now()
        ];

        // Implementation for export functionality
        return response()->json($data);
    }

    private function calculateInitialRoi(PropertyFinancialAnalysis $analysis): void
    {
        $annualIncome = $analysis->rental_income * 12 * (1 - $analysis->vacancy_rate / 100);
        $annualExpenses = $analysis->operating_expenses * 12;
        $netOperatingIncome = $annualIncome - $annualExpenses;
        $roiPercentage = $analysis->purchase_price > 0 
            ? ($netOperatingIncome / $analysis->purchase_price) * 100 
            : 0;

        RoiCalculation::updateOrCreate(
            ['property_financial_analysis_id' => $analysis->id, 'type' => 'initial'],
            [
                'total_investment' => $analysis->purchase_price,
                'annual_income' => $annualIncome,
                'annual_expenses' => $annualExpenses,
                'net_income' => $netOperatingIncome,
                'roi_percentage' => $roiPercentage,
                'calculated_at' => now()
            ]
        );
    }

    private function generateCashFlowProjections(PropertyFinancialAnalysis $analysis): void
    {
        // Clear existing projections
        $analysis->cashFlowProjections()->delete();

        $monthlyIncome = $analysis->rental_income * (1 - $analysis->vacancy_rate / 100);
        $monthlyExpenses = $analysis->operating_expenses;
        $appreciationRate = $analysis->appreciation_rate / 100 / 12; // Monthly appreciation

        for ($year = 1; $year <= $analysis->analysis_period; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $projectedIncome = $monthlyIncome * pow(1 + $appreciationRate, ($year - 1) * 12 + $month - 1);
                $projectedExpenses = $monthlyExpenses * pow(1.03, ($year - 1) * 12 + $month - 1); // 3% annual expense increase
                $netCashFlow = $projectedIncome - $projectedExpenses;

                CashFlowProjection::create([
                    'property_financial_analysis_id' => $analysis->id,
                    'year' => $year,
                    'month' => $month,
                    'projected_income' => $projectedIncome,
                    'projected_expenses' => $projectedExpenses,
                    'net_cash_flow' => $netCashFlow,
                    'cumulative_cash_flow' => $this->calculateCumulativeCashFlow($analysis, $year, $month)
                ]);
            }
        }
    }

    private function calculateCumulativeCashFlow(PropertyFinancialAnalysis $analysis, int $year, int $month): float
    {
        $totalMonths = ($year - 1) * 12 + $month;
        $projections = $analysis->cashFlowProjections()
            ->where(function($query) use ($year, $month) {
                $query->where('year', '<', $year)
                      ->orWhere(function($q) use ($year, $month) {
                          $q->where('year', $year)->where('month', '<=', $month);
                      });
            })
            ->sum('net_cash_flow');

        return $projections;
    }

    private function calculateCashFlowSummary(PropertyFinancialAnalysis $analysis): array
    {
        $projections = $analysis->cashFlowProjections;
        
        return [
            'total_cash_flow' => $projections->sum('net_cash_flow'),
            'average_monthly_cash_flow' => $projections->avg('net_cash_flow'),
            'best_month' => $projections->max('net_cash_flow'),
            'worst_month' => $projections->min('net_cash_flow'),
            'break_even_point' => $this->findBreakEvenPoint($projections)
        ];
    }

    private function calculateValuationTrend(PropertyFinancialAnalysis $analysis): array
    {
        $valuations = $analysis->propertyValuations()->orderBy('valuation_date')->get();
        
        if ($valuations->count() < 2) {
            return ['trend' => 'insufficient_data'];
        }

        $firstValuation = $valuations->first();
        $lastValuation = $valuations->last();
        $appreciation = (($lastValuation->valuation_amount - $firstValuation->valuation_amount) / $firstValuation->valuation_amount) * 100;

        return [
            'trend' => $appreciation > 0 ? 'appreciating' : 'depreciating',
            'total_appreciation' => $appreciation,
            'annual_appreciation' => $appreciation / $firstValuation->created_at->diffInYears($lastValuation->created_at)
        ];
    }

    private function findBreakEvenPoint($projections): ?string
    {
        $cumulative = 0;
        foreach ($projections as $projection) {
            $cumulative += $projection->net_cash_flow;
            if ($cumulative >= 0) {
                return "Year {$projection->year}, Month {$projection->month}";
            }
        }
        return null;
    }
}

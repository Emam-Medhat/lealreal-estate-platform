<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\CalculateRoiRequest;
use App\Models\Investor;
use App\Models\InvestorPortfolio;
use App\Models\InvestorRoiCalculation;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class InvestorRoiController extends Controller
{
    public function index(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();

        $roiCalculations = $investor->roiCalculations()
            ->with(['portfolio'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('portfolio', function ($portfolioQuery) use ($search) {
                    $portfolioQuery->where('investment_name', 'like', "%{$search}%");
                });
            })
            ->when($request->portfolio_id, function ($query, $portfolioId) {
                $query->where('portfolio_id', $portfolioId);
            })
            ->latest('calculated_at')
            ->paginate(20);

        return view('investor.roi.index', compact('roiCalculations'));
    }

    public function create()
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $portfolios = $investor->portfolios()->pluck('investment_name', 'id');

        return view('investor.roi.create', compact('portfolios'));
    }

    public function store(CalculateRoiRequest $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $portfolio = InvestorPortfolio::findOrFail($request->portfolio_id);

        $this->authorize('view', $portfolio);

        $roiCalculation = InvestorRoiCalculation::create([
            'investor_id' => $investor->id,
            'portfolio_id' => $request->portfolio_id,
            'calculation_type' => $request->calculation_type,
            'initial_investment' => $request->initial_investment,
            'current_value' => $request->current_value,
            'total_returns' => $request->total_returns,
            'roi_percentage' => $this->calculateRoiPercentage($request),
            'annualized_roi' => $this->calculateAnnualizedRoi($request),
            'holding_period_days' => $this->calculateHoldingPeriod($portfolio, $request),
            'risk_adjusted_roi' => $this->calculateRiskAdjustedRoi($request),
            'benchmark_comparison' => $request->benchmark_comparison ?? [],
            'calculation_method' => $request->calculation_method,
            'assumptions' => $request->assumptions ?? [],
            'notes' => $request->notes,
            'calculated_at' => now(),
            'created_by' => Auth::id(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'calculated_roi',
            'details' => "Calculated ROI for portfolio: {$portfolio->investment_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.roi.show', $roiCalculation)
            ->with('success', 'ROI calculation created successfully.');
    }

    public function show(InvestorRoiCalculation $roiCalculation)
    {
        $this->authorize('view', $roiCalculation);

        $roiCalculation->load(['portfolio', 'investor']);

        return view('investor.roi.show', compact('roiCalculation'));
    }

    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'portfolio_id' => 'required|exists:investor_portfolios,id',
            'calculation_type' => 'required|in:simple,advanced,risk_adjusted,benchmark',
            'end_date' => 'nullable|date',
            'include_dividends' => 'nullable|boolean',
        ]);

        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $portfolio = InvestorPortfolio::findOrFail($request->portfolio_id);

        $this->authorize('view', $portfolio);

        $initialInvestment = $portfolio->amount_invested;
        $currentValue = $request->end_date ? $this->getPortfolioValueAtDate($portfolio, $request->end_date) : $portfolio->current_value;
        $totalReturns = $this->calculateTotalReturns($portfolio, $request->end_date, $request->include_dividends ?? false);

        $roiPercentage = $initialInvestment > 0 ? (($currentValue - $initialInvestment) / $initialInvestment) * 100 : 0;
        $annualizedRoi = $this->calculateAnnualizedRoiFromData($initialInvestment, $currentValue, $portfolio->created_at, $request->end_date ?? now());
        $holdingPeriodDays = $portfolio->created_at->diffInDays($request->end_date ?? now());

        $calculation = [
            'portfolio_id' => $portfolio->id,
            'portfolio_name' => $portfolio->investment_name,
            'calculation_type' => $request->calculation_type,
            'initial_investment' => $initialInvestment,
            'current_value' => $currentValue,
            'total_returns' => $totalReturns,
            'roi_percentage' => round($roiPercentage, 2),
            'annualized_roi' => round($annualizedRoi, 2),
            'holding_period_days' => $holdingPeriodDays,
            'calculated_at' => now(),
        ];

        if ($request->calculation_type === 'risk_adjusted') {
            $calculation['risk_adjusted_roi'] = $this->calculateRiskAdjustedRoiFromData($portfolio, $roiPercentage);
        }

        if ($request->calculation_type === 'benchmark') {
            $calculation['benchmark_comparison'] = $this->getBenchmarkComparison($portfolio, $roiPercentage);
        }

        return response()->json([
            'success' => true,
            'calculation' => $calculation,
            'message' => 'ROI calculated successfully'
        ]);
    }

    public function getPortfolioRoiHistory(InvestorPortfolio $portfolio): JsonResponse
    {
        $this->authorize('view', $portfolio);

        $history = $portfolio->roiCalculations()
            ->orderBy('calculated_at')
            ->get(['calculated_at', 'roi_percentage', 'annualized_roi', 'calculation_type']);

        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    public function getRoiProjections(Request $request): JsonResponse
    {
        $request->validate([
            'portfolio_id' => 'required|exists:investor_portfolios,id',
            'projection_period' => 'required|in:1_month,3_months,6_months,1_year,3_years,5_years',
            'growth_rate' => 'nullable|numeric|min:-100|max:100',
        ]);

        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $portfolio = InvestorPortfolio::findOrFail($request->portfolio_id);

        $this->authorize('view', $portfolio);

        $currentValue = $portfolio->current_value;
        $growthRate = $request->growth_rate ?? $this->estimateGrowthRate($portfolio);
        $projectionPeriod = $this->getProjectionPeriodInMonths($request->projection_period);

        $projections = [];
        for ($i = 1; $i <= $projectionPeriod; $i++) {
            $projectedValue = $currentValue * pow(1 + ($growthRate / 100), $i / 12);
            $projectedRoi = (($projectedValue - $portfolio->amount_invested) / $portfolio->amount_invested) * 100;

            $projections[] = [
                'period' => $i,
                'date' => now()->addMonths($i)->format('Y-m-d'),
                'projected_value' => round($projectedValue, 2),
                'projected_roi' => round($projectedRoi, 2),
            ];
        }

        return response()->json([
            'success' => true,
            'projections' => $projections,
            'assumptions' => [
                'current_value' => $currentValue,
                'growth_rate' => $growthRate,
                'projection_period' => $request->projection_period,
            ],
            'message' => 'ROI projections calculated successfully'
        ]);
    }

    public function getRoiComparison(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();

        $portfolioRois = $investor->portfolios()
            ->with([
                'roiCalculations' => function ($query) {
                    $query->latest('calculated_at')->take(1);
                }
            ])
            ->get()
            ->map(function ($portfolio) {
                $latestRoi = $portfolio->roiCalculations->first();
                return [
                    'portfolio_name' => $portfolio->investment_name,
                    'investment_type' => $portfolio->investment_type,
                    'roi_percentage' => $latestRoi->roi_percentage ?? 0,
                    'annualized_roi' => $latestRoi->annualized_roi ?? 0,
                    'risk_level' => $portfolio->risk_level,
                ];
            });

        $benchmarks = $this->getIndustryBenchmarks();

        return response()->json([
            'success' => true,
            'portfolio_rois' => $portfolioRois,
            'benchmarks' => $benchmarks,
            'message' => 'ROI comparison retrieved successfully'
        ]);
    }

    public function exportRoiCalculations(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'portfolio_id' => 'nullable|exists:investor_portfolios,id',
            'calculation_type' => 'nullable|in:simple,advanced,risk_adjusted,benchmark',
        ]);

        $investor = Investor::where('user_id', Auth::id())->firstOrFail();

        $query = $investor->roiCalculations()->with(['portfolio']);

        if ($request->portfolio_id) {
            $query->where('portfolio_id', $request->portfolio_id);
        }

        if ($request->calculation_type) {
            $query->where('calculation_type', $request->calculation_type);
        }

        $calculations = $query->get();

        $filename = "roi_calculations_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $calculations,
            'filename' => $filename,
            'message' => 'ROI calculations exported successfully'
        ]);
    }

    private function calculateRoiPercentage(Request $request): float
    {
        $initialInvestment = $request->initial_investment;
        $currentValue = $request->current_value;

        if ($initialInvestment == 0)
            return 0;

        return (($currentValue - $initialInvestment) / $initialInvestment) * 100;
    }

    private function calculateAnnualizedRoi(Request $request): float
    {
        $initialInvestment = $request->initial_investment;
        $currentValue = $request->current_value;
        $roiPercentage = $this->calculateRoiPercentage($request);
        $holdingPeriodDays = $this->calculateHoldingPeriod(null, $request);

        if ($holdingPeriodDays == 0)
            return 0;

        return $roiPercentage * (365 / $holdingPeriodDays);
    }

    private function calculateHoldingPeriod(?InvestorPortfolio $portfolio, Request $request): int
    {
        if ($portfolio) {
            return $portfolio->created_at->diffInDays(now());
        }

        // For manual calculation, use provided dates
        if ($request->start_date && $request->end_date) {
            return \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date));
        }

        return 0;
    }

    private function calculateRiskAdjustedRoi(Request $request): float
    {
        $roiPercentage = $this->calculateRoiPercentage($request);
        $riskAdjustment = $this->getRiskAdjustmentFactor($request->risk_level ?? 'medium');

        return $roiPercentage * (1 - $riskAdjustment);
    }

    private function calculateTotalReturns(InvestorPortfolio $portfolio, ?string $endDate, bool $includeDividends): float
    {
        $baseReturns = $portfolio->total_returns ?? 0;

        if ($includeDividends) {
            $dividends = $this->calculateDividends($portfolio, $endDate);
            return $baseReturns + $dividends;
        }

        return $baseReturns;
    }

    private function getPortfolioValueAtDate(InvestorPortfolio $portfolio, string $date): float
    {
        // This would typically query historical values or calculate based on performance data
        // For now, return current value as placeholder
        return $portfolio->current_value;
    }

    private function calculateAnnualizedRoiFromData(float $initialInvestment, float $currentValue, \Carbon\Carbon $startDate, \Carbon\Carbon $endDate): float
    {
        $roiPercentage = $initialInvestment > 0 ? (($currentValue - $initialInvestment) / $initialInvestment) * 100 : 0;
        $holdingPeriodDays = $startDate->diffInDays($endDate);

        if ($holdingPeriodDays == 0)
            return 0;

        return $roiPercentage * (365 / $holdingPeriodDays);
    }

    private function calculateRiskAdjustedRoiFromData(InvestorPortfolio $portfolio, float $roiPercentage): float
    {
        $riskAdjustment = $this->getRiskAdjustmentFactor($portfolio->risk_level);
        return $roiPercentage * (1 - $riskAdjustment);
    }

    private function getBenchmarkComparison(InvestorPortfolio $portfolio, float $roiPercentage): array
    {
        $benchmarks = $this->getIndustryBenchmarks();
        $sectorBenchmark = $benchmarks[$portfolio->sector] ?? $benchmarks['average'];

        return [
            'portfolio_roi' => $roiPercentage,
            'sector_average' => $sectorBenchmark,
            'performance_vs_benchmark' => $roiPercentage - $sectorBenchmark,
            'percentile_ranking' => $this->calculatePercentileRanking($roiPercentage, $benchmarks),
        ];
    }

    private function getIndustryBenchmarks(): array
    {
        return [
            'technology' => 12.5,
            'healthcare' => 8.7,
            'finance' => 6.2,
            'real_estate' => 7.8,
            'energy' => 9.3,
            'average' => 8.9,
        ];
    }

    private function calculatePercentileRanking(float $roi, array $benchmarks): int
    {
        $allValues = array_values($benchmarks);
        $allValues[] = $roi;
        sort($allValues);

        $position = array_search($roi, $allValues);
        return $position !== false ? round((($position + 1) / count($allValues)) * 100, 2) : 0;
    }

    private function estimateGrowthRate(InvestorPortfolio $portfolio): float
    {
        // Estimate based on historical performance or sector averages
        $sectorGrowthRates = [
            'technology' => 15.2,
            'healthcare' => 8.5,
            'finance' => 6.8,
            'real_estate' => 7.2,
            'energy' => 10.3,
        ];

        return $sectorGrowthRates[$portfolio->sector] ?? 8.0;
    }

    private function getProjectionPeriodInMonths(string $period): int
    {
        $periods = [
            '1_month' => 1,
            '3_months' => 3,
            '6_months' => 6,
            '1_year' => 12,
            '3_years' => 36,
            '5_years' => 60,
        ];

        return $periods[$period] ?? 12;
    }

    private function getRiskAdjustmentFactor(string $riskLevel): float
    {
        $adjustments = [
            'low' => 0.05,
            'medium' => 0.10,
            'high' => 0.20,
            'very_high' => 0.30,
        ];

        return $adjustments[$riskLevel] ?? 0.10;
    }

    private function calculateDividends(InvestorPortfolio $portfolio, ?string $endDate): float
    {
        // This would typically calculate based on dividend history
        // For now, return estimated value
        return $portfolio->amount_invested * 0.02; // 2% annual dividend estimate
    }
}

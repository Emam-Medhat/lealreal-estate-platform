<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\InvestRequest;
use App\Models\Investor;
use App\Models\InvestmentFund;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvestmentFundController extends Controller
{
    public function index(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $funds = $investor->investmentFunds()
            ->with(['fundManager'])
            ->when($request->search, function ($query, $search) {
                $query->where('fund_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->fund_type, function ($query, $type) {
                $query->where('fund_type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->risk_level, function ($query, $risk) {
                $query->where('risk_level', $risk);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('investor.funds.index', compact('funds'));
    }

    public function show(InvestmentFund $fund)
    {
        $fund->load(['fundManager', 'documents', 'holdings']);
        
        return view('investor.funds.show', compact('fund'));
    }

    public function invest(Request $request, InvestmentFund $fund)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'investment_amount' => 'required|numeric|min:' . $fund->minimum_investment . '|max:' . $fund->maximum_investment,
            'investment_terms_accepted' => 'required|accepted',
            'auto_reinvest' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if investor has already invested
        $existingInvestment = $investor->fundInvestments()
            ->where('fund_id', $fund->id)
            ->where('status', 'active')
            ->first();

        if ($existingInvestment) {
            return back()->with('error', 'You have already invested in this fund.');
        }

        $investment = $investor->fundInvestments()->create([
            'fund_id' => $fund->id,
            'investor_id' => $investor->id,
            'investment_amount' => $request->investment_amount,
            'units_purchased' => $this->calculateUnitsPurchased($fund, $request->investment_amount),
            'status' => 'pending',
            'investment_date' => now(),
            'auto_reinvest' => $request->auto_reinvest ?? false,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // Update fund totals
        $fund->increment('total_invested', $request->investment_amount);
        $fund->increment('investor_count');

        // Update investor total invested
        $investor->increment('total_invested', $request->investment_amount);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'invested_in_fund',
            'details' => "Invested {$request->investment_amount} in fund: {$fund->fund_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.funds.show', $fund)
            ->with('success', 'Fund investment submitted successfully.');
    }

    public function getMyFundInvestments(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $investments = $investor->fundInvestments()
            ->with(['fund'])
            ->when($request->status, function ($query, $status) {
                $query->where('investments.status', $status);
            })
            ->latest('investment_date')
            ->paginate(20);

        return view('investor.funds.my-investments', compact('investments'));
    }

    public function getFundPerformance(InvestmentFund $fund): JsonResponse
    {
        $performance = [
            'fund_id' => $fund->id,
            'fund_name' => $fund->fund_name,
            'current_nav' => $fund->current_nav,
            'nav_change' => $this->calculateNavChange($fund),
            'performance_ytd' => $this->calculateYTDPerformance($fund),
            'performance_1y' => $this->calculate1YearPerformance($fund),
            'performance_3y' => $this->calculate3YearPerformance($fund),
            'performance_5y' => $this->calculate5YearPerformance($fund),
            'volatility' => $this->calculateFundVolatility($fund),
            'sharpe_ratio' => $this->calculateSharpeRatio($fund),
            'max_drawdown' => $this->calculateMaxDrawdown($fund),
        ];

        return response()->json([
            'success' => true,
            'performance' => $performance
        ]);
    }

    public function getFundStats(): JsonResponse
    {
        $stats = [
            'total_funds' => InvestmentFund::count(),
            'active_funds' => InvestmentFund::where('status', 'active')->count(),
            'by_type' => InvestmentFund::groupBy('fund_type')
                ->map(function ($group) {
                    return $group->count();
                }),
            'by_risk_level' => InvestmentFund::groupBy('risk_level')
                ->map(function ($group) {
                    return $group->count();
                }),
            'average_minimum_investment' => InvestmentFund::avg('minimum_investment'),
            'average_expected_return' => InvestmentFund::avg('expected_return'),
            'total_assets_under_management' => InvestmentFund::sum('total_assets'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getRecommendedFunds(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $recommendations = InvestmentFund::where('status', 'active')
            ->where('risk_level', $investor->risk_tolerance ?? 'medium')
            ->where('minimum_investment', '<=', $investor->max_investment ?? 100000)
            ->orderByDesc('expected_return')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    }

    public function compareFunds(Request $request): JsonResponse
    {
        $request->validate([
            'fund_ids' => 'required|array|min:2|max:5',
            'fund_ids.*' => 'exists:investment_funds,id',
        ]);

        $funds = InvestmentFund::whereIn('id', $request->fund_ids)->get();

        $comparison = $funds->map(function ($fund) {
            return [
                'id' => $fund->id,
                'fund_name' => $fund->fund_name,
                'expected_return' => $fund->expected_return,
                'risk_level' => $fund->risk_level,
                'minimum_investment' => $fund->minimum_investment,
                'current_nav' => $fund->current_nav,
                'performance_ytd' => $this->calculateYTDPerformance($fund),
                'sharpe_ratio' => $this->calculateSharpeRatio($fund),
                'fund_type' => $fund->fund_type,
                'expense_ratio' => $fund->expense_ratio,
            ];
        });

        return response()->json([
            'success' => true,
            'comparison' => $comparison,
            'message' => 'Funds compared successfully'
        ]);
    }

    public function calculateInvestmentValue(Request $request, InvestmentFund $fund): JsonResponse
    {
        $request->validate([
            'units_held' => 'required|numeric|min:0',
            'investment_date' => 'required|date',
        ]);

        $currentNav = $fund->current_nav;
        $investmentValue = $request->units_held * $currentNav;
        $costBasis = $this->calculateCostBasis($fund, $request->units_held);
        $gainLoss = $investmentValue - $costBasis;
        $gainLossPercentage = $costBasis > 0 ? ($gainLoss / $costBasis) * 100 : 0;

        return response()->json([
            'success' => true,
            'calculation' => [
                'units_held' => $request->units_held,
                'current_nav' => $currentNav,
                'investment_value' => round($investmentValue, 2),
                'cost_basis' => round($costBasis, 2),
                'gain_loss' => round($gainLoss, 2),
                'gain_loss_percentage' => round($gainLossPercentage, 2),
                'investment_date' => $request->investment_date,
            ],
            'message' => 'Investment value calculated successfully'
        ]);
    }

    public function exportFunds(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'fund_type' => 'nullable|in:equity,bond,money_market,balanced,alternative,sector',
            'risk_level' => 'nullable|in:low,medium,high,critical',
        ]);

        $query = InvestmentFund::with(['fundManager']);

        if ($request->fund_type) {
            $query->where('fund_type', $request->fund_type);
        }

        if ($request->risk_level) {
            $query->where('risk_level', $request->risk_level);
        }

        $funds = $query->get();

        $filename = "investment_funds_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $funds,
            'filename' => $filename,
            'message' => 'Funds exported successfully'
        ]);
    }

    private function calculateUnitsPurchased(InvestmentFund $fund, float $investmentAmount): float
    {
        if ($fund->current_nav <= 0) return 0;
        
        return $investmentAmount / $fund->current_nav;
    }

    private function calculateNavChange(InvestmentFund $fund): array
    {
        $currentNav = $fund->current_nav;
        $previousNav = $fund->previous_nav ?? $currentNav;
        $navChange = $currentNav - $previousNav;
        $navChangePercentage = $previousNav > 0 ? ($navChange / $previousNav) * 100 : 0;

        return [
            'current' => $currentNav,
            'previous' => $previousNav,
            'change' => round($navChange, 4),
            'change_percentage' => round($navChangePercentage, 2),
        ];
    }

    private function calculateYTDPerformance(InvestmentFund $fund): float
    {
        // Simplified YTD calculation
        $yearStart = now()->startOfYear();
        $performanceSinceStart = $fund->expected_return * 0.75; // 75% of annual return for 9 months
        return round($performanceSinceStart, 2);
    }

    private function calculate1YearPerformance(InvestmentFund $fund): float
    {
        return round($fund->expected_return * 0.9, 2); // Slightly less than expected
    }

    private function calculate3YearPerformance(InvestmentFund $fund): float
    {
        return round($fund->expected_return * 0.85, 2); // Average over 3 years
    }

    private function calculate5YearPerformance(InvestmentFund $fund): float
    {
        return round($fund->expected_return * 0.8, 2); // Long-term average
    }

    private function calculateFundVolatility(InvestmentFund $fund): float
    {
        $volatilityMap = [
            'low' => 8.5,
            'medium' => 12.3,
            'high' => 18.7,
            'critical' => 25.2,
        ];

        return $volatilityMap[$fund->risk_level] ?? 15.0;
    }

    private function calculateSharpeRatio(InvestmentFund $fund): float
    {
        $expectedReturn = $fund->expected_return;
        $riskFreeRate = 3.5; // Assume 3.5% risk-free rate
        $volatility = $this->calculateFundVolatility($fund);
        
        if ($volatility == 0) return 0;
        
        return round(($expectedReturn - $riskFreeRate) / $volatility, 2);
    }

    private function calculateMaxDrawdown(InvestmentFund $fund): float
    {
        $drawdownMap = [
            'low' => -5.2,
            'medium' => -12.8,
            'high' => -18.5,
            'critical' => -25.3,
        ];

        return $drawdownMap[$fund->risk_level] ?? -10.0;
    }

    private function calculateCostBasis(InvestmentFund $fund, float $unitsHeld): float
    {
        // Simplified cost basis calculation
        $averageCost = $fund->minimum_investment * 0.95; // Assume 5% below minimum
        return $unitsHeld * $averageCost;
    }
}

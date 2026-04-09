<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\InvestorPortfolio;
use App\Models\InvestorTransaction;
use App\Models\InvestorRoiCalculation;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvestorDashboardController extends Controller
{
    public function getQuickStats(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $stats = [
            'total_invested' => $investor->total_invested,
            'total_returns' => $investor->total_returns,
            'portfolio_value' => $investor->portfolios()->sum('current_value'),
            'active_investments' => $investor->portfolios()->where('status', 'active')->count(),
            'pending_investments' => $investor->portfolios()->where('status', 'pending')->count(),
            'completed_investments' => $investor->portfolios()->where('status', 'completed')->count(),
            'average_roi' => $investor->roiCalculations()->avg('roi_percentage') ?? 0,
            'monthly_returns' => $investor->transactions()
                ->where('type', 'return')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount'),
            'recent_investments' => $investor->portfolios()
                ->latest()
                ->take(5)
                ->get(['id', 'investment_name', 'amount_invested', 'current_value', 'status', 'created_at']),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getRecentActivities(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $activities = UserActivityLog::where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get(['action', 'details', 'created_at']);

        $transactions = $investor->transactions()
            ->latest()
            ->take(5)
            ->get(['id', 'type', 'amount', 'transaction_date', 'status']);

        return response()->json([
            'success' => true,
            'activities' => $activities,
            'transactions' => $transactions,
        ]);
    }

    public function getPortfolioPerformance(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $portfolios = $investor->portfolios()
            ->with(['roiCalculations'])
            ->get();

        $performance = $portfolios->map(function ($portfolio) {
            $initialInvestment = $portfolio->amount_invested;
            $currentValue = $portfolio->current_value;
            $totalReturns = $portfolio->total_returns ?? 0;
            $roi = $initialInvestment > 0 ? (($currentValue - $initialInvestment) / $initialInvestment) * 100 : 0;

            return [
                'id' => $portfolio->id,
                'name' => $portfolio->investment_name,
                'initial_investment' => $initialInvestment,
                'current_value' => $currentValue,
                'total_returns' => $totalReturns,
                'roi_percentage' => round($roi, 2),
                'status' => $portfolio->status,
                'created_at' => $portfolio->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'success' => true,
            'performance' => $performance
        ]);
    }

    public function getInvestmentDistribution(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $distribution = $investor->portfolios()
            ->selectRaw('investment_type, SUM(amount_invested) as total_invested, COUNT(*) as count')
            ->groupBy('investment_type')
            ->get();

        $sectorDistribution = $investor->portfolios()
            ->selectRaw('sector, SUM(amount_invested) as total_invested')
            ->groupBy('sector')
            ->get();

        $riskDistribution = $investor->portfolios()
            ->selectRaw('risk_level, SUM(amount_invested) as total_invested')
            ->groupBy('risk_level')
            ->get();

        return response()->json([
            'success' => true,
            'by_type' => $distribution,
            'by_sector' => $sectorDistribution,
            'by_risk' => $riskDistribution,
        ]);
    }

    public function getMonthlyReturns(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $monthlyReturns = $investor->transactions()
            ->selectRaw('MONTH(transaction_date) as month, YEAR(transaction_date) as year, SUM(amount) as total_returns')
            ->where('type', 'return')
            ->where('transaction_date', '>=', now()->subMonths(12))
            ->groupByRaw('YEAR(transaction_date), MONTH(transaction_date)')
            ->orderByRaw('YEAR(transaction_date), MONTH(transaction_date)')
            ->get();

        return response()->json([
            'success' => true,
            'monthly_returns' => $monthlyReturns
        ]);
    }

    public function getTopPerformingInvestments(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $topInvestments = $investor->portfolios()
            ->with(['roiCalculations'])
            ->orderByDesc('current_value')
            ->take(10)
            ->get(['id', 'investment_name', 'amount_invested', 'current_value', 'status']);

        return response()->json([
            'success' => true,
            'top_investments' => $topInvestments
        ]);
    }

    public function getUpcomingMilestones(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $milestones = $investor->portfolios()
            ->where('status', 'active')
            ->whereNotNull('expected_return_date')
            ->where('expected_return_date', '<=', now()->addDays(90))
            ->orderBy('expected_return_date')
            ->get(['id', 'investment_name', 'expected_return_date', 'expected_return_amount']);

        return response()->json([
            'success' => true,
            'milestones' => $milestones
        ]);
    }

    public function getRiskAnalysis(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $riskAnalysis = [
            'portfolio_risk_score' => $this->calculatePortfolioRiskScore($investor),
            'risk_distribution' => $investor->portfolios()
                ->selectRaw('risk_level, SUM(amount_invested) as total_amount')
                ->groupBy('risk_level')
                ->get(),
            'diversification_score' => $this->calculateDiversificationScore($investor),
            'concentration_risk' => $this->calculateConcentrationRisk($investor),
            'recommended_actions' => $this->getRiskRecommendations($investor),
        ];

        return response()->json([
            'success' => true,
            'risk_analysis' => $riskAnalysis
        ]);
    }

    public function getMarketTrends(): JsonResponse
    {
        $marketTrends = [
            'sector_performance' => $this->getSectorPerformance(),
            'market_volatility' => $this->getMarketVolatility(),
            'interest_rates' => $this->getCurrentInterestRates(),
            'market_sentiment' => $this->getMarketSentiment(),
        ];

        return response()->json([
            'success' => true,
            'market_trends' => $marketTrends
        ]);
    }

    public function exportDashboardData(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'period' => 'required|in:week,month,quarter,year',
        ]);

        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $period = $request->period;
        $startDate = match($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
        };

        $data = [
            'summary' => [
                'total_invested' => $investor->total_invested,
                'total_returns' => $investor->total_returns,
                'portfolio_count' => $investor->portfolios()->count(),
                'average_roi' => $investor->roiCalculations()->avg('roi_percentage') ?? 0,
            ],
            'transactions' => $investor->transactions()
                ->where('transaction_date', '>=', $startDate)
                ->get(),
            'portfolios' => $investor->portfolios()
                ->where('created_at', '>=', $startDate)
                ->get(),
            'roi_calculations' => $investor->roiCalculations()
                ->where('calculated_at', '>=', $startDate)
                ->get(),
        ];

        $filename = "investor_dashboard_export_" . $period . "_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $data,
            'filename' => $filename,
            'message' => 'Dashboard data exported successfully'
        ]);
    }

    private function calculatePortfolioRiskScore(Investor $investor): float
    {
        $portfolios = $investor->portfolios;
        $totalInvested = $portfolios->sum('amount_invested');
        
        if ($totalInvested == 0) return 0;

        $riskScores = [
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            'very_high' => 4,
        ];

        $weightedRisk = 0;
        foreach ($portfolios as $portfolio) {
            $riskScore = $riskScores[$portfolio->risk_level] ?? 2;
            $weight = $portfolio->amount_invested / $totalInvested;
            $weightedRisk += $riskScore * $weight;
        }

        return round($weightedRisk, 2);
    }

    private function calculateDiversificationScore(Investor $investor): float
    {
        $uniqueSectors = $investor->portfolios()->distinct('sector')->count();
        $uniqueTypes = $investor->portfolios()->distinct('investment_type')->count();
        
        $sectorScore = min($uniqueSectors * 10, 40);
        $typeScore = min($uniqueTypes * 10, 30);
        
        return $sectorScore + $typeScore;
    }

    private function calculateConcentrationRisk(Investor $investor): array
    {
        $totalInvested = $investor->portfolios()->sum('amount_invested');
        $concentration = $investor->portfolios()
            ->selectRaw('investment_name, SUM(amount_invested) as total, (SUM(amount_invested) / ?) * 100 as concentration')
            ->groupBy('investment_name')
            ->having('concentration', '>', 20)
            ->get();

        return [
            'high_concentration_investments' => $concentration,
            'concentration_score' => $concentration->max('concentration') ?? 0,
        ];
    }

    private function getRiskRecommendations(Investor $investor): array
    {
        $riskScore = $this->calculatePortfolioRiskScore($investor);
        $diversificationScore = $this->calculateDiversificationScore($investor);
        
        $recommendations = [];
        
        if ($riskScore > 3) {
            $recommendations[] = "Consider reducing exposure to high-risk investments";
        }
        
        if ($diversificationScore < 50) {
            $recommendations[] = "Increase portfolio diversification across different sectors";
        }
        
        if ($investor->portfolios()->where('risk_level', 'high')->count() > 3) {
            $recommendations[] = "Rebalance portfolio to reduce risk concentration";
        }

        return $recommendations;
    }

    private function getSectorPerformance(): array
    {
        // Simulated sector performance data
        return [
            'technology' => ['change' => 2.5, 'trend' => 'up'],
            'healthcare' => ['change' => 1.8, 'trend' => 'up'],
            'finance' => ['change' => -0.5, 'trend' => 'down'],
            'real_estate' => ['change' => 1.2, 'trend' => 'up'],
            'energy' => ['change' => 3.1, 'trend' => 'up'],
        ];
    }

    private function getMarketVolatility(): array
    {
        return [
            'vix_index' => 18.5,
            'market_volatility' => 'moderate',
            'trend' => 'stable',
        ];
    }

    private function getCurrentInterestRates(): array
    {
        return [
            'federal_rate' => 5.25,
            'savings_rate' => 4.5,
            'mortgage_rate' => 6.8,
            'last_updated' => now()->format('Y-m-d'),
        ];
    }

    private function getMarketSentiment(): array
    {
        return [
            'fear_greed_index' => 65,
            'sentiment' => 'greed',
            'market_outlook' => 'bullish',
        ];
    }
}

<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\AssessRiskRequest;
use App\Models\Investor;
use App\Models\InvestorPortfolio;
use App\Models\InvestorRiskAssessment;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class InvestorRiskController extends Controller
{
    public function index(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $riskAssessments = $investor->riskAssessments()
            ->with(['portfolio'])
            ->when($request->search, function ($query, $search) {
                $query->where('assessment_name', 'like', "%{$search}%")
                    ->orWhere('risk_type', 'like', "%{$search}%");
            })
            ->when($request->risk_level, function ($query, $level) {
                $query->where('overall_risk_level', $level);
            })
            ->when($request->portfolio_id, function ($query, $portfolioId) {
                $query->where('portfolio_id', $portfolioId);
            })
            ->latest('assessed_at')
            ->paginate(20);

        return view('investor.risk.index', compact('riskAssessments'));
    }

    public function create()
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $portfolios = $investor->portfolios()->pluck('investment_name', 'id');
        
        return view('investor.risk.create', compact('portfolios'));
    }

    public function store(AssessRiskRequest $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $portfolio = InvestorPortfolio::findOrFail($request->portfolio_id);
        
        $this->authorize('view', $portfolio);
        
        $riskAssessment = InvestorRiskAssessment::create([
            'investor_id' => $investor->id,
            'portfolio_id' => $request->portfolio_id,
            'assessment_name' => $request->assessment_name,
            'risk_type' => $request->risk_type,
            'assessment_methodology' => $request->assessment_methodology,
            'market_risk' => $request->market_risk,
            'credit_risk' => $request->credit_risk,
            'liquidity_risk' => $request->liquidity_risk,
            'operational_risk' => $request->operational_risk,
            'concentration_risk' => $request->concentration_risk,
            'currency_risk' => $request->currency_risk,
            'regulatory_risk' => $request->regulatory_risk,
            'overall_risk_score' => $this->calculateOverallRiskScore($request),
            'overall_risk_level' => $this->determineRiskLevel($request->market_risk + $request->credit_risk + $request->liquidity_risk + $request->operational_risk + $request->concentration_risk + $request->currency_risk + $request->regulatory_risk),
            'risk_factors' => $request->risk_factors ?? [],
            'mitigation_strategies' => $request->mitigation_strategies ?? [],
            'risk_tolerance_comparison' => $request->risk_tolerance_comparison ?? [],
            'stress_test_results' => $request->stress_test_results ?? [],
            'scenario_analysis' => $request->scenario_analysis ?? [],
            'recommendations' => $this->generateRiskRecommendations($request),
            'next_review_date' => $request->next_review_date,
            'assessor_notes' => $request->assessor_notes,
            'assessed_at' => now(),
            'created_by' => Auth::id(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_risk_assessment',
            'details' => "Created risk assessment: {$riskAssessment->assessment_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.risk.show', $riskAssessment)
            ->with('success', 'Risk assessment created successfully.');
    }

    public function show(InvestorRiskAssessment $riskAssessment)
    {
        $this->authorize('view', $riskAssessment);
        
        $riskAssessment->load(['portfolio', 'investor']);
        
        return view('investor.risk.show', compact('riskAssessment'));
    }

    public function edit(InvestorRiskAssessment $riskAssessment)
    {
        $this->authorize('update', $riskAssessment);
        
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $portfolios = $investor->portfolios()->pluck('investment_name', 'id');
        
        return view('investor.risk.edit', compact('riskAssessment', 'portfolios'));
    }

    public function update(Request $request, InvestorRiskAssessment $riskAssessment)
    {
        $this->authorize('update', $riskAssessment);
        
        $request->validate([
            'assessment_name' => 'required|string|max:255',
            'risk_type' => 'required|in:portfolio,investment,market,credit,liquidity,operational',
            'next_review_date' => 'required|date|after:today',
            'assessor_notes' => 'nullable|string|max:2000',
        ]);

        $riskAssessment->update([
            'assessment_name' => $request->assessment_name,
            'risk_type' => $request->risk_type,
            'assessment_methodology' => $request->assessment_methodology,
            'market_risk' => $request->market_risk,
            'credit_risk' => $request->credit_risk,
            'liquidity_risk' => $request->liquidity_risk,
            'operational_risk' => $request->operational_risk,
            'concentration_risk' => $request->concentration_risk,
            'currency_risk' => $request->currency_risk,
            'regulatory_risk' => $request->regulatory_risk,
            'overall_risk_score' => $this->calculateOverallRiskScore($request),
            'overall_risk_level' => $this->determineRiskLevel($request->market_risk + $request->credit_risk + $request->liquidity_risk + $request->operational_risk + $request->concentration_risk + $request->currency_risk + $request->regulatory_risk),
            'risk_factors' => $request->risk_factors ?? [],
            'mitigation_strategies' => $request->mitigation_strategies ?? [],
            'risk_tolerance_comparison' => $request->risk_tolerance_comparison ?? [],
            'stress_test_results' => $request->stress_test_results ?? [],
            'scenario_analysis' => $request->scenario_analysis ?? [],
            'recommendations' => $this->generateRiskRecommendations($request),
            'next_review_date' => $request->next_review_date,
            'assessor_notes' => $request->assessor_notes,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_risk_assessment',
            'details' => "Updated risk assessment: {$riskAssessment->assessment_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.risk.show', $riskAssessment)
            ->with('success', 'Risk assessment updated successfully.');
    }

    public function destroy(InvestorRiskAssessment $riskAssessment)
    {
        $this->authorize('delete', $riskAssessment);
        
        $assessmentName = $riskAssessment->assessment_name;
        
        $riskAssessment->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_risk_assessment',
            'details' => "Deleted risk assessment: {$assessmentName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('investor.risk.index')
            ->with('success', 'Risk assessment deleted successfully.');
    }

    public function calculatePortfolioRisk(Request $request): JsonResponse
    {
        $request->validate([
            'portfolio_id' => 'required|exists:investor_portfolios,id',
            'assessment_type' => 'required|in:quick,comprehensive,stress_test',
        ]);

        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $portfolio = InvestorPortfolio::findOrFail($request->portfolio_id);
        
        $this->authorize('view', $portfolio);

        $riskAnalysis = $this->performRiskAnalysis($portfolio, $request->assessment_type);

        return response()->json([
            'success' => true,
            'risk_analysis' => $riskAnalysis,
            'message' => 'Portfolio risk calculated successfully'
        ]);
    }

    public function getRiskDashboard(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $dashboard = [
            'overall_risk_score' => $this->calculateInvestorOverallRisk($investor),
            'risk_distribution' => $this->getRiskDistribution($investor),
            'high_risk_investments' => $this->getHighRiskInvestments($investor),
            'risk_trends' => $this->getRiskTrends($investor),
            'mitigation_status' => $this->getMitigationStatus($investor),
            'upcoming_reviews' => $this->getUpcomingRiskReviews($investor),
            'risk_recommendations' => $this->getDashboardRecommendations($investor),
        ];

        return response()->json([
            'success' => true,
            'dashboard' => $dashboard
        ]);
    }

    public function runStressTest(Request $request): JsonResponse
    {
        $request->validate([
            'portfolio_id' => 'required|exists:investor_portfolios,id',
            'scenarios' => 'required|array|min:1',
            'scenarios.*.name' => 'required|string|max:100',
            'scenarios.*.market_shock' => 'required|numeric|min:-100|max:100',
            'scenarios.*.interest_rate_change' => 'required|numeric|min:-10|max:10',
        ]);

        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $portfolio = InvestorPortfolio::findOrFail($request->portfolio_id);
        
        $this->authorize('view', $portfolio);

        $stressTestResults = [];
        foreach ($request->scenarios as $scenario) {
            $result = $this->runStressScenario($portfolio, $scenario);
            $stressTestResults[] = $result;
        }

        return response()->json([
            'success' => true,
            'stress_test_results' => $stressTestResults,
            'message' => 'Stress test completed successfully'
        ]);
    }

    public function getRiskAlerts(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $alerts = $this->generateRiskAlerts($investor);

        return response()->json([
            'success' => true,
            'alerts' => $alerts
        ]);
    }

    public function exportRiskAssessments(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'risk_type' => 'nullable|in:portfolio,investment,market,credit,liquidity,operational',
            'risk_level' => 'nullable|in:low,medium,high,critical',
        ]);

        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $query = $investor->riskAssessments()->with(['portfolio']);

        if ($request->risk_type) {
            $query->where('risk_type', $request->risk_type);
        }

        if ($request->risk_level) {
            $query->where('overall_risk_level', $request->risk_level);
        }

        $assessments = $query->get();

        $filename = "risk_assessments_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $assessments,
            'filename' => $filename,
            'message' => 'Risk assessments exported successfully'
        ]);
    }

    private function calculateOverallRiskScore(Request $request): float
    {
        $weights = [
            'market_risk' => 0.25,
            'credit_risk' => 0.25,
            'liquidity_risk' => 0.20,
            'operational_risk' => 0.15,
            'concentration_risk' => 0.10,
            'currency_risk' => 0.05,
        ];

        $totalScore = 0;
        foreach ($weights as $risk => $weight) {
            $totalScore += ($request->$risk ?? 0) * $weight;
        }

        return round($totalScore, 2);
    }

    private function determineRiskLevel(float $totalScore): string
    {
        if ($totalScore <= 20) return 'low';
        if ($totalScore <= 40) return 'medium';
        if ($totalScore <= 60) return 'high';
        return 'critical';
    }

    private function generateRiskRecommendations(Request $request): array
    {
        $recommendations = [];
        
        if ($request->market_risk > 70) {
            $recommendations[] = "Consider diversifying across different market sectors";
        }
        
        if ($request->concentration_risk > 60) {
            $recommendations[] = "Reduce concentration in single investments";
        }
        
        if ($request->liquidity_risk > 50) {
            $recommendations[] = "Increase holdings in liquid assets";
        }
        
        if ($request->operational_risk > 40) {
            $recommendations[] = "Review operational processes and controls";
        }

        return $recommendations;
    }

    private function performRiskAnalysis(InvestorPortfolio $portfolio, string $type): array
    {
        switch ($type) {
            case 'quick':
                return $this->quickRiskAnalysis($portfolio);
            case 'comprehensive':
                return $this->comprehensiveRiskAnalysis($portfolio);
            case 'stress_test':
                return $this->stressTestAnalysis($portfolio);
            default:
                return $this->quickRiskAnalysis($portfolio);
        }
    }

    private function quickRiskAnalysis(InvestorPortfolio $portfolio): array
    {
        return [
            'risk_score' => $this->calculatePortfolioRiskScore($portfolio),
            'risk_level' => $portfolio->risk_level,
            'volatility' => $this->estimateVolatility($portfolio),
            'diversification_score' => $this->calculateDiversificationScore($portfolio),
        ];
    }

    private function comprehensiveRiskAnalysis(InvestorPortfolio $portfolio): array
    {
        return [
            'market_risk' => $this->calculateMarketRisk($portfolio),
            'credit_risk' => $this->calculateCreditRisk($portfolio),
            'liquidity_risk' => $this->calculateLiquidityRisk($portfolio),
            'operational_risk' => $this->calculateOperationalRisk($portfolio),
            'concentration_risk' => $this->calculateConcentrationRisk($portfolio),
            'currency_risk' => $this->calculateCurrencyRisk($portfolio),
            'overall_score' => $this->calculatePortfolioRiskScore($portfolio),
        ];
    }

    private function calculateInvestorOverallRisk(Investor $investor): float
    {
        $totalInvested = $investor->portfolios()->sum('amount_invested');
        $weightedRiskScore = 0;
        
        foreach ($investor->portfolios as $portfolio) {
            $weight = $portfolio->amount_invested / $totalInvested;
            $portfolioRiskScore = $this->calculatePortfolioRiskScore($portfolio);
            $weightedRiskScore += $portfolioRiskScore * $weight;
        }

        return round($weightedRiskScore, 2);
    }

    private function getRiskDistribution(Investor $investor): array
    {
        return $investor->portfolios()
            ->selectRaw('risk_level, COUNT(*) as count, SUM(amount_invested) as total_invested')
            ->groupBy('risk_level')
            ->get();
    }

    private function getHighRiskInvestments(Investor $investor): array
    {
        return $investor->portfolios()
            ->whereIn('risk_level', ['high', 'critical'])
            ->get(['id', 'investment_name', 'risk_level', 'amount_invested']);
    }

    private function getRiskTrends(Investor $investor): array
    {
        return $investor->riskAssessments()
            ->selectRaw('DATE(assessed_at) as date, AVG(overall_risk_score) as avg_risk_score')
            ->where('assessed_at', '>=', now()->subMonths(12))
            ->groupByRaw('DATE(assessed_at)')
            ->orderBy('date')
            ->get();
    }

    private function getMitigationStatus(Investor $investor): array
    {
        return [
            'total_mitigations' => $investor->riskAssessments()->sum('mitigation_strategies'),
            'implemented_mitigations' => $investor->riskAssessments()->whereNotNull('mitigation_strategies')->count(),
            'pending_mitigations' => $investor->riskAssessments()->whereNull('mitigation_strategies')->count(),
        ];
    }

    private function getUpcomingRiskReviews(Investor $investor): array
    {
        return $investor->riskAssessments()
            ->where('next_review_date', '>', now())
            ->where('next_review_date', '<=', now()->addDays(30))
            ->orderBy('next_review_date')
            ->get(['assessment_name', 'next_review_date', 'overall_risk_level']);
    }

    private function getDashboardRecommendations(Investor $investor): array
    {
        return [
            'immediate_actions' => $this->getImmediateRiskActions($investor),
            'long_term_strategies' => $this->getLongTermRiskStrategies($investor),
            'monitoring_points' => $this->getMonitoringPoints($investor),
        ];
    }

    private function runStressScenario(InvestorPortfolio $portfolio, array $scenario): array
    {
        $currentValue = $portfolio->current_value;
        $marketShock = $scenario['market_shock'];
        $interestRateChange = $scenario['interest_rate_change'];
        
        $impactValue = $currentValue * (1 + ($marketShock / 100));
        $interestRateImpact = $interestRateChange * 0.1; // Simplified calculation
        
        $finalValue = $impactValue - ($interestRateImpact * $currentValue);
        $lossAmount = $currentValue - $finalValue;
        $lossPercentage = ($lossAmount / $currentValue) * 100;

        return [
            'scenario_name' => $scenario['name'],
            'market_shock' => $marketShock,
            'interest_rate_change' => $interestRateChange,
            'initial_value' => $currentValue,
            'final_value' => round($finalValue, 2),
            'loss_amount' => round($lossAmount, 2),
            'loss_percentage' => round($lossPercentage, 2),
            'status' => $lossPercentage > 20 ? 'critical' : ($lossPercentage > 10 ? 'high' : 'moderate'),
        ];
    }

    private function generateRiskAlerts(Investor $investor): array
    {
        $alerts = [];
        
        // Check for high risk investments
        $highRiskCount = $investor->portfolios()->whereIn('risk_level', ['high', 'critical'])->count();
        if ($highRiskCount > 0) {
            $alerts[] = [
                'type' => 'high_risk_exposure',
                'message' => "You have {$highRiskCount} high-risk investments requiring attention",
                'priority' => 'high',
            ];
        }
        
        // Check for upcoming reviews
        $upcomingReviews = $investor->riskAssessments()
            ->where('next_review_date', '<=', now()->addDays(7))
            ->where('next_review_date', '>', now())
            ->count();
        
        if ($upcomingReviews > 0) {
            $alerts[] = [
                'type' => 'upcoming_review',
                'message' => "You have {$upcomingReviews} risk assessment(s) due for review",
                'priority' => 'medium',
            ];
        }

        return $alerts;
    }

    // Helper methods for risk calculations
    private function calculatePortfolioRiskScore(InvestorPortfolio $portfolio): float
    {
        $riskScores = [
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            'critical' => 4,
        ];

        return $riskScores[$portfolio->risk_level] ?? 2;
    }

    private function calculateDiversificationScore(InvestorPortfolio $portfolio): float
    {
        // Simplified diversification calculation
        return min($portfolio->sector_count * 10, 50);
    }

    private function estimateVolatility(InvestorPortfolio $portfolio): float
    {
        // Simplified volatility estimation based on risk level
        $volatilityMap = [
            'low' => 15.2,
            'medium' => 22.8,
            'high' => 31.5,
            'critical' => 42.3,
        ];

        return $volatilityMap[$portfolio->risk_level] ?? 25.0;
    }

    private function getImmediateRiskActions(Investor $investor): array
    {
        return [
            'review_high_risk_investments',
            'update_mitigation_strategies',
            'schedule_risk_assessment',
        ];
    }

    private function getLongTermRiskStrategies(Investor $investor): array
    {
        return [
            'diversify_portfolio',
            'implement_hedging_strategies',
            'regular_monitoring',
        ];
    }

    private function getMonitoringPoints(Investor $investor): array
    {
        return [
            'market_volatility',
            'concentration_risk',
            'liquidity_levels',
        ];
    }
}

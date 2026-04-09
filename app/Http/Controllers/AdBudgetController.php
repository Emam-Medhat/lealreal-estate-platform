<?php

namespace App\Http\Controllers;

use App\Models\AdBudget;
use App\Models\AdCampaign;
use App\Models\Advertisement;
use App\Models\AdImpression;
use App\Models\AdClick;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdBudgetController extends Controller
{
    public function index()
    {
        $budgets = AdBudget::with(['campaign', 'campaign.user'])
            ->whereHas('campaign', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('ads.budgets', compact('budgets'));
    }

    public function create()
    {
        $campaigns = AdCampaign::where('user_id', Auth::id())
            ->whereDoesntHave('budget')
            ->get();

        return view('ads.create-budget', compact('campaigns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:ad_campaigns,id|unique:ad_budgets',
            'total_budget' => 'required|numeric|min:10',
            'daily_budget' => 'required|numeric|min:1',
            'budget_type' => 'required|in:standard,accelerated,limited',
            'delivery_method' => 'required|in:standard,accelerated',
            'auto_renew' => 'boolean',
            'renewal_amount' => 'nullable|numeric|min:10',
            'spending_limit' => 'nullable|numeric|min:1',
            'alert_threshold' => 'nullable|numeric|min:1|max:100'
        ]);

        $campaign = AdCampaign::findOrFail($request->campaign_id);
        
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $budget = AdBudget::create([
            'campaign_id' => $request->campaign_id,
            'total_budget' => $request->total_budget,
            'daily_budget' => $request->daily_budget,
            'remaining_budget' => $request->total_budget,
            'daily_remaining' => $request->daily_budget,
            'spent_amount' => 0,
            'daily_spent' => 0,
            'budget_type' => $request->budget_type,
            'delivery_method' => $request->delivery_method,
            'auto_renew' => $request->auto_renew ?? false,
            'renewal_amount' => $request->renewal_amount,
            'spending_limit' => $request->spending_limit,
            'alert_threshold' => $request->alert_threshold,
            'status' => 'active'
        ]);

        return redirect()->route('budgets.show', $budget->id)
            ->with('success', 'تم إنشاء الميزانية بنجاح');
    }

    public function show(AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id() && !Auth::user()->role === 'admin') {
            abort(403);
        }

        $budget->load(['campaign', 'campaign.ads']);

        // Get spending analytics
        $analytics = [
            'daily_spending' => $this->getDailySpending($budget),
            'spending_trends' => $this->getSpendingTrends($budget),
            'budget_utilization' => $this->calculateBudgetUtilization($budget),
            'projected_exhaustion' => $this->projectBudgetExhaustion($budget),
            'performance_vs_budget' => $this->getPerformanceVsBudget($budget)
        ];

        // Get recent transactions
        $transactions = $this->getBudgetTransactions($budget);

        return view('ads.show-budget', compact('budget', 'analytics', 'transactions'));
    }

    public function edit(AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        return view('ads.edit-budget', compact('budget'));
    }

    public function update(Request $request, AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'total_budget' => 'required|numeric|min:' . $budget->spent_amount,
            'daily_budget' => 'required|numeric|min:1',
            'budget_type' => 'required|in:standard,accelerated,limited',
            'delivery_method' => 'required|in:standard,accelerated',
            'auto_renew' => 'boolean',
            'renewal_amount' => 'nullable|numeric|min:10',
            'spending_limit' => 'nullable|numeric|min:1',
            'alert_threshold' => 'nullable|numeric|min:1|max:100'
        ]);

        $budget->update([
            'total_budget' => $request->total_budget,
            'daily_budget' => $request->daily_budget,
            'remaining_budget' => $request->total_budget - $budget->spent_amount,
            'budget_type' => $request->budget_type,
            'delivery_method' => $request->delivery_method,
            'auto_renew' => $request->auto_renew ?? false,
            'renewal_amount' => $request->renewal_amount,
            'spending_limit' => $request->spending_limit,
            'alert_threshold' => $request->alert_threshold
        ]);

        return redirect()->route('budgets.show', $budget->id)
            ->with('success', 'تم تحديث الميزانية بنجاح');
    }

    public function pause(AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $budget->update(['status' => 'paused']);

        // Pause all ads in campaign
        $budget->campaign->ads()->update(['status' => 'paused']);

        return back()->with('success', 'تم إيقاف الميزانية مؤقتاً');
    }

    public function resume(AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $budget->update(['status' => 'active']);

        // Resume approved ads in campaign
        $budget->campaign->ads()->where('approval_status', 'approved')->update(['status' => 'active']);

        return back()->with('success', 'تم استئناف الميزانية');
    }

    public function addFunds(Request $request, AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string'
        ]);

        $budget->increment('total_budget', $request->amount);
        $budget->increment('remaining_budget', $request->amount);

        // Log the transaction
        $this->logBudgetTransaction($budget, 'add_funds', $request->amount, $request->transaction_id);

        return back()->with('success', 'تم إضافة أموال للميزانية بنجاح');
    }

    public function adjustDailyBudget(Request $request, AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'daily_budget' => 'required|numeric|min:1',
            'effective_date' => 'required|date|after_or_equal:today'
        ]);

        // Create budget adjustment record
        $this->createBudgetAdjustment($budget, 'daily_budget', $request->daily_budget, $request->effective_date);

        return back()->with('success', 'تم تعديل الميزانية اليومية بنجاح');
    }

    public function setSpendingLimit(Request $request, AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'spending_limit' => 'required|numeric|min:1',
            'limit_type' => 'required|in:daily,weekly,monthly,total'
        ]);

        $budget->update([
            'spending_limit' => $request->spending_limit,
            'limit_type' => $request->limit_type
        ]);

        return back()->with('success', 'تم تحديد حد الإنفاق بنجاح');
    }

    public function enableAutoRenewal(Request $request, AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'renewal_amount' => 'required|numeric|min:10',
            'renewal_trigger' => 'required|in:exhausted,below_threshold'
        ]);

        $budget->update([
            'auto_renew' => true,
            'renewal_amount' => $request->renewal_amount,
            'renewal_trigger' => $request->renewal_trigger
        ]);

        return back()->with('success', 'تم تفعيل التجديد التلقائي بنجاح');
    }

    public function disableAutoRenewal(AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $budget->update([
            'auto_renew' => false,
            'renewal_amount' => null,
            'renewal_trigger' => null
        ]);

        return back()->with('success', 'تم إلغاء التجديد التلقائي');
    }

    public function spendingReport(AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id() && !Auth::user()->role === 'admin') {
            abort(403);
        }

        $report = [
            'overview' => $this->getBudgetOverview($budget),
            'daily_breakdown' => $this->getDailySpendingBreakdown($budget),
            'ad_performance' => $this->getAdPerformanceVsBudget($budget),
            'roi_analysis' => $this->getROIAnalysis($budget),
            'projections' => $this->getBudgetProjections($budget)
        ];

        return view('ads.budget-report', compact('budget', 'report'));
    }

    public function optimizeBudget(AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $optimizations = $this->generateBudgetOptimizations($budget);

        return view('ads.optimize-budget', compact('budget', 'optimizations'));
    }

    public function applyOptimizations(Request $request, AdBudget $budget)
    {
        if ($budget->campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $optimizations = $request->optimizations ?? [];

        foreach ($optimizations as $key => $value) {
            $this->applyOptimization($budget, $key, $value);
        }

        return back()->with('success', 'تم تطبيق تحسينات الميزانية بنجاح');
    }

    private function getDailySpending($budget)
    {
        return DB::table('budget_spending_logs')
            ->where('budget_id', $budget->id)
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as spent')
            ->orderBy('date')
            ->get();
    }

    private function getSpendingTrends($budget)
    {
        return [
            'last_7_days' => $this->getSpendingInPeriod($budget, 7),
            'last_30_days' => $this->getSpendingInPeriod($budget, 30),
            'last_90_days' => $this->getSpendingInPeriod($budget, 90)
        ];
    }

    private function calculateBudgetUtilization($budget)
    {
        return [
            'total_utilization' => ($budget->spent_amount / $budget->total_budget) * 100,
            'daily_utilization' => ($budget->daily_spent / $budget->daily_budget) * 100,
            'remaining_percentage' => ($budget->remaining_budget / $budget->total_budget) * 100
        ];
    }

    private function projectBudgetExhaustion($budget)
    {
        $dailySpend = $budget->daily_spent;
        $remainingBudget = $budget->remaining_budget;
        
        if ($dailySpend > 0) {
            $daysUntilExhaustion = floor($remainingBudget / $dailySpend);
            $exhaustionDate = Carbon::now()->addDays($daysUntilExhaustion);
            
            return [
                'days_remaining' => $daysUntilExhaustion,
                'exhaustion_date' => $exhaustionDate,
                'risk_level' => $this->assessExhaustionRisk($daysUntilExhaustion)
            ];
        }

        return [
            'days_remaining' => null,
            'exhaustion_date' => null,
            'risk_level' => 'low'
        ];
    }

    private function getPerformanceVsBudget($budget)
    {
        $campaign = $budget->campaign;
        $ads = $campaign->ads;

        return [
            'total_impressions' => $ads->sum('impressions_count'),
            'total_clicks' => $ads->sum('clicks_count'),
            'total_conversions' => $ads->sum('conversions_count'),
            'cost_per_impression' => $budget->spent_amount / max($ads->sum('impressions_count'), 1),
            'cost_per_click' => $budget->spent_amount / max($ads->sum('clicks_count'), 1),
            'cost_per_conversion' => $budget->spent_amount / max($ads->sum('conversions_count'), 1)
        ];
    }

    private function getBudgetTransactions($budget)
    {
        return DB::table('budget_transactions')
            ->where('budget_id', $budget->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    private function logBudgetTransaction($budget, $type, $amount, $transactionId = null)
    {
        DB::table('budget_transactions')->insert([
            'budget_id' => $budget->id,
            'type' => $type,
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'created_at' => now()
        ]);
    }

    private function createBudgetAdjustment($budget, $adjustmentType, $newValue, $effectiveDate)
    {
        DB::table('budget_adjustments')->insert([
            'budget_id' => $budget->id,
            'adjustment_type' => $adjustmentType,
            'old_value' => $budget->daily_budget,
            'new_value' => $newValue,
            'effective_date' => $effectiveDate,
            'created_at' => now()
        ]);
    }

    private function getSpendingInPeriod($budget, $days)
    {
        return DB::table('budget_spending_logs')
            ->where('budget_id', $budget->id)
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->sum('amount');
    }

    private function assessExhaustionRisk($daysRemaining)
    {
        if ($daysRemaining <= 3) return 'critical';
        if ($daysRemaining <= 7) return 'high';
        if ($daysRemaining <= 14) return 'medium';
        return 'low';
    }

    // Additional helper methods for budget optimization and reporting
    private function getBudgetOverview($budget) { return []; }
    private function getDailySpendingBreakdown($budget) { return []; }
    private function getAdPerformanceVsBudget($budget) { return []; }
    private function getROIAnalysis($budget) { return []; }
    private function getBudgetProjections($budget) { return []; }
    private function generateBudgetOptimizations($budget) { return []; }
    private function applyOptimization($budget, $key, $value) { return; }
}

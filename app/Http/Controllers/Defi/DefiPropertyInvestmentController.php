<?php

namespace App\Http\Controllers\Defi;

use App\Http\Controllers\Controller;
use App\Models\Defi\DefiPropertyInvestment;
use App\Models\Defi\PropertyLiquidityPool;
use App\Models\Defi\PropertyToken;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DefiPropertyInvestmentController extends Controller
{
    /**
     * Display a listing of DeFi property investments.
     */
    public function index(Request $request)
    {
        $query = DefiPropertyInvestment::with(['user', 'property', 'token', 'pool'])
            ->where('user_id', auth()->id());

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by investment type
        if ($request->has('investment_type') && $request->investment_type) {
            $query->where('investment_type', $request->investment_type);
        }

        // Filter by property
        if ($request->has('property_id') && $request->property_id) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by amount range
        if ($request->has('min_amount') && $request->min_amount) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->has('max_amount') && $request->max_amount) {
            $query->where('amount', '<=', $request->max_amount);
        }

        $investments = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics
        $stats = [
            'total_investments' => DefiPropertyInvestment::where('user_id', auth()->id())->count(),
            'active_investments' => DefiPropertyInvestment::where('user_id', auth()->id())
                ->where('status', 'active')->count(),
            'total_invested' => DefiPropertyInvestment::where('user_id', auth()->id())
                ->where('status', 'active')->sum('amount'),
            'total_returns' => DefiPropertyInvestment::where('user_id', auth()->id())
                ->where('status', 'active')->sum('total_returns'),
            'total_profit_loss' => DefiPropertyInvestment::where('user_id', auth()->id())
                ->where('status', 'active')->sum('profit_loss'),
            'average_roi' => DefiPropertyInvestment::where('user_id', auth()->id())
                ->where('status', 'active')->avg('roi'),
        ];

        return Inertia::render('defi/investment/index', [
            'investments' => $investments,
            'stats' => $stats,
            'filters' => $request->only(['status', 'investment_type', 'property_id', 'min_amount', 'max_amount']),
        ]);
    }

    /**
     * Show the form for creating a new DeFi property investment.
     */
    public function create()
    {
        // Get available investment opportunities
        $properties = MetaverseProperty::where('status', 'active')
            ->where('investment_enabled', true)
            ->get();

        $tokens = PropertyToken::where('status', 'active')
            ->where('investment_enabled', true)
            ->get();

        $pools = PropertyLiquidityPool::where('status', 'active')
            ->where('is_public', true)
            ->get();

        return Inertia::render('defi/investment/create', [
            'properties' => $properties,
            'tokens' => $tokens,
            'pools' => $pools,
        ]);
    }

    /**
     * Store a newly created DeFi property investment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'investment_type' => 'required|in:direct,token,pool,fractional',
            'amount' => 'required|numeric|min:100',
            'currency' => 'required|string|max:10|in:USD,EUR,GBP,ETH,BTC,USDT',
            'property_id' => 'nullable|exists:metaverse_properties,id',
            'property_token_id' => 'nullable|exists:property_tokens,id',
            'liquidity_pool_id' => 'nullable|exists:property_liquidity_pools,id',
            'investment_period' => 'required|integer|min:1',
            'expected_roi' => 'required|numeric|min:0|max:100',
            'risk_level' => 'required|in:low,medium,high',
            'auto_reinvest' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // Calculate investment details
            $investmentType = $request->investment_type;
            $amount = $request->amount;
            $expectedRoi = $request->expected_roi;
            $investmentPeriod = $request->investment_period;

            // Calculate expected returns
            $totalReturns = $amount * (1 + ($expectedRoi / 100));
            $profitLoss = $totalReturns - $amount;
            $roi = $expectedRoi;

            // Create investment
            $investment = DefiPropertyInvestment::create([
                'user_id' => auth()->id(),
                'investment_type' => $investmentType,
                'amount' => $amount,
                'currency' => $request->currency,
                'property_id' => $request->property_id,
                'property_token_id' => $request->property_token_id,
                'liquidity_pool_id' => $request->liquidity_pool_id,
                'investment_period' => $investmentPeriod,
                'expected_roi' => $expectedRoi,
                'actual_roi' => 0,
                'total_returns' => 0,
                'profit_loss' => 0,
                'roi' => $roi,
                'risk_level' => $request->risk_level,
                'auto_reinvest' => $request->auto_reinvest,
                'status' => 'active',
                'matured_at' => now()->addDays($investmentPeriod),
                'created_at' => now(),
            ]);

            // Process investment based on type
            $this->processInvestment($investment);

            DB::commit();

            return redirect()->route('defi.investment.show', $investment)
                ->with('success', 'تم إنشاء الاستثمار بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء الاستثمار: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified DeFi property investment.
     */
    public function show(DefiPropertyInvestment $investment)
    {
        // Check if user owns the investment
        if ($investment->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الاستثمار');
        }

        $investment->load(['user', 'property', 'token', 'pool', 'transactions']);

        // Calculate investment statistics
        $statistics = [
            'current_value' => $this->calculateCurrentValue($investment),
            'total_returns' => $investment->total_returns,
            'profit_loss' => $investment->profit_loss,
            'roi_percentage' => $investment->roi,
            'daily_returns' => $this->calculateDailyReturns($investment),
            'monthly_returns' => $this->calculateMonthlyReturns($investment),
            'progress_percentage' => $this->calculateProgressPercentage($investment),
            'days_until_maturity' => $this->calculateDaysUntilMaturity($investment),
            'is_matured' => $investment->matured_at <= now(),
            'can_withdraw' => $this->canWithdraw($investment),
            'next_payout_date' => $this->calculateNextPayoutDate($investment),
            'total_payouts' => $investment->transactions()->where('type', 'payout')->sum('amount'),
        ];

        return Inertia::render('defi/investment/show', [
            'investment' => $investment,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for editing the specified DeFi property investment.
     */
    public function edit(DefiPropertyInvestment $investment)
    {
        // Check if user owns the investment and it's active
        if ($investment->user_id !== auth()->id() || $investment->status !== 'active') {
            abort(403, 'لا يمكن تعديل هذا الاستثمار');
        }

        return Inertia::render('defi/investment/edit', [
            'investment' => $investment,
        ]);
    }

    /**
     * Update the specified DeFi property investment in storage.
     */
    public function update(Request $request, DefiPropertyInvestment $investment)
    {
        // Check if user owns the investment and it's active
        if ($investment->user_id !== auth()->id() || $investment->status !== 'active') {
            abort(403, 'لا يمكن تعديل هذا الاستثمار');
        }

        $request->validate([
            'auto_reinvest' => 'boolean',
            'risk_level' => 'required|in:low,medium,high',
        ]);

        $investment->update([
            'auto_reinvest' => $request->auto_reinvest,
            'risk_level' => $request->risk_level,
            'updated_at' => now(),
        ]);

        return redirect()->route('defi.investment.show', $investment)
            ->with('success', 'تم تحديث الاستثمار بنجاحاح');
    }

    /**
     * Remove the specified DeFi property investment from storage.
     */
    public function destroy(DefiPropertyInvestment $investment)
    {
        // Check if user owns the investment and can withdraw
        if ($investment->user_id !== auth()->id() || !$this->canWithdraw($investment)) {
            abort(403, 'لا يمكن إلغاء هذا الاستثمار');
        }

        DB::beginTransaction();

        try {
            // Process withdrawal
            $this->processWithdrawal($investment);

            // Update status
            $investment->update([
                'status' => 'withdrawn',
                'withdrawn_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('defi.investment.index')
                ->with('success', 'تم سحب الاستثمار بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء سحب الاستثمار: ' . $e->getMessage());
        }
    }

    /**
     * Withdraw investment returns.
     */
    public function withdraw(Request $request, DefiPropertyInvestment $investment)
    {
        // Check if user owns the investment
        if ($investment->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بسحب هذا الاستثمار');
        }

        if (!$this->canWithdraw($investment)) {
            return back()->with('error', 'لا يمكن سحب الاستثمار قبل نضوجه');
        }

        DB::beginTransaction();

        try {
            // Process withdrawal
            $this->processWithdrawal($investment);

            // Update status
            $investment->update([
                'status' => 'withdrawn',
                'withdrawn_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'تم سحب الاستثمار بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء سحب الاستثمار: ' . $e->getMessage());
        }
    }

    /**
     * Reinvest investment returns.
     */
    public function reinvest(Request $request, DefiPropertyInvestment $investment)
    {
        // Check if user owns the investment
        if ($investment->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بإعادة استثمار هذا الاستثمار');
        }

        if (!$investment->auto_reinvest) {
            abort(403, 'إعادة الاستثمار التلقائي غير مفعلة');
        }

        DB::beginTransaction();

        try {
            // Calculate returns to reinvest
            $returnsToReinvest = $investment->total_returns;

            // Create new investment
            $newInvestment = DefiPropertyInvestment::create([
                'user_id' => auth()->id(),
                'investment_type' => $investment->investment_type,
                'amount' => $returnsToReinvest,
                'currency' => $investment->currency,
                'property_id' => $investment->property_id,
                'property_token_id' => $investment->property_token_id,
                'liquidity_pool_id' => $investment->liquidity_pool_id,
                'investment_period' => $investment->investment_period,
                'expected_roi' => $investment->expected_roi,
                'actual_roi' => 0,
                'total_returns' => 0,
                'profit_loss' => 0,
                'roi' => 0,
                'risk_level' => $investment->risk_level,
                'auto_reinvest' => $investment->auto_reinvest,
                'status' => 'active',
                'matured_at' => now()->addDays($investment->investment_period),
                'parent_investment_id' => $investment->id,
                'created_at' => now(),
            ]);

            // Update original investment
            $investment->update([
                'status' => 'reinvested',
                'reinvested_at' => now(),
                'reinvested_into_id' => $newInvestment->id,
            ]);

            // Process new investment
            $this->processInvestment($newInvestment);

            DB::commit();

            return back()->with('success', 'تم إعادة استثمار العوائد بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إعادة الاستثمار: ' . $e->getMessage());
        }
    }

    /**
     * Get investment analytics.
     */
    public function analytics()
    {
        $userInvestments = DefiPropertyInvestment::where('user_id', auth()->id())->get();

        $analytics = [
            'total_invested' => $userInvestments->where('status', 'active')->sum('amount'),
            'total_returns' => $userInvestments->where('status', 'active')->sum('total_returns'),
            'total_profit_loss' => $userInvestments->where('status', 'active')->sum('profit_loss'),
            'active_investments' => $userInvestments->where('status', 'active')->count(),
            'completed_investments' => $userInvestments->where('status', 'completed')->count(),
            'average_roi' => $userInvestments->where('status', 'active')->avg('roi'),
            'investment_distribution' => $this->getInvestmentDistribution($userInvestments),
            'risk_distribution' => $this->getRiskDistribution($userInvestments),
            'monthly_returns' => $this->calculateMonthlyReturnsForPortfolio($userInvestments),
            'top_performing_investments' => $this->getTopPerformingInvestments($userInvestments),
            'diversification_score' => $this->calculateDiversificationScore($userInvestments),
        ];

        return Inertia::render('defi/investment/analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Get investment marketplace.
     */
    public function marketplace(Request $request)
    {
        $query = DefiPropertyInvestment::with(['user', 'property', 'token', 'pool'])
            ->where('status', 'active')
            ->where('is_public', true);

        // Filter by investment type
        if ($request->has('investment_type') && $request->investment_type) {
            $query->where('investment_type', $request->investment_type);
        }

        // Filter by ROI range
        if ($request->has('min_roi') && $request->min_roi) {
            $query->where('expected_roi', '>=', $request->min_roi);
        }

        if ($request->has('max_roi') && $request->max_roi) {
            $query->where('expected_roi', '<=', $request->max_roi);
        }

        // Filter by risk level
        if ($request->has('risk_level') && $request->risk_level) {
            $query->where('risk_level', $request->risk_level);
        }

        // Filter by amount range
        if ($request->has('min_amount') && $request->min_amount) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->has('max_amount') && $request->max_amount) {
            $query->where('amount', '<=', $request->max_amount);
        }

        $investments = $query->orderBy('expected_roi', 'desc')
            ->paginate(12);

        return Inertia::render('defi/investment/marketplace', [
            'investments' => $investments,
            'filters' => $request->only(['investment_type', 'min_roi', 'max_roi', 'risk_level', 'min_amount', 'max_amount']),
        ]);
    }

    /**
     * Process investment based on type.
     */
    private function processInvestment($investment): void
    {
        switch ($investment->investment_type) {
            case 'direct':
                $this->processDirectInvestment($investment);
                break;
            case 'token':
                $this->processTokenInvestment($investment);
                break;
            case 'pool':
                $this->processPoolInvestment($investment);
                break;
            case 'fractional':
                $this->processFractionalInvestment($investment);
                break;
        }
    }

    /**
     * Process direct investment.
     */
    private function processDirectInvestment($investment): void
    {
        // This would handle direct property investment
        // For now, just log the action
        \Log::info("Processed direct investment: {$investment->id}");
    }

    /**
     * Process token investment.
     */
    private function processTokenInvestment($investment): void
    {
        // This would handle token-based investment
        // For now, just log the action
        \Log::info("Processed token investment: {$investment->id}");
    }

    /**
     * Process pool investment.
     */
    private function processPoolInvestment($investment): void
    {
        // This would handle liquidity pool investment
        // For now, just log the action
        \Log::info("Processed pool investment: {$investment->id}");
    }

    /**
     * Process fractional investment.
     */
    private function processFractionalInvestment($investment): void
    {
        // This would handle fractional ownership investment
        // For now, just log the action
        \Log::info("Processed fractional investment: {$investment->id}");
    }

    /**
     * Process withdrawal.
     */
    private function processWithdrawal($investment): void
    {
        // This would handle the withdrawal process
        // For now, just log the action
        \Log::info("Processed withdrawal: {$investment->id}");
    }

    /**
     * Calculate current value.
     */
    private function calculateCurrentValue($investment): float
    {
        // This would calculate based on current market conditions
        // For now, return the invested amount plus returns
        return $investment->amount + $investment->total_returns;
    }

    /**
     * Calculate daily returns.
     */
    private function calculateDailyReturns($investment): float
    {
        $dailyRate = $investment->expected_roi / 365 / 100;
        return $investment->amount * $dailyRate;
    }

    /**
     * Calculate monthly returns.
     */
    private function calculateMonthlyReturns($investment): float
    {
        return $this->calculateDailyReturns($investment) * 30;
    }

    /**
     * Calculate progress percentage.
     */
    private function calculateProgressPercentage($investment): float
    {
        if ($investment->investment_period <= 0) {
            return 0;
        }

        $daysElapsed = now()->diffInDays($investment->created_at);
        return min(100, ($daysElapsed / $investment->investment_period) * 100);
    }

    /**
     * Calculate days until maturity.
     */
    private function calculateDaysUntilMaturity($investment): int
    {
        return max(0, now()->diffInDays($investment->matured_at));
    }

    /**
     * Check if investment can be withdrawn.
     */
    private function canWithdraw($investment): bool
    {
        return $investment->matured_at <= now();
    }

    /**
     * Calculate next payout date.
     */
    private function calculateNextPayoutDate($investment): string
    {
        // This would calculate based on actual payout schedule
        // For now, return a mock date
        return now()->addMonth()->format('Y-m-d');
    }

    /**
     * Get investment distribution.
     */
    private function getInvestmentDistribution($investments): array
    {
        $distribution = [];

        foreach ($investments->where('status', 'active') as $investment) {
            $type = $investment->investment_type;

            if (!isset($distribution[$type])) {
                $distribution[$type] = [
                    'count' => 0,
                    'total_invested' => 0,
                    'average_roi' => 0,
                ];
            }

            $distribution[$type]['count']++;
            $distribution[$type]['total_invested'] += $investment->amount;
            $distribution[$type]['average_roi'] = ($distribution[$type]['average_roi'] * ($distribution[$type]['count'] - 1) + $investment->expected_roi) / $distribution[$type]['count'];
        }

        return $distribution;
    }

    /**
     * Get risk distribution.
     */
    private function getRiskDistribution($investments): array
    {
        $distribution = [
            'low' => ['count' => 0, 'total_invested' => 0],
            'medium' => ['count' => 0, 'total_invested' => 0],
            'high' => ['count' => 0, 'total_invested' => 0],
        ];

        foreach ($investments->where('status', 'active') as $investment) {
            $risk = $investment->risk_level;
            $distribution[$risk]['count']++;
            $distribution[$risk]['total_invested'] += $investment->amount;
        }

        return $distribution;
    }

    private function calculateMonthlyReturnsForPortfolio($investments): array
    {
        $monthlyData = [];

        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthReturns = 0;

            foreach ($investments->where('status', 'active') as $investment) {
                $monthReturns += $this->calculateMonthlyReturns($investment);
            }

            $monthlyData[$date->format('Y-m')] = $monthReturns;
        }

        return $monthlyData;
    }

    /**
     * Get top performing investments.
     */
    private function getTopPerformingInvestments($investments): array
    {
        return $investments->sortByDesc(function ($investment) {
            return $investment->roi;
        })->take(5)->values()->toArray();
    }

    /**
     * Calculate diversification score.
     */
    private function calculateDiversificationScore($investments): float
    {
        $activeInvestments = $investments->where('status', 'active');
        $totalInvested = $activeInvestments->sum('amount');

        if ($totalInvested <= 0) {
            return 0;
        }

        // Calculate concentration (inverse of diversification)
        $concentration = 0;
        foreach ($activeInvestments as $investment) {
            $weight = $investment->amount / $totalInvested;
            $concentration += $weight * $weight;
        }

        // Diversification score (0-100, higher is more diversified)
        return (1 - $concentration) * 100;
    }
}

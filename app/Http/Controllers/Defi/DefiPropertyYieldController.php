<?php

namespace App\Http\Controllers\Defi;

use App\Http\Controllers\Controller;
use App\Models\Defi\PropertyYield;
use App\Models\Defi\PropertyStaking;
use App\Models\Defi\PropertyLiquidityPool;
use App\Models\Defi\FractionalOwnership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DefiPropertyYieldController extends Controller
{
    /**
     * Display a listing of property yields.
     */
    public function index(Request $request)
    {
        $query = PropertyYield::with(['staking', 'pool', 'ownership'])
            ->where('user_id', auth()->id());

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by period
        if ($request->has('period') && $request->period) {
            $query->where('period', $request->period);
        }

        // Filter by APR range
        if ($request->has('min_apr') && $request->min_apr) {
            $query->where('apr', '>=', $request->min_apr);
        }

        if ($request->has('max_apr') && $request->max_apr) {
            $query->where('apr', '<=', $request->max_apr);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $yields = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get statistics
        $stats = [
            'total_yields' => PropertyYield::where('user_id', auth()->id())->count(),
            'active_yields' => PropertyYield::where('user_id', auth()->id())
                ->where('status', 'active')->count(),
            'total_earned' => PropertyYield::where('user_id', auth()->id())
                ->where('status', 'active')->sum('amount'),
            'staking_yields' => PropertyYield::where('user_id', auth()->id())
                ->where('type', 'staking')->sum('amount'),
            'pool_yields' => PropertyYield::where('user_id', auth()->id())
                ->where('type', 'pool')->sum('amount'),
            'ownership_yields' => PropertyYield::where('user_id', auth()->id())
                ->where('type', 'dividend')->sum('amount'),
            'average_apr' => PropertyYield::where('user_id', auth()->id())
                ->where('status', 'active')->avg('apr'),
            'monthly_earnings' => $this->calculateMonthlyEarnings(),
        ];

        return Inertia::render('defi/yields/index', [
            'yields' => $yields,
            'stats' => $stats,
            'filters' => $request->only(['type', 'status', 'period', 'min_apr', 'max_apr', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Show the form for creating a new property yield.
     */
    public function create()
    {
        // Get user's active staking positions
        $stakingPositions = PropertyStaking::where('user_id', auth()->id())
            ->where('status', 'active')
            ->get();

        // Get user's liquidity pools
        $liquidityPools = PropertyLiquidityPool::whereHas('liquidityProviders', function ($query) {
            $query->where('user_id', auth()->id())->where('status', 'active');
        })->get();

        // Get user's fractional ownership positions
        $ownershipPositions = FractionalOwnership::where('user_id', auth()->id())
            ->where('status', 'active')
            ->get();

        return Inertia::render('defi/yields/create', [
            'staking_positions' => $stakingPositions,
            'liquidity_pools' => $liquidityPools,
            'ownership_positions' => $ownershipPositions,
        ]);
    }

    /**
     * Store a newly created property yield in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:staking,pool,dividend,compound,referral',
            'source_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'apr' => 'required|numeric|min:0|max:100',
            'period' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'description' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // Validate source and create yield
            $yield = $this->createYieldFromSource($request);

            DB::commit();

            return redirect()->route('defi.yields.show', $yield)
                ->with('success', 'تم إنشاء العائد بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء العائد: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified property yield.
     */
    public function show(PropertyYield $yield)
    {
        // Check if user owns the yield
        if ($yield->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا العائد');
        }

        $yield->load(['user', 'staking', 'pool', 'ownership']);

        // Calculate yield statistics
        $statistics = [
            'current_amount' => $yield->amount,
            'total_earned' => $yield->amount,
            'daily_earnings' => $this->calculateDailyEarnings($yield),
            'weekly_earnings' => $this->calculateWeeklyEarnings($yield),
            'monthly_earnings' => $this->calculateMonthlyEarningsForYield($yield),
            'yearly_earnings' => $this->calculateYearlyEarnings($yield),
            'next_payout_date' => $this->calculateNextPayoutDate($yield),
            'total_payouts' => $yield->payouts()->count(),
            'average_payout' => $yield->payouts()->avg('amount'),
            'yield_on_investment' => $this->calculateYieldOnInvestment($yield),
            'effective_apr' => $this->calculateEffectiveAPR($yield),
            'compounding_effect' => $this->calculateCompoundingEffect($yield),
        ];

        return Inertia::render('defi/yields/show', [
            'yield' => $yield,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for editing the specified property yield.
     */
    public function edit(PropertyYield $yield)
    {
        // Check if user owns the yield and it's active
        if ($yield->user_id !== auth()->id() || $yield->status !== 'active') {
            abort(403, 'لا يمكن تعديل هذا العائد');
        }

        return Inertia::render('defi/yields/edit', [
            'yield' => $yield,
        ]);
    }

    /**
     * Update the specified property yield in storage.
     */
    public function update(Request $request, PropertyYield $yield)
    {
        // Check if user owns the yield and it's active
        if ($yield->user_id !== auth()->id() || $yield->status !== 'active') {
            abort(403, 'لا يمكن تعديل هذا العائد');
        }

        $request->validate([
            'description' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
        ]);

        $yield->update([
            'description' => $request->description,
            'metadata' => $request->metadata,
            'updated_at' => now(),
        ]);

        return redirect()->route('defi.yields.show', $yield)
            ->with('success', 'تم تحديث العائد بنجاحاح');
    }

    /**
     * Remove the specified property yield from storage.
     */
    public function destroy(PropertyYield $yield)
    {
        // Check if user owns the yield
        if ($yield->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بحذف هذا العائد');
        }

        DB::beginTransaction();

        try {
            // Delete payouts
            $yield->payouts()->delete();
            
            // Delete yield
            $yield->delete();

            DB::commit();

            return redirect()->route('defi.yields.index')
                ->with('success', 'تم حذف العائد بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف العائد: ' . $e->getMessage());
        }
    }

    /**
     * Claim yield earnings.
     */
    public function claim(Request $request, PropertyYield $yield)
    {
        // Check if user owns the yield
        if ($yield->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بclaim هذا العائد');
        }

        if ($yield->status !== 'active') {
            abort(403, 'العائد غير نشط');
        }

        DB::beginTransaction();

        try {
            // Calculate claimable amount
            $claimableAmount = $this->calculateClaimableAmount($yield);

            if ($claimableAmount <= 0) {
                return back()->with('error', 'لا يوجد مبلغ قابل للسحب');
            }

            // Create payout record
            $yield->payouts()->create([
                'amount' => $claimableAmount,
                'currency' => 'USD', // Would be based on yield currency
                'status' => 'completed',
                'transaction_hash' => $this->generateTransactionHash(),
                'claimed_at' => now(),
                'created_at' => now(),
            ]);

            // Update yield
            $yield->update([
                'amount' => $yield->amount + $claimableAmount,
                'last_claimed_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'تم سحب العائد بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء سحب العائد: ' . $e->getMessage());
        }
    }

    /**
     * Compound yield earnings.
     */
    public function compound(Request $request, PropertyYield $yield)
    {
        // Check if user owns the yield
        if ($yield->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بتراكم هذا العائد');
        }

        if ($yield->type !== 'compound') {
            abort(403, 'التراكم غير متاحر لهذا النوع من العائد');
        }

        DB::beginTransaction();

        try {
            // Calculate compound amount
            $compoundAmount = $this->calculateCompoundAmount($yield);

            if ($compoundAmount <= 0) {
                return back()->with('error', 'لا يوجد مبلغ قابل للتراكم');
            }

            // Create compound record
            $yield->compounds()->create([
                'amount' => $compoundAmount,
                'apr' => $yield->apr,
                'period' => $yield->period,
                'status' => 'completed',
                'compounded_at' => now(),
                'created_at' => now(),
            ]);

            // Update yield
            $yield->update([
                'amount' => $yield->amount + $compoundAmount,
                'last_compounded_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'تم تراكم العائد بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تراكم العائد: ' . $e->getMessage());
        }
    }

    /**
     * Get yield analytics.
     */
    public function analytics()
    {
        $userYields = PropertyYield::where('user_id', auth()->id())->get();

        $analytics = [
            'total_earned' => $userYields->sum('amount'),
            'active_yields' => $userYields->where('status', 'active')->count(),
            'average_apr' => $userYields->where('status', 'active')->avg('apr'),
            'yield_distribution' => $this->getYieldDistribution($userYields),
            'period_distribution' => $this->getPeriodDistribution($userYields),
            'type_distribution' => $this->getTypeDistribution($userYields),
            'monthly_earnings' => $this->calculateMonthlyEarnings($userYields),
            'top_performing_yields' => $this->getTopPerformingYields($userYields),
            'yield_growth' => $this->getYieldGrowth($userYields),
            'diversification_score' => $this->calculateYieldDiversification($userYields),
        ];

        return Inertia::render('defi/yields/analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Get yield marketplace.
     */
    public function marketplace(Request $request)
    {
        $query = PropertyYield::with(['user', 'staking', 'pool', 'ownership'])
            ->where('status', 'active')
            ->where('is_public', true);

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by APR range
        if ($request->has('min_apr') && $request->min_apr) {
            $query->where('apr', '>=', $request->min_apr);
        }

        if ($request->has('max_apr') && $request->max_apr) {
            $query->where('apr', '<=', $request->max_apr);
        }

        // Filter by period
        if ($request->has('period') && $request->period) {
            $query->where('period', $request->period);
        }

        $yields = $query->orderBy('apr', 'desc')
            ->paginate(12);

        return Inertia::render('defi/yields/marketplace', [
            'yields' => $yields,
            'filters' => $request->only(['type', 'min_apr', 'max_apr', 'period']),
        ]);
    }

    /**
     * Create yield from source.
     */
    private function createYieldFromSource($request): PropertyYield
    {
        $sourceType = $request->type;
        $sourceId = $request->source_id;

        switch ($sourceType) {
            case 'staking':
                $staking = PropertyStaking::findOrFail($sourceId);
                if ($staking->user_id !== auth()->id()) {
                    abort(403, 'غير مصرح لك بإنشاء عائد من هذا التخزين');
                }
                
                return PropertyYield::create([
                    'user_id' => auth()->id(),
                    'property_staking_id' => $staking->id,
                    'type' => 'staking',
                    'amount' => $request->amount,
                    'apr' => $request->apr,
                    'period' => $request->period,
                    'description' => $request->description,
                    'metadata' => $request->metadata,
                    'status' => 'active',
                    'created_at' => now(),
                ]);

            case 'pool':
                $pool = PropertyLiquidityPool::findOrFail($sourceId);
                if (!$pool->liquidityProviders()->where('user_id', auth()->id())->exists()) {
                    abort(403, 'غير مصرح لك بإنشاء عائد من هذا المجمع');
                }
                
                return PropertyYield::create([
                    'user_id' => auth()->id(),
                    'property_liquidity_pool_id' => $pool->id,
                    'type' => 'pool',
                    'amount' => $request->amount,
                    'apr' => $request->apr,
                    'period' => $request->period,
                    'description' => $request->description,
                    'metadata' => $request->metadata,
                    'status' => 'active',
                    'created_at' => now(),
                ]);

            case 'dividend':
                $ownership = FractionalOwnership::findOrFail($sourceId);
                if ($ownership->user_id !== auth()->id()) {
                    abort(403, 'غير مصرح لك بإنشاء عائد من هذه الملكية');
                }
                
                return PropertyYield::create([
                    'user_id' => auth()->id(),
                    'fractional_ownership_id' => $ownership->id,
                    'type' => 'dividend',
                    'amount' => $request->amount,
                    'apr' => $request->apr,
                    'period' => $request->period,
                    'description' => $request->description,
                    'metadata' => $request->metadata,
                    'status' => 'active',
                    'created_at' => now(),
                ]);

            default:
                abort(400, 'نوع العائد غير صالح');
        }
    }

    /**
     * Generate transaction hash.
     */
    private function generateTransactionHash(): string
    {
        return '0x' . bin2hex(random_bytes(32));
    }

    /**
     * Calculate claimable amount.
     */
    private function calculateClaimableAmount($yield): float
    {
        // This would calculate based on actual yield accumulation
        // For now, return a mock calculation
        $dailyRate = $yield->apr / 365 / 100;
        $daysSinceLastClaim = now()->diffInDays($yield->last_claimed_at ?? $yield->created_at);
        
        return $yield->amount * $dailyRate * $daysSinceLastClaim;
    }

    /**
     * Calculate compound amount.
     */
    private function calculateCompoundAmount($yield): float
    {
        // This would calculate based on actual compound formula
        // For now, return a mock calculation
        $principal = $yield->amount;
        $rate = $yield->apr / 100 / 365;
        $days = 1; // Daily compounding
        
        return $principal * pow(1 + $rate, $days) - $principal;
    }

    /**
     * Calculate daily earnings.
     */
    private function calculateDailyEarnings($yield): float
    {
        $dailyRate = $yield->apr / 365 / 100;
        return $yield->amount * $dailyRate;
    }

    /**
     * Calculate weekly earnings.
     */
    private function calculateWeeklyEarnings($yield): float
    {
        return $this->calculateDailyEarnings($yield) * 7;
    }

    /**
     * Calculate monthly earnings for yield.
     */
    private function calculateMonthlyEarningsForYield($yield): float
    {
        return $this->calculateDailyEarnings($yield) * 30;
    }

    /**
     * Calculate yearly earnings.
     */
    private function calculateYearlyEarnings($yield): float
    {
        return $yield->amount * ($yield->apr / 100);
    }

    /**
     * Calculate next payout date.
     */
    private function calculateNextPayoutDate($yield): string
    {
        $period = $yield->period;
        $lastPayout = $yield->last_claimed_at ?? $yield->created_at;
        
        switch ($period) {
            case 'daily':
                return $lastPayout->addDay()->format('Y-m-d');
            case 'weekly':
                return $lastPayout->addWeek()->format('Y-m-d');
            case 'monthly':
                return $lastPayout->addMonth()->format('Y-m-d');
            case 'quarterly':
                return $lastPayout->addQuarter()->format('Y-m-d');
            case 'yearly':
                return $lastPayout->addYear()->format('Y-m-d');
            default:
                return now()->addMonth()->format('Y-m-d');
        }
    }

    /**
     * Calculate yield on investment.
     */
    private function calculateYieldOnInvestment($yield): float
    {
        $principal = $this->getInvestmentAmount($yield);
        
        if ($principal <= 0) {
            return 0;
        }
        
        return ($yield->amount / $principal) * 100;
    }

    /**
     * Calculate effective APR.
     */
    private function calculateEffectiveAPR($yield): float
    {
        // This would calculate based on compounding frequency
        // For now, return the nominal APR
        return $yield->apr;
    }

    /**
     * Calculate compounding effect.
     */
    private function calculateCompoundingEffect($yield): float
    {
        // This would calculate the difference between simple and compound interest
        // For now, return a mock calculation
        return $yield->apr * 0.05; // 5% additional yield from compounding
    }

    /**
     * Get investment amount.
     */
    private function getInvestmentAmount($yield): float
    {
        switch ($yield->type) {
            case 'staking':
                return $yield->staking->amount ?? 0;
            case 'pool':
                $userLiquidity = $yield->pool->liquidityProviders()
                    ->where('user_id', auth()->id())
                    ->first();
                return $userLiquidity ? $userLiquidity->amount : 0;
            case 'dividend':
                return $yield->ownership->total_invested ?? 0;
            default:
                return 0;
        }
    }

    /**
     * Calculate monthly earnings.
     */
    private function calculateMonthlyEarnings($yields = null): float
    {
        if ($yields === null) {
            $yields = PropertyYield::where('user_id', auth()->id())
                ->where('status', 'active')
                ->get();
        }
        
        $monthlyEarnings = 0;
        
        foreach ($yields as $yield) {
            $monthlyEarnings += $this->calculateMonthlyEarningsForYield($yield);
        }
        
        return $monthlyEarnings;
    }

    /**
     * Get yield distribution.
     */
    private function getYieldDistribution($yields): array
    {
        $distribution = [];
        
        foreach ($yields->where('status', 'active') as $yield) {
            $aprRange = $this->getAprRange($yield->apr);
            $key = $aprRange;
            
            if (!isset($distribution[$key])) {
                $distribution[$key] = [
                    'count' => 0,
                    'total_amount' => 0,
                    'average_apr' => 0,
                ];
            }
            
            $distribution[$key]['count']++;
            $distribution[$key]['total_amount'] += $yield->amount;
            $distribution[$key]['average_apr'] = ($distribution[$key]['average_apr'] * ($distribution[$key]['count'] - 1) + $yield->apr) / $distribution[$key]['count'];
        }
        
        return $distribution;
    }

    /**
     * Get period distribution.
     */
    private function getPeriodDistribution($yields): array
    {
        $distribution = [
            'daily' => ['count' => 0, 'total_amount' => 0],
            'weekly' => ['count' => 0, 'total_amount' => 0],
            'monthly' => ['count' => 0, 'total_amount' => 0],
            'quarterly' => ['count' => 0, 'total_amount' => 0],
            'yearly' => ['count' => 0, 'total_amount' => 0],
        ];
        
        foreach ($yields->where('status', 'active') as $yield) {
            $period = $yield->period;
            $distribution[$period]['count']++;
            $distribution[$period]['total_amount'] += $yield->amount;
        }
        
        return $distribution;
    }

    /**
     * Get type distribution.
     */
    private function getTypeDistribution($yields): array
    {
        $distribution = [
            'staking' => ['count' => 0, 'total_amount' => 0],
            'pool' => ['count' => 0, 'total_amount' => 0],
            'dividend' => ['count' => 0, 'total_amount' => 0],
            'compound' => ['count' => 0, 'total_amount' => 0],
            'referral' => ['count' => 0, 'total_amount' => 0],
        ];
        
        foreach ($yields->where('status', 'active') as $yield) {
            $type = $yield->type;
            $distribution[$type]['count']++;
            $distribution[$type]['total_amount'] += $yield->amount;
        }
        
        return $distribution;
    }

    /**
     * Get top performing yields.
     */
    private function getTopPerformingYields($yields): array
    {
        return $yields->where('status', 'active')
            ->sortByDesc('apr')
            ->take(5)
            ->values()
            ->toArray();
    }

    /**
     * Get yield growth.
     */
    private function getYieldGrowth($yields): array
    {
        $growth = [];
        
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths($i);
            $monthYields = 0;
            
            foreach ($yields->where('status', 'active') as $yield) {
                $monthYields += $this->calculateMonthlyEarningsForYield($yield);
            }
            
            $growth[$date->format('Y-m')] = $monthYields;
        }
        
        return $growth;
    }

    /**
     * Calculate yield diversification.
     */
    private function calculateYieldDiversification($yields): float
    {
        $activeYields = $yields->where('status', 'active');
        $totalAmount = $activeYields->sum('amount');
        
        if ($totalAmount <= 0) {
            return 0;
        }
        
        // Calculate concentration (inverse of diversification)
        $concentration = 0;
        foreach ($activeYields as $yield) {
            $weight = $yield->amount / $totalAmount;
            $concentration += $weight * $weight;
        }
        
        // Diversification score (0-100, higher is more diversified)
        return (1 - $concentration) * 100;
    }

    /**
     * Get APR range category.
     */
    private function getAprRange($apr): string
    {
        if ($apr < 5) {
            return '0-5%';
        } elseif ($apr < 10) {
            return '5-10%';
        } elseif ($apr < 15) {
            return '10-15%';
        } elseif ($apr < 20) {
            return '15-20%';
        } else {
            return '20%+';
        }
    }
}

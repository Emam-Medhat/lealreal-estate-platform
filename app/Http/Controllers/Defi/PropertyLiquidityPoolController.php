<?php

namespace App\Http\Controllers\Defi;

use App\Http\Controllers\Controller;
use App\Models\Defi\PropertyLiquidityPool;
use App\Models\Defi\PropertyToken;
use App\Models\Defi\DefiPropertyInvestment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PropertyLiquidityPoolController extends Controller
{
    /**
     * Display a listing of property liquidity pools.
     */
    public function index(Request $request)
    {
        $query = PropertyLiquidityPool::with(['token', 'property', 'creator', 'liquidityProviders'])
            ->where('is_public', true);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by token
        if ($request->has('token_id') && $request->token_id) {
            $query->where('property_token_id', $request->token_id);
        }

        // Filter by APR range
        if ($request->has('min_apr') && $request->min_apr) {
            $query->where('apr', '>=', $request->min_apr);
        }

        if ($request->has('max_apr') && $request->max_apr) {
            $query->where('apr', '<=', $request->max_apr);
        }

        // Filter by TVL range
        if ($request->has('min_tvl') && $request->min_tvl) {
            $query->where('total_liquidity', '>=', $request->min_tvl);
        }

        if ($request->has('max_tvl') && $request->max_tvl) {
            $query->where('total_liquidity', '<=', $request->max_tvl);
        }

        $pools = $query->orderBy('apr', 'desc')
            ->paginate(12);

        // Get statistics
        $stats = [
            'total_pools' => PropertyLiquidityPool::where('is_public', true)->count(),
            'active_pools' => PropertyLiquidityPool::where('is_public', true)
                ->where('status', 'active')->count(),
            'total_tvl' => PropertyLiquidityPool::where('is_public', true)
                ->where('status', 'active')->sum('total_liquidity'),
            'average_apr' => PropertyLiquidityPool::where('is_public', true)
                ->where('status', 'active')->avg('apr'),
            'total_providers' => PropertyLiquidityPool::where('is_public', true)
                ->where('status', 'active')->sum('provider_count'),
            'total_volume_24h' => PropertyLiquidityPool::where('is_public', true)
                ->where('status', 'active')->sum('volume_24h'),
        ];

        return Inertia::render('defi/liquidity-pools/index', [
            'pools' => $pools,
            'stats' => $stats,
            'filters' => $request->only(['status', 'token_id', 'min_apr', 'max_apr', 'min_tvl', 'max_tvl']),
        ]);
    }

    /**
     * Show the form for creating a new property liquidity pool.
     */
    public function create()
    {
        // Get user's tokens that can be used for pools
        $tokens = PropertyToken::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->where('distributed_supply', '>', 0)
            ->get();

        return Inertia::render('defi/liquidity-pools/create', [
            'tokens' => $tokens,
        ]);
    }

    /**
     * Store a newly created property liquidity pool in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'property_token_id' => 'required|exists:property_tokens,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'apr' => 'required|numeric|min:0|max:100',
            'fee_percentage' => 'required|numeric|min:0|max:10',
            'minimum_liquidity' => 'required|numeric|min:100',
            'maximum_liquidity' => 'nullable|numeric|min:0',
            'lock_period' => 'required|integer|min:0',
            'auto_compound' => 'boolean',
            'rebalancing_enabled' => 'boolean',
            'rebalancing_threshold' => 'nullable|numeric|min:0|max:100',
            'is_public' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // Validate token ownership
            $token = PropertyToken::findOrFail($request->property_token_id);
            if ($token->owner_id !== auth()->id()) {
                abort(403, 'غير مصرح لك بإنشاء مجمع سيولة لهذا التوكن');
            }

            // Create liquidity pool
            $pool = PropertyLiquidityPool::create([
                'property_token_id' => $request->property_token_id,
                'property_id' => $token->property_id,
                'creator_id' => auth()->id(),
                'name' => $request->name,
                'description' => $request->description,
                'apr' => $request->apr,
                'fee_percentage' => $request->fee_percentage,
                'total_liquidity' => 0,
                'total_shares' => 0,
                'provider_count' => 0,
                'minimum_liquidity' => $request->minimum_liquidity,
                'maximum_liquidity' => $request->maximum_liquidity,
                'lock_period' => $request->lock_period,
                'auto_compound' => $request->auto_compound,
                'rebalancing_enabled' => $request->rebalancing_enabled,
                'rebalancing_threshold' => $request->rebalancing_threshold,
                'volume_24h' => 0,
                'volume_7d' => 0,
                'volume_30d' => 0,
                'fees_collected_24h' => 0,
                'fees_collected_7d' => 0,
                'fees_collected_30d' => 0,
                'status' => 'pending',
                'is_public' => $request->is_public,
                'smart_contract_address' => null, // Will be set when deployed
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('defi.pools.show', $pool)
                ->with('success', 'تم إنشاء مجمع السيولة بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء مجمع السيولة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified property liquidity pool.
     */
    public function show(PropertyLiquidityPool $pool)
    {
        $pool->load(['token', 'property', 'creator', 'liquidityProviders']);

        // Calculate pool statistics
        $statistics = [
            'tvl' => $pool->total_liquidity,
            'total_shares' => $pool->total_shares,
            'share_price' => $pool->total_shares > 0 ? $pool->total_liquidity / $pool->total_shares : 0,
            'apr' => $pool->apr,
            'daily_volume' => $pool->volume_24h,
            'weekly_volume' => $pool->volume_7d,
            'monthly_volume' => $pool->volume_30d,
            'daily_fees' => $pool->fees_collected_24h,
            'weekly_fees' => $pool->fees_collected_7d,
            'monthly_fees' => $pool->fees_collected_30d,
            'provider_count' => $pool->provider_count,
            'utilization_rate' => $this->calculateUtilizationRate($pool),
            'impermanent_loss' => $this->calculateImpermanentLoss($pool),
            'user_liquidity' => $this->getUserLiquidity($pool),
            'user_shares' => $this->getUserShares($pool),
            'user_earnings' => $this->getUserEarnings($pool),
            'can_withdraw' => $this->canWithdrawFromPool($pool),
            'next_compound_date' => $pool->auto_compound ? now()->addDay() : null,
        ];

        return Inertia::render('defi/liquidity-pools/show', [
            'pool' => $pool,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for editing the specified property liquidity pool.
     */
    public function edit(PropertyLiquidityPool $pool)
    {
        // Check if user owns the pool and it's not deployed
        if ($pool->creator_id !== auth()->id() || $pool->status === 'active') {
            abort(403, 'لا يمكن تعديل هذا المجمع');
        }

        return Inertia::render('defi/liquidity-pools/edit', [
            'pool' => $pool,
        ]);
    }

    /**
     * Update the specified property liquidity pool in storage.
     */
    public function update(Request $request, PropertyLiquidityPool $pool)
    {
        // Check if user owns the pool and it's not deployed
        if ($pool->creator_id !== auth()->id() || $pool->status === 'active') {
            abort(403, 'لا يمكن تعديل هذا المجمع');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'apr' => 'required|numeric|min:0|max:100',
            'fee_percentage' => 'required|numeric|min:0|max:10',
            'minimum_liquidity' => 'required|numeric|min:100',
            'maximum_liquidity' => 'nullable|numeric|min:0',
            'lock_period' => 'required|integer|min:0',
            'auto_compound' => 'boolean',
            'rebalancing_enabled' => 'boolean',
            'rebalancing_threshold' => 'nullable|numeric|min:0|max:100',
            'is_public' => 'boolean',
        ]);

        $pool->update([
            'name' => $request->name,
            'description' => $request->description,
            'apr' => $request->apr,
            'fee_percentage' => $request->fee_percentage,
            'minimum_liquidity' => $request->minimum_liquidity,
            'maximum_liquidity' => $request->maximum_liquidity,
            'lock_period' => $request->lock_period,
            'auto_compound' => $request->auto_compound,
            'rebalancing_enabled' => $request->rebalancing_enabled,
            'rebalancing_threshold' => $request->rebalancing_threshold,
            'is_public' => $request->is_public,
            'updated_at' => now(),
        ]);

        return redirect()->route('defi.pools.show', $pool)
            ->with('success', 'تم تحديث المجمع بنجاحاح');
    }

    /**
     * Remove the specified property liquidity pool from storage.
     */
    public function destroy(PropertyLiquidityPool $pool)
    {
        // Check if user owns the pool and it's not active
        if ($pool->creator_id !== auth()->id() || $pool->status === 'active') {
            abort(403, 'لا يمكن حذف هذا المجمع');
        }

        DB::beginTransaction();

        try {
            // Delete liquidity providers
            $pool->liquidityProviders()->delete();
            
            // Delete pool
            $pool->delete();

            DB::commit();

            return redirect()->route('defi.pools.index')
                ->with('success', 'تم حذف المجمع بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف المجمع: ' . $e->getMessage());
        }
    }

    /**
     * Deploy pool smart contract.
     */
    public function deploy(Request $request, PropertyLiquidityPool $pool)
    {
        // Check if user owns the pool
        if ($pool->creator_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بنشر هذا المجمع');
        }

        if ($pool->status !== 'pending') {
            abort(403, 'المجمع ليس في حالة انتظار');
        }

        DB::beginTransaction();

        try {
            // Deploy smart contract
            $smartContractAddress = $this->deployPoolSmartContract($pool);

            // Update pool status
            $pool->update([
                'status' => 'active',
                'smart_contract_address' => $smartContractAddress,
                'deployed_at' => now(),
                'deployed_by' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'تم نشر المجمع بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء نشر المجمع: ' . $e->getMessage());
        }
    }

    /**
     * Add liquidity to pool.
     */
    public function addLiquidity(Request $request, PropertyLiquidityPool $pool)
    {
        $request->validate([
            'amount' => 'required|numeric|min:' . $pool->minimum_liquidity,
            'currency' => 'required|string|max:10|in:USD,EUR,GBP,ETH,BTC,USDT',
        ]);

        if ($pool->status !== 'active') {
            abort(403, 'المجمع غير نشط');
        }

        if ($pool->maximum_liquidity && $pool->total_liquidity + $request->amount > $pool->maximum_liquidity) {
            return back()->with('error', 'المبلغ يتجاوز الحد الأقصى للمجمع');
        }

        DB::beginTransaction();

        try {
            // Calculate shares
            $sharePrice = $pool->total_shares > 0 ? $pool->total_liquidity / $pool->total_shares : 1;
            $shares = $request->amount / $sharePrice;

            // Create liquidity provider record
            $pool->liquidityProviders()->create([
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'shares' => $shares,
                'currency' => $request->currency,
                'lock_period' => $pool->lock_period,
                'locked_until' => now()->addDays($pool->lock_period),
                'status' => 'active',
                'created_at' => now(),
            ]);

            // Update pool
            $pool->update([
                'total_liquidity' => $pool->total_liquidity + $request->amount,
                'total_shares' => $pool->total_shares + $shares,
                'provider_count' => $pool->liquidityProviders()->distinct('user_id')->count(),
            ]);

            DB::commit();

            return back()->with('success', 'تم إضافة السيولة بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إضافة السيولة: ' . $e->getMessage());
        }
    }

    /**
     * Remove liquidity from pool.
     */
    public function removeLiquidity(Request $request, PropertyLiquidityPool $pool)
    {
        $request->validate([
            'shares' => 'required|numeric|min:0.01',
        ]);

        // Check if user has liquidity in the pool
        $userLiquidity = $pool->liquidityProviders()
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (!$userLiquidity) {
            return back()->with('error', 'ليس لديك سيولة في هذا المجمع');
        }

        if ($userLiquidity->shares < $request->shares) {
            return back()->with('error', 'الأسهم المطلوبة تتجاوز الأسهم المملوكة');
        }

        if (!$this->canWithdrawFromPool($pool)) {
            return back()->with('error', 'فترة القفل لم تنتهِ بعد');
        }

        DB::beginTransaction();

        try {
            // Calculate withdrawal amount
            $sharePrice = $pool->total_shares > 0 ? $pool->total_liquidity / $pool->total_shares : 1;
            $withdrawalAmount = $request->shares * $sharePrice;

            // Update user liquidity
            if ($userLiquidity->shares - $request->shares <= 0) {
                $userLiquidity->update([
                    'status' => 'withdrawn',
                    'withdrawn_at' => now(),
                ]);
            } else {
                $userLiquidity->update([
                    'shares' => $userLiquidity->shares - $request->shares,
                    'amount' => ($userLiquidity->shares - $request->shares) * $sharePrice,
                    'updated_at' => now(),
                ]);
            }

            // Update pool
            $pool->update([
                'total_liquidity' => $pool->total_liquidity - $withdrawalAmount,
                'total_shares' => $pool->total_shares - $request->shares,
                'provider_count' => $pool->liquidityProviders()->where('status', 'active')->distinct('user_id')->count(),
            ]);

            DB::commit();

            return back()->with('success', 'تم سحب السيولة بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء سحب السيولة: ' . $e->getMessage());
        }
    }

    /**
     * Compound pool earnings.
     */
    public function compound(Request $request, PropertyLiquidityPool $pool)
    {
        if (!$pool->auto_compound) {
            abort(403, 'التراكم التلقائي غير مفعلل لهذا المجمع');
        }

        DB::beginTransaction();

        try {
            // Calculate compound earnings
            $feesToCompound = $pool->fees_collected_24h;
            $newShares = $feesToCompound / ($pool->total_shares > 0 ? $pool->total_liquidity / $pool->total_shares : 1);

            // Update pool
            $pool->update([
                'total_liquidity' => $pool->total_liquidity + $feesToCompound,
                'total_shares' => $pool->total_shares + $newShares,
                'last_compounded_at' => now(),
                'updated_at' => now(),
            ]);

            // Distribute shares to liquidity providers proportionally
            $this->distributeCompoundShares($pool, $newShares);

            DB::commit();

            return back()->with('success', 'تم تراكم الأرباح بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تراكم الأرباح: ' . $e->getMessage());
        }
    }

    /**
     * Get pool analytics.
     */
    public function analytics()
    {
        $userPools = PropertyLiquidityPool::where('creator_id', auth()->id())->get();

        $analytics = [
            'total_pools' => $userPools->count(),
            'active_pools' => $userPools->where('status', 'active')->count(),
            'total_tvl' => $userPools->where('status', 'active')->sum('total_liquidity'),
            'total_providers' => $userPools->where('status', 'active')->sum('provider_count'),
            'total_fees' => $userPools->where('status', 'active')->sum('fees_collected_30d'),
            'average_apr' => $userPools->where('status', 'active')->avg('apr'),
            'total_volume' => $userPools->where('status', 'active')->sum('volume_30d'),
            'pool_performance' => $this->getPoolPerformance($userPools),
            'monthly_tvl' => $this->calculateMonthlyTVL($userPools),
            'top_performing_pools' => $this->getTopPerformingPools($userPools),
        ];

        return Inertia::render('defi/liquidity-pools/analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Get user's liquidity positions.
     */
    public function myPositions()
    {
        $positions = PropertyLiquidityPool::with(['token', 'property'])
            ->whereHas('liquidityProviders', function ($query) {
                $query->where('user_id', auth()->id())->where('status', 'active');
            })
            ->get()
            ->map(function ($pool) {
                $userLiquidity = $pool->liquidityProviders()
                    ->where('user_id', auth()->id())
                    ->where('status', 'active')
                    ->first();

                return [
                    'pool' => $pool,
                    'user_liquidity' => $userLiquidity,
                    'share_percentage' => $pool->total_shares > 0 ? ($userLiquidity->shares / $pool->total_shares) * 100 : 0,
                    'daily_earnings' => $this->calculateUserDailyEarnings($pool, $userLiquidity),
                    'can_withdraw' => $this->canWithdrawFromPool($pool),
                ];
            });

        return Inertia::render('defi/liquidity-pools/positions', [
            'positions' => $positions,
        ]);
    }

    /**
     * Deploy pool smart contract.
     */
    private function deployPoolSmartContract($pool): string
    {
        // This would integrate with a smart contract deployment service
        // For now, return a mock address
        return '0x' . bin2hex(random_bytes(20));
    }

    /**
     * Calculate utilization rate.
     */
    private function calculateUtilizationRate($pool): float
    {
        // This would calculate based on actual pool usage
        // For now, return a mock calculation
        return min(100, ($pool->volume_24h / $pool->total_liquidity) * 100);
    }

    /**
     * Calculate impermanent loss.
     */
    private function calculateImpermanentLoss($pool): float
    {
        // This would calculate based on actual price movements
        // For now, return a mock calculation
        return 0; // No impermanent loss for single-asset pools
    }

    /**
     * Get user liquidity.
     */
    private function getUserLiquidity($pool): float
    {
        $userLiquidity = $pool->liquidityProviders()
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        return $userLiquidity ? $userLiquidity->amount : 0;
    }

    /**
     * Get user shares.
     */
    private function getUserShares($pool): float
    {
        $userLiquidity = $pool->liquidityProviders()
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        return $userLiquidity ? $userLiquidity->shares : 0;
    }

    /**
     * Get user earnings.
     */
    private function getUserEarnings($pool): float
    {
        // This would calculate based on actual earnings
        // For now, return a mock calculation
        $userShares = $this->getUserShares($pool);
        $totalFees = $pool->fees_collected_30d;
        $userSharePercentage = $pool->total_shares > 0 ? ($userShares / $pool->total_shares) : 0;

        return $totalFees * $userSharePercentage;
    }

    /**
     * Check if user can withdraw from pool.
     */
    private function canWithdrawFromPool($pool): bool
    {
        $userLiquidity = $pool->liquidityProviders()
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (!$userLiquidity) {
            return false;
        }

        return $userLiquidity->locked_until <= now();
    }

    /**
     * Distribute compound shares.
     */
    private function distributeCompoundShares($pool, $newShares): void
    {
        $providers = $pool->liquidityProviders()->where('status', 'active')->get();
        
        foreach ($providers as $provider) {
            $sharePercentage = $provider->shares / $pool->total_shares;
            $additionalShares = $newShares * $sharePercentage;
            
            $provider->update([
                'shares' => $provider->shares + $additionalShares,
                'amount' => ($provider->shares + $additionalShares) * ($pool->total_liquidity / $pool->total_shares),
            ]);
        }
    }

    /**
     * Get pool performance.
     */
    private function getPoolPerformance($pools): array
    {
        return $pools->where('status', 'active')->map(function ($pool) {
            return [
                'name' => $pool->name,
                'tvl' => $pool->total_liquidity,
                'apr' => $pool->apr,
                'volume_24h' => $pool->volume_24h,
                'fees_24h' => $pool->fees_collected_24h,
                'providers' => $pool->provider_count,
            ];
        })->toArray();
    }

    /**
     * Calculate monthly TVL.
     */
    private function calculateMonthlyTVL($pools): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthTVL = 0;
            
            foreach ($pools->where('status', 'active') as $pool) {
                // This would calculate based on historical data
                // For now, use current TVL
                $monthTVL += $pool->total_liquidity;
            }
            
            $monthlyData[$date->format('Y-m')] = $monthTVL;
        }

        return $monthlyData;
    }

    /**
     * Get top performing pools.
     */
    private function getTopPerformingPools($pools): array
    {
        return $pools->where('status', 'active')
            ->sortByDesc('apr')
            ->take(5)
            ->values()
            ->toArray();
    }

    /**
     * Calculate user daily earnings.
     */
    private function calculateUserDailyEarnings($pool, $userLiquidity): float
    {
        $dailyFees = $pool->fees_collected_24h;
        $userSharePercentage = $pool->total_shares > 0 ? ($userLiquidity->shares / $pool->total_shares) : 0;

        return $dailyFees * $userSharePercentage;
    }
}

<?php

namespace App\Http\Controllers\Defi;

use App\Http\Controllers\Controller;
use App\Models\Defi\PropertyStaking;
use App\Models\Defi\PropertyYield;
use App\Models\Defi\PropertyToken;
use App\Models\User;
use App\Http\Requests\Defi\StakePropertyTokensRequest;
use App\Http\Requests\Defi\UnstakePropertyTokensRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DefiPropertyStakingController extends Controller
{
    /**
     * Display a listing of property staking positions.
     */
    public function index(Request $request)
    {
        $query = PropertyStaking::with(['user', 'token', 'property', 'yields'])
            ->where('user_id', auth()->id());

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by token
        if ($request->has('token_id') && $request->token_id) {
            $query->where('property_token_id', $request->token_id);
        }

        // Filter by staking period
        if ($request->has('min_period') && $request->min_period) {
            $query->where('staking_period', '>=', $request->min_period);
        }

        if ($request->has('max_period') && $request->max_period) {
            $query->where('staking_period', '<=', $request->max_period);
        }

        $stakes = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics
        $stats = [
            'total_stakes' => PropertyStaking::where('user_id', auth()->id())->count(),
            'active_stakes' => PropertyStaking::where('user_id', auth()->id())
                ->where('status', 'active')->count(),
            'total_staked' => PropertyStaking::where('user_id', auth()->id())
                ->where('status', 'active')->sum('amount'),
            'total_earned' => PropertyStaking::where('user_id', auth()->id())
                ->where('status', 'active')->sum('total_earned'),
            'average_apr' => PropertyStaking::where('user_id', auth()->id())
                ->where('status', 'active')->avg('apr'),
            'total_yields' => PropertyStaking::where('user_id', auth()->id())
                ->where('status', 'active')->sum('total_yields'),
        ];

        return Inertia::render('defi/staking/index', [
            'stakes' => $stakes,
            'stats' => $stats,
            'filters' => $request->only(['status', 'token_id', 'min_period', 'max_period']),
        ]);
    }

    /**
     * Show the form for creating a new property staking position.
     */
    public function create()
    {
        // Get user's tokens that can be staked
        $tokens = PropertyToken::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->where('distributed_supply', '>', 0)
            ->get();

        return Inertia::render('defi/staking/create', [
            'tokens' => $tokens,
        ]);
    }

    /**
     * Store a newly created property staking position in storage.
     */
    public function store(StakePropertyTokensRequest $request)
    {
        DB::beginTransaction();

        try {
            // Validate token ownership and balance
            $token = PropertyToken::findOrFail($request->property_token_id);
            if ($token->owner_id !== auth()->id()) {
                abort(403, 'غير مصرح لك بتخزين هذا التوكن');
            }

            $userBalance = $this->getUserTokenBalance($token, auth()->user());
            if ($userBalance < $request->amount) {
                return back()->with('error', 'رصيدك غير كافي للتخزين');
            }

            // Calculate staking rewards
            $apr = $request->apr;
            $stakingPeriod = $request->staking_period;
            $totalRewards = $this->calculateStakingRewards($request->amount, $apr, $stakingPeriod);

            // Create staking position
            $stake = PropertyStaking::create([
                'user_id' => auth()->id(),
                'property_token_id' => $request->property_token_id,
                'amount' => $request->amount,
                'staking_period' => $request->staking_period,
                'apr' => $apr,
                'rewards_rate' => $request->rewards_rate,
                'lock_period' => $request->lock_period,
                'auto_compound' => $request->auto_compound,
                'minimum_stake' => $request->minimum_stake,
                'maximum_stake' => $request->maximum_stake,
                'total_earned' => 0,
                'total_yields' => 0,
                'last_compound_at' => null,
                'unstaking_available_at' => now()->addDays($request->lock_period),
                'status' => 'active',
                'smart_contract_address' => null, // Will be set when deployed
                'created_at' => now(),
            ]);

            // Lock tokens in smart contract
            $this->lockTokensInContract($stake);

            // Create initial yield record
            PropertyYield::create([
                'property_staking_id' => $stake->id,
                'amount' => 0,
                'type' => 'staking',
                'apr' => $apr,
                'period' => 'monthly',
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('defi.staking.show', $stake)
                ->with('success', 'تم بدء التخزين بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء بدء التخزين: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified property staking position.
     */
    public function show(PropertyStaking $stake)
    {
        // Check if user owns the staking position
        if ($stake->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا التخزين');
        }

        $stake->load(['user', 'token', 'property', 'yields']);

        // Calculate staking statistics
        $statistics = [
            'current_value' => $stake->amount * $stake->token->price_per_token,
            'total_value_earned' => $stake->total_earned,
            'total_yields_earned' => $stake->total_yields,
            'daily_earnings' => $this->calculateDailyEarnings($stake),
            'monthly_earnings' => $this->calculateMonthlyEarnings($stake),
            'progress_percentage' => $this->calculateStakingProgress($stake),
            'days_until_unlock' => $this->calculateDaysUntilUnlock($stake),
            'compound_frequency' => $stake->auto_compound ? 'daily' : 'manual',
            'next_compound_date' => $stake->auto_compound ? $stake->last_compound_at->addDay() : null,
            'estimated_total_rewards' => $this->calculateStakingRewards($stake->amount, $stake->apr, $stake->staking_period),
        ];

        return Inertia::render('defi/staking/show', [
            'stake' => $stake,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for editing the specified property staking position.
     */
    public function edit(PropertyStaking $stake)
    {
        // Check if user owns the staking position and it's not locked
        if ($stake->user_id !== auth()->id() || $stake->status !== 'active') {
            abort(403, 'لا يمكن تعديل هذا التخزين');
        }

        return Inertia::render('defi/staking/edit', [
            'stake' => $stake,
        ]);
    }

    /**
     * Update the specified property staking position in storage.
     */
    public function update(StakePropertyTokensRequest $request, PropertyStaking $stake)
    {
        // Check if user owns the staking position and it's active
        if ($stake->user_id !== auth()->id() || $stake->status !== 'active') {
            abort(403, 'لا يمكن تعديل هذا التخزين');
        }

        DB::beginTransaction();

        try {
            // Update staking position
            $stake->update([
                'apr' => $request->apr,
                'rewards_rate' => $request->rewards_rate,
                'auto_compound' => $request->auto_compound,
                'updated_at' => now(),
            ]);

            // Update yield record
            $stake->yields()->latest()->update([
                'apr' => $request->apr,
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('defi.staking.show', $stake)
                ->with('success', 'تم تحديث التخزين بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث التخزين: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified property staking position from storage.
     */
    public function destroy(PropertyStaking $stake)
    {
        // Check if user owns the staking position and it can be unstaked
        if ($stake->user_id !== auth()->id() || !$this->canUnstake($stake)) {
            abort(403, 'لا يمكن إلغاء هذا التخزين');
        }

        DB::beginTransaction();

        try {
            // Unstake tokens
            $this->unstakeTokensFromContract($stake);

            // Update status
            $stake->update([
                'status' => 'completed',
                'completed_at' => now(),
                'unstaking_date' => now(),
            ]);

            // Release any remaining yields
            $stake->yields()->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('defi.staking.index')
                ->with('success', 'تم إلغاء التخزين بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إلغاء التخزين: ' . $e->getMessage());
        }
    }

    /**
     * Unstake tokens from staking position.
     */
    public function unstake(UnstakePropertyTokensRequest $request, PropertyStaking $stake)
    {
        // Check if user owns the staking position
        if ($stake->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بإلغاء هذا التخزين');
        }

        if (!$this->canUnstake($stake)) {
            return back()->with('error', 'لا يمكن إلغاء التخزين قبل انتهاء فترة القفل');
        }

        DB::beginTransaction();

        try {
            // Validate unstake amount
            if ($request->amount > $stake->amount) {
                return back()->with('error', 'المبلغ المطلوب يتجاوز المبلغ المخزون');
            }

            // Unstake tokens
            $this->unstakeTokensFromContract($stake, $request->amount);

            // Update staking position
            $stake->update([
                'amount' => $stake->amount - $request->amount,
                'updated_at' => now(),
            ]);

            // If fully unstaked, mark as completed
            if ($stake->amount <= 0) {
                $stake->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'unstaking_date' => now(),
                ]);

                // Release yields
                $stake->yields()->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }

            DB::commit();

            return back()->with('success', 'تم إلغاء جزء من التخزين بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إلغاء التخزين: ' . $e->getMessage());
        }
    }

    /**
     * Compound staking rewards.
     */
    public function compound(PropertyStaking $stake)
    {
        // Check if user owns the staking position and compounding is enabled
        if ($stake->user_id !== auth()->id() || !$stake->auto_compound) {
            abort(403, 'التراكم التلقائي غير مفعلل لهذا التخزين');
        }

        DB::beginTransaction();

        try {
            // Calculate compound rewards
            $rewards = $this->calculateCompoundRewards($stake);

            // Update staking position
            $stake->update([
                'total_earned' => $stake->total_earned + $rewards,
                'total_yields' => $stake->total_yields + $rewards,
                'last_compound_at' => now(),
                'updated_at' => now(),
            ]);

            // Create yield record
            PropertyYield::create([
                'property_staking_id' => $stake->id,
                'amount' => $rewards,
                'type' => 'compound',
                'apr' => $stake->apr,
                'period' => $stake->auto_compound ? 'daily' : 'manual',
                'created_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'تم تراكم المكافآت بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تراكم المكافآت: ' . $e->getMessage());
        }
    }

    /**
     * Get staking analytics.
     */
    public function analytics()
    {
        $userStakes = PropertyStaking::where('user_id', auth()->id())->get();

        $analytics = [
            'total_staked' => $userStakes->where('status', 'active')->sum('amount'),
            'total_earned' => $userStakes->where('status', 'active')->sum('total_earned'),
            'total_yields' => $userStakes->where('status', 'active')->sum('total_yields'),
            'active_stakes' => $userStakes->where('status', 'active')->count(),
            'completed_stakes' => $userStakes->where('status', 'completed')->count(),
            'average_apr' => $userStakes->where('status', 'active')->avg('apr'),
            'staking_distribution' => $this->getStakingDistribution($userStakes),
            'monthly_earnings' => $this->calculateMonthlyEarningsForStakes($userStakes),
            'top_performing_stakes' => $this->getTopPerformingStakes($userStakes),
            'auto_compound_stakes' => $userStakes->where('auto_compound', true)->count(),
        ];

        return Inertia::render('defi/staking/analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Get staking marketplace.
     */
    public function marketplace(Request $request)
    {
        $query = PropertyStaking::with(['user', 'token', 'property'])
            ->where('status', 'active')
            ->where('is_public', true);

        // Filter by APR range
        if ($request->has('min_apr') && $request->min_apr) {
            $query->where('apr', '>=', $request->min_apr);
        }

        if ($request->has('max_apr') && $request->max_apr) {
            $query->where('apr', '<=', $request->max_apr);
        }

        // Filter by staking period
        if ($request->has('min_period') && $request->min_period) {
            $query->where('staking_period', '>=', $request->min_period);
        }

        if ($request->has('max_period') && $request->max_period) {
            $query->where('staking_period', '<=', $request->max_period);
        }

        // Filter by minimum stake
        if ($request->has('min_stake') && $request->min_stake) {
            $query->where('minimum_stake', '<=', $request->min_stake);
        }

        $stakes = $query->orderBy('apr', 'desc')
            ->paginate(12);

        return Inertia::render('defi/staking/marketplace', [
            'stakes' => $stakes,
            'filters' => $request->only(['min_apr', 'max_apr', 'min_period', 'max_period', 'min_stake']),
        ]);
    }

    /**
     * Get staking pools.
     */
    public function pools()
    {
        // Get available staking pools
        $pools = PropertyStaking::with(['token', 'property'])
            ->where('status', 'active')
            ->where('is_public', true)
            ->where('minimum_stake', '<=', 1000) // Reasonable minimum
            ->orderBy('apr', 'desc')
            ->get()
            ->groupBy('property_token_id')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'token' => $first->token,
                    'property' => $first->property,
                    'total_staked' => $group->sum('amount'),
                    'stakers_count' => $group->count(),
                    'average_apr' => $group->avg('apr'),
                    'minimum_stake' => $first->minimum_stake,
                    'maximum_stake' => $first->maximum_stake,
                    'staking_period' => $first->staking_period,
                    'auto_compound' => $first->auto_compound,
                ];
            });

        return Inertia::render('defi/staking/pools', [
            'pools' => $pools,
        ]);
    }

    /**
     * Get user token balance.
     */
    private function getUserTokenBalance($token, $user): float
    {
        // This would query the blockchain or token contract
        // For now, return a mock balance
        return 1000; // Mock balance
    }

    /**
     * Lock tokens in smart contract.
     */
    private function lockTokensInContract($stake): void
    {
        // This would interact with the smart contract to lock tokens
        // For now, just log the action
        \Log::info("Locked {$stake->amount} tokens for staking {$stake->id}");
    }

    /**
     * Unstake tokens from smart contract.
     */
    private function unstakeTokensFromContract($stake, $amount = null): void
    {
        $unstakeAmount = $amount ?? $stake->amount;

        // This would interact with the smart contract to unlock tokens
        // For now, just log the action
        \Log::info("Unstaked {$unstakeAmount} tokens from staking {$stake->id}");
    }

    /**
     * Calculate staking rewards.
     */
    private function calculateStakingRewards($amount, $apr, $period): float
    {
        // Simple interest calculation: P * r * t
        // where P = principal, r = annual rate, t = time in years
        $dailyRate = $apr / 365 / 100;
        $periodInDays = $period;

        return $amount * $dailyRate * $periodInDays;
    }

    /**
     * Calculate compound rewards.
     */
    private function calculateCompoundRewards($stake): float
    {
        // Compound interest calculation
        $principal = $stake->amount;
        $rate = $stake->apr / 100 / 365;
        $days = 1; // Daily compounding

        // Compound interest formula: P * (1 + r)^t - P
        return $principal * pow(1 + $rate, $days) - $principal;
    }

    /**
     * Check if staking can be unstaked.
     */
    private function canUnstake($stake): bool
    {
        // Check if lock period has passed
        if ($stake->lock_period > 0) {
            return now()->diffInDays($stake->created_at) >= $stake->lock_period;
        }

        return true;
    }

    /**
     * Calculate daily earnings.
     */
    private function calculateDailyEarnings($stake): float
    {
        $dailyRate = $stake->apr / 365 / 100;
        return $stake->amount * $dailyRate;
    }

    /**
     * Calculate monthly earnings.
     */
    private function calculateMonthlyEarnings($stake): float
    {
        return $this->calculateDailyEarnings($stake) * 30;
    }

    /**
     * Calculate staking progress.
     */
    private function calculateStakingProgress($stake): float
    {
        if ($stake->staking_period <= 0) {
            return 0;
        }

        $daysElapsed = now()->diffInDays($stake->created_at);
        return min(100, ($daysElapsed / $stake->staking_period) * 100);
    }

    /**
     * Calculate days until unlock.
     */
    private function calculateDaysUntilUnlock($stake): int
    {
        if ($stake->lock_period <= 0) {
            return 0;
        }

        $unlockDate = $stake->created_at->addDays($stake->lock_period);
        return max(0, now()->diffInDays($unlockDate));
    }

    /**
     * Get staking distribution.
     */
    private function getStakingDistribution($stakes): array
    {
        $distribution = [];

        foreach ($stakes as $stake) {
            $aprRange = $this->getAprRange($stake->apr);
            $key = $aprRange;

            if (!isset($distribution[$key])) {
                $distribution[$key] = [
                    'count' => 0,
                    'total_staked' => 0,
                    'average_apr' => 0,
                ];
            }

            $distribution[$key]['count']++;
            $distribution[$key]['total_staked'] += $stake->amount;
            $distribution[$key]['average_apr'] = ($distribution[$key]['average_apr'] * ($distribution[$key]['count'] - 1) + $stake->apr) / $distribution[$key]['count'];
        }

        return $distribution;
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

    private function calculateMonthlyEarningsForStakes($stakes): array
    {
        $monthlyData = [];

        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthEarnings = 0;

            foreach ($stakes->where('status', 'active') as $stake) {
                $monthEarnings += $this->calculateMonthlyEarnings($stake);
            }

            $monthlyData[$date->format('Y-m')] = $monthEarnings;
        }

        return $monthlyData;
    }

    /**
     * Get top performing stakes.
     */
    private function getTopPerformingStakes($stakes): array
    {
        return $stakes->sortByDesc(function ($stake) {
            return ($stake->total_earned / $stake->amount) * 100;
        })->take(5)->values()->toArray();
    }
}

<?php

namespace App\Http\Controllers\Defi;

use App\Http\Controllers\Controller;
use App\Models\Defi\FractionalOwnership;
use App\Models\Defi\PropertyToken;
use App\Models\Defi\TokenDistribution;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\User;
use App\Http\Requests\Defi\BuyFractionalOwnershipRequest;
use App\Http\Requests\Defi\SellFractionalOwnershipRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PropertyFractionalOwnershipController extends Controller
{
    /**
     * Display a listing of fractional ownership positions.
     */
    public function index(Request $request)
    {
        $query = FractionalOwnership::with(['user', 'token', 'property', 'distributions'])
            ->where('user_id', auth()->id());

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by property
        if ($request->has('property_id') && $request->property_id) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by token
        if ($request->has('token_id') && $request->token_id) {
            $query->where('property_token_id', $request->token_id);
        }

        // Filter by ownership percentage range
        if ($request->has('min_percentage') && $request->min_percentage) {
            $query->where('ownership_percentage', '>=', $request->min_percentage);
        }

        if ($request->has('max_percentage') && $request->max_percentage) {
            $query->where('ownership_percentage', '<=', $request->max_percentage);
        }

        $ownerships = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics
        $stats = [
            'total_ownerships' => FractionalOwnership::where('user_id', auth()->id())->count(),
            'active_ownerships' => FractionalOwnership::where('user_id', auth()->id())
                ->where('status', 'active')->count(),
            'total_invested' => FractionalOwnership::where('user_id', auth()->id())
                ->where('status', 'active')->sum('total_invested'),
            'total_shares' => FractionalOwnership::where('user_id', auth()->id())
                ->where('status', 'active')->sum('shares_owned'),
            'total_dividends' => FractionalOwnership::where('user_id', auth()->id())
                ->where('status', 'active')->sum('total_dividends'),
            'average_ownership' => FractionalOwnership::where('user_id', auth()->id())
                ->where('status', 'active')->avg('ownership_percentage'),
        ];

        return Inertia::render('defi/fractional-ownership/index', [
            'ownerships' => $ownerships,
            'stats' => $stats,
            'filters' => $request->only(['status', 'property_id', 'token_id', 'min_percentage', 'max_percentage']),
        ]);
    }

    /**
     * Show the form for creating a new fractional ownership position.
     */
    public function create()
    {
        // Get available tokens for fractional ownership
        $tokens = PropertyToken::where('status', 'active')
            ->where('fractional_ownership_enabled', true)
            ->where('distributed_supply', '>', 0)
            ->get();

        return Inertia::render('defi/fractional-ownership/create', [
            'tokens' => $tokens,
        ]);
    }

    /**
     * Store a newly created fractional ownership position in storage.
     */
    public function store(BuyFractionalOwnershipRequest $request)
    {
        DB::beginTransaction();

        try {
            // Validate token and availability
            $token = PropertyToken::findOrFail($request->property_token_id);
            if (!$token->fractional_ownership_enabled) {
                return back()->with('error', 'الملكية الجزئية غير مفعلة لهذا التوكن');
            }

            // Calculate shares and cost
            $sharesToBuy = $request->shares;
            $sharePrice = $token->price_per_token;
            $totalCost = $sharesToBuy * $sharePrice;
            $ownershipPercentage = ($sharesToBuy / $token->total_supply) * 100;

            // Check if shares are available
            $availableShares = $token->total_supply - $token->distributed_supply;
            if ($sharesToBuy > $availableShares) {
                return back()->with('error', 'الأسهم المطلوبة غير متوفرة');
            }

            // Check if user already has ownership in this token
            $existingOwnership = FractionalOwnership::where('user_id', auth()->id())
                ->where('property_token_id', $token->id)
                ->where('status', 'active')
                ->first();

            if ($existingOwnership) {
                // Update existing ownership
                $existingOwnership->update([
                    'shares_owned' => $existingOwnership->shares_owned + $sharesToBuy,
                    'total_invested' => $existingOwnership->total_invested + $totalCost,
                    'average_cost_per_share' => ($existingOwnership->total_invested + $totalCost) / ($existingOwnership->shares_owned + $sharesToBuy),
                    'ownership_percentage' => (($existingOwnership->shares_owned + $sharesToBuy) / $token->total_supply) * 100,
                    'updated_at' => now(),
                ]);

                $ownership = $existingOwnership;
            } else {
                // Create new ownership
                $ownership = FractionalOwnership::create([
                    'user_id' => auth()->id(),
                    'property_token_id' => $token->id,
                    'property_id' => $token->property_id,
                    'shares_owned' => $sharesToBuy,
                    'total_invested' => $totalCost,
                    'average_cost_per_share' => $sharePrice,
                    'ownership_percentage' => $ownershipPercentage,
                    'total_dividends' => 0,
                    'last_dividend_date' => null,
                    'status' => 'active',
                    'created_at' => now(),
                ]);
            }

            // Create token distribution
            TokenDistribution::create([
                'property_token_id' => $token->id,
                'from_address' => '0x0000000000000000000000000000000000000000', // Minting address
                'to_address' => auth()->user()->wallet_address,
                'amount' => $sharesToBuy,
                'transaction_hash' => $this->generateTransactionHash(),
                'block_number' => 0,
                'gas_used' => 0,
                'gas_price' => 0,
                'status' => 'completed',
                'created_at' => now(),
            ]);

            // Update token distributed supply
            $token->increment('distributed_supply', $sharesToBuy);

            DB::commit();

            return redirect()->route('defi.fractional.show', $ownership)
                ->with('success', 'تم شراء الأسهم بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء شراء الأسهم: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified fractional ownership position.
     */
    public function show(FractionalOwnership $ownership)
    {
        // Check if user owns the ownership position
        if ($ownership->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الملكية');
        }

        $ownership->load(['user', 'token', 'property', 'distributions']);

        // Calculate ownership statistics
        $statistics = [
            'current_value' => $ownership->shares_owned * $ownership->token->price_per_token,
            'total_invested' => $ownership->total_invested,
            'profit_loss' => ($ownership->shares_owned * $ownership->token->price_per_token) - $ownership->total_invested,
            'profit_loss_percentage' => $this->calculateProfitLossPercentage($ownership),
            'dividend_yield' => $this->calculateDividendYield($ownership),
            'monthly_dividends' => $this->calculateMonthlyDividends($ownership),
            'ownership_rank' => $this->calculateOwnershipRank($ownership),
            'total_shareholders' => $this->getTotalShareholders($ownership->token),
            'next_dividend_date' => $this->calculateNextDividendDate($ownership),
            'estimated_annual_dividends' => $this->calculateEstimatedAnnualDividends($ownership),
        ];

        return Inertia::render('defi/fractional-ownership/show', [
            'ownership' => $ownership,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for editing the specified fractional ownership position.
     */
    public function edit(FractionalOwnership $ownership)
    {
        // Check if user owns the ownership position and it's active
        if ($ownership->user_id !== auth()->id() || $ownership->status !== 'active') {
            abort(403, 'لا يمكن تعديل هذه الملكية');
        }

        return Inertia::render('defi/fractional-ownership/edit', [
            'ownership' => $ownership,
        ]);
    }

    /**
     * Update the specified fractional ownership position in storage.
     */
    public function update(Request $request, FractionalOwnership $ownership)
    {
        // Check if user owns the ownership position and it's active
        if ($ownership->user_id !== auth()->id() || $ownership->status !== 'active') {
            abort(403, 'لا يمكن تعديل هذه الملكية');
        }

        // For fractional ownership, only certain fields can be updated
        $ownership->update([
            'updated_at' => now(),
        ]);

        return redirect()->route('defi.fractional.show', $ownership)
            ->with('success', 'تم تحديث الملكية بنجاحاح');
    }

    /**
     * Remove the specified fractional ownership position from storage.
     */
    public function destroy(FractionalOwnership $ownership)
    {
        // Check if user owns the ownership position
        if ($ownership->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بحذف هذه الملكية');
        }

        DB::beginTransaction();

        try {
            // Sell all shares
            $this->sellShares($ownership, $ownership->shares_owned);

            // Update status
            $ownership->update([
                'status' => 'sold',
                'sold_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('defi.fractional.index')
                ->with('success', 'تم بيع جميع الأسهم بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء بيع الأسهم: ' . $e->getMessage());
        }
    }

    /**
     * Sell fractional ownership shares.
     */
    public function sell(SellFractionalOwnershipRequest $request, FractionalOwnership $ownership)
    {
        // Check if user owns the ownership position
        if ($ownership->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك ببيع هذه الملكية');
        }

        if ($ownership->status !== 'active') {
            abort(403, 'الملكية غير نشطة');
        }

        DB::beginTransaction();

        try {
            // Validate sell amount
            if ($request->shares > $ownership->shares_owned) {
                return back()->with('error', 'الأسهم المطلوبة تتجاوز الأسهم المملوكة');
            }

            // Sell shares
            $this->sellShares($ownership, $request->shares);

            // Update ownership
            $ownership->update([
                'shares_owned' => $ownership->shares_owned - $request->shares,
                'ownership_percentage' => (($ownership->shares_owned - $request->shares) / $ownership->token->total_supply) * 100,
                'updated_at' => now(),
            ]);

            // If all shares sold, mark as sold
            if ($ownership->shares_owned - $request->shares <= 0) {
                $ownership->update([
                    'status' => 'sold',
                    'sold_at' => now(),
                ]);
            }

            DB::commit();

            return back()->with('success', 'تم بيع الأسهم بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء بيع الأسهم: ' . $e->getMessage());
        }
    }

    /**
     * Get fractional ownership analytics.
     */
    public function analytics()
    {
        $userOwnerships = FractionalOwnership::where('user_id', auth()->id())->get();

        $analytics = [
            'total_invested' => $userOwnerships->where('status', 'active')->sum('total_invested'),
            'current_value' => $userOwnerships->where('status', 'active')->sum(function ($ownership) {
                return $ownership->shares_owned * $ownership->token->price_per_token;
            }),
            'total_profit_loss' => $userOwnerships->where('status', 'active')->sum(function ($ownership) {
                return ($ownership->shares_owned * $ownership->token->price_per_token) - $ownership->total_invested;
            }),
            'total_dividends' => $userOwnerships->where('status', 'active')->sum('total_dividends'),
            'active_ownerships' => $userOwnerships->where('status', 'active')->count(),
            'diversification_score' => $this->calculateDiversificationScore($userOwnerships),
            'portfolio_distribution' => $this->getPortfolioDistribution($userOwnerships),
            'monthly_dividends' => $this->calculateMonthlyDividendsForPortfolio($userOwnerships),
            'top_performing_ownerships' => $this->getTopPerformingOwnerships($userOwnerships),
        ];

        return Inertia::render('defi/fractional-ownership/analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Get fractional ownership marketplace.
     */
    public function marketplace(Request $request)
    {
        $query = FractionalOwnership::with(['user', 'token', 'property'])
            ->where('status', 'active')
            ->where('is_for_sale', true);

        // Filter by ownership percentage range
        if ($request->has('min_percentage') && $request->min_percentage) {
            $query->where('ownership_percentage', '>=', $request->min_percentage);
        }

        if ($request->has('max_percentage') && $request->max_percentage) {
            $query->where('ownership_percentage', '<=', $request->max_percentage);
        }

        // Filter by investment range
        if ($request->has('min_investment') && $request->min_investment) {
            $query->where('total_invested', '>=', $request->min_investment);
        }

        if ($request->has('max_investment') && $request->max_investment) {
            $query->where('total_invested', '<=', $request->max_investment);
        }

        // Filter by property type
        if ($request->has('property_type') && $request->property_type) {
            $query->whereHas('property', function ($q) use ($request) {
                $q->where('property_type', $request->property_type);
            });
        }

        $ownerships = $query->orderBy('ownership_percentage', 'desc')
            ->paginate(12);

        return Inertia::render('defi/fractional-ownership/marketplace', [
            'ownerships' => $ownerships,
            'filters' => $request->only(['min_percentage', 'max_percentage', 'min_investment', 'max_investment', 'property_type']),
        ]);
    }

    /**
     * Get ownership holders for a token.
     */
    public function holders(PropertyToken $token)
    {
        $holders = FractionalOwnership::with(['user'])
            ->where('property_token_id', $token->id)
            ->where('status', 'active')
            ->orderBy('ownership_percentage', 'desc')
            ->get();

        return Inertia::render('defi/fractional-ownership/holders', [
            'token' => $token,
            'holders' => $holders,
        ]);
    }

    /**
     * Get ownership transactions.
     */
    public function transactions(FractionalOwnership $ownership)
    {
        // Check if user owns the ownership position
        if ($ownership->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بعرض معاملات الملكية');
        }

        $transactions = $ownership->distributions()
            ->with(['fromUser', 'toUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('defi/fractional-ownership/transactions', [
            'ownership' => $ownership,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Generate transaction hash.
     */
    private function generateTransactionHash(): string
    {
        return '0x' . bin2hex(random_bytes(32));
    }

    /**
     * Sell shares.
     */
    private function sellShares($ownership, $shares): void
    {
        $salePrice = $ownership->token->price_per_token;
        $totalSaleAmount = $shares * $salePrice;

        // Create sell transaction
        TokenDistribution::create([
            'property_token_id' => $ownership->property_token_id,
            'from_address' => auth()->user()->wallet_address,
            'to_address' => '0x0000000000000000000000000000000000000000', // Burn address for selling
            'amount' => $shares,
            'price' => $salePrice,
            'total_amount' => $totalSaleAmount,
            'transaction_hash' => $this->generateTransactionHash(),
            'block_number' => 0,
            'gas_used' => 0,
            'gas_price' => 0,
            'status' => 'completed',
            'created_at' => now(),
        ]);

        // Update token distributed supply
        $ownership->token->decrement('distributed_supply', $shares);
    }

    /**
     * Calculate profit/loss percentage.
     */
    private function calculateProfitLossPercentage($ownership): float
    {
        $currentValue = $ownership->shares_owned * $ownership->token->price_per_token;
        $totalInvested = $ownership->total_invested;

        if ($totalInvested <= 0) {
            return 0;
        }

        return (($currentValue - $totalInvested) / $totalInvested) * 100;
    }

    /**
     * Calculate dividend yield.
     */
    private function calculateDividendYield($ownership): float
    {
        $currentValue = $ownership->shares_owned * $ownership->token->price_per_token;
        $totalDividends = $ownership->total_dividends;

        if ($currentValue <= 0) {
            return 0;
        }

        return ($totalDividends / $currentValue) * 100;
    }

    /**
     * Calculate monthly dividends.
     */
    private function calculateMonthlyDividends($ownership): float
    {
        // This would calculate based on actual dividend payments
        // For now, return a mock calculation
        $annualDividendRate = 0.05; // 5% annual dividend
        $currentValue = $ownership->shares_owned * $ownership->token->price_per_token;

        return ($currentValue * $annualDividendRate) / 12;
    }

    /**
     * Calculate ownership rank.
     */
    private function calculateOwnershipRank($ownership): int
    {
        return FractionalOwnership::where('property_token_id', $ownership->property_token_id)
            ->where('status', 'active')
            ->where('ownership_percentage', '>', $ownership->ownership_percentage)
            ->count() + 1;
    }

    /**
     * Get total shareholders.
     */
    private function getTotalShareholders($token): int
    {
        return FractionalOwnership::where('property_token_id', $token->id)
            ->where('status', 'active')
            ->count();
    }

    /**
     * Calculate next dividend date.
     */
    private function calculateNextDividendDate($ownership): string
    {
        // This would calculate based on actual dividend schedule
        // For now, return a mock date
        return now()->addMonth()->format('Y-m-d');
    }

    /**
     * Calculate estimated annual dividends.
     */
    private function calculateEstimatedAnnualDividends($ownership): float
    {
        return $this->calculateMonthlyDividends($ownership) * 12;
    }

    /**
     * Calculate diversification score.
     */
    private function calculateDiversificationScore($ownerships): float
    {
        $activeOwnerships = $ownerships->where('status', 'active');
        $totalInvested = $activeOwnerships->sum('total_invested');

        if ($totalInvested <= 0) {
            return 0;
        }

        // Calculate concentration (inverse of diversification)
        $concentration = 0;
        foreach ($activeOwnerships as $ownership) {
            $weight = $ownership->total_invested / $totalInvested;
            $concentration += $weight * $weight;
        }

        // Diversification score (0-100, higher is more diversified)
        return (1 - $concentration) * 100;
    }

    /**
     * Get portfolio distribution.
     */
    private function getPortfolioDistribution($ownerships): array
    {
        $distribution = [];

        foreach ($ownerships->where('status', 'active') as $ownership) {
            $propertyType = $ownership->property->property_type ?? 'other';

            if (!isset($distribution[$propertyType])) {
                $distribution[$propertyType] = [
                    'count' => 0,
                    'total_invested' => 0,
                    'ownership_percentage' => 0,
                ];
            }

            $distribution[$propertyType]['count']++;
            $distribution[$propertyType]['total_invested'] += $ownership->total_invested;
            $distribution[$propertyType]['ownership_percentage'] += $ownership->ownership_percentage;
        }

        return $distribution;
    }

    private function calculateMonthlyDividendsForPortfolio($ownerships): array
    {
        $monthlyData = [];

        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthDividends = 0;

            foreach ($ownerships->where('status', 'active') as $ownership) {
                $monthDividends += $this->calculateMonthlyDividends($ownership);
            }

            $monthlyData[$date->format('Y-m')] = $monthDividends;
        }

        return $monthlyData;
    }

    /**
     * Get top performing ownerships.
     */
    private function getTopPerformingOwnerships($ownerships): array
    {
        return $ownerships->sortByDesc(function ($ownership) {
            return $this->calculateProfitLossPercentage($ownership);
        })->take(5)->values()->toArray();
    }
}

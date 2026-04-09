<?php

namespace App\Http\Controllers\Defi;

use App\Http\Controllers\Controller;
use App\Models\Defi\PropertyToken;
use App\Models\Defi\TokenDistribution;
use App\Models\Defi\DefiCollateral;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\User;
use App\Http\Requests\Defi\TokenizePropertyRequest;
use App\Http\Requests\Defi\MintTokenRequest;
use App\Http\Requests\Defi\TransferTokenRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PropertyTokenizationController extends Controller
{
    /**
     * Display a listing of property tokens.
     */
    public function index(Request $request)
    {
        $query = PropertyToken::with(['property', 'owner', 'distributions'])
            ->where('owner_id', auth()->id());

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by property
        if ($request->has('property_id') && $request->property_id) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by blockchain
        if ($request->has('blockchain') && $request->blockchain) {
            $query->where('blockchain', $request->blockchain);
        }

        $tokens = $query->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get statistics
        $stats = [
            'total_tokens' => PropertyToken::where('owner_id', auth()->id())->count(),
            'active_tokens' => PropertyToken::where('owner_id', auth()->id())
                ->where('status', 'active')->count(),
            'total_supply' => PropertyToken::where('owner_id', auth()->id())
                ->where('status', 'active')->sum('total_supply'),
            'total_value' => PropertyToken::where('owner_id', auth()->id())
                ->where('status', 'active')->sum('current_value'),
            'total_distributed' => PropertyToken::where('owner_id', auth()->id())
                ->where('status', 'active')->sum('distributed_supply'),
        ];

        return Inertia::render('defi/tokenization/index', [
            'tokens' => $tokens,
            'stats' => $stats,
            'filters' => $request->only(['status', 'property_id', 'blockchain']),
        ]);
    }

    /**
     * Show the form for creating a new property token.
     */
    public function create()
    {
        // Get user's properties that can be tokenized
        $properties = MetaverseProperty::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->where('is_nft', false) // Not already NFT
            ->get();

        return Inertia::render('defi/tokenization/create', [
            'properties' => $properties,
        ]);
    }

    /**
     * Store a newly created property token in storage.
     */
    public function store(TokenizePropertyRequest $request)
    {
        DB::beginTransaction();

        try {
            // Validate property ownership
            $property = MetaverseProperty::findOrFail($request->property_id);
            if ($property->owner_id !== auth()->id()) {
                abort(403, 'غير مصرح لك بتوريق هذا العقار');
            }

            // Check if property is already tokenized
            if (PropertyToken::where('property_id', $request->property_id)->exists()) {
                return back()->with('error', 'العقار موريق بالفعل');
            }

            // Create property token
            $token = PropertyToken::create([
                'property_id' => $request->property_id,
                'owner_id' => auth()->id(),
                'token_name' => $request->token_name,
                'token_symbol' => $request->token_symbol,
                'total_supply' => $request->total_supply,
                'distributed_supply' => 0,
                'price_per_token' => $request->price_per_token,
                'currency' => $request->currency,
                'blockchain' => $request->blockchain,
                'smart_contract_address' => null, // Will be set when minted
                'token_standard' => $request->token_standard,
                'decimals' => $request->decimals,
                'minting_enabled' => $request->minting_enabled,
                'burning_enabled' => $request->burning_enabled,
                'transfer_fee' => $request->transfer_fee,
                'royalty_percentage' => $request->royalty_percentage,
                'metadata' => [
                    'description' => $request->description,
                    'image_url' => $request->image_url,
                    'external_url' => $request->external_url,
                    'attributes' => $request->attributes,
                ],
                'status' => 'pending',
                'created_at' => now(),
            ]);

            // Lock property as collateral
            DefiCollateral::create([
                'collateral_type' => 'property',
                'collateral_id' => $request->property_id,
                'value' => $property->price,
                'status' => 'locked',
                'locked_at' => now(),
                'tokenization_id' => $token->id,
            ]);

            // Update property status
            $property->update([
                'is_tokenized' => true,
                'tokenization_date' => now(),
            ]);

            DB::commit();

            return redirect()->route('defi.tokens.show', $token)
                ->with('success', 'تم إنشاء التوكن بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء التوكن: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified property token.
     */
    public function show(PropertyToken $token)
    {
        // Check if user owns the token
        if ($token->owner_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا التوكن');
        }

        $token->load(['property', 'owner', 'distributions', 'transactions']);

        // Calculate token statistics
        $statistics = [
            'available_supply' => $token->total_supply - $token->distributed_supply,
            'total_value' => $token->total_supply * $token->price_per_token,
            'distributed_value' => $token->distributed_supply * $token->price_per_token,
            'available_value' => ($token->total_supply - $token->distributed_supply) * $token->price_per_token,
            'ownership_percentage' => $this->calculateOwnershipPercentage($token),
            'monthly_yield' => $this->calculateMonthlyYield($token),
            'total_distributions' => $token->distributions()->sum('amount'),
        ];

        return Inertia::render('defi/tokenization/show', [
            'token' => $token,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for editing the specified property token.
     */
    public function edit(PropertyToken $token)
    {
        // Check if user owns the token and it's not minted
        if ($token->owner_id !== auth()->id() || $token->status === 'minted') {
            abort(403, 'لا يمكن تعديل هذا التوكن');
        }

        return Inertia::render('defi/tokenization/edit', [
            'token' => $token,
        ]);
    }

    /**
     * Update the specified property token in storage.
     */
    public function update(TokenizePropertyRequest $request, PropertyToken $token)
    {
        // Check if user owns the token and it's not minted
        if ($token->owner_id !== auth()->id() || $token->status === 'minted') {
            abort(403, 'لا يمكن تعديل هذا التوكن');
        }

        $token->update([
            'token_name' => $request->token_name,
            'token_symbol' => $request->token_symbol,
            'price_per_token' => $request->price_per_token,
            'currency' => $request->currency,
            'blockchain' => $request->blockchain,
            'token_standard' => $request->token_standard,
            'decimals' => $request->decimals,
            'minting_enabled' => $request->minting_enabled,
            'burning_enabled' => $request->burning_enabled,
            'transfer_fee' => $request->transfer_fee,
            'royalty_percentage' => $request->royalty_percentage,
            'metadata' => [
                'description' => $request->description,
                'image_url' => $request->image_url,
                'external_url' => $request->external_url,
                'attributes' => $request->attributes,
            ],
            'updated_at' => now(),
        ]);

        return redirect()->route('defi.tokens.show', $token)
            ->with('success', 'تم تحديث التوكن بنجاحاح');
    }

    /**
     * Remove the specified property token from storage.
     */
    public function destroy(PropertyToken $token)
    {
        // Check if user owns the token and it's not minted
        if ($token->owner_id !== auth()->id() || $token->status === 'minted') {
            abort(403, 'لا يمكن حذف هذا التوكن');
        }

        DB::beginTransaction();

        try {
            // Release collateral
            $token->collateral()->delete();
            
            // Update property status
            $token->property()->update([
                'is_tokenized' => false,
                'tokenization_date' => null,
            ]);
            
            // Delete token
            $token->delete();

            DB::commit();

            return redirect()->route('defi.tokens.index')
                ->with('success', 'تم حذف التوكن بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف التوكن: ' . $e->getMessage());
        }
    }

    /**
     * Mint tokens (deploy smart contract).
     */
    public function mint(MintTokenRequest $request, PropertyToken $token)
    {
        // Check if user owns the token
        if ($token->owner_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بنشر هذا التوكن');
        }

        if ($token->status !== 'pending') {
            abort(403, 'التوكن ليس في حالة انتظار');
        }

        DB::beginTransaction();

        try {
            // Deploy smart contract
            $smartContractAddress = $this->deployTokenSmartContract($token);

            // Update token status
            $token->update([
                'status' => 'active',
                'smart_contract_address' => $smartContractAddress,
                'minted_at' => now(),
                'minted_by' => auth()->id(),
            ]);

            // Create initial distribution (owner gets all tokens initially)
            TokenDistribution::create([
                'property_token_id' => $token->id,
                'from_address' => '0x0000000000000000000000000000000000000000', // Zero address for minting
                'to_address' => auth()->user()->wallet_address,
                'amount' => $token->total_supply,
                'transaction_hash' => $this->generateTransactionHash(),
                'block_number' => 0,
                'gas_used' => 0,
                'gas_price' => 0,
                'status' => 'completed',
                'created_at' => now(),
            ]);

            // Update distributed supply
            $token->update([
                'distributed_supply' => $token->total_supply,
            ]);

            DB::commit();

            return back()->with('success', 'تم نشر التوكن بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء نشر التوكن: ' . $e->getMessage());
        }
    }

    /**
     * Transfer tokens.
     */
    public function transfer(TransferTokenRequest $request, PropertyToken $token)
    {
        // Check if user owns the tokens
        if ($token->owner_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بنقل هذا التوكن');
        }

        if ($token->status !== 'active') {
            abort(403, 'التوكن غير نشط');
        }

        DB::beginTransaction();

        try {
            // Validate transfer
            $fromBalance = $this->getUserTokenBalance($token, auth()->user());
            if ($fromBalance < $request->amount) {
                return back()->with('error', 'رصيدك غير كافي');
            }

            // Calculate transfer fee
            $fee = $request->amount * ($token->transfer_fee / 100);
            $netAmount = $request->amount - $fee;

            // Create distribution record
            TokenDistribution::create([
                'property_token_id' => $token->id,
                'from_address' => auth()->user()->wallet_address,
                'to_address' => $request->to_address,
                'amount' => $netAmount,
                'fee' => $fee,
                'transaction_hash' => $this->generateTransactionHash(),
                'block_number' => 0,
                'gas_used' => 0,
                'gas_price' => 0,
                'status' => 'pending',
                'created_at' => now(),
            ]);

            // Update distributed supply
            $token->increment('distributed_supply', $netAmount);

            DB::commit();

            return back()->with('success', 'تم بدء عملية النقل');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء نقل التوكن: ' . $e->getMessage());
        }
    }

    /**
     * Burn tokens.
     */
    public function burn(Request $request, PropertyToken $token)
    {
        // Check if user owns the tokens
        if ($token->owner_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بحرق هذا التوكن');
        }

        if (!$token->burning_enabled) {
            abort(403, 'حرق التوكن غير مفعلل');
        }

        DB::beginTransaction();

        try {
            // Validate burn amount
            $fromBalance = $this->getUserTokenBalance($token, auth()->user());
            if ($fromBalance < $request->amount) {
                return back()->with('error', 'رصيدك غير كافي للحرق');
            }

            // Create burn transaction
            TokenDistribution::create([
                'property_token_id' => $token->id,
                'from_address' => auth()->user()->wallet_address,
                'to_address' => '0x0000000000000000000000000000000000000000', // Burn address
                'amount' => $request->amount,
                'transaction_hash' => $this->generateTransactionHash(),
                'block_number' => 0,
                'gas_used' => 0,
                'gas_price' => 0,
                'status' => 'pending',
                'created_at' => now(),
            ]);

            // Update distributed supply
            $token->decrement('distributed_supply', $request->amount);
            $token->decrement('total_supply', $request->amount);

            DB::commit();

            return back()->with('success', 'تم حرق التوكن بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حرق التوكن: ' . $e->getMessage());
        }
    }

    /**
     * Get token analytics.
     */
    public function analytics()
    {
        $userTokens = PropertyToken::where('owner_id', auth()->id())->get();

        $analytics = [
            'total_tokens' => $userTokens->count(),
            'total_supply' => $userTokens->sum('total_supply'),
            'total_value' => $userTokens->sum(function ($token) {
                return $token->total_supply * $token->price_per_token;
            }),
            'total_distributed' => $userTokens->sum('distributed_supply'),
            'total_distributions' => $userTokens->sum(function ($token) {
                return $token->distributions()->count();
            }),
            'total_fees' => $userTokens->sum(function ($token) {
                return $token->distributions()->sum('fee');
            }),
            'blockchain_distribution' => $userTokens->groupBy('blockchain')->map->count(),
            'monthly_distributions' => $this->calculateMonthlyDistributions($userTokens),
            'top_performing_tokens' => $this->getTopPerformingTokens($userTokens),
        ];

        return Inertia::render('defi/tokenization/analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Get token marketplace.
     */
    public function marketplace(Request $request)
    {
        $query = PropertyToken::with(['property', 'owner'])
            ->where('status', 'active')
            ->where('distributed_supply', '>', 0);

        // Filter by blockchain
        if ($request->has('blockchain') && $request->blockchain) {
            $query->where('blockchain', $request->blockchain);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price_per_token', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price) {
            $query->where('price_per_token', '<=', $request->max_price);
        }

        // Filter by available supply
        if ($request->has('min_supply') && $request->min_supply) {
            $query->whereRaw('(total_supply - distributed_supply) >= ?', [$request->min_supply]);
        }

        $tokens = $query->orderBy('created_at', 'desc')
            ->paginate(12);

        return Inertia::render('defi/tokenization/marketplace', [
            'tokens' => $tokens,
            'filters' => $request->only(['blockchain', 'min_price', 'max_price', 'min_supply']),
        ]);
    }

    /**
     * Get token holders.
     */
    public function holders(PropertyToken $token)
    {
        // Check if user owns the token
        if ($token->owner_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بعرض حاملي التوكن');
        }

        $holders = $token->distributions()
            ->selectRaw('to_address, SUM(amount) as balance')
            ->where('status', 'completed')
            ->groupBy('to_address')
            ->orderBy('balance', 'desc')
            ->get();

        return Inertia::render('defi/tokenization/holders', [
            'token' => $token,
            'holders' => $holders,
        ]);
    }

    /**
     * Get token transactions.
     */
    public function transactions(PropertyToken $token)
    {
        // Check if user owns the token
        if ($token->owner_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بعرض معاملات التوكن');
        }

        $transactions = $token->distributions()
            ->with(['fromUser', 'toUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('defi/tokenization/transactions', [
            'token' => $token,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Deploy token smart contract.
     */
    private function deployTokenSmartContract($token): string
    {
        // This would integrate with a smart contract deployment service
        // For now, return a mock address
        return '0x' . bin2hex(random_bytes(20));
    }

    /**
     * Generate transaction hash.
     */
    private function generateTransactionHash(): string
    {
        return '0x' . bin2hex(random_bytes(32));
    }

    /**
     * Get user token balance.
     */
    private function getUserTokenBalance($token, $user): float
    {
        return $token->distributions()
            ->where('to_address', $user->wallet_address)
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Calculate ownership percentage.
     */
    private function calculateOwnershipPercentage($token): float
    {
        $userBalance = $this->getUserTokenBalance($token, auth()->user());
        return $token->total_supply > 0 ? ($userBalance / $token->total_supply) * 100 : 0;
    }

    /**
     * Calculate monthly yield.
     */
    private function calculateMonthlyYield($token): float
    {
        // This would calculate based on rental income or other yield sources
        // For now, return a mock calculation
        $propertyValue = $token->property->price;
        $tokenValue = $token->total_supply * $token->price_per_token;
        
        return $propertyValue > 0 ? ($tokenValue / $propertyValue) * 0.08 * 100 : 0; // 8% annual yield
    }

    /**
     * Calculate monthly distributions.
     */
    private function calculateMonthlyDistributions($tokens): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthDistributions = 0;
            
            foreach ($tokens as $token) {
                $monthDistributions += $token->distributions()
                    ->where('created_at', '>=', $date->startOfMonth())
                    ->where('created_at', '<=', $date->endOfMonth())
                    ->sum('amount');
            }
            
            $monthlyData[$date->format('Y-m')] = $monthDistributions;
        }

        return $monthlyData;
    }

    /**
     * Get top performing tokens.
     */
    private function getTopPerformingTokens($tokens): array
    {
        return $tokens->sortByDesc(function ($token) {
            return ($token->distributed_supply * $token->price_per_token) / ($token->total_supply * $token->price_per_token) * 100;
        })->take(5)->values()->toArray();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PropertyAsset;
use App\Models\FractionalOwnership;
use App\Services\FractionalOwnershipService;
use App\Exceptions\ShareLimitExceededException;
use App\Exceptions\DoubleSpendingException;
use App\Events\SharePurchased;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FractionalInvestmentController extends Controller
{
    public function __construct(
        private FractionalOwnershipService $ownershipService
    ) {
        $this->middleware('auth');
    }

    /**
     * GET /blockchain/fractional/{asset}
     * Show shares information for a specific property asset.
     */
    public function index(PropertyAsset $asset): JsonResponse
    {
        $asset->load(['property', 'fractionalOwnerships.user']);

        $shareholders = $asset->fractionalOwnerships->map(fn($o) => [
            'user_id' => $o->user_id,
            'user_name' => $o->user?->name,
            'shares_owned' => $o->shares_owned,
            'percentage' => number_format($o->percentage, 2) . '%',
            'purchase_price' => $o->purchase_price,
            'dividends_earned' => $o->dividends_earned,
        ]);

        return response()->json([
            'asset' => [
                'id' => $asset->id,
                'property_id' => $asset->property_id,
                'property_title' => $asset->property?->title,
                'token_id' => $asset->token_id,
                'total_shares' => $asset->total_shares,
                'available_shares' => $asset->available_shares,
                'sold_shares' => $asset->sold_shares,
                'share_price' => $asset->share_price,
                'total_value' => $asset->total_value,
                'ownership_pct_sold' => $asset->ownership_percentage_sold . '%',
                'status' => $asset->status,
                'network' => $asset->network,
            ],
            'shareholders' => $shareholders,
            'total_shareholders' => $shareholders->count(),
        ]);
    }

    /**
     * POST /blockchain/fractional/{asset}/buy
     * Atomically purchase shares. Total sold shares can NEVER exceed 100%.
     *
     * Request body: { "shares": <int> }
     */
    public function buyShare(Request $request, PropertyAsset $asset): JsonResponse
    {
        $request->validate([
            'shares' => ['required', 'integer', 'min:1'],
        ]);

        $buyer = Auth::user();
        $shares = (int) $request->input('shares');

        try {
            $ownership = $this->ownershipService->buyShare($asset, $buyer, $shares);

            // Fire event — UpdateLedger already handled by the service;
            // this event allows additional listeners (email confirmations, etc.)
            event(new SharePurchased($ownership));

            $asset->refresh();

            return response()->json([
                'success' => true,
                'message' => "Successfully purchased {$shares} share(s) in asset #{$asset->id}.",
                'ownership' => [
                    'id' => $ownership->id,
                    'shares_owned' => $ownership->shares_owned,
                    'percentage' => number_format($ownership->percentage, 2) . '%',
                    'purchase_price' => $ownership->purchase_price,
                    'dividends_earned' => $ownership->dividends_earned,
                ],
                'asset_status' => [
                    'available_shares' => $asset->available_shares,
                    'status' => $asset->status,
                ],
            ], 201);

        } catch (ShareLimitExceededException $e) {
            return response()->json([
                'success' => false,
                'error' => 'share_limit_exceeded',
                'message' => $e->getMessage(),
            ], 409);

        } catch (DoubleSpendingException $e) {
            return response()->json([
                'success' => false,
                'error' => 'double_spending',
                'message' => $e->getMessage(),
            ], 409);

        } catch (\Throwable $e) {
            Log::error('FractionalInvestmentController: buyShare failed', [
                'error' => $e->getMessage(),
                'asset_id' => $asset->id,
                'user_id' => $buyer->id,
                'shares' => $shares,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'purchase_failed',
                'message' => 'Unable to complete share purchase. Please try again.',
            ], 500);
        }
    }

    /**
     * GET /blockchain/fractional/holdings
     * Authenticated user's fractional ownership portfolio.
     */
    public function myHoldings(): JsonResponse
    {
        $holdings = FractionalOwnership::where('user_id', Auth::id())
            ->with(['propertyAsset.property'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($o) => [
                'id' => $o->id,
                'asset_id' => $o->property_asset_id,
                'property_title' => $o->propertyAsset?->property?->title,
                'token_id' => $o->propertyAsset?->token_id,
                'shares_owned' => $o->shares_owned,
                'percentage' => number_format($o->percentage, 2) . '%',
                'purchase_price' => $o->purchase_price,
                'dividends_earned' => $o->dividends_earned,
                'dividends_available' => $o->withdrawable_dividends,
                'roi_percentage' => $o->roi_percentage . '%',
                'purchased_at' => $o->purchased_at?->toDateTimeString(),
            ]);

        return response()->json([
            'total_holdings' => $holdings->count(),
            'total_invested' => $holdings->sum('purchase_price'),
            'total_dividends' => $holdings->sum('dividends_earned'),
            'holdings' => $holdings,
        ]);
    }
}

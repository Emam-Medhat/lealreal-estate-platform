<?php

namespace App\Http\Controllers\Metaverse;

use App\Http\Controllers\Controller;
use App\Models\Metaverse\MetaversePropertyNft;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MetaversePropertyNftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of metaverse property NFTs.
     */
    public function index(Request $request)
    {
        $query = MetaversePropertyNft::with(['metaverseProperty', 'owner', 'creator'])
            ->withCount(['transfers', 'bids']);

        // Filters
        if ($request->filled('property_id')) {
            $query->where('metaverse_property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('blockchain')) {
            $query->where('blockchain', $request->blockchain);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('token_id', 'like', "%{$search}%")
                  ->orWhere('contract_address', 'like', "%{$search}%")
                  ->orWhereHas('metaverseProperty', function ($subQuery) use ($search) {
                      $subQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $nfts = $query->paginate(12);

        return Inertia::render('Metaverse/Nfts/Index', [
            'nfts' => $nfts,
            'filters' => $request->only(['property_id', 'status', 'blockchain', 'price_min', 'price_max', 'search', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Show the form for creating a new metaverse property NFT.
     */
    public function create()
    {
        $properties = MetaverseProperty::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->whereDoesntHave('nft')
            ->with(['virtualWorld'])
            ->get();

        return Inertia::render('Metaverse/Nfts/Create', [
            'properties' => $properties,
        ]);
    }

    /**
     * Store a newly created metaverse property NFT.
     */
    public function store(Request $request)
    {
        $request->validate([
            'metaverse_property_id' => 'required|exists:metaverse_properties,id',
            'blockchain' => 'required|in:ethereum,polygon,binance_smart_chain,solana,avalanche',
            'contract_address' => 'required|string|max:255',
            'token_id' => 'required|string|max:255|unique:metaverse_property_nfts,token_id',
            'token_uri' => 'required|url|max:500',
            'metadata' => 'required|array',
            'metadata.name' => 'required|string|max:255',
            'metadata.description' => 'required|string|max:2000',
            'metadata.image' => 'required|url|max:500',
            'metadata.attributes' => 'nullable|array',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'royalty_percentage' => 'nullable|numeric|min:0|max:50',
            'is_for_sale' => 'boolean',
            'auction_settings' => 'nullable|array',
            'verification_status' => 'nullable|string|max:50',
        ]);

        $property = MetaverseProperty::findOrFail($request->metaverse_property_id);

        // Check if user owns the property
        if ($property->owner_id !== auth()->id()) {
            return back()->with('error', 'لا يمكنك إنشاء NFT لعقار لا تملكه');
        }

        // Check if property already has NFT
        if ($property->nft) {
            return back()->with('error', 'العقار لديه بالفعل NFT');
        }

        $nft = MetaversePropertyNft::create([
            'metaverse_property_id' => $request->metaverse_property_id,
            'blockchain' => $request->blockchain,
            'contract_address' => $request->contract_address,
            'token_id' => $request->token_id,
            'token_uri' => $request->token_uri,
            'metadata' => $request->metadata,
            'price' => $request->price,
            'currency' => $request->currency ?? 'ETH',
            'royalty_percentage' => $request->royalty_percentage ?? 10,
            'is_for_sale' => $request->boolean('is_for_sale', false),
            'auction_settings' => $request->auction_settings ?? [],
            'verification_status' => $request->verification_status ?? 'pending',
            'status' => 'minted',
            'owner_id' => auth()->id(),
            'creator_id' => auth()->id(),
            'minted_at' => now(),
            'created_by' => auth()->id(),
        ]);

        // Update property to link with NFT
        $property->update([
            'nft_id' => $nft->id,
            'is_nft' => true,
        ]);

        return redirect()->route('metaverse.nfts.show', $nft)
            ->with('success', 'تم إنشاء NFT العقار بنجاح');
    }

    /**
     * Display the specified metaverse property NFT.
     */
    public function show(MetaversePropertyNft $nft)
    {
        $nft->load([
            'metaverseProperty' => function ($query) {
                $query->with(['virtualWorld', 'owner', 'images']);
            },
            'owner',
            'creator',
            'transfers' => function ($query) {
                $query->latest()->limit(10);
            },
            'bids' => function ($query) {
                $query->where('status', 'active')->with('bidder');
            },
            'auction',
        ]);

        // Get NFT statistics
        $stats = [
            'total_transfers' => $nft->transfers_count,
            'total_bids' => $nft->bids_count,
            'highest_bid' => $nft->bids()->where('status', 'active')->max('amount') ?? 0,
            'current_price' => $nft->price,
            'price_history' => $this->getPriceHistory($nft),
            'ownership_history' => $this->getOwnershipHistory($nft),
        ];

        // Get similar NFTs
        $similarNfts = MetaversePropertyNft::where('id', '!=', $nft->id)
            ->where('blockchain', $nft->blockchain)
            ->where('status', 'minted')
            ->with(['metaverseProperty', 'owner'])
            ->limit(6)
            ->get();

        return Inertia::render('Metaverse/Nfts/Show', [
            'nft' => $nft,
            'stats' => $stats,
            'similarNfts' => $similarNfts,
        ]);
    }

    /**
     * Show the form for editing the specified metaverse property NFT.
     */
    public function edit(MetaversePropertyNft $nft)
    {
        $this->authorize('update', $nft);

        $nft->load(['metaverseProperty', 'auction']);

        return Inertia::render('Metaverse/Nfts/Edit', [
            'nft' => $nft,
        ]);
    }

    /**
     * Update the specified metaverse property NFT.
     */
    public function update(Request $request, MetaversePropertyNft $nft)
    {
        $this->authorize('update', $nft);

        $request->validate([
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'royalty_percentage' => 'nullable|numeric|min:0|max:50',
            'is_for_sale' => 'boolean',
            'auction_settings' => 'nullable|array',
            'metadata' => 'nullable|array',
            'metadata.name' => 'nullable|string|max:255',
            'metadata.description' => 'nullable|string|max:2000',
            'metadata.image' => 'nullable|url|max:500',
            'metadata.attributes' => 'nullable|array',
        ]);

        $nft->update([
            'price' => $request->price,
            'currency' => $request->currency ?? $nft->currency,
            'royalty_percentage' => $request->royalty_percentage ?? $nft->royalty_percentage,
            'is_for_sale' => $request->boolean('is_for_sale'),
            'auction_settings' => $request->auction_settings ?? $nft->auction_settings,
            'metadata' => array_merge($nft->metadata, $request->metadata ?? []),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('metaverse.nfts.show', $nft)
            ->with('success', 'تم تحديث NFT العقار بنجاح');
    }

    /**
     * Remove the specified metaverse property NFT.
     */
    public function destroy(MetaversePropertyNft $nft)
    {
        $this->authorize('delete', $nft);

        // Update property to remove NFT link
        if ($nft->metaverseProperty) {
            $nft->metaverseProperty->update([
                'nft_id' => null,
                'is_nft' => false,
            ]);
        }

        $nft->delete();

        return redirect()->route('metaverse.nfts.index')
            ->with('success', 'تم حذف NFT العقار بنجاح');
    }

    /**
     * Place bid on NFT.
     */
    public function placeBid(Request $request, MetaversePropertyNft $nft)
    {
        $this->authorize('bid', $nft);

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'message' => 'nullable|string|max:500',
        ]);

        // Check if NFT is for sale or in auction
        if (!$nft->is_for_sale && !$nft->auction) {
            return back()->with('error', 'NFT غير متاح للبيع');
        }

        // Check if bid amount is higher than current highest bid
        $highestBid = $nft->bids()->where('status', 'active')->max('amount');
        if ($highestBid && $request->amount <= $highestBid) {
            return back()->with('error', 'يجب أن يكون المبلغ أعلى من أعلى عرض حالي');
        }

        // Create bid
        $bid = $nft->bids()->create([
            'bidder_id' => auth()->id(),
            'amount' => $request->amount,
            'currency' => $request->currency,
            'message' => $request->message,
            'status' => 'active',
            'expires_at' => now()->addDays(7), // Bid expires in 7 days
        ]);

        return back()->with('success', 'تم تقديم العرض بنجاح');
    }

    /**
     * Accept bid.
     */
    public function acceptBid(MetaversePropertyNft $nft, $bidId)
    {
        $this->authorize('manageBids', $nft);

        $bid = $nft->bids()->findOrFail($bidId);

        // Process the sale
        return $this->processSale($nft, $bid);
    }

    /**
     * Reject bid.
     */
    public function rejectBid(MetaversePropertyNft $nft, $bidId)
    {
        $this->authorize('manageBids', $nft);

        $bid = $nft->bids()->findOrFail($bidId);

        $bid->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'تم رفض العرض بنجاح');
    }

    /**
     * Start auction.
     */
    public function startAuction(Request $request, MetaversePropertyNft $nft)
    {
        $this->authorize('manageAuction', $nft);

        $request->validate([
            'starting_price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'duration' => 'required|integer|min:1|max:30', // days
            'reserve_price' => 'nullable|numeric|min:0',
            'buy_now_price' => 'nullable|numeric|min:0',
        ]);

        // Check if there's already an active auction
        if ($nft->auction && $nft->auction->status === 'active') {
            return back()->with('error', 'يوجد مزاد نشط بالفعل');
        }

        // Create auction
        $auction = $nft->auction()->create([
            'starting_price' => $request->starting_price,
            'currency' => $request->currency,
            'reserve_price' => $request->reserve_price,
            'buy_now_price' => $request->buy_now_price,
            'duration' => $request->duration,
            'starts_at' => now(),
            'ends_at' => now()->addDays($request->duration),
            'status' => 'active',
        ]);

        // Update NFT status
        $nft->update([
            'is_for_sale' => true,
            'auction_settings' => [
                'auction_id' => $auction->id,
                'current_bid' => $request->starting_price,
                'bid_count' => 0,
            ],
        ]);

        return back()->with('success', 'تم بدء المزاد بنجاح');
    }

    /**
     * End auction.
     */
    public function endAuction(MetaversePropertyNft $nft)
    {
        $this->authorize('manageAuction', $nft);

        $auction = $nft->auction;
        if (!$auction || $auction->status !== 'active') {
            return back()->with('error', 'لا يوجد مزاد نشط');
        }

        // Get highest bid
        $highestBid = $nft->bids()
            ->where('status', 'active')
            ->orderBy('amount', 'desc')
            ->first();

        if ($highestBid && $highestBid->amount >= ($auction->reserve_price ?? 0)) {
            // Process sale
            $this->processSale($nft, $highestBid);
        } else {
            // Auction ended without sale
            $auction->update([
                'status' => 'ended',
                'ended_at' => now(),
                'result' => 'no_sale',
            ]);

            $nft->update([
                'is_for_sale' => false,
            ]);
        }

        return back()->with('success', 'تم إنهاء المزاد بنجاح');
    }

    /**
     * Transfer NFT.
     */
    public function transfer(Request $request, MetaversePropertyNft $nft)
    {
        $this->authorize('transfer', $nft);

        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:500',
        ]);

        $recipient = User::findOrFail($request->recipient_id);

        // Create transfer record
        $transfer = $nft->transfers()->create([
            'from_user_id' => auth()->id(),
            'to_user_id' => $recipient->id,
            'amount' => $nft->price ?? 0,
            'currency' => $nft->currency ?? 'ETH',
            'transaction_hash' => $this->generateTransactionHash(),
            'message' => $request->message,
            'status' => 'completed',
            'transferred_at' => now(),
        ]);

        // Update NFT ownership
        $nft->update([
            'owner_id' => $recipient->id,
        ]);

        // Update property ownership
        if ($nft->metaverseProperty) {
            $nft->metaverseProperty->update([
                'owner_id' => $recipient->id,
            ]);
        }

        return back()->with('success', 'تم نقل NFT بنجاح');
    }

    /**
     * Get NFT metadata.
     */
    public function metadata(MetaversePropertyNft $nft)
    {
        return response()->json($nft->metadata);
    }

    /**
     * Verify NFT.
     */
    public function verify(MetaversePropertyNft $nft)
    {
        $this->authorize('verify', $nft);

        // Verify NFT on blockchain
        $verificationResult = $this->verifyOnBlockchain($nft);

        $nft->update([
            'verification_status' => $verificationResult['status'],
            'verified_at' => now(),
            'verification_details' => $verificationResult['details'],
        ]);

        return back()->with('success', 'تم التحقق من NFT بنجاح');
    }

    /**
     * Get NFT analytics.
     */
    public function analytics(MetaversePropertyNft $nft)
    {
        $this->authorize('view', $nft);

        $analytics = [
            'price_history' => $this->getPriceHistory($nft),
            'transfer_history' => $nft->transfers()
                ->selectRaw('DATE(transferred_at) as date, amount, currency')
                ->orderBy('date')
                ->get(),
            
            'bid_history' => $nft->bids()
                ->selectRaw('DATE(created_at) as date, amount, currency, status')
                ->orderBy('date')
                ->get(),
            
            'view_stats' => [
                'total_views' => $nft->views_count ?? 0,
                'unique_viewers' => $nft->views()->distinct('user_id')->count('user_id'),
                'daily_views' => $nft->views()
                    ->selectRaw('DATE(viewed_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->limit(30)
                    ->get(),
            ],
            
            'market_performance' => [
                'current_price' => $nft->price,
                'price_change_24h' => $this->calculatePriceChange($nft, 24),
                'price_change_7d' => $this->calculatePriceChange($nft, 168),
                'total_volume' => $nft->transfers()->sum('amount'),
                'market_cap' => $nft->price * 1, // Simplified market cap
            ],
        ];

        return response()->json($analytics);
    }

    /**
     * Process NFT sale.
     */
    private function processSale(MetaversePropertyNft $nft, $bid)
    {
        // Create transaction record
        $transaction = $nft->transfers()->create([
            'from_user_id' => $nft->owner_id,
            'to_user_id' => $bid->bidder_id,
            'amount' => $bid->amount,
            'currency' => $bid->currency,
            'transaction_hash' => $this->generateTransactionHash(),
            'status' => 'completed',
            'transferred_at' => now(),
        ]);

        // Update NFT ownership
        $nft->update([
            'owner_id' => $bid->bidder_id,
            'price' => $bid->amount,
            'is_for_sale' => false,
            'last_sale_price' => $bid->amount,
            'last_sale_at' => now(),
        ]);

        // Update property ownership
        if ($nft->metaverseProperty) {
            $nft->metaverseProperty->update([
                'owner_id' => $bid->bidder_id,
            ]);
        }

        // Update bid status
        $bid->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Reject other bids
        $nft->bids()
            ->where('id', '!=', $bid->id)
            ->where('status', 'active')
            ->update([
                'status' => 'rejected',
                'rejected_at' => now(),
            ]);

        // End auction if exists
        if ($nft->auction) {
            $nft->auction->update([
                'status' => 'ended',
                'ended_at' => now(),
                'result' => 'sold',
                'winning_bid_id' => $bid->id,
            ]);
        }

        return $transaction;
    }

    /**
     * Generate transaction hash.
     */
    private function generateTransactionHash(): string
    {
        return '0x' . Str::random(64);
    }

    /**
     * Verify NFT on blockchain.
     */
    private function verifyOnBlockchain(MetaversePropertyNft $nft): array
    {
        // This would integrate with blockchain verification service
        // For now, simulate verification
        
        return [
            'status' => 'verified',
            'details' => [
                'blockchain' => $nft->blockchain,
                'contract_address' => $nft->contract_address,
                'token_id' => $nft->token_id,
                'owner' => $nft->owner->wallet_address ?? null,
                'verified_at' => now(),
            ],
        ];
    }

    /**
     * Get price history.
     */
    private function getPriceHistory(MetaversePropertyNft $nft): array
    {
        return $nft->transfers()
            ->where('status', 'completed')
            ->selectRaw('DATE(transferred_at) as date, amount')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Get ownership history.
     */
    private function getOwnershipHistory(MetaversePropertyNft $nft): array
    {
        return $nft->transfers()
            ->where('status', 'completed')
            ->with(['fromUser', 'toUser'])
            ->orderBy('transferred_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Calculate price change.
     */
    private function calculatePriceChange(MetaversePropertyNft $nft, int $hours): float
    {
        $priceHistory = $nft->transfers()
            ->where('status', 'completed')
            ->where('transferred_at', '>', now()->subHours($hours))
            ->orderBy('transferred_at', 'desc')
            ->get();

        if ($priceHistory->count() < 2) {
            return 0;
        }

        $currentPrice = $priceHistory->first()->amount;
        $previousPrice = $priceHistory->last()->amount;

        return (($currentPrice - $previousPrice) / $previousPrice) * 100;
    }
}

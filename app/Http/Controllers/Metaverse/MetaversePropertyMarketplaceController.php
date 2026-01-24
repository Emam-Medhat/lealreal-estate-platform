<?php

namespace App\Http\Controllers\Metaverse;

use App\Http\Controllers\Controller;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\Metaverse\VirtualLand;
use App\Models\Metaverse\MetaversePropertyNft;
use App\Models\Metaverse\MetaverseTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MetaversePropertyMarketplaceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display the metaverse property marketplace.
     */
    public function index(Request $request)
    {
        // Properties for sale
        $propertiesQuery = MetaverseProperty::with(['virtualWorld', 'owner', 'nft'])
            ->where('is_for_sale', true)
            ->where('status', 'active')
            ->withCount(['tours', 'events']);

        // Lands for sale
        $landsQuery = VirtualLand::with(['virtualWorld', 'owner'])
            ->where('ownership_status', 'for_sale')
            ->where('status', 'active')
            ->withCount(['properties']);

        // NFTs for sale
        $nftsQuery = MetaversePropertyNft::with(['metaverseProperty', 'owner', 'creator'])
            ->where('is_for_sale', true)
            ->where('status', 'minted')
            ->withCount(['bids', 'transfers']);

        // Apply filters
        $this->applyFilters($request, $propertiesQuery, 'property');
        $this->applyFilters($request, $landsQuery, 'land');
        $this->applyFilters($request, $nftsQuery, 'nft');

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $propertiesQuery->orderBy($sortBy, $sortOrder);
        $landsQuery->orderBy($sortBy, $sortOrder);
        $nftsQuery->orderBy($sortBy, $sortOrder);

        // Get results
        $properties = $propertiesQuery->paginate(12);
        $lands = $landsQuery->paginate(12);
        $nfts = $nftsQuery->paginate(12);

        // Get marketplace statistics
        $stats = [
            'total_properties' => MetaverseProperty::where('is_for_sale', true)->where('status', 'active')->count(),
            'total_lands' => VirtualLand::where('ownership_status', 'for_sale')->where('status', 'active')->count(),
            'total_nfts' => MetaversePropertyNft::where('is_for_sale', true)->where('status', 'minted')->count(),
            'total_volume' => MetaverseTransaction::where('status', 'completed')->sum('amount'),
            'active_listings' => $properties->total() + $lands->total() + $nfts->total(),
        ];

        return Inertia::render('Metaverse/Marketplace/Index', [
            'properties' => $properties,
            'lands' => $lands,
            'nfts' => $nfts,
            'stats' => $stats,
            'filters' => $request->only([
                'world_id', 'property_type', 'land_type', 'price_min', 'price_max', 
                'currency', 'search', 'sort_by', 'sort_order', 'category'
            ]),
        ]);
    }

    /**
     * Show property details in marketplace.
     */
    public function showProperty(MetaverseProperty $property)
    {
        $property->load([
            'virtualWorld',
            'owner',
            'nft',
            'images',
            'models',
            'tours' => function ($query) {
                $query->where('is_active', true)->latest();
            },
            'events' => function ($query) {
                $query->where('start_time', '>', now())->orderBy('start_time');
            },
            'transactions' => function ($query) {
                $query->where('status', 'completed')->latest()->limit(10);
            },
        ]);

        // Get market data
        $marketData = $this->getPropertyMarketData($property);

        // Get similar properties
        $similarProperties = MetaverseProperty::where('id', '!=', $property->id)
            ->where('virtual_world_id', $property->virtual_world_id)
            ->where('property_type', $property->property_type)
            ->where('is_for_sale', true)
            ->where('status', 'active')
            ->with(['owner', 'nft'])
            ->limit(6)
            ->get();

        return Inertia::render('Metaverse/Marketplace/Property', [
            'property' => $property,
            'marketData' => $marketData,
            'similarProperties' => $similarProperties,
        ]);
    }

    /**
     * Show land details in marketplace.
     */
    public function showLand(VirtualLand $land)
    {
        $land->load([
            'virtualWorld',
            'owner',
            'properties' => function ($query) {
                $query->where('status', 'active')->with(['owner', 'nft']);
            },
            'transactions' => function ($query) {
                $query->where('status', 'completed')->latest()->limit(10);
            },
            'neighbors'
        ]);

        // Get market data
        $marketData = $this->getLandMarketData($land);

        // Get similar lands
        $similarLands = VirtualLand::where('id', '!=', $land->id)
            ->where('virtual_world_id', $land->virtual_world_id)
            ->where('land_type', $land->land_type)
            ->where('ownership_status', 'for_sale')
            ->where('status', 'active')
            ->with(['owner'])
            ->limit(6)
            ->get();

        return Inertia::render('Metaverse/Marketplace/Land', [
            'land' => $land,
            'marketData' => $marketData,
            'similarLands' => $similarLands,
        ]);
    }

    /**
     * Show NFT details in marketplace.
     */
    public function showNft(MetaversePropertyNft $nft)
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

        // Get market data
        $marketData = $this->getNftMarketData($nft);

        // Get similar NFTs
        $similarNfts = MetaversePropertyNft::where('id', '!=', $nft->id)
            ->where('blockchain', $nft->blockchain)
            ->where('is_for_sale', true)
            ->where('status', 'minted')
            ->with(['metaverseProperty', 'owner'])
            ->limit(6)
            ->get();

        return Inertia::render('Metaverse/Marketplace/Nft', [
            'nft' => $nft,
            'marketData' => $marketData,
            'similarNfts' => $similarNfts,
        ]);
    }

    /**
     * Make an offer on property.
     */
    public function makePropertyOffer(Request $request, MetaverseProperty $property)
    {
        $this->authorize('makeOffer', $property);

        $request->validate([
            'offer_price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'message' => 'nullable|string|max:500',
            'financing_type' => 'nullable|string|max:50',
            'contingencies' => 'nullable|array',
        ]);

        // Create offer transaction
        $transaction = MetaverseTransaction::create([
            'type' => 'property_offer',
            'metaverse_property_id' => $property->id,
            'buyer_id' => auth()->id(),
            'seller_id' => $property->owner_id,
            'amount' => $request->offer_price,
            'currency' => $request->currency,
            'status' => 'pending',
            'message' => $request->message,
            'metadata' => [
                'financing_type' => $request->financing_type,
                'contingencies' => $request->contingencies ?? [],
                'original_price' => $property->price,
                'price_difference' => $request->offer_price - $property->price,
            ],
        ]);

        return back()->with('success', 'تم إرسال العرض بنجاح');
    }

    /**
     * Make an offer on land.
     */
    public function makeLandOffer(Request $request, VirtualLand $land)
    {
        $this->authorize('makeOffer', $land);

        $request->validate([
            'offer_price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'message' => 'nullable|string|max:500',
            'development_plans' => 'nullable|array',
        ]);

        // Create offer transaction
        $transaction = MetaverseTransaction::create([
            'type' => 'land_offer',
            'virtual_land_id' => $land->id,
            'buyer_id' => auth()->id(),
            'seller_id' => $land->owner_id,
            'amount' => $request->offer_price,
            'currency' => $request->currency,
            'status' => 'pending',
            'message' => $request->message,
            'metadata' => [
                'development_plans' => $request->development_plans ?? [],
                'original_price' => $land->price,
                'price_difference' => $request->offer_price - $land->price,
            ],
        ]);

        return back()->with('success', 'تم إرسال العرض بنجاح');
    }

    /**
     * Purchase property directly.
     */
    public function purchaseProperty(Request $request, MetaverseProperty $property)
    {
        $this->authorize('purchase', $property);

        $request->validate([
            'payment_method' => 'required|string|max:50',
            'transaction_hash' => 'nullable|string|max:255',
            'financing_details' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($request, $property) {
            // Create transaction record
            $transaction = MetaverseTransaction::create([
                'type' => 'property_purchase',
                'metaverse_property_id' => $property->id,
                'buyer_id' => auth()->id(),
                'seller_id' => $property->owner_id,
                'amount' => $property->price,
                'currency' => $property->currency,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'transaction_hash' => $request->transaction_hash,
                'metadata' => [
                    'financing_details' => $request->financing_details ?? [],
                ],
            ]);

            // Process payment
            $paymentResult = $this->processPayment($transaction);
            
            if ($paymentResult['success']) {
                // Update property ownership
                $property->update([
                    'owner_id' => auth()->id(),
                    'is_for_sale' => false,
                    'sale_price' => $property->price,
                    'sale_currency' => $property->currency,
                    'sold_at' => now(),
                ]);

                // Update transaction status
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                return redirect()->route('metaverse.marketplace.property', $property)
                    ->with('success', 'تم شراء العقار بنجاح');
            } else {
                $transaction->update(['status' => 'failed']);
                
                return back()->with('error', 'فشلت عملية الشراء: ' . $paymentResult['message']);
            }
        });
    }

    /**
     * Purchase land directly.
     */
    public function purchaseLand(Request $request, VirtualLand $land)
    {
        $this->authorize('purchase', $land);

        $request->validate([
            'payment_method' => 'required|string|max:50',
            'transaction_hash' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($request, $land) {
            // Create transaction record
            $transaction = MetaverseTransaction::create([
                'type' => 'land_purchase',
                'virtual_land_id' => $land->id,
                'buyer_id' => auth()->id(),
                'seller_id' => $land->owner_id,
                'amount' => $land->price,
                'currency' => $land->currency,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'transaction_hash' => $request->transaction_hash,
            ]);

            // Process payment
            $paymentResult = $this->processPayment($transaction);
            
            if ($paymentResult['success']) {
                // Update land ownership
                $land->update([
                    'owner_id' => auth()->id(),
                    'ownership_status' => 'owned',
                    'status' => 'active',
                    'last_purchase_date' => now(),
                    'purchase_price' => $land->price,
                    'purchase_currency' => $land->currency,
                ]);

                // Update transaction status
                $transaction->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                return redirect()->route('metaverse.marketplace.land', $land)
                    ->with('success', 'تم شراء الأرض بنجاح');
            } else {
                $transaction->update(['status' => 'failed']);
                
                return back()->with('error', 'فشلت عملية الشراء: ' . $paymentResult['message']);
            }
        });
    }

    /**
     * Get marketplace analytics.
     */
    public function analytics()
    {
        $this->authorize('viewAnalytics');

        $analytics = [
            'market_overview' => [
                'total_volume' => MetaverseTransaction::where('status', 'completed')->sum('amount'),
                'total_transactions' => MetaverseTransaction::where('status', 'completed')->count(),
                'active_listings' => $this->getActiveListingsCount(),
                'average_prices' => $this->getAveragePrices(),
            ],
            
            'price_trends' => [
                'property_prices' => $this->getPropertyPriceTrends(),
                'land_prices' => $this->getLandPriceTrends(),
                'nft_prices' => $this->getNftPriceTrends(),
            ],
            
            'transaction_volume' => [
                'daily_volume' => $this->getDailyVolume(),
                'monthly_volume' => $this->getMonthlyVolume(),
                'by_currency' => $this->getVolumeByCurrency(),
            ],
            
            'popular_categories' => [
                'property_types' => $this->getPopularPropertyTypes(),
                'land_types' => $this->getPopularLandTypes(),
                'virtual_worlds' => $this->getPopularVirtualWorlds(),
            ],
        ];

        return response()->json($analytics);
    }

    /**
     * Get marketplace statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_properties' => MetaverseProperty::count(),
            'total_lands' => VirtualLand::count(),
            'total_nfts' => MetaversePropertyNft::count(),
            'for_sale_properties' => MetaverseProperty::where('is_for_sale', true)->count(),
            'for_sale_lands' => VirtualLand::where('ownership_status', 'for_sale')->count(),
            'for_sale_nfts' => MetaversePropertyNft::where('is_for_sale', true)->count(),
            'total_transactions' => MetaverseTransaction::count(),
            'total_volume' => MetaverseTransaction::where('status', 'completed')->sum('amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Apply filters to queries.
     */
    private function applyFilters(Request $request, $query, $type)
    {
        if ($request->filled('world_id')) {
            $query->where('virtual_world_id', $request->world_id);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            if ($type === 'property') {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            } elseif ($type === 'land') {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('coordinates', 'like', "%{$search}%");
                });
            } elseif ($type === 'nft') {
                $query->where(function ($q) use ($search) {
                    $q->where('token_id', 'like', "%{$search}%")
                      ->orWhereHas('metaverseProperty', function ($subQuery) use ($search) {
                          $subQuery->where('title', 'like', "%{$search}%");
                      });
                });
            }
        }

        // Type-specific filters
        if ($type === 'property' && $request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($type === 'land' && $request->filled('land_type')) {
            $query->where('land_type', $request->land_type);
        }

        if ($type === 'nft' && $request->filled('blockchain')) {
            $query->where('blockchain', $request->blockchain);
        }
    }

    /**
     * Get property market data.
     */
    private function getPropertyMarketData(MetaverseProperty $property): array
    {
        return [
            'price_history' => $property->transactions()
                ->where('status', 'completed')
                ->selectRaw('DATE(completed_at) as date, amount as price')
                ->orderBy('date')
                ->get(),
            
            'similar_properties' => $this->getSimilarPropertiesData($property),
            
            'market_comparison' => [
                'average_price' => MetaverseProperty::where('property_type', $property->property_type)
                    ->where('virtual_world_id', $property->virtual_world_id)
                    ->avg('price'),
                'price_per_square_meter' => $this->calculatePricePerSquareMeter($property),
                'days_on_market' => $property->created_at->diffInDays(now()),
            ],
        ];
    }

    /**
     * Get land market data.
     */
    private function getLandMarketData(VirtualLand $land): array
    {
        return [
            'price_history' => $land->transactions()
                ->where('status', 'completed')
                ->selectRaw('DATE(completed_at) as date, amount as price')
                ->orderBy('date')
                ->get(),
            
            'similar_lands' => $this->getSimilarLandsData($land),
            
            'market_comparison' => [
                'average_price_per_area' => VirtualLand::where('land_type', $land->land_type)
                    ->where('virtual_world_id', $land->virtual_world_id)
                    ->avg(\DB::raw('price / area')),
                'price_per_square_meter' => $land->area > 0 ? $land->price / $land->area : 0,
                'days_on_market' => $land->created_at->diffInDays(now()),
            ],
        ];
    }

    /**
     * Get NFT market data.
     */
    private function getNftMarketData(MetaversePropertyNft $nft): array
    {
        return [
            'price_history' => $nft->transfers()
                ->where('status', 'completed')
                ->selectRaw('DATE(transferred_at) as date, amount as price')
                ->orderBy('date')
                ->get(),
            
            'similar_nfts' => $this->getSimilarNftsData($nft),
            
            'market_comparison' => [
                'floor_price' => $this->getFloorPrice($nft),
                'average_price' => MetaversePropertyNft::where('blockchain', $nft->blockchain)
                    ->where('status', 'minted')
                    ->avg('price'),
                'price_change_24h' => $this->calculatePriceChange24h($nft),
            ],
        ];
    }

    /**
     * Process payment.
     */
    private function processPayment($transaction): array
    {
        // This would integrate with actual payment gateway
        // For now, simulate payment processing
        
        try {
            if ($transaction->currency === 'ETH') {
                $result = $this->processCryptoPayment($transaction);
            } else {
                $result = $this->processTraditionalPayment($transaction);
            }

            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process cryptocurrency payment.
     */
    private function processCryptoPayment($transaction): array
    {
        // Simulate crypto payment processing
        return [
            'success' => true,
            'message' => 'Payment processed successfully',
        ];
    }

    /**
     * Process traditional payment.
     */
    private function processTraditionalPayment($transaction): array
    {
        // Simulate traditional payment processing
        return [
            'success' => true,
            'message' => 'Payment processed successfully',
        ];
    }

    // Additional helper methods for analytics
    private function getActiveListingsCount(): int
    {
        return MetaverseProperty::where('is_for_sale', true)->count() +
               VirtualLand::where('ownership_status', 'for_sale')->count() +
               MetaversePropertyNft::where('is_for_sale', true)->count();
    }

    private function getAveragePrices(): array
    {
        return [
            'property' => MetaverseProperty::where('is_for_sale', true)->avg('price'),
            'land' => VirtualLand::where('ownership_status', 'for_sale')->avg('price'),
            'nft' => MetaversePropertyNft::where('is_for_sale', true)->avg('price'),
        ];
    }

    private function getPropertyPriceTrends(): array
    {
        return []; // Placeholder
    }

    private function getLandPriceTrends(): array
    {
        return []; // Placeholder
    }

    private function getNftPriceTrends(): array
    {
        return []; // Placeholder
    }

    private function getDailyVolume(): array
    {
        return []; // Placeholder
    }

    private function getMonthlyVolume(): array
    {
        return []; // Placeholder
    }

    private function getVolumeByCurrency(): array
    {
        return []; // Placeholder
    }

    private function getPopularPropertyTypes(): array
    {
        return []; // Placeholder
    }

    private function getPopularLandTypes(): array
    {
        return []; // Placeholder
    }

    private function getPopularVirtualWorlds(): array
    {
        return []; // Placeholder
    }

    private function getSimilarPropertiesData(MetaverseProperty $property): array
    {
        return []; // Placeholder
    }

    private function getSimilarLandsData(VirtualLand $land): array
    {
        return []; // Placeholder
    }

    private function getSimilarNftsData(MetaversePropertyNft $nft): array
    {
        return []; // Placeholder
    }

    private function calculatePricePerSquareMeter(MetaverseProperty $property): float
    {
        // Calculate based on property dimensions
        return 0; // Placeholder
    }

    private function getFloorPrice(MetaversePropertyNft $nft): float
    {
        return MetaversePropertyNft::where('blockchain', $nft->blockchain)
            ->where('status', 'minted')
            ->min('price') ?? 0;
    }

    private function calculatePriceChange24h(MetaversePropertyNft $nft): float
    {
        return 0; // Placeholder
    }
}

<?php

namespace App\Http\Controllers\Metaverse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Metaverse\PurchaseVirtualLandRequest;
use App\Models\Metaverse\VirtualLand;
use App\Models\Metaverse\VirtualWorld;
use App\Models\Metaverse\MetaverseTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VirtualLandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->only(['purchase', 'transfer', 'develop']);
    }

    /**
     * Display a listing of virtual lands.
     */
    public function index(Request $request)
    {
        $query = VirtualLand::with(['virtualWorld', 'owner'])
            ->withCount(['properties', 'transactions']);

        // Filters
        if ($request->filled('world_id')) {
            $query->where('virtual_world_id', $request->world_id);
        }

        if ($request->filled('land_type')) {
            $query->where('land_type', $request->land_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('ownership_status')) {
            $query->where('ownership_status', $request->ownership_status);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('area_min')) {
            $query->where('area', '>=', $request->area_min);
        }

        if ($request->filled('area_max')) {
            $query->where('area', '<=', $request->area_max);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('coordinates', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $lands = $query->paginate(12);
        $virtualWorlds = VirtualWorld::where('is_active', true)->get();

        return Inertia::render('Metaverse/Lands/Index', [
            'lands' => $lands,
            'virtualWorlds' => $virtualWorlds,
            'filters' => $request->only([
                'world_id', 'land_type', 'status', 'ownership_status', 
                'price_min', 'price_max', 'area_min', 'area_max', 'search', 'sort_by', 'sort_order'
            ]),
        ]);
    }

    /**
     * Display the specified virtual land.
     */
    public function show(VirtualLand $land)
    {
        $land->load([
            'virtualWorld',
            'owner',
            'properties' => function ($query) {
                $query->where('status', 'active')->with(['owner', 'nft']);
            },
            'transactions' => function ($query) {
                $query->latest()->limit(10);
            },
            'neighbors'
        ]);

        // Get land statistics
        $stats = [
            'total_properties' => $land->properties_count,
            'total_transactions' => $land->transactions_count,
            'average_property_value' => $land->properties()->avg('price') ?? 0,
            'development_potential' => $this->calculateDevelopmentPotential($land),
            'zoning_restrictions' => $this->getZoningRestrictions($land),
        ];

        // Get nearby lands
        $nearbyLands = VirtualLand::where('virtual_world_id', $land->virtual_world_id)
            ->where('id', '!=', $land->id)
            ->where('status', 'active')
            ->with(['owner'])
            ->limit(6)
            ->get();

        return Inertia::render('Metaverse/Lands/Show', [
            'land' => $land,
            'stats' => $stats,
            'nearbyLands' => $nearbyLands,
        ]);
    }

    /**
     * Purchase virtual land.
     */
    public function purchase(PurchaseVirtualLandRequest $request, VirtualLand $land)
    {
        $this->authorize('purchase', $land);

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
                'metadata' => [
                    'land_title' => $land->title,
                    'land_coordinates' => $land->coordinates,
                    'virtual_world' => $land->virtualWorld->name,
                ],
            ]);

            // Process payment (this would integrate with payment gateway)
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

                return redirect()->route('metaverse.lands.show', $land)
                    ->with('success', 'تم شراء الأرض الافتراضية بنجاح');
            } else {
                $transaction->update(['status' => 'failed']);
                
                return back()->with('error', 'فشلت عملية الشراء: ' . $paymentResult['message']);
            }
        });
    }

    /**
     * Transfer virtual land ownership.
     */
    public function transfer(Request $request, VirtualLand $land)
    {
        $this->authorize('transfer', $land);

        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'transfer_price' => 'nullable|numeric|min:0',
            'transfer_currency' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        $recipient = User::findOrFail($request->recipient_id);

        return DB::transaction(function () use ($request, $land, $recipient) {
            // Create transfer transaction
            $transaction = MetaverseTransaction::create([
                'type' => 'land_transfer',
                'virtual_land_id' => $land->id,
                'buyer_id' => $recipient->id,
                'seller_id' => auth()->id(),
                'amount' => $request->transfer_price ?? 0,
                'currency' => $request->transfer_currency ?? $land->currency,
                'status' => 'pending',
                'notes' => $request->notes,
                'metadata' => [
                    'transfer_type' => $request->transfer_price > 0 ? 'paid' : 'gift',
                    'land_title' => $land->title,
                ],
            ]);

            // Update land ownership
            $land->update([
                'owner_id' => $recipient->id,
                'ownership_status' => 'owned',
                'last_transfer_date' => now(),
            ]);

            // Update transaction status
            $transaction->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return redirect()->route('metaverse.lands.show', $land)
                ->with('success', 'تم نقل ملكية الأرض الافتراضية بنجاح');
        });
    }

    /**
     * Develop virtual land.
     */
    public function develop(Request $request, VirtualLand $land)
    {
        $this->authorize('develop', $land);

        $request->validate([
            'development_type' => 'required|in:residential,commercial,mixed,industrial,recreational',
            'development_plan' => 'required|string|max:2000',
            'estimated_cost' => 'nullable|numeric|min:0',
            'estimated_timeline' => 'nullable|string|max:100',
            'zoning_compliance' => 'required|boolean',
            'environmental_impact' => 'nullable|string|max:1000',
            'infrastructure_requirements' => 'nullable|string|max:1000',
        ]);

        $land->update([
            'development_status' => 'planned',
            'development_type' => $request->development_type,
            'development_plan' => $request->development_plan,
            'estimated_development_cost' => $request->estimated_cost,
            'estimated_development_timeline' => $request->estimated_timeline,
            'zoning_compliance' => $request->zoning_compliance,
            'environmental_impact_assessment' => $request->environmental_impact,
            'infrastructure_requirements' => $request->infrastructure_requirements,
            'development_started_at' => now(),
        ]);

        return redirect()->route('metaverse.lands.show', $land)
            ->with('success', 'تم بدء عملية تطوير الأرض الافتراضية');
    }

    /**
     * Get land valuation.
     */
    public function valuation(VirtualLand $land)
    {
        $valuation = $this->calculateLandValuation($land);

        return response()->json([
            'current_value' => $valuation['current_value'],
            'market_value' => $valuation['market_value'],
            'potential_value' => $valuation['potential_value'],
            'value_trend' => $valuation['value_trend'],
            'comparable_sales' => $valuation['comparable_sales'],
            'factors' => $valuation['factors'],
        ]);
    }

    /**
     * Get land analytics.
     */
    public function analytics(VirtualLand $land)
    {
        $this->authorize('view', $land);

        $analytics = [
            'price_history' => $land->transactions()
                ->where('type', 'land_purchase')
                ->where('status', 'completed')
                ->selectRaw('DATE(created_at) as date, amount as price')
                ->orderBy('date')
                ->get(),
            
            'development_timeline' => $this->getDevelopmentTimeline($land),
            
            'neighborhood_analysis' => [
                'average_land_value' => $land->neighbors()->avg('price') ?? 0,
                'development_density' => $this->calculateDevelopmentDensity($land),
                'property_types' => $this->getPropertyTypesDistribution($land),
            ],
            
            'market_trends' => [
                'price_appreciation' => $this->calculatePriceAppreciation($land),
                'demand_index' => $this->calculateDemandIndex($land),
                'development_activity' => $this->getDevelopmentActivity($land),
            ],
        ];

        return response()->json($analytics);
    }

    /**
     * Process payment for land purchase.
     */
    private function processPayment($transaction): array
    {
        // This would integrate with actual payment gateway
        // For now, simulate payment processing
        
        try {
            // Simulate payment processing
            if ($transaction->payment_method === 'crypto') {
                // Process cryptocurrency payment
                $result = $this->processCryptoPayment($transaction);
            } else {
                // Process traditional payment
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
        // In real implementation, this would interact with blockchain
        
        return [
            'success' => true,
            'message' => 'Payment processed successfully',
            'transaction_hash' => $transaction->transaction_hash,
        ];
    }

    /**
     * Process traditional payment.
     */
    private function processTraditionalPayment($transaction): array
    {
        // Simulate traditional payment processing
        // In real implementation, this would interact with payment gateway
        
        return [
            'success' => true,
            'message' => 'Payment processed successfully',
        ];
    }

    /**
     * Calculate development potential.
     */
    private function calculateDevelopmentPotential(VirtualLand $land): array
    {
        $potential = [
            'max_buildings' => floor($land->area / 100), // Assume 100 units per building
            'max_floors' => $land->virtualWorld->max_building_height ?? 10,
            'building_types_allowed' => $this->getAllowedBuildingTypes($land),
            'infrastructure_score' => $this->calculateInfrastructureScore($land),
            'environmental_score' => $this->calculateEnvironmentalScore($land),
        ];

        return $potential;
    }

    /**
     * Get zoning restrictions.
     */
    private function getZoningRestrictions(VirtualLand $land): array
    {
        return [
            'residential_allowed' => in_array('residential', $land->zoning_types ?? []),
            'commercial_allowed' => in_array('commercial', $land->zoning_types ?? []),
            'industrial_allowed' => in_array('industrial', $land->zoning_types ?? []),
            'max_building_height' => $land->max_building_height,
            'min_lot_size' => $land->min_lot_size,
            'setback_requirements' => $land->setback_requirements,
            'parking_requirements' => $land->parking_requirements,
        ];
    }

    /**
     * Calculate land valuation.
     */
    private function calculateLandValuation(VirtualLand $land): array
    {
        $baseValue = $land->price;
        $locationMultiplier = $this->getLocationMultiplier($land);
        $developmentMultiplier = $this->getDevelopmentMultiplier($land);
        $marketMultiplier = $this->getMarketMultiplier($land);

        $marketValue = $baseValue * $locationMultiplier * $developmentMultiplier * $marketMultiplier;
        $potentialValue = $marketValue * 1.5; // Assume 50% potential increase

        return [
            'current_value' => $baseValue,
            'market_value' => $marketValue,
            'potential_value' => $potentialValue,
            'value_trend' => $this->getValueTrend($land),
            'comparable_sales' => $this->getComparableSales($land),
            'factors' => [
                'location_score' => $locationMultiplier,
                'development_score' => $developmentMultiplier,
                'market_score' => $marketMultiplier,
            ],
        ];
    }

    /**
     * Get location multiplier.
     */
    private function getLocationMultiplier(VirtualLand $land): float
    {
        // Calculate based on proximity to key locations
        $multiplier = 1.0;
        
        // Add premium for prime locations
        if ($land->is_prime_location) {
            $multiplier += 0.3;
        }
        
        // Add premium for waterfront properties
        if ($land->is_waterfront) {
            $multiplier += 0.2;
        }
        
        return $multiplier;
    }

    /**
     * Get development multiplier.
     */
    private function getDevelopmentMultiplier(VirtualLand $land): float
    {
        $multiplier = 1.0;
        
        // Add premium for developed areas
        if ($land->development_status === 'developed') {
            $multiplier += 0.4;
        } elseif ($land->development_status === 'developing') {
            $multiplier += 0.2;
        }
        
        return $multiplier;
    }

    /**
     * Get market multiplier.
     */
    private function getMarketMultiplier(VirtualLand $land): float
    {
        // Calculate based on market conditions
        $demand = $this->calculateDemandIndex($land);
        return 1.0 + ($demand - 50) / 100;
    }

    /**
     * Calculate demand index.
     */
    private function calculateDemandIndex(VirtualLand $land): float
    {
        // Calculate demand based on recent sales and inquiries
        $recentSales = $land->transactions()
            ->where('type', 'land_purchase')
            ->where('created_at', '>', now()->subDays(30))
            ->count();
            
        return min(100, $recentSales * 10); // Simple calculation
    }

    /**
     * Get comparable sales.
     */
    private function getComparableSales(VirtualLand $land): array
    {
        return $land->neighbors()
            ->where('land_type', $land->land_type)
            ->where('area', '>=', $land->area * 0.8)
            ->where('area', '<=', $land->area * 1.2)
            ->with(['transactions' => function ($query) {
                $query->where('type', 'land_purchase')
                      ->where('status', 'completed')
                      ->latest();
            }])
            ->get()
            ->map(function ($neighbor) {
                $lastSale = $neighbor->transactions->first();
                return [
                    'land_id' => $neighbor->id,
                    'title' => $neighbor->title,
                    'area' => $neighbor->area,
                    'price' => $lastSale ? $lastSale->amount : $neighbor->price,
                    'sale_date' => $lastSale ? $lastSale->created_at : null,
                ];
            })
            ->toArray();
    }

    /**
     * Get value trend.
     */
    private function getValueTrend(VirtualLand $land): string
    {
        $sales = $land->transactions()
            ->where('type', 'land_purchase')
            ->where('status', 'completed')
            ->orderBy('created_at')
            ->get();
            
        if ($sales->count() < 2) {
            return 'stable';
        }
        
        $firstSale = $sales->first();
        $lastSale = $sales->last();
        
        $change = (($lastSale->amount - $firstSale->amount) / $firstSale->amount) * 100;
        
        if ($change > 10) {
            return 'increasing';
        } elseif ($change < -10) {
            return 'decreasing';
        } else {
            return 'stable';
        }
    }

    // Additional helper methods would be implemented here...
    private function getAllowedBuildingTypes(VirtualLand $land): array
    {
        return $land->zoning_types ?? ['residential', 'commercial'];
    }

    private function calculateInfrastructureScore(VirtualLand $land): float
    {
        // Calculate infrastructure score based on available utilities
        return 0.8; // Placeholder
    }

    private function calculateEnvironmentalScore(VirtualLand $land): float
    {
        // Calculate environmental impact score
        return 0.7; // Placeholder
    }

    private function getDevelopmentTimeline(VirtualLand $land): array
    {
        return []; // Placeholder
    }

    private function calculateDevelopmentDensity(VirtualLand $land): float
    {
        return 0.5; // Placeholder
    }

    private function getPropertyTypesDistribution(VirtualLand $land): array
    {
        return []; // Placeholder
    }

    private function calculatePriceAppreciation(VirtualLand $land): float
    {
        return 5.2; // Placeholder
    }

    private function getDevelopmentActivity(VirtualLand $land): array
    {
        return []; // Placeholder
    }
}

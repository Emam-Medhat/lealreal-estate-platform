<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyPrice;
use App\Models\PropertyPriceHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PropertyPriceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['show', 'history']);
    }

    public function index(Property $property)
    {
        $prices = $property->prices()
            ->with(['changedBy:id,name'])
            ->orderBy('effective_date', 'desc')
            ->get();

        return view('properties.prices.index', compact('property', 'prices'));
    }

    public function show(Property $property, PropertyPrice $price)
    {
        return response()->json([
            'success' => true,
            'data' => $price,
        ]);
    }

    public function store(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:SAR,USD,EUR',
            'price_type' => 'required|in:sale,rent,lease',
            'is_negotiable' => 'boolean',
            'includes_vat' => 'boolean',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'service_charges' => 'nullable|numeric|min:0',
            'maintenance_fees' => 'nullable|numeric|min:0',
            'payment_frequency' => 'nullable|string|in:monthly,quarterly,annually',
            'payment_terms' => 'nullable|array',
            'effective_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'change_reason' => 'nullable|string|max:500',
        ]);

        // Deactivate existing prices
        $property->prices()->where('is_active', true)->update(['is_active' => false]);

        // Create new price
        $newPrice = PropertyPrice::create([
            'property_id' => $property->id,
            'price' => $request->price,
            'currency' => $request->currency,
            'price_type' => $request->price_type,
            'price_per_sqm' => $property->details ? $request->price / $property->details->area : null,
            'is_negotiable' => $request->is_negotiable ?? false,
            'includes_vat' => $request->includes_vat ?? false,
            'vat_rate' => $request->vat_rate ?? 0,
            'service_charges' => $request->service_charges,
            'maintenance_fees' => $request->maintenance_fees,
            'payment_frequency' => $request->payment_frequency,
            'payment_terms' => $request->payment_terms,
            'effective_date' => $request->effective_date,
            'expiry_date' => $request->expiry_date,
            'is_active' => true,
        ]);

        // Record price history if there was a previous price
        $oldPrice = $property->prices()->where('id', '!=', $newPrice->id)->first();
        if ($oldPrice) {
            $changeType = $request->price > $oldPrice->price ? 'increase' : 'decrease';
            $changePercentage = abs(($request->price - $oldPrice->price) / $oldPrice->price * 100);

            PropertyPriceHistory::create([
                'property_id' => $property->id,
                'old_price' => $oldPrice->price,
                'new_price' => $request->price,
                'currency' => $request->currency,
                'change_reason' => $request->change_reason,
                'change_type' => $changeType,
                'change_percentage' => $changePercentage,
                'changed_by' => Auth::id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Price added successfully',
            'price' => $newPrice,
        ]);
    }

    public function update(Request $request, Property $property, PropertyPrice $price): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:SAR,USD,EUR',
            'price_type' => 'required|in:sale,rent,lease',
            'is_negotiable' => 'boolean',
            'includes_vat' => 'boolean',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'service_charges' => 'nullable|numeric|min:0',
            'maintenance_fees' => 'nullable|numeric|min:0',
            'payment_frequency' => 'nullable|string|in:monthly,quarterly,annually',
            'payment_terms' => 'nullable|array',
            'effective_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'change_reason' => 'nullable|string|max:500',
        ]);

        $oldPrice = $price->price;

        $price->update([
            'price' => $request->price,
            'currency' => $request->currency,
            'price_type' => $request->price_type,
            'price_per_sqm' => $property->details ? $request->price / $property->details->area : null,
            'is_negotiable' => $request->is_negotiable ?? false,
            'includes_vat' => $request->includes_vat ?? false,
            'vat_rate' => $request->vat_rate ?? 0,
            'service_charges' => $request->service_charges,
            'maintenance_fees' => $request->maintenance_fees,
            'payment_frequency' => $request->payment_frequency,
            'payment_terms' => $request->payment_terms,
            'effective_date' => $request->effective_date,
            'expiry_date' => $request->expiry_date,
        ]);

        // Record price history if price changed
        if ($oldPrice != $request->price) {
            $changeType = $request->price > $oldPrice ? 'increase' : 'decrease';
            $changePercentage = abs(($request->price - $oldPrice) / $oldPrice * 100);

            PropertyPriceHistory::create([
                'property_id' => $property->id,
                'old_price' => $oldPrice,
                'new_price' => $request->price,
                'currency' => $request->currency,
                'change_reason' => $request->change_reason,
                'change_type' => $changeType,
                'change_percentage' => $changePercentage,
                'changed_by' => Auth::id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Price updated successfully',
            'price' => $price,
        ]);
    }

    public function destroy(Property $property, PropertyPrice $price): JsonResponse
    {
        $this->authorize('update', $property);

        $price->delete();

        return response()->json([
            'success' => true,
            'message' => 'Price deleted successfully',
        ]);
    }

    public function history(Property $property): JsonResponse
    {
        $history = $property->priceHistory()
            ->with(['changedBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }

    public function activate(Request $request, Property $property, PropertyPrice $price): JsonResponse
    {
        $this->authorize('update', $property);

        // Deactivate all other prices
        $property->prices()->where('id', '!=', $price->id)->update(['is_active' => false]);

        // Activate this price
        $price->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Price activated successfully',
            'price' => $price,
        ]);
    }

    public function getPriceAnalysis(Property $property): JsonResponse
    {
        $this->authorize('viewStats', $property);

        $currentPrice = $property->price;
        $priceHistory = $property->priceHistory()->orderBy('created_at', 'desc')->get();

        $analysis = [
            'current_price' => $currentPrice,
            'price_trend' => $this->calculatePriceTrend($priceHistory),
            'price_changes' => $priceHistory->count(),
            'last_change' => $priceHistory->first(),
            'average_price' => $property->prices()->avg('price'),
            'min_price' => $property->prices()->min('price'),
            'max_price' => $property->prices()->max('price'),
            'price_volatility' => $this->calculatePriceVolatility($priceHistory),
            'market_comparison' => $this->getMarketComparison($property),
        ];

        return response()->json([
            'success' => true,
            'data' => $analysis,
        ]);
    }

    public function getPriceSuggestions(Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $suggestions = $this->generatePriceSuggestions($property);

        return response()->json([
            'success' => true,
            'data' => $suggestions,
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->authorize('bulkUpdate', Property::class);

        $request->validate([
            'property_ids' => 'required|array',
            'property_ids.*' => 'exists:properties,id',
            'price_change_type' => 'required|in:percentage,fixed',
            'price_change_value' => 'required|numeric',
            'change_reason' => 'required|string|max:500',
        ]);

        $properties = Property::whereIn('id', $request->property_ids)
            ->with(['price', 'details'])
            ->get();

        $updatedCount = 0;
        $errors = [];

        foreach ($properties as $property) {
            try {
                $currentPrice = $property->price;
                if (!$currentPrice) {
                    $errors[] = "Property {$property->id} has no current price";
                    continue;
                }

                $oldPrice = $currentPrice->price;
                $newPrice = $request->price_change_type === 'percentage' 
                    ? $oldPrice * (1 + $request->price_change_value / 100)
                    : $oldPrice + $request->price_change_value;

                if ($newPrice <= 0) {
                    $errors[] = "Invalid new price for property {$property->id}";
                    continue;
                }

                // Deactivate old price
                $currentPrice->update(['is_active' => false]);

                // Create new price
                $newPriceRecord = PropertyPrice::create([
                    'property_id' => $property->id,
                    'price' => $newPrice,
                    'currency' => $currentPrice->currency,
                    'price_type' => $currentPrice->price_type,
                    'price_per_sqm' => $property->details ? $newPrice / $property->details->area : null,
                    'is_negotiable' => $currentPrice->is_negotiable,
                    'includes_vat' => $currentPrice->includes_vat,
                    'vat_rate' => $currentPrice->vat_rate,
                    'service_charges' => $currentPrice->service_charges,
                    'maintenance_fees' => $currentPrice->maintenance_fees,
                    'payment_frequency' => $currentPrice->payment_frequency,
                    'payment_terms' => $currentPrice->payment_terms,
                    'effective_date' => now(),
                    'is_active' => true,
                ]);

                // Record price history
                $changeType = $newPrice > $oldPrice ? 'increase' : 'decrease';
                $changePercentage = abs(($newPrice - $oldPrice) / $oldPrice * 100);

                PropertyPriceHistory::create([
                    'property_id' => $property->id,
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice,
                    'currency' => $currentPrice->currency,
                    'change_reason' => $request->change_reason,
                    'change_type' => $changeType,
                    'change_percentage' => $changePercentage,
                    'changed_by' => Auth::id(),
                ]);

                $updatedCount++;

            } catch (\Exception $e) {
                $errors[] = "Failed to update property {$property->id}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => $updatedCount > 0,
            'message' => $updatedCount . ' property prices updated successfully',
            'updated_count' => $updatedCount,
            'errors' => $errors,
        ]);
    }

    private function calculatePriceTrend($priceHistory): string
    {
        if ($priceHistory->count() < 2) {
            return 'insufficient_data';
        }

        $recentChanges = $priceHistory->take(10);
        $increases = $recentChanges->where('change_type', 'increase')->count();
        $decreases = $recentChanges->where('change_type', 'decrease')->count();

        if ($increases > $decreases * 1.5) {
            return 'strong_increase';
        } elseif ($increases > $decreases) {
            return 'moderate_increase';
        } elseif ($decreases > $increases * 1.5) {
            return 'strong_decrease';
        } elseif ($decreases > $increases) {
            return 'moderate_decrease';
        } else {
            return 'stable';
        }
    }

    private function calculatePriceVolatility($priceHistory): float
    {
        if ($priceHistory->count() < 2) {
            return 0;
        }

        $changes = $priceHistory->pluck('change_percentage');
        $mean = $changes->sum() / $changes->count();
        $variance = $changes->sum(function($change) use ($mean) {
            return pow($change - $mean, 2);
        }) / $changes->count();

        return sqrt($variance);
    }

    private function getMarketComparison(Property $property): array
    {
        $similarProperties = Property::where('property_type_id', $property->property_type_id)
            ->where('id', '!=', $property->id)
            ->where('status', 'active')
            ->whereHas('location', function($query) use ($property) {
                $query->where('city', $property->location->city);
            })
            ->with(['price'])
            ->get();

        if ($similarProperties->isEmpty()) {
            return [
                'average_price' => null,
                'median_price' => null,
                'price_percentile' => null,
                'comparison_message' => 'No similar properties found for comparison',
            ];
        }

        $prices = $similarProperties->pluck('price.price')->filter();
        $averagePrice = $prices->avg();
        $medianPrice = $prices->median();
        $currentPrice = $property->price->price;

        $pricePercentile = $prices->filter(function($price) use ($currentPrice) {
            return $price <= $currentPrice;
        })->count() / $prices->count() * 100;

        return [
            'average_price' => $averagePrice,
            'median_price' => $medianPrice,
            'price_percentile' => $pricePercentile,
            'comparison_message' => $this->getComparisonMessage($pricePercentile),
        ];
    }

    private function getComparisonMessage(float $percentile): string
    {
        if ($percentile >= 80) {
            return 'Your property is priced higher than 80% of similar properties';
        } elseif ($percentile >= 60) {
            return 'Your property is priced higher than 60% of similar properties';
        } elseif ($percentile >= 40) {
            return 'Your property is priced in the middle range of similar properties';
        } elseif ($percentile >= 20) {
            return 'Your property is priced lower than 60% of similar properties';
        } else {
            return 'Your property is priced lower than 80% of similar properties';
        }
    }

    private function generatePriceSuggestions(Property $property): array
    {
        $marketComparison = $this->getMarketComparison($property);
        $priceTrend = $this->calculatePriceTrend($property->priceHistory()->get());

        $suggestions = [];

        // Market-based suggestions
        if ($marketComparison['average_price']) {
            $currentPrice = $property->price->price;
            $averagePrice = $marketComparison['average_price'];
            $difference = (($currentPrice - $averagePrice) / $averagePrice) * 100;

            if (abs($difference) > 15) {
                $suggestions[] = [
                    'type' => 'market_adjustment',
                    'message' => "Consider adjusting price by " . round($difference, 1) . "% to align with market average",
                    'suggested_price' => $averagePrice,
                    'confidence' => 'medium',
                ];
            }
        }

        // Trend-based suggestions
        if ($priceTrend === 'strong_decrease') {
            $suggestions[] = [
                'type' => 'trend_alert',
                'message' => 'Market prices are trending down. Consider competitive pricing',
                'suggested_price' => null,
                'confidence' => 'high',
            ];
        }

        // Days on market suggestion
        $daysOnMarket = $property->created_at->diffInDays(now());
        if ($daysOnMarket > 90 && $property->status === 'active') {
            $suggestions[] = [
                'type' => 'market_time',
                'message' => 'Property has been on market for ' . $daysOnMarket . ' days. Consider price reduction',
                'suggested_price' => $property->price->price * 0.95, // 5% reduction
                'confidence' => 'medium',
            ];
        }

        return $suggestions;
    }
}

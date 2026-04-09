<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\RecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Recommendation Controller
 *
 * GET /api/ai/recommendations        → returns scored property list for auth user
 * GET /api/ai/recommendations/{id}   → recommendations for a specific property (similar)
 */
class RecommendationController extends Controller
{
    public function __construct(
        private readonly RecommendationService $service
    ) {
    }

    /**
     * GET /api/ai/recommendations
     * Personalised recommendations for the authenticated user.
     */
    public function forUser(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => ['sometimes', 'integer', 'min:1', 'max:20'],
        ]);

        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $result = $this->service->recommend($userId, $request->input('limit', 5));
        
        // Enrich with full property details
        $items = $result['items'];
        $ids = array_column($items, 'property_id');
        
        if (!empty($ids)) {
            $properties = \App\Models\Property::whereIn('id', $ids)->get()->keyBy('id');
            
            foreach ($items as &$item) {
                $prop = $properties->get($item['property_id']);
                if ($prop) {
                    $item['image'] = $prop->main_image ?? ($prop->images[0] ?? null);
                    $item['price'] = $prop->price;
                    $item['currency'] = $prop->currency ?? 'EGP';
                    $item['location'] = trim(($prop->city ?? '') . ', ' . ($prop->state ?? ''), ', ');
                    $item['bedrooms'] = $prop->bedrooms;
                    $item['bathrooms'] = $prop->bathrooms;
                    $item['area'] = $prop->area;
                    $item['slug'] = $prop->slug;
                }
            }
        }

        return response()->json([
            'success' => true,
            'source' => $result['source'],
            'data' => $items,
        ]);
    }

    /**
     * GET /api/ai/recommendations/{propertyId}/similar
     * Properties similar to the given one (anonymous users too).
     */
    public function similar(int $propertyId): JsonResponse
    {
        // Build a minimal "profile" from the viewed property
        $property = \App\Models\Property::with(['price', 'details', 'location'])->findOrFail($propertyId);

        // Build synthetic profile from the property itself
        $syntheticProfile = [
            'avg_price' => optional($property->price)->price,
            'preferred_cities' => [optional($property->location)->city],
            'preferred_districts' => [optional($property->location)->district],
            'preferred_bedrooms' => optional($property->details)->bedrooms,
            'searched_property_types' => [$property->listing_type],
            'contacted_cities' => [],
            'viewed_ids' => [$propertyId],
            'saved_ids' => [],
        ];

        // Compute scores against this synthetic profile
        $candidates = \Illuminate\Support\Facades\DB::table('properties as p')
            ->join('property_prices   as pp', 'p.id', '=', 'pp.property_id')
            ->join('property_details  as pd', 'p.id', '=', 'pd.property_id')
            ->join('property_locations as pl', 'p.id', '=', 'pl.property_id')
            ->select('p.id', 'p.title', 'p.listing_type', 'pp.price', 'pd.bedrooms', 'pd.bathrooms', 'pl.city', 'pl.district')
            ->where('p.status', 'active')
            ->where('p.id', '!=', $propertyId)
            ->orderByDesc('p.featured')
            ->limit(200)
            ->get();

        $scored = [];
        foreach ($candidates as $c) {
            $priceScore = $this->priceSim($c->price, $syntheticProfile['avg_price']);
            $locationScore = in_array($c->city, $syntheticProfile['preferred_cities'], true) ? 100 : 10;
            $bedroomScore = $syntheticProfile['preferred_bedrooms']
                ? max(0, 100 - abs($c->bedrooms - $syntheticProfile['preferred_bedrooms']) * 25)
                : 50;

            $total = round(
                ($priceScore * 0.40)
                + ($locationScore * 0.40)
                + ($bedroomScore * 0.20),
                1
            );

            $scored[] = [
                'property_id' => $c->id,
                'title' => $c->title,
                'score' => min(100, max(0, $total)),
            ];
        }

        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);

        return response()->json([
            'success' => true,
            'data' => array_slice($scored, 0, 5),
        ]);
    }

    private function priceSim(?float $price, ?float $ref): float
    {
        if (!$price || !$ref)
            return 50.0;
        $ratio = abs($price - $ref) / $ref;
        return max(0, 100 - ($ratio * 200));
    }
}

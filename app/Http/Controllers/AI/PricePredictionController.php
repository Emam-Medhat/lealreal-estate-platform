<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\PricePredictionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Price Prediction Controller
 *
 * POST /api/ai/price-prediction
 * POST /api/ai/price-prediction/property/{id}  → predict for an existing property
 */
class PricePredictionController extends Controller
{
    public function __construct(
        private readonly PricePredictionService $service
    ) {
    }

    /**
     * POST /api/ai/price-prediction
     *
     * Body: { area, city, bedrooms, floor?, finishing?, building_age?, listing_type? }
     */
    public function predict(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'area' => ['required', 'numeric', 'min:10'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['sometimes', 'nullable', 'string', 'max:100'],
            'bedrooms' => ['required', 'integer', 'min:0', 'max:20'],
            'floor' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'finishing' => [
                'sometimes',
                'nullable',
                'string',
                'in:unfinished,semi-finished,standard,super-lux,luxury'
            ],
            'building_age' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'listing_type' => ['sometimes', 'nullable', 'string', 'in:sale,rent'],
        ]);

        $result = $this->service->predict($validated);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Prediction failed',
            ], 503);
        }

        return response()->json([
            'success' => true,
            'source' => $result['source'],
            'cached' => $result['cached'] ?? false,
            'predicted_price_min' => $result['predicted_price_min'],
            'predicted_price_max' => $result['predicted_price_max'],
            'predicted_price_mid' => $result['predicted_price_mid'],
            'confidence_score' => $result['confidence_score'],
            'investment_score' => $result['investment_score'],
            'risk_level' => $result['risk_level'],
            'valuation_rationale' => $result['valuation_rationale'] ?? null,
        ]);
    }

    /**
     * POST /api/ai/price-prediction/property/{id}
     * Auto-fill inputs from an existing property record.
     */
    public function predictForProperty(int $propertyId): JsonResponse
    {
        $property = \App\Models\Property::with(['details', 'location', 'price'])->findOrFail($propertyId);

        $input = [
            'area' => optional($property->details)->area,
            'city' => optional($property->location)->city,
            'district' => optional($property->location)->district,
            'bedrooms' => optional($property->details)->bedrooms ?? 0,
            'floor' => optional($property->details)->floor ?? 0,
            'finishing' => optional($property->details)->finishing ?? 'standard',
            'building_age' => optional($property->details)->building_age ?? 0,
            'listing_type' => $property->listing_type,
        ];

        $result = $this->service->predict($input);

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 503);
        }

        return response()->json([
            'success' => true,
            'property_id' => $propertyId,
            'current_price' => optional($property->price)->price,
            'source' => $result['source'],
            'predicted_price_min' => $result['predicted_price_min'],
            'predicted_price_max' => $result['predicted_price_max'],
            'predicted_price_mid' => $result['predicted_price_mid'],
            'confidence_score' => $result['confidence_score'],
            'investment_score' => $result['investment_score'],
            'risk_level' => $result['risk_level'],
            'valuation_rationale' => $result['valuation_rationale'] ?? null,
        ]);
    }
}

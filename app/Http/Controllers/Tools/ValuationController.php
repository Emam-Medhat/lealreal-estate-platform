<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\ValuationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ValuationController extends Controller
{
    public function index(): View
    {
        return view('tools.valuation');
    }

    public function evaluate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address' => 'required|string|min:3|max:255',
            'area_sqm' => 'nullable|numeric|min:10|max:10000',
            'bedrooms' => 'nullable|integer|min:0|max:10',
            'bathrooms' => 'nullable|integer|min:0|max:10',
            'property_type' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:100',
        ]);

        $basePricePerSqm = 1200;
        $cityMultiplier = match (strtolower($validated['city'] ?? '')) {
            'dubai' => 2.0,
            'riyadh' => 1.6,
            'cairo' => 1.2,
            default => 1.0,
        };
        $typeMultiplier = match (strtolower($validated['property_type'] ?? '')) {
            'apartment' => 1.0,
            'villa' => 1.5,
            'townhouse' => 1.2,
            default => 1.0,
        };

        $area = (float) ($validated['area_sqm'] ?? 120);
        $roomsBoost = (($validated['bedrooms'] ?? 2) * 0.03) + (($validated['bathrooms'] ?? 2) * 0.02);
        $seasonality = 1.0;

        $estimate = round($basePricePerSqm * $cityMultiplier * $typeMultiplier * $seasonality * $area * (1 + $roomsBoost), 0);
        $pricePerSqm = round($estimate / max($area, 1), 2);

        $trend = [
            ['month' => '-6m', 'price' => round($estimate * 0.93)],
            ['month' => '-3m', 'price' => round($estimate * 0.97)],
            ['month' => 'now', 'price' => $estimate],
            ['month' => '+3m', 'price' => round($estimate * 1.02)],
            ['month' => '+6m', 'price' => round($estimate * 1.05)],
        ];

        $requestModel = ValuationRequest::create([
            'address' => $validated['address'],
            'city' => $validated['city'] ?? null,
            'inputs' => [
                'area_sqm' => $validated['area_sqm'] ?? null,
                'bedrooms' => $validated['bedrooms'] ?? null,
                'bathrooms' => $validated['bathrooms'] ?? null,
                'property_type' => $validated['property_type'] ?? null,
            ],
            'result_summary' => [
                'estimate' => $estimate,
                'price_per_sqm' => $pricePerSqm,
                'trend' => $trend,
                'confidence' => 0.72,
            ],
            'status' => 'completed',
        ]);

        return response()->json([
            'success' => true,
            'id' => $requestModel->id,
            'estimate' => $estimate,
            'price_per_sqm' => $pricePerSqm,
            'trend' => $trend,
            'confidence' => 0.72,
        ]);
    }
}

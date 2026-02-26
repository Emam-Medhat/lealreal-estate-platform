<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\PredictionResult;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PredictivePricingController extends Controller
{
    public function index(): View
    {
        return view('tools.predictive_pricing');
    }

    public function predict(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city' => 'required|string|max:100',
            'current_price' => 'required|numeric|min:10000|max:100000000',
            'property_type' => 'nullable|string|max:50',
        ]);

        $baseGrowth = match (strtolower($validated['city'])) {
            'dubai' => 0.08,
            'riyadh' => 0.06,
            'cairo' => 0.05,
            default => 0.04,
        };
        $typeAdj = match (strtolower($validated['property_type'] ?? '')) {
            'apartment' => -0.005,
            'villa' => 0.01,
            'townhouse' => 0.003,
            default => 0.0,
        };

        $growth = $baseGrowth + $typeAdj;

        $oneYear = round($validated['current_price'] * (1 + $growth), 0);
        $threeYear = round($validated['current_price'] * pow(1 + $growth, 3), 0);
        $fiveYear = round($validated['current_price'] * pow(1 + $growth, 5), 0);

        $confidence = 0.68;
        $factors = [
            'city_growth' => $baseGrowth,
            'type_adjustment' => $typeAdj,
            'demand_signals' => 0.02,
            'infrastructure' => 0.015,
            'macro' => -0.005,
        ];

        $result = PredictionResult::create([
            'horizon' => '1y_3y_5y',
            'forecast_value' => $fiveYear,
            'confidence' => $confidence,
            'factors_json' => $factors,
            'property_id' => null,
        ]);

        return response()->json([
            'success' => true,
            'id' => $result->id,
            'one_year' => $oneYear,
            'three_year' => $threeYear,
            'five_year' => $fiveYear,
            'confidence' => $confidence,
            'factors' => $factors,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Intelligence;

use App\Http\Controllers\Controller;
use App\Jobs\ComputeInvestmentScores;
use App\Jobs\PredictPropertyPricing;
use App\Jobs\RunFraudDetection;
use App\Jobs\ComputeRiskTrust;
use App\Models\Property;
use Illuminate\Http\Request;

/**
 * Example controller integration to trigger intelligence pipelines.
 */
class PropertyIntelligenceController extends Controller
{
    public function run(Request $request, int $id)
    {
        $property = Property::findOrFail($id);

        ComputeInvestmentScores::dispatch([$property->id], [
            $property->id => [
                'historical_trend' => (float) $request->input('historical_trend', 3.5),
                'rental_avg' => (float) $request->input('rental_avg', 3000),
                'listing_velocity' => (float) $request->input('listing_velocity', 45),
                'search_frequency' => (int) $request->input('search_frequency', 50),
                'neighborhood_growth' => (float) $request->input('neighborhood_growth', 2.0),
            ],
        ]);

        PredictPropertyPricing::dispatch([$property->id]);

        RunFraudDetection::dispatch([$property->id], [
            $property->id => [
                'pricing_stats' => [
                    'mean' => $property->price,
                    'std' => max(1, $property->price * 0.1),
                ],
            ],
        ]);

        ComputeRiskTrust::dispatch([$property->id]);

        return response()->json([
            'message' => 'Intelligence pipeline queued',
            'property_id' => $property->id,
        ]);
    }
}

<?php

namespace Tests\Feature\AI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\User;
use App\Services\AI\PricePredictionService;

class PricePredictionTest extends TestCase
{
    use RefreshDatabase;

    public function test_calls_microservice_successfully()
    {
        $service = app(PricePredictionService::class);

        Http::fake([
            'http://localhost:8001/predict' => Http::response([
                'predicted_price_mid' => 5000000,
                'confidence_score' => 0.9,
                'risk_level' => 'low',
                'valuation_rationale' => 'Market match'
            ], 200)
        ]);

        $result = $service->predict([
            'area' => 100,
            'city' => 'Cairo',
            'bedrooms' => 3
        ]);

        $this->assertEquals(5000000, $result['price']);
        $this->assertEquals('low', $result['risk']);
    }

    public function test_falls_back_to_ai_on_microservice_failure()
    {
        // Configure to allow fallback
        config(['ai.price_prediction.fallback_to_ai' => true]);

        $service = app(PricePredictionService::class);

        // Mock microservice failure and OpenAI success
        Http::fake([
            'http://localhost:8001/predict' => Http::response([], 500),
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'estimated_price' => 4500000,
                                'confidence' => 0.8,
                                'risk' => 'medium',
                                'reason' => 'AI Fallback'
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $service->predict([
            'area' => 100,
            'city' => 'Cairo',
            'bedrooms' => 3
        ]);

        $this->assertEquals(4500000, $result['price']);
        $this->assertEquals('medium', $result['risk']);
    }
}

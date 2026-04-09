<?php

namespace Tests\Feature\AI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\User;

class SmartSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_smart_search_requires_authentication()
    {
        $response = $this->postJson('/api/ai/smart-search', [
            'text' => 'apartments in Cairo'
        ]);

        $response->assertStatus(401);
    }

    public function test_smart_search_returns_structured_results()
    {
        $user = User::factory()->create();

        // Mock OpenAI Response
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'property_type' => 'apartment',
                                'city' => 'Cairo',
                                'listing_type' => 'sale'
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/ai/smart-search', [
                'text' => 'شقة للبيع في القاهرة'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'raw',
                'data'
            ])
            ->assertJson([
                'success' => true
            ]);
    }
}

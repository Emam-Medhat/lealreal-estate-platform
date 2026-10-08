<?php

namespace Tests\Feature;

use App\Http\Controllers\AiImageRecognitionController;
use App\Models\Agent;
use App\Models\AiImageAnalysis;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AiImageRecognitionBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_query_count_and_calculation_accuracy()
    {
        // 1. Setup required dependencies
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'phone' => '1234567890',
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'Agent Smith',
            'email' => 'agent@example.com',
            'phone' => '0987654321',
            'hire_date' => now(),
            'license_number' => 'LIC123456',
        ]);

        $property = Property::create([
            'area' => 120,
            'property_code' => 'PROP-001',
            'title' => 'Test Villa',
            'description' => 'Beautiful villa in test area',
            'address' => '123 Main St',
            'city' => 'Riyadh',
            'state' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'area_unit' => 'sqm',
            'property_type' => 'villa',
            'listing_type' => 'sale',
            'agent_id' => $agent->id,
            'price' => 500000,
        ]);

        // 2. Create test AiImageAnalysis records
        // 3 completed, 1 pending, 1 failed
        AiImageAnalysis::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'image_path' => 'images/1.jpg',
            'image_hash' => hash('sha256', 'img1'),
            'analysis_type' => 'room_detection',
            'quality_score' => 8.50,
            'aesthetic_score' => 8.00,
            'ai_model_version' => 'v1.0',
            'processing_time' => 1.2,
            'confidence_level' => 0.90, // High confidence
            'status' => 'completed',
        ]);

        AiImageAnalysis::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'image_path' => 'images/2.jpg',
            'image_hash' => hash('sha256', 'img2'),
            'analysis_type' => 'object_recognition',
            'quality_score' => 7.50,
            'aesthetic_score' => 7.00,
            'ai_model_version' => 'v1.0',
            'processing_time' => 1.5,
            'confidence_level' => 0.80, // High confidence
            'status' => 'completed',
        ]);

        AiImageAnalysis::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'image_path' => 'images/3.jpg',
            'image_hash' => hash('sha256', 'img3'),
            'analysis_type' => 'quality_assessment',
            'quality_score' => 6.00,
            'aesthetic_score' => 6.00,
            'ai_model_version' => 'v1.0',
            'processing_time' => 0.8,
            'confidence_level' => 0.70, // Medium confidence
            'status' => 'completed',
        ]);

        AiImageAnalysis::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'image_path' => 'images/4.jpg',
            'image_hash' => hash('sha256', 'img4'),
            'analysis_type' => 'room_detection',
            'quality_score' => 5.00,
            'aesthetic_score' => 5.00,
            'ai_model_version' => 'v1.0',
            'processing_time' => 0.0,
            'confidence_level' => 0.50,
            'status' => 'pending',
        ]);

        AiImageAnalysis::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'image_path' => 'images/5.jpg',
            'image_hash' => hash('sha256', 'img5'),
            'analysis_type' => 'comprehensive',
            'quality_score' => 4.00,
            'aesthetic_score' => 4.00,
            'ai_model_version' => 'v1.0',
            'processing_time' => 0.5,
            'confidence_level' => 0.40,
            'status' => 'failed',
        ]);

        // 3. Invoke controller dashboard and count DB queries
        DB::flushQueryLog();
        DB::enableQueryLog();

        $controller = new AiImageRecognitionController();
        $response = $controller->dashboard();

        $queryLog = DB::getQueryLog();
        DB::disableQueryLog();

        // 4. Extract response data and verify calculation accuracy
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
        } else {
            $data = $response->getData();
        }
        $stats = $data['stats'];
        $performanceMetrics = $data['performanceMetrics'];

        $this->assertEquals(5, $stats['total_analyses']);
        $this->assertEquals(3, $stats['completed_analyses']);
        $this->assertEquals(1, $stats['pending_analyses']);
        $this->assertEquals(0.80, $stats['average_confidence']); // (0.90 + 0.80 + 0.70) / 3 = 0.80
        $this->assertEquals(3, $stats['total_images_processed']);
        $this->assertEquals(60.0, $stats['recognition_success_rate']); // (3 / 5) * 100 = 60.0%

        $this->assertEquals(5, $performanceMetrics['total_analyses']);
        $this->assertEquals(2, $performanceMetrics['high_confidence_analyses']); // 0.90, 0.80
        $this->assertEquals(0.66, $performanceMetrics['average_confidence']); // (0.9 + 0.8 + 0.7 + 0.5 + 0.4) / 5 = 0.66

        // 5. Assert query count optimization
        // Expected queries:
        // 1. Consolidated selectRaw query for stats & performance metrics
        // 2-4. Recent analyses query + eager loaded relations (properties, users)
        // 5. Trends query (groupBy date)
        $this->assertLessThanOrEqual(5, count($queryLog), 'Dashboard executed too many database queries.');
    }
}

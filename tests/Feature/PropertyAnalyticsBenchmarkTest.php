<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use App\Services\PropertyAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PropertyAnalyticsBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_performance_metrics_runs_single_query()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
        ]);

        $property = Property::create([
            'title' => 'Test Property',
            'description' => 'Test Description',
            'address' => '123 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'Test Country',
            'area' => 100,
            'area_unit' => 'sqft',
            'price' => 500000,
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'property_code' => 'PROP-12345',
            'agent_id' => $user->id,
            'views_count' => 100,
            'favorites_count' => 20,
            'inquiries_count' => 5,
        ]);

        $service = new PropertyAnalyticsService;

        DB::flushQueryLog();
        DB::enableQueryLog();

        $metrics = $service->getPerformanceMetrics($property->id);

        $queries = DB::getQueryLog();

        $this->assertCount(1, $queries, 'getPerformanceMetrics should run exactly 1 query.');
        $this->assertEquals([
            'views' => 100,
            'favorites' => 20,
            'inquiries' => 5,
        ], $metrics);
    }

    public function test_track_view_increments_views_count()
    {
        $user = User::factory()->create([
            'username' => 'testuser2',
            'first_name' => 'Test2',
            'last_name' => 'User2',
            'email' => 'test2@example.com',
            'phone' => '1234567891',
        ]);

        $property = Property::create([
            'title' => 'Test Property 2',
            'description' => 'Test Description 2',
            'address' => '124 Test St',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'Test Country',
            'area' => 120,
            'area_unit' => 'sqft',
            'price' => 600000,
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'property_code' => 'PROP-12346',
            'agent_id' => $user->id,
            'views_count' => 10,
        ]);

        $service = new PropertyAnalyticsService;
        $service->trackView($property->id);

        $property->refresh();
        $this->assertEquals(11, $property->views_count);
    }
}

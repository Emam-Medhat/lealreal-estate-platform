<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Property;
use App\Models\User;
use App\Services\PropertySearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class NearbySearchOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected $agent;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure we have an agent for properties
        $this->agent = User::factory()->create();
    }

    public function test_nearby_search_includes_bounding_box_filter()
    {
        $service = new PropertySearchService();
        $lat = 24.7136; // Riyadh
        $lng = 46.6753;
        $radius = 10;

        // Start listening to database queries
        DB::enableQueryLog();

        $service->nearbySearch($lat, $lng, $radius);

        $queryLog = DB::getQueryLog();
        $this->assertNotEmpty($queryLog);

        $sql = $queryLog[0]['query'];

        // Check for latitude and longitude bounding box filters
        $this->assertStringContainsString('"latitude" between ? and ?', $sql);
        $this->assertStringContainsString('"longitude" between ? and ?', $sql);

        // Check that Haversine formula is still there
        $this->assertStringContainsString('6371 * acos', $sql);

        DB::disableQueryLog();
    }

    public function test_nearby_search_performance_with_many_properties()
    {
        // Seed some properties
        $baseLat = 24.7136;
        $baseLng = 46.6753;

        // Create 100 properties: 10 nearby, 90 far away
        for ($i = 0; $i < 100; $i++) {
            $isNearby = $i < 10;
            $offset = $isNearby ? 0.001 * $i : 1.0 * $i;

            Property::create([
                'agent_id' => $this->agent->id,
                'property_code' => 'PROP-' . $i,
                'title' => 'Property ' . $i,
                'description' => 'Description ' . $i,
                'property_type' => 'apartment',
                'listing_type' => 'sale',
                'price' => 1000000,
                'area' => 150,
                'address' => 'Address ' . $i,
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'latitude' => $baseLat + $offset,
                'longitude' => $baseLng + $offset,
                'status' => 'active',
            ]);
        }

        $service = new PropertySearchService();

        $start = microtime(true);
        $results = $service->nearbySearch($baseLat, $baseLng, 10);
        $end = microtime(true);

        $executionTime = ($end - $start) * 1000;

        $this->assertCount(10, $results);
        $this->assertLessThan(500, $executionTime, "Nearby search should be fast");

        echo "\nExecution time for nearby search: {$executionTime} ms\n";
    }
}

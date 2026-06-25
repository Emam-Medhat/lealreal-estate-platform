<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use App\Services\PropertySearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NearbySearchBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an agent user
        $user = User::factory()->create(['role' => 'agent']);

        // Create 20 properties scattered around Riyadh manually since factory is missing
        for ($i = 0; $i < 20; $i++) {
            Property::create([
                'agent_id' => $user->id,
                'property_code' => 'PROP-' . uniqid(),
                'title' => 'Property ' . $i,
                'description' => 'Description ' . $i,
                'property_type' => 'apartment',
                'listing_type' => 'sale',
                'price' => 100000 + ($i * 1000),
                'area' => 100 + $i,
                'address' => 'Address ' . $i,
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'status' => 'active',
                'latitude' => 24.7136 + (rand(-100, 100) / 1000),
                'longitude' => 46.6753 + (rand(-100, 100) / 1000),
            ]);
        }
    }

    public function test_nearby_search_returns_correct_results()
    {
        $service = new PropertySearchService();
        $lat = 24.7136;
        $lng = 46.6753;
        $radius = 50; // Use large radius to ensure results

        $results = $service->nearbySearch($lat, $lng, $radius);

        $this->assertNotEmpty($results);
        foreach ($results as $property) {
            $this->assertLessThan($radius, $property->distance);
        }
    }

    public function test_nearby_search_performance()
    {
        $service = new PropertySearchService();
        $lat = 24.7136;
        $lng = 46.6753;
        $radius = 10;

        // Enable query logging
        DB::enableQueryLog();

        $start = microtime(true);
        $results = $service->nearbySearch($lat, $lng, $radius);
        $end = microtime(true);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        echo "\nNearby search took " . (($end - $start) * 1000) . "ms\n";
        echo "Number of queries: " . count($queries) . "\n";

        if (count($queries) > 0) {
            $sql = $queries[0]['query'];
            echo "Last query: " . $sql . "\n";
            // Check if bounding box was used
            $this->assertStringContainsString('latitude', $sql);
            $this->assertStringContainsString('longitude', $sql);
            $this->assertStringContainsString('between', strtolower($sql));
        }

        $this->assertNotNull($results);
    }
}

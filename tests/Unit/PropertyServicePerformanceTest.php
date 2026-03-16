<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyServicePerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected $propertyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = new PropertyService();
    }

    public function test_get_property_statistics_performance_and_accuracy()
    {
        // Create properties with different statuses
        // Note: PropertyService uses 'available' in queries but migration has 'active', 'sold', 'rented'
        // Let's check what PropertyService actually uses.

        $this->createProperty(['status' => 'available', 'price' => 100000]);
        $this->createProperty(['status' => 'available', 'price' => 150000]);
        $this->createProperty(['status' => 'sold', 'price' => 200000]);
        $this->createProperty(['status' => 'rented', 'price' => 50000]);

        DB::enableQueryLog();
        $initialQueryCount = count(DB::getQueryLog());

        $stats = $this->propertyService->getPropertyStatistics();

        $queries = DB::getQueryLog();
        $queryCount = count($queries) - $initialQueryCount;

        // Assertions for accuracy
        $this->assertEquals(4, $stats['total_properties'], 'Total properties count mismatch');
        $this->assertEquals(2, $stats['available_properties'], 'Available properties count mismatch');
        $this->assertEquals(1, $stats['sold_properties'], 'Sold properties count mismatch');
        $this->assertEquals(1, $stats['rented_properties'], 'Rented properties count mismatch');
        $this->assertEquals(500000, $stats['total_value'], 'Total value mismatch');
        $this->assertEquals(125000, $stats['average_price'], 'Average price mismatch');

        // Assertions for performance
        $this->assertEquals(1, $queryCount, 'Expected exactly one query in optimized implementation');
    }

    protected function createProperty(array $data)
    {
        return Property::withoutEvents(function() use ($data) {
            return Property::create(array_merge([
                'title' => 'Test Property',
                'description' => 'Test Description',
                'listing_type' => 'sale',
                'property_type' => 'apartment',
                'price' => 100000,
                'status' => 'available',
                'agent_id' => 1,
                'property_code' => 'PROP-' . Str::random(8),
                'slug' => 'test-property-' . Str::random(10),
                'area' => 100,
                'address' => 'Test Address',
                'city' => 'Test City',
                'country' => 'Test Country',
            ], $data));
        });
    }
}

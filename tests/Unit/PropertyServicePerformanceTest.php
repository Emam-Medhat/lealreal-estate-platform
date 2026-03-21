<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Property;
use App\Models\Agent;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PropertyServicePerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected PropertyService $propertyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = new PropertyService();
    }

    public function test_get_property_statistics_returns_correct_data()
    {
        User::withoutEvents(function () {
            // Create sample data
            // Statuses: ['draft', 'active', 'inactive', 'sold', 'rented']
            Property::factory()->count(5)->create(['status' => 'active', 'price' => 100000]);
            Property::factory()->count(3)->create(['status' => 'sold', 'price' => 150000]);
            Property::factory()->count(2)->create(['status' => 'rented', 'price' => 50000]);
            Property::factory()->count(1)->create(['status' => 'draft', 'price' => 80000]);
        });

        $stats = $this->propertyService->getPropertyStatistics();

        $this->assertEquals(11, $stats['total_properties']);
        $this->assertEquals(5, $stats['available_properties'], 'Available properties count is wrong');
        $this->assertEquals(3, $stats['sold_properties'], 'Sold properties count is wrong');
        $this->assertEquals(2, $stats['rented_properties'], 'Rented properties count is wrong');

        $expectedTotalValue = (5 * 100000) + (3 * 150000) + (2 * 50000) + (1 * 80000);
        $this->assertEquals($expectedTotalValue, $stats['total_value']);
        $this->assertEquals($expectedTotalValue/11, $stats['average_price']);
    }

    public function test_get_property_statistics_query_count()
    {
        User::withoutEvents(function () {
            Property::factory()->count(5)->create(['status' => 'active', 'price' => 100000]);
        });

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->propertyService->getPropertyStatistics();

        $queryCount = count(DB::getQueryLog());

        // After optimization it should be 1
        $this->assertEquals(1, $queryCount, 'Optimization failed: expected 1 query, got ' . $queryCount);
    }
}

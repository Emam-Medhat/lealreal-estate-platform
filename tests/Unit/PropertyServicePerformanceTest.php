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

    public function test_get_property_statistics_returns_correct_counts_and_is_optimized()
    {
        // Setup data
        // status enum: ['draft', 'active', 'inactive', 'sold', 'rented']
        // Wrap User creation in withoutEvents to bypass UserAnalytics missing model error
        User::withoutEvents(function () {
            Property::factory()->count(5)->create(['status' => 'active', 'price' => 100000]);
            Property::factory()->count(3)->create(['status' => 'sold', 'price' => 200000]);
            Property::factory()->count(2)->create(['status' => 'rented', 'price' => 50000]);
            Property::factory()->count(1)->create(['status' => 'draft']);
        });

        // Record query count
        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $this->propertyService->getPropertyStatistics();

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Verify counts
        $this->assertEquals(11, $stats['total_properties'], 'Total properties count mismatch');
        $this->assertEquals(5, $stats['available_properties'], 'Available properties count mismatch (likely due to available vs active mismatch)');
        $this->assertEquals(3, $stats['sold_properties'], 'Sold properties count mismatch (likely due to stacked filters bug)');
        $this->assertEquals(2, $stats['rented_properties'], 'Rented properties count mismatch (likely due to stacked filters bug)');

        // Performance expectation: should be 1 query after optimization
        // Currently it's about 6 queries
        $this->assertLessThanOrEqual(1, $queryCount, "Expected 1 query, but got {$queryCount}");
    }

    public function test_get_available_properties_uses_correct_status()
    {
        User::withoutEvents(function () {
            Property::factory()->count(3)->create(['status' => 'active']);
            Property::factory()->count(2)->create(['status' => 'sold']);
        });

        $available = $this->propertyService->getAvailableProperties();

        // If it uses 'available' instead of 'active', it will return 0
        $this->assertCount(3, $available, 'Should find 3 active properties');
    }
}

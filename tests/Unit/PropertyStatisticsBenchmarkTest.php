<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PropertyStatisticsBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected $propertyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = new PropertyService();
    }

    public function test_get_property_statistics_performance()
    {
        // Create 50 properties with various statuses
        Property::factory()->count(20)->create(['status' => 'active']);
        Property::factory()->count(15)->create(['status' => 'sold']);
        Property::factory()->count(10)->create(['status' => 'rented']);
        Property::factory()->count(5)->create(['status' => 'inactive']);

        DB::enableQueryLog();

        $startTime = microtime(true);
        $stats = $this->propertyService->getPropertyStatistics();
        $endTime = microtime(true);

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        echo "\nQueries: " . $queryCount . "\n";
        echo "Time: " . ($endTime - $startTime) . "s\n";

        $this->assertEquals(50, $stats['total_properties']);
        $this->assertEquals(20, $stats['available_properties']);
        $this->assertEquals(15, $stats['sold_properties']);
        $this->assertEquals(10, $stats['rented_properties']);
    }
}

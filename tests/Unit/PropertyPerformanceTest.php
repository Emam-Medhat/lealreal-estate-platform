<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Property;
use App\Models\User;
use App\Models\Agent;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PropertyPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected $propertyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = new PropertyService();
    }

    public function test_get_property_statistics_accuracy_and_performance()
    {
        // Setup data without events to avoid missing UserAnalytics issue
        $user = User::withoutEvents(function () {
            return User::factory()->create();
        });

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'Test Agent',
            'email' => 'agent@test.com',
            'phone' => '123456789',
            'hire_date' => now(),
            'license_number' => 'LIC123'
        ]);

        Property::create([
            'agent_id' => $agent->user_id,
            'property_code' => 'P1',
            'title' => 'Active Prop',
            'description' => 'Desc',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'status' => 'active',
            'price' => 100000,
            'area' => 100,
            'address' => 'Addr',
            'city' => 'City',
            'country' => 'Country'
        ]);

        Property::create([
            'agent_id' => $agent->user_id,
            'property_code' => 'P2',
            'title' => 'Sold Prop',
            'description' => 'Desc',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'status' => 'sold',
            'price' => 200000,
            'area' => 100,
            'address' => 'Addr',
            'city' => 'City',
            'country' => 'Country'
        ]);

        Property::create([
            'agent_id' => $agent->user_id,
            'property_code' => 'P3',
            'title' => 'Rented Prop',
            'description' => 'Desc',
            'property_type' => 'apartment',
            'listing_type' => 'rent',
            'status' => 'rented',
            'price' => 300000,
            'area' => 100,
            'address' => 'Addr',
            'city' => 'City',
            'country' => 'Country'
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $this->propertyService->getPropertyStatistics();

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        echo "\n[AFTER OPTIMIZATION]\n";
        echo "Query Count: " . $queryCount . "\n";
        echo "Stats: " . json_encode($stats, JSON_PRETTY_PRINT) . "\n";

        $this->assertEquals(1, $queryCount, 'Database queries should be reduced to 1');
        $this->assertEquals(3, $stats['total_properties']);
        $this->assertEquals(1, $stats['available_properties']);
        $this->assertEquals(1, $stats['sold_properties']);
        $this->assertEquals(1, $stats['rented_properties']);
        $this->assertEquals(600000, $stats['total_value']);
        $this->assertEquals(200000, $stats['average_price']);
    }
}

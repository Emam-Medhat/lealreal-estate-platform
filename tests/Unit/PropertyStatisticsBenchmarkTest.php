<?php

namespace Tests\Unit;

use App\Models\Agent;
use App\Models\Property;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PropertyStatisticsBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected $propertyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = new PropertyService;
    }

    public function test_get_property_statistics_performance_and_accuracy()
    {
        // 1. Setup data
        $user = User::factory()->create();
        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'Test Agent',
            'email' => 'agent@test.com',
            'phone' => '123456789',
            'license_number' => 'LIC123',
            'is_active' => true,
            'is_verified' => true,
            'hire_date' => now(),
        ]);

        // Create properties with different statuses
        // 3 Active (the service calls them 'available' but schema says 'active')
        for ($i = 0; $i < 3; $i++) {
            Property::create([
                'agent_id' => $agent->id,
                'property_code' => 'PROP-ACT-'.$i,
                'title' => 'Active Property '.$i,
                'description' => 'Description',
                'property_type' => 'apartment',
                'listing_type' => 'sale',
                'price' => 100000,
                'area' => 100,
                'address' => 'Address',
                'city' => 'City',
                'country' => 'Country',
                'status' => 'active',
            ]);
        }

        // 2 Sold
        for ($i = 0; $i < 2; $i++) {
            Property::create([
                'agent_id' => $agent->id,
                'property_code' => 'PROP-SLD-'.$i,
                'title' => 'Sold Property '.$i,
                'description' => 'Description',
                'property_type' => 'apartment',
                'listing_type' => 'sale',
                'price' => 200000,
                'area' => 100,
                'address' => 'Address',
                'city' => 'City',
                'country' => 'Country',
                'status' => 'sold',
            ]);
        }

        // 1 Rented
        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-RNT-1',
            'title' => 'Rented Property',
            'description' => 'Description',
            'property_type' => 'apartment',
            'listing_type' => 'rent',
            'price' => 50000,
            'area' => 100,
            'address' => 'Address',
            'city' => 'City',
            'country' => 'Country',
            'status' => 'rented',
        ]);

        // Total: 6 properties. Total value: (3*100k) + (2*200k) + (1*50k) = 750k. Avg price: 750k / 6 = 125k.

        // 2. Measure Query Count
        DB::enableQueryLog();
        $stats = $this->propertyService->getPropertyStatistics();
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // 3. Assertions

        // The current implementation is EXPECTED TO BE BROKEN
        // It uses available instead of active, and it stacks filters.

        // Expected if fixed:
        // 'total_properties' => 6
        // 'available_properties' => 3
        // 'sold_properties' => 2
        // 'rented_properties' => 1
        // 'total_value' => 750000
        // 'average_price' => 125000

        // Current implementation will likely return:
        // 'total_properties' => 6
        // 'available_properties' => 0 (because it searches for 'available' status)
        // 'sold_properties' => 0 (because it adds 'where status=sold' to the query that already has 'count' called,
        //                        but actually Laravel's count() doesn't modify the builder permanently IF it's called properly,
        //                        HOWEVER, PropertyService.php does:
        //                        $totalProperties = $query->count();
        //                        $availableProperties = $query->where('status', 'available')->count();
        //                        $soldProperties = $query->where('status', 'sold')->count();
        //                        This STACKS filters. So $soldProperties will be WHERE status='available' AND status='sold'.

        $this->assertEquals(6, $stats['total_properties'], 'Total properties should be 6');
        $this->assertEquals(3, $stats['available_properties'], 'Available (active) properties should be 3');
        $this->assertEquals(2, $stats['sold_properties'], 'Sold properties should be 2');
        $this->assertEquals(1, $stats['rented_properties'], 'Rented properties should be 1');
        $this->assertEquals(750000, $stats['total_value'], 'Total value should be 750,000');
        $this->assertEquals(125000, $stats['average_price'], 'Average price should be 125,000');

        $this->assertEquals(1, $queryCount, 'Should execute exactly 1 query in optimized implementation');
    }
}

<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Property;
use App\Models\User;
use App\Models\Agent;
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

    public function test_get_property_statistics_accuracy_and_query_count()
    {
        // Create an agent first (required for property)
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'username' => 'johndoe',
            'phone' => '1234567890',
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'hire_date' => now(),
            'license_number' => 'LIC123',
            'is_active' => true,
            'is_verified' => true,
        ]);

        // Create properties with different statuses
        // Note: The service currently uses 'available', but schema uses 'active'
        // We will test with 'active' and see it fail (count 0) for available_properties

        Property::create([
            'agent_id' => $user->id, // Property model uses agent_id pointing to users table in migration
            'property_code' => 'P1',
            'title' => 'Property 1',
            'description' => 'Description 1',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'price' => 100000,
            'area' => 100,
            'status' => 'active',
            'address' => 'Address 1',
            'city' => 'City 1',
            'country' => 'Country 1',
        ]);

        Property::create([
            'agent_id' => $user->id,
            'property_code' => 'P2',
            'title' => 'Property 2',
            'description' => 'Description 2',
            'property_type' => 'villa',
            'listing_type' => 'sale',
            'price' => 200000,
            'area' => 200,
            'status' => 'sold',
            'address' => 'Address 2',
            'city' => 'City 1',
            'country' => 'Country 1',
        ]);

        Property::create([
            'agent_id' => $user->id,
            'property_code' => 'P3',
            'title' => 'Property 3',
            'description' => 'Description 3',
            'property_type' => 'apartment',
            'listing_type' => 'rent',
            'price' => 300000,
            'area' => 150,
            'status' => 'rented',
            'address' => 'Address 3',
            'city' => 'City 2',
            'country' => 'Country 1',
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $this->propertyService->getPropertyStatistics();

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Currently it should take 6 queries:
        // 1. total count
        // 2. available count
        // 3. sold count
        // 4. rented count
        // 5. sum price
        // 6. avg price

        $this->assertEquals(3, $stats['total_properties']);
        // This will likely fail or return 0 because service looks for 'available' but we have 'active'
        // $this->assertEquals(1, $stats['available_properties']);
        $this->assertEquals(1, $stats['available_properties']);
        $this->assertEquals(1, $stats['sold_properties']);
        $this->assertEquals(1, $stats['rented_properties']);
        $this->assertEquals(600000, $stats['total_value']);
        $this->assertEquals(200000, $stats['average_price']);

        echo "\nOptimized Query Count: " . $queryCount . "\n";
        $this->assertLessThanOrEqual(1, $queryCount);
    }
}

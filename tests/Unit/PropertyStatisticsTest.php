<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Property;
use App\Models\Agent;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PropertyStatisticsTest extends TestCase
{
    use RefreshDatabase;

    protected PropertyService $propertyService;

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
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'license_number' => 'LIC123456',
            'is_active' => true,
            'is_verified' => true,
            'hire_date' => now(), // Required field
        ]);

        // Create properties with different statuses
        Property::create([
            'agent_id' => $user->id,
            'property_code' => 'PROP1',
            'title' => 'Active Property 1',
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

        Property::create([
            'agent_id' => $user->id,
            'property_code' => 'PROP2',
            'title' => 'Active Property 2',
            'description' => 'Description',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'price' => 200000,
            'area' => 120,
            'address' => 'Address',
            'city' => 'City',
            'country' => 'Country',
            'status' => 'active',
        ]);

        Property::create([
            'agent_id' => $user->id,
            'property_code' => 'PROP3',
            'title' => 'Sold Property',
            'description' => 'Description',
            'property_type' => 'house',
            'listing_type' => 'sale',
            'price' => 300000,
            'area' => 150,
            'address' => 'Address',
            'city' => 'City',
            'country' => 'Country',
            'status' => 'sold',
        ]);

        Property::create([
            'agent_id' => $user->id,
            'property_code' => 'PROP4',
            'title' => 'Rented Property',
            'description' => 'Description',
            'property_type' => 'villa',
            'listing_type' => 'rent',
            'price' => 5000,
            'area' => 200,
            'address' => 'Address',
            'city' => 'City',
            'country' => 'Country',
            'status' => 'rented',
        ]);

        // Record queries
        DB::enableQueryLog();

        $stats = $this->propertyService->getPropertyStatistics();

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertEquals(4, $stats['total_properties'], 'Total properties count mismatch');
        $this->assertEquals(2, $stats['available_properties'], 'Available properties count mismatch');
        $this->assertEquals(1, $stats['sold_properties'], 'Sold properties count mismatch');
        $this->assertEquals(1, $stats['rented_properties'], 'Rented properties count mismatch');
        $this->assertEquals(605000, $stats['total_value'], 'Total value mismatch');
        $this->assertEquals(151250, $stats['average_price'], 'Average price mismatch');

        $this->assertLessThanOrEqual(1, $queryCount, 'Query count is not optimized');
    }
}

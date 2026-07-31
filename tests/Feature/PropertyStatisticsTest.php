<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PropertyStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_property_statistics_executes_single_query_and_calculates_correctly(): void
    {
        // 1. Create a user (agent) with required NOT NULL fields
        $agent = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe_agent',
            'phone' => '+1234567890',
            'email' => 'agent@example.com',
            'password' => bcrypt('password'),
            'uuid' => 'agent-uuid-12345',
            'user_type' => 'agent',
            'account_status' => 'active',
        ]);

        // 2. Create sample properties satisfying NOT NULL constraints
        // 2 active properties
        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-001',
            'title' => 'Luxury Apartment',
            'description' => 'A beautiful luxury apartment',
            'property_type' => 'apartment',
            'listing_type' => 'rent',
            'price' => 1500.00,
            'currency' => 'SAR',
            'area' => 120.00,
            'area_unit' => 'sq_m',
            'status' => 'active',
            'address' => 'Olaya St',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
        ]);

        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-002',
            'title' => 'Spacious Villa',
            'description' => 'A spacious villa with pool',
            'property_type' => 'villa',
            'listing_type' => 'sale',
            'price' => 2500000.00,
            'currency' => 'SAR',
            'area' => 450.00,
            'area_unit' => 'sq_m',
            'status' => 'active',
            'address' => 'Yasmin District',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
        ]);

        // 1 sold property
        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-003',
            'title' => 'Cozy House',
            'description' => 'Cozy family house',
            'property_type' => 'house',
            'listing_type' => 'sale',
            'price' => 1200000.00,
            'currency' => 'SAR',
            'area' => 280.00,
            'area_unit' => 'sq_m',
            'status' => 'sold',
            'address' => 'Malqa District',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
        ]);

        // 1 rented property
        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-004',
            'title' => 'Commercial Office',
            'description' => 'Prime location commercial office',
            'property_type' => 'commercial',
            'listing_type' => 'rent',
            'price' => 45000.00,
            'currency' => 'SAR',
            'area' => 150.00,
            'area_unit' => 'sq_m',
            'status' => 'rented',
            'address' => 'King Fahd Rd',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
        ]);

        // 3. Enable Query Log and instantiate the PropertyService
        DB::enableQueryLog();
        $propertyService = new PropertyService;

        // 4. Retrieve statistics
        $stats = $propertyService->getPropertyStatistics();

        // 5. Get and inspect queries executed
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // 6. Assert correct calculations
        $this->assertEquals(4, $stats['total_properties']);
        $this->assertEquals(2, $stats['available_properties']);
        $this->assertEquals(1, $stats['sold_properties']);
        $this->assertEquals(1, $stats['rented_properties']);

        $expectedTotalValue = 1500.00 + 2500000.00 + 1200000.00 + 45000.00;
        $this->assertEquals($expectedTotalValue, $stats['total_value']);

        $expectedAveragePrice = $expectedTotalValue / 4;
        $this->assertEquals($expectedAveragePrice, $stats['average_price']);

        $this->assertEquals(50.0, $stats['availability_rate']);

        // 7. Assert that exactly 1 query was executed to fetch stats
        $this->assertCount(1, $queries, 'Stats retrieval should only execute a single query.');
        $this->assertStringContainsString('select', strtolower($queries[0]['query']));
        $this->assertStringContainsString('count(*)', strtolower($queries[0]['query']));
        $this->assertStringContainsString('sum(case when status', strtolower($queries[0]['query']));
    }

    public function test_statistics_with_type_filter_maps_correctly(): void
    {
        $agent = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'username' => 'janesmith_agent',
            'phone' => '+1234567891',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'uuid' => 'agent-uuid-56789',
            'user_type' => 'agent',
            'account_status' => 'active',
        ]);

        // Create 1 villa
        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-V1',
            'title' => 'Villa Olaya',
            'description' => 'Olaya villa description',
            'property_type' => 'villa',
            'listing_type' => 'sale',
            'price' => 3000000.00,
            'currency' => 'SAR',
            'area' => 500.00,
            'area_unit' => 'sq_m',
            'status' => 'active',
            'address' => 'Olaya',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
        ]);

        // Create 1 apartment
        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-A1',
            'title' => 'Apartment Olaya',
            'description' => 'Olaya apartment description',
            'property_type' => 'apartment',
            'listing_type' => 'rent',
            'price' => 50000.00,
            'currency' => 'SAR',
            'area' => 110.00,
            'area_unit' => 'sq_m',
            'status' => 'active',
            'address' => 'Olaya',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
        ]);

        $propertyService = new PropertyService;

        // Statistics filtered by type = 'villa'
        $villaStats = $propertyService->getPropertyStatistics(['type' => 'villa']);

        $this->assertEquals(1, $villaStats['total_properties']);
        $this->assertEquals(1, $villaStats['available_properties']);
        $this->assertEquals(3000000.00, $villaStats['total_value']);
    }
}

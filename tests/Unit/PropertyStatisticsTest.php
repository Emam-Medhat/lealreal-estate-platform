<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PropertyStatisticsTest extends TestCase
{
    use RefreshDatabase;

    protected $propertyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = new PropertyService;
    }

    public function test_get_property_statistics_accuracy_and_query_count()
    {
        // Create an agent (user) - bypassing events to avoid missing UserAnalytics error
        $agent = User::withoutEvents(function () {
            return User::factory()->create(['user_type' => 'agent']);
        });

        // Create some properties
        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-1',
            'title' => 'Active Property',
            'description' => 'Description',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'price' => 100000,
            'area' => 100,
            'status' => 'active',
            'address' => 'Address',
            'city' => 'City',
            'country' => 'Country',
        ]);

        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-2',
            'title' => 'Sold Property',
            'description' => 'Description',
            'property_type' => 'house',
            'listing_type' => 'sale',
            'price' => 200000,
            'area' => 200,
            'status' => 'sold',
            'address' => 'Address',
            'city' => 'City',
            'country' => 'Country',
        ]);

        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-3',
            'title' => 'Rented Property',
            'description' => 'Description',
            'property_type' => 'villa',
            'listing_type' => 'rent',
            'price' => 5000,
            'area' => 150,
            'status' => 'rented',
            'address' => 'Address',
            'city' => 'City',
            'country' => 'Country',
        ]);

        // Start counting queries
        DB::enableQueryLog();

        $stats = $this->propertyService->getPropertyStatistics();

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Check accuracy
        $this->assertEquals(3, $stats['total_properties'], 'Total properties count is wrong');
        $this->assertEquals(1, $stats['available_properties'], 'Available properties count is wrong (should count "active" status)');
        $this->assertEquals(1, $stats['sold_properties'], 'Sold properties count is wrong');
        $this->assertEquals(1, $stats['rented_properties'], 'Rented properties count is wrong');
        $this->assertEquals(305000, $stats['total_value'], 'Total value is wrong');

        // Output query count for confirmation
        echo "\nQuery count after optimization: $queryCount\n";

        // After optimization, it should take exactly 1 query
        $this->assertEquals(1, $queryCount, 'It should take exactly 1 query after optimization');
    }
}

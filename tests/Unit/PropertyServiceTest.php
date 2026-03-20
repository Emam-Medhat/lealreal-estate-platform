<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PropertyService $propertyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = new PropertyService;
    }

    public function test_get_property_statistics_returns_correct_counts()
    {
        $agent = User::withoutEvents(fn () => User::factory()->create());

        // Create properties with different statuses
        // Note: Using 'active' as per migration, while service currently uses 'available'
        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-1',
            'title' => 'Active Property',
            'description' => 'Description',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'price' => 100000,
            'area' => 100,
            'address' => 'Address 1',
            'city' => 'City 1',
            'country' => 'Country 1',
            'status' => 'active',
        ]);

        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-2',
            'title' => 'Sold Property',
            'description' => 'Description',
            'property_type' => 'villa',
            'listing_type' => 'sale',
            'price' => 200000,
            'area' => 200,
            'address' => 'Address 2',
            'city' => 'City 2',
            'country' => 'Country 2',
            'status' => 'sold',
        ]);

        $stats = $this->propertyService->getPropertyStatistics();

        // These are expected to fail currently because:
        // 1. Service uses 'available' instead of 'active'
        // 2. Service stacks filters, so sold_properties will be 0 because it's filtered by status='available' first

        $this->assertEquals(2, $stats['total_properties'], 'Total properties count is wrong');
        $this->assertEquals(1, $stats['available_properties'], 'Available properties count is wrong (expected 1 active)');
        $this->assertEquals(1, $stats['sold_properties'], 'Sold properties count is wrong');
        $this->assertEquals(150000, $stats['average_price'], 'Average price is wrong');
    }

    public function test_get_available_properties_uses_correct_status()
    {
        $agent = User::withoutEvents(fn () => User::factory()->create());

        Property::create([
            'agent_id' => $agent->id,
            'property_code' => 'PROP-1',
            'title' => 'Active Property',
            'description' => 'Description',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'price' => 100000,
            'area' => 100,
            'address' => 'Address 1',
            'city' => 'City 1',
            'country' => 'Country 1',
            'status' => 'active',
        ]);

        $available = $this->propertyService->getAvailableProperties();

        // This is expected to fail currently because service looks for 'available' instead of 'active'
        $this->assertCount(1, $available, 'Should find 1 active property');
    }
}

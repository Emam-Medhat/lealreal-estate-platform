<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PropertyServicePerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected $propertyService;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = new PropertyService;
        $this->user = User::withoutEvents(function () {
            return User::factory()->create([
                'first_name' => 'Test',
                'last_name' => 'User',
                'username' => 'testuser'.uniqid(),
                'phone' => '123456789'.rand(10, 99),
            ]);
        });
    }

    private function createProperty(array $attributes = [])
    {
        $title = $attributes['title'] ?? 'Test Property';

        return Property::create(array_merge([
            'title' => $title,
            'description' => 'Test Description',
            'slug' => Str::slug($title).'-'.uniqid(),
            'property_code' => 'PROP-'.uniqid(),
            'area' => 100,
            'price' => 100000,
            'status' => 'active',
            'agent_id' => $this->user->id,
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'Test Country',
        ], $attributes));
    }

    /**
     * @test
     */
    public function it_has_optimized_query_count_and_correct_results()
    {
        // Create properties with different statuses
        $this->createProperty(['status' => 'active', 'price' => 100000]);
        $this->createProperty(['status' => 'active', 'price' => 200000]);
        $this->createProperty(['status' => 'sold', 'price' => 300000]);
        $this->createProperty(['status' => 'rented', 'price' => 50000]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $this->propertyService->getPropertyStatistics();

        $queryLog = DB::getQueryLog();
        DB::disableQueryLog();

        // Verify query count is optimized (reduced from 6 to 1)
        $this->assertCount(1, $queryLog, 'PropertyService::getPropertyStatistics should only execute 1 query.');

        // Verify correctness
        $this->assertEquals(4, $stats['total_properties']);
        $this->assertEquals(2, $stats['available_properties']);
        $this->assertEquals(1, $stats['sold_properties']);
        $this->assertEquals(1, $stats['rented_properties']);
        $this->assertEquals(650000, $stats['total_value']);
        $this->assertEquals(162500, $stats['average_price']);
        $this->assertEquals(50.0, $stats['availability_rate']);
    }

    /**
     * @test
     */
    public function it_filters_correctly_by_property_type()
    {
        $this->createProperty(['property_type' => 'apartment', 'status' => 'active']);
        $this->createProperty(['property_type' => 'villa', 'status' => 'active']);

        $stats = $this->propertyService->getPropertyStatistics(['type' => 'apartment']);

        $this->assertEquals(1, $stats['total_properties']);
        $this->assertEquals(1, $stats['available_properties']);
    }
}

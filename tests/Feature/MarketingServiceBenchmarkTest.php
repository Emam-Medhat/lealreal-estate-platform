<?php

namespace Tests\Feature;

use App\Models\Marketing\VirtualOpenHouseMarketing;
use App\Models\Property;
use App\Models\User;
use App\Services\MarketingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

use Tests\TestCase;

class MarketingServiceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_virtual_open_house_stats_returns_correct_data_and_executes_single_query(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'phone' => '1234567890',
        ]);

        $property = Property::create([
            'agent_id' => $user->id,
            'area' => 120.5,
            'property_code' => 'PROP-1001',
            'title' => 'Sample Property',
            'description' => 'Test Description',
            'address' => '123 Main St',
            'city' => 'Riyadh',
            'state' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'area_unit' => 'sqm',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'price' => 250000.00,
            'status' => 'active',
        ]);

        VirtualOpenHouseMarketing::create([
            'property_id' => $property->id,
            'title' => 'Active Open House',
            'description' => 'Desc',
            'platform' => 'zoom',
            'event_type' => 'live_tour',
            'status' => 'active',
            'scheduled_at' => now()->addHour(),
            'duration' => 60,
            'total_attendees' => 25,
            'total_views' => 100,
            'created_by' => $user->id,
        ]);

        VirtualOpenHouseMarketing::create([
            'property_id' => $property->id,
            'title' => 'Scheduled Open House',
            'description' => 'Desc',
            'platform' => 'teams',
            'event_type' => 'webinar',
            'status' => 'scheduled',
            'scheduled_at' => now()->addDays(2),
            'duration' => 45,
            'total_attendees' => 0,
            'total_views' => 10,
            'created_by' => $user->id,
        ]);

        VirtualOpenHouseMarketing::create([
            'property_id' => $property->id,
            'title' => 'Completed Open House',
            'description' => 'Desc',
            'platform' => 'zoom',
            'event_type' => 'qna_session',
            'status' => 'completed',
            'scheduled_at' => now()->subDays(1),
            'duration' => 30,
            'total_attendees' => 15,
            'total_views' => 50,
            'created_by' => $user->id,
        ]);

        $service = new MarketingService();
        $service->clearCache();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $service->getVirtualOpenHouseStats();

        $queryCount = count(DB::getQueryLog());

        $this->assertEquals(1, $queryCount, "Expected exactly 1 database query, but executed {$queryCount}");

        $this->assertEquals(3, $stats['total_campaigns']);
        $this->assertEquals(1, $stats['active_campaigns']);
        $this->assertEquals(1, $stats['scheduled_campaigns']);
        $this->assertEquals(1, $stats['completed_campaigns']);
        $this->assertEquals(40, $stats['total_attendees']);
        $this->assertEquals(160, $stats['total_views']);
    }
}

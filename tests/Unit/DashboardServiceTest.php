<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserFavorite;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Models\Property;
use App\Models\Agent;
use App\Models\UserComparison;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private $dashboardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = new DashboardService();
    }

    public function test_get_user_dashboard_data_structure_and_accuracy()
    {
        $user = User::withoutEvents(function () {
            return User::factory()->create([
                'first_name' => 'Test',
                'last_name' => 'User',
                'username' => 'testuser' . rand(1, 1000),
                'phone' => '123456789' . rand(1, 1000),
            ]);
        });

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone ?? '123456789',
            'hire_date' => now(),
            'license_number' => 'LIC' . rand(1, 1000),
            'status' => 'active',
        ]);

        // Create activity logs
        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'property_view',
            'created_at' => now(),
        ]);
        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'search',
            'created_at' => now(),
        ]);

        // Create favorites
        $property = Property::create([
            'agent_id' => $agent->id,
            'title' => "Test Property",
            'price' => 100000,
            'area' => 100,
            'property_code' => "PROP-" . rand(1, 1000),
            'status' => 'active',
            'description' => 'Test property',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'Test Country',
            'registration_number' => 'REG' . rand(1, 1000),
            'zoning' => 'Residential',
            'zoning_type' => 'R1',
            'land_use' => 'Residential',
            'ownership_type' => 'Freehold',
        ]);

        UserFavorite::create([
            'user_id' => $user->id,
            'favoritable_type' => Property::class,
            'favoritable_id' => $property->id,
            'created_at' => now(),
        ]);

        // Create notification
        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Test Title',
            'message' => 'Test Message',
            'is_read' => false,
        ]);

        // Create comparison
        $comparison = UserComparison::create([
            'user_id' => $user->id,
            'name' => 'Test Comparison',
            'items' => [$property->id],
        ]);

        $data = $this->dashboardService->getUserDashboardData($user);

        // Verify counts
        $this->assertEquals(1, $data['properties_viewed_today']);
        $this->assertEquals(1, $data['searches_today']);
        $this->assertEquals(1, $data['saved_properties']);
        $this->assertEquals(1, $data['saved_today']);
        $this->assertEquals(1, $data['unread_notifications']);
        $this->assertEquals(1, $data['comparisons_count']);

        // Verify collections
        $this->assertCount(1, $data['recently_saved']);
        $this->assertCount(1, $data['latest_notifications']);
        $this->assertCount(1, $data['comparisons']);
    }
}

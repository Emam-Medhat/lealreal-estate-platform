<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserFavorite;
use App\Models\UserNotification;
use App\Models\UserComparison;
use App\Models\Property;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $dashboardService;

    protected function setUp(): void
    {
        parent::setUp();
        // Disable events to avoid UserObserver issues with missing models
        \Illuminate\Support\Facades\Event::fake();
        $this->dashboardService = new DashboardService();
    }

    public function test_get_user_dashboard_data_returns_correct_structure()
    {
        $user = User::factory()->create();

        // Seed some data manually since we faked events
        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'property_view',
            'created_at' => Carbon::now(),
        ]);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'search',
            'created_at' => Carbon::now(),
        ]);

        $data = $this->dashboardService->getUserDashboardData($user);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('properties_viewed_today', $data);
        $this->assertArrayHasKey('searches_today', $data);
        $this->assertArrayHasKey('searches', $data);
        $this->assertEquals(1, $data['properties_viewed_today']);
        $this->assertEquals(1, $data['searches_today']);
        $this->assertEquals(1, $data['searches']);
    }

    public function test_get_quick_stats_returns_correct_data()
    {
        $user = User::factory()->create();

        $data = $this->dashboardService->getQuickStats($user);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('favorites_count', $data);
        $this->assertArrayHasKey('notifications_count', $data);
    }
}

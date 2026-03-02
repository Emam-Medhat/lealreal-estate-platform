<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserFavorite;
use App\Models\UserNotification;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardService();
        $this->user = User::factory()->create();
    }

    public function test_get_user_dashboard_data_returns_correct_structure()
    {
        Cache::flush();
        $data = $this->service->getUserDashboardData($this->user);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('properties_viewed_today', $data);
        $this->assertArrayHasKey('saved_properties', $data);
        $this->assertArrayHasKey('unread_notifications', $data);
    }

    public function test_get_user_dashboard_data_calculates_today_stats_correctly()
    {
        Cache::flush();
        $today = Carbon::today();

        // Activity today
        UserActivityLog::insert([
            'user_id' => $this->user->id,
            'action' => 'property_view',
            'created_at' => $today->copy()->addHour()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        // Activity yesterday
        UserActivityLog::insert([
            'user_id' => $this->user->id,
            'action' => 'property_view',
            'created_at' => $today->copy()->subDay()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        // Favorite today
        UserFavorite::insert([
            'user_id' => $this->user->id,
            'favoritable_type' => 'App\Models\Property',
            'favoritable_id' => 1,
            'created_at' => $today->copy()->addHour()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        // Favorite yesterday
        UserFavorite::insert([
            'user_id' => $this->user->id,
            'favoritable_type' => 'App\Models\Property',
            'favoritable_id' => 2,
            'created_at' => $today->copy()->subDay()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        // Notification unread
        UserNotification::insert([
            'user_id' => $this->user->id,
            'type' => 'info',
            'title' => 'Test',
            'message' => 'Test',
            'is_read' => false,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        $data = $this->service->getUserDashboardData($this->user);

        $this->assertEquals(1, $data['properties_viewed_today'], "Properties viewed today count is wrong");
        $this->assertEquals(1, $data['saved_today'], "Saved today count is wrong");
        $this->assertEquals(1, $data['unread_notifications'], "Unread notifications count is wrong");
        $this->assertEquals(2, $data['saved_properties'], "Total saved properties count is wrong");
    }
}

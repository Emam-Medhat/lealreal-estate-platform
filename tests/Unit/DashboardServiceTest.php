<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserFavorite;
use App\Models\UserComparison;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Models\Property;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardService();
        $this->user = User::factory()->create();
    }

    public function test_get_user_dashboard_data_structure()
    {
        // Setup data
        UserWallet::create(['user_id' => $this->user->id, 'balance' => 1000]);

        $data = $this->service->getUserDashboardData($this->user);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('properties_viewed', $data);
        $this->assertArrayHasKey('saved_properties', $data);
        $this->assertArrayHasKey('unread_notifications', $data);
        $this->assertArrayHasKey('wallet_balance', $data);
        $this->assertEquals(1000, $data['wallet_balance']);
    }

    public function test_get_quick_stats_structure()
    {
        UserWallet::create(['user_id' => $this->user->id, 'balance' => 500]);

        $stats = $this->service->getQuickStats($this->user);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('favorites_count', $stats);
        $this->assertArrayHasKey('wallet_balance', $stats);
        $this->assertEquals(500, $stats['wallet_balance']);
    }
}

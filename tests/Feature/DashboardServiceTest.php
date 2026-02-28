<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserFavorite;
use App\Models\UserWallet;
use App\Services\DashboardService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set APP_KEY for tests
        config(['app.key' => 'base64:uY66666666666666666666666666666666666666666=']);
    }

    public function test_get_user_dashboard_data_returns_correct_structure()
    {
        $user = User::factory()->create();
        UserWallet::firstOrCreate(['user_id' => $user->id], ['balance' => 500]);

        $service = new DashboardService();
        $data = $service->getUserDashboardData($user);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('properties_viewed', $data);
        $this->assertArrayHasKey('saved_today', $data);
        $this->assertArrayHasKey('wallet_balance', $data);
        $this->assertEquals(500, $data['wallet_balance']);
    }

    public function test_get_user_dashboard_data_calculates_today_stats_correctly()
    {
        $user = User::factory()->create();
        // Clear activity log created by observer
        UserActivityLog::where('user_id', $user->id)->delete();

        $today = Carbon::today();

        // Activity today
        DB::table('user_activity_logs')->insert([
            'user_id' => $user->id,
            'action' => 'property_view',
            'created_at' => $today->copy()->addMinutes(10),
            'updated_at' => $today->copy()->addMinutes(10),
        ]);

        // Activity yesterday
        DB::table('user_activity_logs')->insert([
            'user_id' => $user->id,
            'action' => 'property_view',
            'created_at' => $today->copy()->subDay(),
            'updated_at' => $today->copy()->subDay(),
        ]);

        // Favorite today
        UserFavorite::create([
            'user_id' => $user->id,
            'favoritable_type' => 'App\Models\Property',
            'favoritable_id' => 1,
            'created_at' => $today->copy()->addMinutes(20),
        ]);

        $service = new DashboardService();
        $data = $service->getUserDashboardData($user);

        $this->assertEquals(1, $data['properties_viewed_today']);
        $this->assertEquals(1, $data['saved_today']);
    }
}

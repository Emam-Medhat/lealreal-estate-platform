<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardPerformanceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected $dashboardService;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = new DashboardService;

        // Create user manually since we know factories might be missing for some models
        // Using withoutEvents to avoid missing UserAnalytics in Observer
        $this->user = User::withoutEvents(fn () => User::create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'agent',
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'phone' => '1234567890',
            'uuid' => Str::uuid(),
        ]));

        // Setup some data
        UserWallet::create(['user_id' => $this->user->id, 'balance' => 1000]);

        for ($i = 0; $i < 10; $i++) {
            UserActivityLog::create([
                'user_id' => $this->user->id,
                'action' => $i % 2 == 0 ? 'property_view' : 'search',
                'details' => 'Test activity '.$i,
                'created_at' => now(),
            ]);

            UserNotification::create([
                'user_id' => $this->user->id,
                'type' => 'info',
                'title' => 'Test Notification '.$i,
                'message' => 'Test message '.$i,
                'is_read' => false,
            ]);
        }
    }

    public function test_get_user_dashboard_data_query_count()
    {
        Cache::flush();

        DB::enableQueryLog();
        $this->dashboardService->getUserDashboardData($this->user);
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        echo "\nTotal queries for getUserDashboardData: ".$queryCount."\n";
        $this->assertTrue($queryCount > 0);
    }
}

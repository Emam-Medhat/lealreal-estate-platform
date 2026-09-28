<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\ActivityController;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ActivityControllerBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_controller_index_stats_calculation_and_query_count()
    {
        // Seeding users automatically triggers UserObserver creating 2 UserActivityLog records today
        $user1 = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
        ]);

        $user2 = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'username' => 'janesmith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'phone' => '0987654321',
        ]);

        // Create 3 activity logs today
        UserActivityLog::create([
            'user_id' => $user1->id,
            'action' => 'login',
        ]);

        UserActivityLog::create([
            'user_id' => $user1->id,
            'action' => 'property_viewed',
        ]);

        UserActivityLog::create([
            'user_id' => $user2->id,
            'action' => 'login',
        ]);

        // Create 2 activity logs yesterday by setting created_at directly
        $log1 = UserActivityLog::create([
            'user_id' => $user1->id,
            'action' => 'login',
        ]);
        $log1->created_at = now()->subDay();
        $log1->save();

        $log2 = UserActivityLog::create([
            'user_id' => $user2->id,
            'action' => 'profile_updated',
        ]);
        $log2->created_at = now()->subDay();
        $log2->save();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $controller = new ActivityController();
        $response = $controller->index(new Request());

        $executedQueries = DB::getQueryLog();
        DB::disableQueryLog();

        $stats = $response->getData()['stats'];

        // Assert stats accuracy (2 observer user creation logs today + 3 explicit logs today + 2 explicit logs yesterday = 7 total)
        $this->assertEquals(7, $stats['total_activities']);
        $this->assertEquals(5, $stats['today_activities']);
        $this->assertEquals(2, $stats['unique_users']);

        // Stats queries should be consolidated (3 pagination/eager loading queries + 1 aggregate stats query + 1 top actions query = 5 queries)
        $this->assertLessThanOrEqual(5, count($executedQueries), "Total query count should be <= 5");
    }
}

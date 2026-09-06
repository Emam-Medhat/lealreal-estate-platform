<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SystemErrorLogBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_error_log_stats_returns_correct_counts_and_executes_minimal_queries(): void
    {
        // Seed system error logs
        $now = now();
        $yesterday = now()->subDays(2);

        // Today resolved
        DB::table('system_error_logs')->insert([
            'message' => 'Test error 1',
            'is_resolved' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Today unresolved
        DB::table('system_error_logs')->insert([
            'message' => 'Test error 2',
            'is_resolved' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Old resolved
        DB::table('system_error_logs')->insert([
            'message' => 'Test error 3',
            'is_resolved' => true,
            'created_at' => $yesterday,
            'updated_at' => $yesterday,
        ]);

        // Old unresolved
        DB::table('system_error_logs')->insert([
            'message' => 'Test error 4',
            'is_resolved' => false,
            'created_at' => $yesterday,
            'updated_at' => $yesterday,
        ]);

        DB::enableQueryLog();

        $controller = new \App\Http\Controllers\SystemErrorLogController;
        $response = $controller->index();

        $queryLog = DB::getQueryLog();

        // 2 queries for pagination (count + select) + 1 query for stats = 3 total queries
        $this->assertLessThanOrEqual(3, count($queryLog), 'SystemErrorLogController::index should execute at most 3 queries total.');

        $data = $response->getData();
        $stats = $data['stats'];

        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(2, $stats['resolved']);
        $this->assertEquals(2, $stats['unresolved']);
        $this->assertEquals(2, $stats['today']);
    }
}

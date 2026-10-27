<?php

namespace Tests\Feature;

use App\Models\Geospatial\LocationIntelligence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LocationIntelligenceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_intelligence_index_stats_query_count_and_accuracy(): void
    {
        // Seed LocationIntelligence records
        LocationIntelligence::create([
            'intelligence_type' => 'market_intelligence',
            'location_score' => 85.00,
            'status' => 'active',
        ]);

        LocationIntelligence::create([
            'intelligence_type' => 'location_score',
            'location_score' => 90.00,
            'status' => 'active',
        ]);

        LocationIntelligence::create([
            'intelligence_type' => 'investment_intelligence',
            'location_score' => 75.00,
            'status' => 'active',
        ]);

        LocationIntelligence::create([
            'intelligence_type' => 'demographic_intelligence',
            'location_score' => 60.00,
            'status' => 'pending',
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $controller = new \App\Http\Controllers\Geospatial\LocationIntelligenceController();
        $inertiaResponse = $controller->index(request());

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Check query count for stats + recent intelligence
        // 1 query for stats, 1 query for recent intelligence reports
        $this->assertLessThanOrEqual(2, count($queries), 'Query count should be consolidated into <= 2 queries');

        $props = (fn () => $this->props)->call($inertiaResponse);
        $stats = $props['stats'];

        $this->assertEquals(4, $stats['total_intelligence']);
        $this->assertEquals(3, $stats['active_intelligence']);
        $this->assertEquals(2, $stats['high_score_locations']);
        $this->assertEquals(77.5, $stats['average_score']);
    }
}

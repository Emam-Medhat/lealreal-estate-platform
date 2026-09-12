<?php

namespace Tests\Feature;

use App\Http\Controllers\AiLeadScoringController;
use App\Models\AiLeadScore;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AiLeadScoringBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_score_stats_returns_accurate_metrics_in_one_query(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'phone' => '1234567890',
        ]);

        $lead = Lead::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'first_name' => 'Lead',
            'last_name' => 'One',
            'email' => 'lead1@example.com',
            'created_by' => $user->id,
        ]);

        // Create test scores across quality tiers
        AiLeadScore::create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'scoring_model' => 'predictive_ml',
            'total_score' => 85.00,
            'demographic_score' => 85.00,
            'behavioral_score' => 85.00,
            'engagement_score' => 85.00,
            'source_quality_score' => 85.00,
            'timing_score' => 85.00,
            'budget_score' => 85.00,
            'property_match_score' => 85.00,
            'conversion_probability' => 0.80,
            'confidence_level' => 0.85,
            'lead_quality_level' => 'high',
            'priority_level' => 'high',
            'ai_model_version' => '1.0',
            'status' => 'active',
            'last_scored_at' => now(),
        ]);

        AiLeadScore::create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'scoring_model' => 'predictive_ml',
            'total_score' => 65.00,
            'demographic_score' => 65.00,
            'behavioral_score' => 65.00,
            'engagement_score' => 65.00,
            'source_quality_score' => 65.00,
            'timing_score' => 65.00,
            'budget_score' => 65.00,
            'property_match_score' => 65.00,
            'conversion_probability' => 0.60,
            'confidence_level' => 0.85,
            'lead_quality_level' => 'medium',
            'priority_level' => 'medium',
            'ai_model_version' => '1.0',
            'status' => 'active',
            'last_scored_at' => now(),
        ]);

        AiLeadScore::create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'scoring_model' => 'predictive_ml',
            'total_score' => 40.00,
            'demographic_score' => 40.00,
            'behavioral_score' => 40.00,
            'engagement_score' => 40.00,
            'source_quality_score' => 40.00,
            'timing_score' => 40.00,
            'budget_score' => 40.00,
            'property_match_score' => 40.00,
            'conversion_probability' => 0.30,
            'confidence_level' => 0.85,
            'lead_quality_level' => 'low',
            'priority_level' => 'low',
            'ai_model_version' => '1.0',
            'status' => 'active',
            'last_scored_at' => now(),
        ]);

        // Enable query log
        DB::enableQueryLog();

        $controller = new AiLeadScoringController;

        // Reflection to call private getScoreStats
        $reflection = new \ReflectionClass(AiLeadScoringController::class);
        $method = $reflection->getMethod('getScoreStats');
        $method->setAccessible(true);

        $stats = $method->invoke($controller);

        $queryLog = DB::getQueryLog();

        // Verify only 1 query was executed
        $this->assertCount(1, $queryLog, 'Expected exactly 1 database query for getScoreStats');

        // Verify calculations
        $this->assertEquals(3, $stats['total_scores']);
        $this->assertEquals(1, $stats['high_quality_leads']);
        $this->assertEquals(1, $stats['medium_quality_leads']);
        $this->assertEquals(1, $stats['low_quality_leads']);
        $this->assertEquals(63.33, $stats['average_score']);
    }
}

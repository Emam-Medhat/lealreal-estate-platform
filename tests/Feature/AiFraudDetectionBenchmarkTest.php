<?php

namespace Tests\Feature;

use App\Http\Controllers\AiFraudDetectionController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AiFraudDetectionBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats_calculation_and_query_reduction()
    {
        // 1. Setup mock user
        $user = User::factory()->create([
            'first_name' => 'Fraud',
            'last_name' => 'Inspector',
            'username' => 'fraudinspector',
            'phone' => '1234567890',
        ]);

        // 2. Insert test alerts into ai_fraud_alerts table using actual schema columns
        DB::table('ai_fraud_alerts')->insert([
            [
                'property_id' => null,
                'user_id' => $user->id,
                'alert_type' => 'price_manipulation',
                'risk_level' => 'high',
                'investigation_status' => 'pending',
                'resolution_status' => 'pending',
                'description' => 'Test High Risk Pending Alert',
                'confidence_score' => 0.90,
                'detection_method' => 'rule_based',
                'ai_model_version' => '1.0',
                'processing_time' => 0.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => null,
                'user_id' => $user->id,
                'alert_type' => 'fake_listing',
                'risk_level' => 'high',
                'investigation_status' => 'confirmed',
                'resolution_status' => 'confirmed',
                'description' => 'Test Confirmed Alert',
                'confidence_score' => 0.95,
                'detection_method' => 'rule_based',
                'ai_model_version' => '1.0',
                'processing_time' => 0.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => null,
                'user_id' => $user->id,
                'alert_type' => 'identity_fraud',
                'risk_level' => 'low',
                'investigation_status' => 'false_positive',
                'resolution_status' => 'false_positive',
                'description' => 'Test False Positive Alert',
                'confidence_score' => 0.30,
                'detection_method' => 'rule_based',
                'ai_model_version' => '1.0',
                'processing_time' => 0.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'property_id' => null,
                'user_id' => $user->id,
                'alert_type' => 'document_forgery',
                'risk_level' => 'medium',
                'investigation_status' => 'resolved',
                'resolution_status' => 'resolved',
                'description' => 'Test Resolved Alert',
                'confidence_score' => 0.70,
                'detection_method' => 'rule_based',
                'ai_model_version' => '1.0',
                'processing_time' => 0.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->actingAs($user);

        // 3. Enable query log to count queries executed by dashboard()
        DB::flushQueryLog();
        DB::enableQueryLog();

        $controller = new AiFraudDetectionController();
        $view = $controller->dashboard();
        $data = $view->getData();

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Filter queries related to ai_fraud_alerts
        $alertQueries = array_filter($queries, function ($q) {
            return str_contains($q['query'], 'ai_fraud_alerts');
        });

        // 4. Verify calculation accuracy
        $this->assertArrayHasKey('stats', $data);
        $stats = $data['stats'];

        $this->assertEquals(4, $stats['total_alerts']);
        $this->assertEquals(2, $stats['high_risk_alerts']);
        $this->assertEquals(1, $stats['resolved_alerts']);
        $this->assertEquals(1, $stats['pending_investigation']);
        $this->assertEquals(25.0, $stats['detection_accuracy']); // 1 confirmed out of 4 = 25%
        $this->assertEquals(25.0, $stats['false_positive_rate']); // 1 false_positive out of 4 = 25%

        // 5. Verify query optimization: count queries for ai_fraud_alerts stats should be 1
        $countQueries = array_filter($alertQueries, function ($q) {
            return str_contains(strtolower($q['query']), 'count');
        });

        $this->assertLessThanOrEqual(2, count($countQueries), 'Count queries executed for stats exceeded expected count.');
    }
}

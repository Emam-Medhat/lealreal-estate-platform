<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\AiPricePrediction;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AiPricePredictorBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats_and_accuracy_metrics_calculation_and_query_reduction(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'Agent Smith',
            'email' => 'agent@example.com',
            'phone' => '0987654321',
            'hire_date' => now(),
            'license_number' => 'LIC-12345',
        ]);

        $property1 = Property::create([
            'agent_id' => $agent->id,
            'area' => 120,
            'property_code' => 'P-101',
            'title' => 'Sample Property 1',
            'description' => 'Test description',
            'address' => '123 Test St',
            'city' => 'Riyadh',
            'state' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'area_unit' => 'sqm',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'price' => 500000,
        ]);

        $property2 = Property::create([
            'agent_id' => $agent->id,
            'area' => 150,
            'property_code' => 'P-102',
            'title' => 'Sample Property 2',
            'description' => 'Test description',
            'address' => '456 Test St',
            'city' => 'Jeddah',
            'state' => 'Makkah',
            'country' => 'Saudi Arabia',
            'area_unit' => 'sqm',
            'property_type' => 'villa',
            'listing_type' => 'sale',
            'price' => 1200000,
        ]);

        // Prediction 1: Completed, today, property 1, high accuracy (0.9)
        AiPricePrediction::create([
            'property_id' => $property1->id,
            'user_id' => $user->id,
            'prediction_model' => 'neural_network',
            'time_horizon' => '1_year',
            'prediction_date' => now(),
            'current_price' => 500000,
            'predicted_price' => 520000,
            'accuracy' => 0.90,
            'status' => 'completed',
        ]);

        // Prediction 2: Completed, today, property 1, medium accuracy (0.7)
        AiPricePrediction::create([
            'property_id' => $property1->id,
            'user_id' => $user->id,
            'prediction_model' => 'random_forest',
            'time_horizon' => '6_months',
            'prediction_date' => now(),
            'current_price' => 500000,
            'predicted_price' => 510000,
            'accuracy' => 0.70,
            'status' => 'completed',
        ]);

        // Prediction 3: Completed, 2 days ago, property 2, high accuracy (0.85)
        $pred3 = AiPricePrediction::create([
            'property_id' => $property2->id,
            'user_id' => $user->id,
            'prediction_model' => 'linear_regression',
            'time_horizon' => '3_months',
            'prediction_date' => now()->subDays(2),
            'current_price' => 1200000,
            'predicted_price' => 1250000,
            'accuracy' => 0.85,
            'status' => 'completed',
        ]);
        DB::table('ai_price_predictions')->where('id', $pred3->id)->update(['created_at' => now()->subDays(2)]);

        // Prediction 4: Pending, today, property 2, low accuracy (0.50)
        AiPricePrediction::create([
            'property_id' => $property2->id,
            'user_id' => $user->id,
            'prediction_model' => 'xgboost',
            'time_horizon' => '1_month',
            'prediction_date' => now(),
            'current_price' => 1200000,
            'predicted_price' => 1100000,
            'accuracy' => 0.50,
            'status' => 'pending',
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $controller = new \App\Http\Controllers\AiPricePredictorController();
        $response = $controller->dashboard();

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // 1. Assert calculation accuracy
        $data = $response->getData();
        $stats = $data['stats'];
        $accuracyMetrics = $data['accuracyMetrics'];

        $this->assertEquals(4, $stats['total_predictions']);
        $this->assertEquals(3, $stats['today_predictions']);
        // Average accuracy of completed: (0.90 + 0.70 + 0.85) / 3 = 0.81666666...
        $this->assertEqualsWithDelta(0.81666666, $stats['average_accuracy'], 0.001);
        $this->assertEquals(2, $stats['total_properties_analyzed']); // properties 1 and 2 completed
        $this->assertEquals(75.0, $stats['prediction_success_rate']); // 3 / 4 * 100

        $this->assertEquals(4, $accuracyMetrics['total_predictions']);
        $this->assertEquals(2, $accuracyMetrics['high_accuracy_predictions']); // 0.90 and 0.85 >= 0.8
        // Overall average accuracy: (0.90 + 0.70 + 0.85 + 0.50) / 4 = 0.7375
        $this->assertEqualsWithDelta(0.7375, $accuracyMetrics['average_accuracy'], 0.001);

        // 2. Assert stats query count reduction (exactly 1 consolidated query for stats & accuracyMetrics calculations)
        $statsQueries = array_filter($queries, function ($query) {
            return str_contains($query['query'], 'total_predictions');
        });

        $this->assertCount(1, $statsQueries, 'Dashboard stats calculation should run exactly 1 consolidated SQL query.');
    }
}

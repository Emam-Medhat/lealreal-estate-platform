<?php

namespace Tests\Feature;

use App\Http\Controllers\AppraisalController;
use App\Models\Appraisal;
use App\Models\AppraisalReport;
use App\Models\Appraiser;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AppraisalDashboardBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_appraisal_dashboard_aggregates_stats_correctly_and_efficiently()
    {
        // 1. Create dependencies
        $appraiserUser = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'phone' => '1234567890',
        ]);

        DB::table('appraisers')->insert([
            'id' => $appraiserUser->id,
            'user_id' => $appraiserUser->id,
            'name' => 'John Doe',
            'email' => $appraiserUser->email,
            'phone' => '1234567890',
            'license_number' => 'LIC-12345',
            'license_type' => 'certified',
            'license_expiry' => now()->addYear()->toDateString(),
            'status' => 'active',
            'specializations' => json_encode(['residential']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $property = Property::create([
            'property_code' => 'PROP-101',
            'title' => 'Test Villa',
            'description' => 'A beautiful villa for testing.',
            'price' => 1000000.00,
            'area' => 250.0,
            'address' => '123 Main St',
            'city' => 'Riyadh',
            'state' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'area_unit' => 'sqm',
            'property_type' => 'residential',
            'listing_type' => 'sale',
            'agent_id' => $appraiserUser->id,
        ]);

        // 2. Create sample Appraisals
        $scheduledAppraisal = Appraisal::create([
            'property_id' => $property->id,
            'appraiser_id' => $appraiserUser->id,
            'appraisal_type' => 'market_value',
            'purpose' => 'Selling',
            'scheduled_date' => now()->addDays(2),
            'priority' => 'medium',
            'status' => 'scheduled',
        ]);

        $inProgressAppraisal = Appraisal::create([
            'property_id' => $property->id,
            'appraiser_id' => $appraiserUser->id,
            'appraisal_type' => 'insurance',
            'purpose' => 'Insurance',
            'scheduled_date' => now()->subDays(1),
            'priority' => 'high',
            'status' => 'in_progress',
        ]);

        $completedAppraisal = Appraisal::create([
            'property_id' => $property->id,
            'appraiser_id' => $appraiserUser->id,
            'appraisal_type' => 'tax',
            'purpose' => 'Tax Assessment',
            'scheduled_date' => now()->subDays(5),
            'priority' => 'low',
            'status' => 'completed',
        ]);

        // 3. Create Appraisal Report for estimated value aggregation
        AppraisalReport::create([
            'appraisal_id' => $completedAppraisal->id,
            'appraiser_id' => $appraiserUser->id,
            'report_number' => 'REP-001',
            'title' => 'Tax Appraisal Report',
            'market_value' => 1200000.00,
            'valuation_method' => 'sales_comparison',
            'valuation_date' => now()->subDays(5)->toDateString(),
            'report_date' => now()->subDays(5)->toDateString(),
            'status' => 'approved',
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $controller = new AppraisalController();
        $response = $controller->dashboard();

        $executedQueries = DB::getQueryLog();
        $queryCount = count($executedQueries);

        // Assert response view data is correct
        $viewData = $response->getData();
        $this->assertArrayHasKey('stats', $viewData);

        $stats = $viewData['stats'];
        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(1, $stats['scheduled']);
        $this->assertEquals(1, $stats['in_progress']);
        $this->assertEquals(1, $stats['completed']);
        $this->assertEquals(3, $stats['this_month']);
        $this->assertEquals(1200000.00, $stats['total_value']);

        // Before optimization, 5 separate count queries + sum query + recent queries + type query = 10 queries.
        // Now optimized down to 6 queries (1 selectRaw for counts instead of 5 separate count queries).
        $this->assertLessThanOrEqual(6, $queryCount, "Dashboard query count should be optimized");
    }
}

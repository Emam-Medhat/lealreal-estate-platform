<?php

namespace Tests\Feature;

use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\User;
use App\Services\DeveloperDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeveloperDashboardPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_quick_stats_executes_minimal_queries_and_calculates_correctly(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Developer',
            'username' => 'johndev',
            'phone' => '123456789',
        ]);

        $developer = Developer::create([
            'user_id' => $user->id,
            'company_name' => 'Apex Developments',
            'license_number' => 'DEV-12345',
            'commercial_register' => 'CR-999',
            'tax_number' => 'TAX-888',
            'developer_type' => 'residential',
            'status' => 'active',
            'phone' => '123456789',
            'email' => 'dev@apex.com',
            'website' => 'https://apex.com',
        ]);

        // Under construction project created this month
        $p1 = new DeveloperProject;
        $p1->developer_id = $developer->id;
        $p1->title = 'Project Alpha';
        $p1->slug = 'project-alpha';
        $p1->description = 'Alpha desc';
        $p1->project_type = 'residential';
        $p1->status = 'under_construction';
        $p1->location = json_encode(['city' => 'Riyadh']);
        $p1->total_area = 1000.00;
        $p1->total_investment = 500000.00;
        $p1->total_units = 10;
        $p1->sold_units = 4;
        $p1->created_at = now();
        $p1->save();

        // Completed project created earlier
        $p2 = new DeveloperProject;
        $p2->developer_id = $developer->id;
        $p2->title = 'Project Beta';
        $p2->slug = 'project-beta';
        $p2->description = 'Beta desc';
        $p2->project_type = 'commercial';
        $p2->status = 'completed';
        $p2->location = json_encode(['city' => 'Jeddah']);
        $p2->total_area = 2000.00;
        $p2->total_investment = 1000000.00;
        $p2->total_units = 20;
        $p2->sold_units = 20;
        $p2->created_at = now()->subMonths(2);
        $p2->save();

        $service = new DeveloperDashboardService;

        // Clear any cache before testing
        \Illuminate\Support\Facades\Cache::forget("developer_quick_stats_{$developer->id}");

        DB::enableQueryLog();

        $stats = $service->getQuickStats($developer);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Assert query count is 2 (one selectRaw for project stats, one count for ongoingPhases)
        $this->assertLessThanOrEqual(2, count($queries), 'Expected 2 or fewer queries, executed '.count($queries));

        // Assert calculated values
        $this->assertEquals(2, $stats['total_projects']);
        $this->assertEquals(1, $stats['active_projects']);
        $this->assertEquals(1, $stats['completed_projects']);
        $this->assertEquals(30, $stats['total_units']);
        $this->assertEquals(24, $stats['sold_units']);
        $this->assertEquals(1500000.00, $stats['total_revenue']);
        $this->assertEquals(500000.00, $stats['this_month_revenue']);
        $this->assertEquals(0, $stats['ongoing_phases']);
    }
}

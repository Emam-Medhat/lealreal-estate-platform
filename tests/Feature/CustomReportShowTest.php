<?php

namespace Tests\Feature;

use App\Models\CustomReport;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomReportShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_by_report_id()
    {
        $user = User::factory()->create();

        $report = Report::create([
            'title' => 'Test Report',
            'type' => 'custom',
            'description' => 'Test Description',
            'parameters' => [],
            'filters' => [],
            'status' => 'completed',
            'format' => 'json',
            'generated_by' => $user->id,
        ]);

        $customReport = CustomReport::create([
            'report_id' => $report->id,
            'name' => 'Test Custom Report',
            'description' => 'Test Description',
            'data_sources' => ['properties'],
            'query_config' => [],
            'visualization_config' => [],
            'custom_fields' => [],
            'is_template' => false,
            'created_by' => $user->id,
            'active' => true,
        ]);

        $response = $this->actingAs($user)
            ->get(route('reports.custom.show', $report->id));

        $response->assertStatus(200);
        $response->assertViewHas('report');
        $response->assertViewHas('customReport');
    }

    public function test_show_by_custom_report_id()
    {
        $user = User::factory()->create();

        $report = Report::create([
            'title' => 'Test Report 2',
            'type' => 'custom',
            'description' => 'Test Description 2',
            'parameters' => [],
            'filters' => [],
            'status' => 'completed',
            'format' => 'json',
            'generated_by' => $user->id,
        ]);

        $customReport = CustomReport::create([
            'report_id' => $report->id,
            'name' => 'Test Custom Report 2',
            'description' => 'Test Description 2',
            'data_sources' => ['properties'],
            'query_config' => [],
            'visualization_config' => [],
            'custom_fields' => [],
            'is_template' => false,
            'created_by' => $user->id,
            'active' => true,
        ]);

        // Access using CustomReport ID, which should be different from Report ID
        // To be safe, let's assume they might be different. 
        // If IDs are auto-increment, they might be same if tables are fresh.
        // But the controller logic handles both.
        
        $response = $this->actingAs($user)
            ->get(route('reports.custom.show', $customReport->id));

        $response->assertStatus(200);
        $response->assertViewHas('report');
        $response->assertViewHas('customReport');
    }

    public function test_self_healing_orphaned_report()
    {
        $user = User::factory()->create();

        // Create a Report WITHOUT a corresponding CustomReport
        $report = Report::create([
            'title' => 'Orphaned Report',
            'type' => 'custom',
            'description' => 'This report has no custom_report entry',
            'parameters' => ['param1' => 'value1'],
            'filters' => ['filter1' => 'value1'],
            'status' => 'completed',
            'format' => 'json',
            'generated_by' => $user->id,
        ]);

        // Accessing this should trigger self-healing instead of 404
        $response = $this->actingAs($user)
            ->get(route('reports.custom.show', $report->id));

        $response->assertStatus(200);
        $response->assertViewHas('report');
        $response->assertViewHas('customReport');

        // Verify that the CustomReport was actually created
        $this->assertDatabaseHas('custom_reports', [
            'report_id' => $report->id,
            'name' => 'Orphaned Report',
            'created_by' => $user->id,
        ]);
    }
}

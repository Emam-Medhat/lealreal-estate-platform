<?php

namespace Tests\Feature;

use App\Models\CustomReport;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomReportUsageTest extends TestCase
{
    use RefreshDatabase;

    public function test_increment_usage()
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
            'usage_count' => 0
        ]);

        $this->assertEquals(0, $customReport->usage_count);

        $customReport->incrementUsage();

        $this->assertEquals(1, $customReport->fresh()->usage_count);
    }
}

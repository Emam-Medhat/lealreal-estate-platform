<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\User;
use App\Services\LeadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LeadConversionFunnelBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_conversion_funnel_returns_correct_counts_and_percentages_efficiently(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password'),
        ]);

        $status1 = LeadStatus::create([
            'name' => 'New',
            'color' => '#000000',
            'order' => 1,
            'is_active' => true,
        ]);

        $status2 = LeadStatus::create([
            'name' => 'Qualified',
            'color' => '#111111',
            'order' => 2,
            'is_active' => true,
        ]);

        $status3 = LeadStatus::create([
            'name' => 'Converted',
            'color' => '#222222',
            'order' => 3,
            'is_active' => true,
        ]);

        Lead::create([
            'first_name' => 'Lead',
            'last_name' => 'One',
            'email' => 'lead1@example.com',
            'lead_status' => 'New',
            'status_id' => $status1->id,
            'created_by' => $user->id,
        ]);

        Lead::create([
            'first_name' => 'Lead',
            'last_name' => 'Two',
            'email' => 'lead2@example.com',
            'lead_status' => 'New',
            'status_id' => $status1->id,
            'created_by' => $user->id,
        ]);

        Lead::create([
            'first_name' => 'Lead',
            'last_name' => 'Three',
            'email' => 'lead3@example.com',
            'lead_status' => 'Qualified',
            'status_id' => $status2->id,
            'created_by' => $user->id,
        ]);

        /** @var LeadService $service */
        $service = app(LeadService::class);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $funnel = $service->getConversionFunnel();

        $queryCount = count(DB::getQueryLog());

        // Assert correct result structure and calculations
        $this->assertCount(3, $funnel);
        $this->assertEquals('New', $funnel[0]['status']);
        $this->assertEquals(2, $funnel[0]['count']);
        $this->assertEquals(66.67, $funnel[0]['percentage']);

        $this->assertEquals('Qualified', $funnel[1]['status']);
        $this->assertEquals(1, $funnel[1]['count']);
        $this->assertEquals(33.33, $funnel[1]['percentage']);

        $this->assertEquals('Converted', $funnel[2]['status']);
        $this->assertEquals(0, $funnel[2]['count']);
        $this->assertEquals(0, $funnel[2]['percentage']);

        $this->assertLessThanOrEqual(3, $queryCount, "Expected <= 3 queries, got {$queryCount}");
    }
}

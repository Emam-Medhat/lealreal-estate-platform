<?php

namespace Tests\Feature;

use App\Http\Controllers\ComplianceController;
use App\Models\ComplianceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ComplianceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('compliance_records')) {
            Schema::create('compliance_records', function ($table) {
                $table->id();
                $table->foreignId('user_id');
                $table->string('compliance_status')->default('compliant');
                $table->string('risk_level')->default('low');
                $table->decimal('compliance_score', 5, 2)->default(100.00);
                $table->dateTime('next_assessment_date')->nullable();
                $table->dateTime('assessment_date')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!File::exists(resource_path('views/security/compliance/index.blade.php'))) {
            File::makeDirectory(resource_path('views/security/compliance'), 0755, true, true);
            File::put(resource_path('views/security/compliance/index.blade.php'), '<div>Compliance Index</div>');
        }
    }

    protected function tearDown(): void
    {
        if (File::exists(resource_path('views/security/compliance/index.blade.php'))) {
            File::delete(resource_path('views/security/compliance/index.blade.php'));
        }

        parent::tearDown();
    }

    public function test_compliance_controller_index_stats_uses_single_query_and_calculates_accurately()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'phone' => '1234567890',
        ]);

        ComplianceRecord::create([
            'user_id' => $user->id,
            'compliance_status' => 'compliant',
            'risk_level' => 'low',
            'compliance_score' => 95,
        ]);

        ComplianceRecord::create([
            'user_id' => $user->id,
            'compliance_status' => 'pending_review',
            'risk_level' => 'medium',
            'compliance_score' => 80,
        ]);

        ComplianceRecord::create([
            'user_id' => $user->id,
            'compliance_status' => 'non_compliant',
            'risk_level' => 'high',
            'compliance_score' => 40,
        ]);

        $this->actingAs($user);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $controller = new ComplianceController();
        $response = $controller->index();

        $queryCount = count(DB::getQueryLog());
        $data = $response->getData();
        $stats = $data['complianceStats'];

        // Assert query count is optimized (1 count query for stats + 1 query for recentRecords)
        $this->assertLessThanOrEqual(2, $queryCount);

        // Assert calculations
        $this->assertEquals(3, $stats['total_records']);
        $this->assertEquals(1, $stats['compliant_records']);
        $this->assertEquals(1, $stats['pending_reviews']);
        $this->assertEquals(1, $stats['non_compliant']);
    }

    public function test_get_compliance_overview_uses_single_query_and_handles_empty_records()
    {
        $user = User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'username' => 'janedoe',
            'phone' => '0987654321',
        ]);

        $controller = new ComplianceController();
        $reflection = new \ReflectionClass(ComplianceController::class);
        $method = $reflection->getMethod('getComplianceOverview');
        $method->setAccessible(true);

        $overviewEmpty = $method->invoke($controller, $user->id);

        $this->assertEquals(0, $overviewEmpty['total_score']);
        $this->assertEquals(0, $overviewEmpty['compliance_rate']);
        $this->assertEquals(0, $overviewEmpty['high_risk_items']);
        $this->assertEquals(0, $overviewEmpty['overdue_assessments']);

        ComplianceRecord::create([
            'user_id' => $user->id,
            'compliance_status' => 'compliant',
            'risk_level' => 'high',
            'compliance_score' => 90,
            'next_assessment_date' => now()->subDay(),
        ]);

        ComplianceRecord::create([
            'user_id' => $user->id,
            'compliance_status' => 'non_compliant',
            'risk_level' => 'low',
            'compliance_score' => 50,
            'next_assessment_date' => now()->addDays(5),
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $overview = $method->invoke($controller, $user->id);

        $queryCount = count(DB::getQueryLog());

        $this->assertEquals(1, $queryCount);
        $this->assertEquals(70, $overview['total_score']); // AVG(90, 50) = 70
        $this->assertEquals(50, $overview['compliance_rate']); // 1 of 2 is compliant = 50%
        $this->assertEquals(1, $overview['high_risk_items']);
        $this->assertEquals(1, $overview['overdue_assessments']);
    }
}

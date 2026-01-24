<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\CompanyBranch;
use App\Models\Property;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GenerateCompanyReport implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 600;

    protected $companyId;
    protected $reportType;
    protected $filters;
    protected $requestedBy;
    protected $reportFormat;

    /**
     * Create a new job instance.
     */
    public function __construct(int $companyId, string $reportType, array $filters = [], int $requestedBy = null, string $reportFormat = 'pdf')
    {
        $this->companyId = $companyId;
        $this->reportType = $reportType;
        $this->filters = $filters;
        $this->requestedBy = $requestedBy;
        $this->reportFormat = $reportFormat;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $company = Company::with(['members', 'branches', 'properties'])
                ->findOrFail($this->companyId);

            // Generate report data based on type
            $reportData = $this->generateReportData($company, $this->reportType, $this->filters);

            // Generate report file
            $filePath = $this->generateReportFile($reportData, $this->reportFormat);

            // Save report record
            $report = $company->reports()->create([
                'type' => $this->reportType,
                'status' => 'generating',
                'file_path' => $filePath,
                'filters' => $this->filters,
                'requested_by' => $this->requestedBy,
                'generated_at' => now()
            ]);

            // Update status to completed
            $report->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            // Send notification to user
            if ($this->requestedBy) {
                $user = User::find($this->requestedBy);

                if ($user) {
                    $user->notifications()->create([
                        'title' => 'تقريرك جاهز',
                        'message' => "تم إنشاء تقرير {$this->getReportTypeName()} بنجاح",
                        'type' => 'report_ready',
                        'data' => [
                            'report_id' => $report->id,
                            'report_type' => $this->reportType,
                            'download_url' => route('companies.reports.download', $report->id)
                        ]
                    ]);
                }
            }

            Log::info('Company report generated', [
                'company_id' => $this->companyId,
                'report_type' => $this->reportType,
                'report_id' => $report->id,
                'file_path' => $filePath,
                'requested_by' => $this->requestedBy
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate company report', [
                'company_id' => $this->companyId,
                'report_type' => $this->reportType,
                'error' => $e->getMessage(),
                'filters' => $this->filters
            ]);

            // Update report status to failed
            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'failed_at' => now()
            ]);

            throw $e;
        }
    }

    /**
     * Generate report data based on type
     */
    private function generateReportData(Company $company, string $reportType, array $filters): array
    {
        $dateRange = $this->getDateRange($filters['date_range'] ?? 'month');

        switch ($reportType) {
            case 'performance':
                return $this->generatePerformanceReport($company, $dateRange);
            case 'financial':
                return $this->generateFinancialReport($company, $dateRange);
            case 'properties':
                return $this->generatePropertiesReport($company, $dateRange);
            case 'team':
                return $this->generateTeamReport($company, $dateRange);
            case 'comprehensive':
                return $this->generateComprehensiveReport($company, $dateRange, $filters);
            default:
                return $this->generateOverviewReport($company);
        }
    }

    /**
     * Generate performance report data
     */
    private function generatePerformanceReport(Company $company, array $dateRange): array
    {
        $teamMembers = $company->members()->with('user')->get();
        $properties = Property::where('company_id', $company->id)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get();

        $teamPerformance = [];
        foreach ($teamMembers as $member) {
            $memberPerformance = $this->getMemberPerformance($member, $company->id);
            $teamPerformance[] = $memberPerformance;
        }

        return [
            'report_type' => 'performance',
            'period' => $dateRange,
            'company' => [
                'name' => $company->name,
                'id' => $company->id
            ],
            'team' => [
                'total_members' => count($teamMembers),
                'active_members' => $teamMembers->where('status', 'active')->count(),
                'performance_data' => $teamPerformance,
                'top_performers' => $this->getTopPerformers($teamPerformance),
                'bottom_performers' => $this->getBottomPerformers($teamPerformance),
                'average_productivity' => count($teamPerformance) > 0 ? array_sum(array_column($teamPerformance, 'productivity_score')) / count($teamPerformance) : 0,
                'productivity_distribution' => $this->getProductivityDistribution($teamPerformance)
            ],
            'generated_at' => now()
        ];
    }

    /**
     * Generate financial report data
     */
    private function generateFinancialReport(Company $company, array $dateRange): array
    {
        $properties = Property::where('company_id', $company->id)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get(['id', 'type', 'price', 'status', 'created_at']);

        $totalRevenue = $properties->where('status', 'sold')
            ->sum('price');

        $totalValue = $properties->sum('price');
        $averagePrice = $properties->avg('price');
        $commissionEarned = $totalRevenue * 0.025; // 2.5% average commission

        return [
            'report_type' => 'financial',
            'period' => $dateRange,
            'company' => [
                'name' => $company->name,
                'id' => $company->id
            ],
            'properties' => [
                'total' => $properties->count(),
                'sold' => $properties->where('status', 'sold')->count(),
                'total_revenue' => $totalRevenue,
                'average_price' => $averagePrice,
                'total_value' => $totalValue,
                'commission_earned' => $totalRevenue * 0.025
            ],
            'revenue_breakdown' => $this->getRevenueBreakdown($properties),
            'generated_at' => now()
        ];
    }

    /**
     * Generate properties report data
     */
    private function generatePropertiesReport(Company $company, array $dateRange): array
    {
        $properties = Property::where('company_id', $company->id)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get(['id', 'type', 'price', 'status', 'created_at']);

        $totalProperties = $properties->count();
        $statusBreakdown = [
            'for_sale' => 0,
            'for_rent' => 0,
            'sold' => 0,
            'pending' => 0,
            'off_market' => 0,
            'under_contract' => 0,
            'valuation_updated' => 0
        ];

        $totalValue = $properties->sum('price');
        $averagePrice = $properties->avg('price');
        $typeBreakdown = [];

        foreach ($properties as $property) {
            if (isset($typeBreakdown[$property->type])) {
                $typeBreakdown[$property->type]++;
            }
        }

        return [
            'report_type' => 'properties',
            'period' => $dateRange,
            'company' => [
                'name' => $company->name,
                'id' => $company->id
            ],
            'properties' => [
                'total' => $totalProperties,
                'total_value' => $totalValue,
                'average_price' => $averagePrice,
                'status_breakdown' => $statusBreakdown,
                'type_breakdown' => $typeBreakdown
            ],
            'generated_at' => now()
        ];
    }

    /**
     * Generate team report data
     */
    private function generateTeamReport(Company $company, array $dateRange): array
    {
        $teamMembers = $company->members()->with('user')->get();
        $teamPerformance = [];

        foreach ($teamMembers as $member) {
            $memberPerformance = $this->getMemberPerformance($member, $company->id);
            $teamPerformance[] = $memberPerformance;
        }

        return [
            'report_type' => 'team',
            'period' => $dateRange,
            'company' => [
                'name' => $company->name,
                'id' => $company->id
            ],
            'team' => [
                'total_members' => count($teamMembers),
                'active_members' => $teamMembers->where('status', 'active')->count(),
                'performance_data' => $teamPerformance,
                'top_performers' => $this->getTopPerformers($teamPerformance),
                'bottom_performers' => $this->getBottomPerformers($teamPerformance),
                'productivity_distribution' => $this->getProductivityDistribution($teamPerformance),
                'average_productivity' => array_sum(array_column($teamPerformance, 'productivity_score')) / count($teamPerformance)
            ],
            'generated_at' => now()
        ];
    }

    /**
     * Generate comprehensive report data
     */
    private function generateComprehensiveReport(Company $company, array $dateRange, array $filters): array
    {
        $properties = Property::where('company_id', $company->id)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get(['id', 'type', 'price', 'status', 'created_at']);

        $teamMembers = $company->members()->with('user')->get();
        $totalProperties = $properties->count();
        $totalValue = $properties->sum('price');
        $totalRevenue = $properties->where('status', 'sold')
            ->sum('price');

        return [
            'report_type' => 'comprehensive',
            'period' => $dateRange,
            'company' => [
                'name' => $company->name,
                'id' => $company->id
            ],
            'summary' => [
                'total_properties' => $totalProperties,
                'total_value' => $totalValue,
                'total_revenue' => $totalRevenue,
                'average_price' => $totalProperties > 0 ? $totalValue / $totalProperties : 0
            ],
            'team' => [
                'team_size' => count($teamMembers),
                'active_members' => $teamMembers->where('status', 'active')->count()
            ],
            'branches' => [
                'branches_count' => $company->branches()->count()
            ],
            'timestamps' => [
                'created_at' => $company->created_at->toDateString(),
                'updated_at' => $company->updated_at->toDateString()
            ],
            'generated_at' => now()
        ];
    }

    /**
     * Generate overview report data
     */
    private function generateOverviewReport(Company $company, array $dateRange = null): array
    {
        $properties = Property::where('company_id', $company->id)->get();
        $teamMembers = $company->members()->with('user')->get();
        $branches = $company->branches()->get();

        return [
            'report_type' => 'overview',
            'company' => [
                'name' => $company->name,
                'id' => $company->id,
                'created_at' => $company->created_at->toDateString(),
                'updated_at' => $company->updated_at->toDateString()
            ],
            'subscription' => [
                'plan' => $company->subscriptionPlan ? $company->subscriptionPlan->name : null,
                'expires_at' => $company->subscription_expires_at ? $company->subscription_expires_at->toDateString() : null,
                'status' => $company->status
            ],
            'summary' => [
                'total_properties' => $properties->count(),
                'total_value' => $properties->sum('price'),
                'team_size' => count($teamMembers),
                'active_members' => $teamMembers->where('status', 'active')->count(),
                'branches_count' => $branches->count(),
                'created_at' => $company->created_at->toDateString(),
                'updated_at' => $company->updated_at->toDateString()
            ],
            'generated_at' => now()
        ];
    }

    /**
     * Get revenue breakdown
     */
    private function getRevenueBreakdown($properties): array
    {
        $breakdown = [
            'residential' => 0,
            'commercial' => 0,
            'industrial' => 0,
            'land' => 0,
            'other' => 0
        ];

        foreach ($properties as $property) {
            $type = $this->getPropertyType($property);

            if (isset($breakdown[$type])) {
                $breakdown[$type] += $property->price;
            }
        }

        return $breakdown;
    }

    /**
     * Get property type
     */
    private function getPropertyType($property): string
    {
        if ($property->type === 'residential') {
            return 'residential';
        } elseif ($property->type === 'commercial') {
            return 'commercial';
        } elseif ($property->type === 'industrial') {
            return 'industrial';
        } elseif ($property->type === 'land') {
            return 'land';
        } else {
            return 'other';
        }
    }

    /**
     * Get top performers from team performance data
     */
    private function getTopPerformers(array $teamPerformance): array
    {
        return array_slice($teamPerformance, 0, 5);
    }

    /**
     * Get bottom performers from team performance data
     */
    private function getBottomPerformers(array $teamPerformance): array
    {
        return array_slice($teamPerformance, -5, 5);
    }

    /**
     * Get productivity distribution
     */
    private function getProductivityDistribution(array $teamPerformance): array
    {
        $distribution = [
            'high' => 0,
            'medium' => 0,
            'low' => 0
        ];

        foreach ($teamPerformance as $performance) {
            $score = $performance['productivity_score'];

            if ($score >= 80) {
                $distribution['high']++;
            } elseif ($score >= 60) {
                $distribution['medium']++;
            } elseif ($score >= 40) {
                $distribution['low']++;
            }
        }

        return $distribution;
    }

    /**
     * Get member performance metrics
     */
    private function getMemberPerformance(CompanyMember $member, int $companyId): array
    {
        $memberProperties = Property::where('company_id', $companyId)
            ->where('assigned_agent_id', $member->user_id)
            ->whereBetween('created_at', [now()->subMonth(), now()])
            ->count();

        $memberLeads = $member->leads()
            ->whereBetween('created_at', [now()->subMonth(), now()])
            ->count();

        $memberDeals = $member->leads()
            ->whereBetween('created_at', [now()->subMonth(), now()])
            ->where('status', 'converted')
            ->sum('value');

        $memberTasks = $member->tasks()
            ->whereBetween('due_date', [now()->subMonth(), now()])
            ->count();

        $memberAppointments = $member->appointments()
            ->whereBetween('start_time', [now()->subMonth(), now()])
            ->count();

        // Calculate performance metrics
        $productivityScore = 0;

        // Properties (40%)
        if ($memberProperties > 0) {
            $productivityScore += 40;
        }

        // Leads (30%)
        if ($memberLeads > 0) {
            $productivityScore += 30;
        }

        // Deals (20%)
        if ($memberDeals > 0) {
            $productivityScore += 20;
        }

        // Tasks (10%)
        if ($memberTasks > 0) {
            $productivityScore += 10;
        }

        return [
            'member_id' => $member->id,
            'member_name' => $member->user->name,
            'member_role' => $member->role,
            'productivity_score' => $productivityScore,
            'properties_count' => $memberProperties,
            'leads_count' => $memberLeads,
            'deals_count' => $memberDeals,
            'tasks_count' => $memberTasks,
            'appointments_count' => $memberAppointments
        ];
    }


    /**
     * Get report type name in Arabic
     */
    private function getReportTypeName(): string
    {
        $types = [
            'performance' => 'أداء الأداء',
            'financial' => 'التقرير المالي',
            'properties' => 'تقرير العقارات',
            'team' => 'تقرير الفريق',
            'comprehensive' => 'تقرير شامل'
        ];

        return $this->reportType ?? 'overview';
    }

    /**
     * Generate report file
     */
    private function generateReportFile(array $data, string $format): string
    {
        $filename = "company-report-{$data['report_type']}-{$data['company']['id']}-" . date('Y-m-d') . "-" . $data['generated_at']->format('Y-m-d') . ".pdf";
        $path = "reports/company/{$filename}";

        // Generate PDF content
        $pdf = new \Dompdf\Dompdf();

        switch ($format) {
            case 'pdf':
                $this->generatePdfReport($pdf, $data);
                break;
            case 'excel':
                $this->generateExcelReport($data, $data);
                break;
            default:
                $this->generatePdfReport($pdf, $data);
        }

        Storage::disk('private')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate PDF report
     */
    private function generatePdfReport($pdf, array $data): void
    {
        $pdf->loadView('reports.company-pdf', [
            'company' => $data['company'],
            'period' => $data['period'] ?? null,
            'generated_at' => $data['generated_at']
        ]);

        $pdf->output();
    }

    /**
     * Generate Excel report
     */
    private function generateExcelReport($excel, array $data): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $this->addExcelHeader($spreadsheet);
        $this->addExcelData($spreadsheet, $data);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = "company-report-{$data['report_type']}-{$data['company']['id']}-" . date('Y-m-d') . ".xlsx";
        $path = "reports/company/{$filename}";

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        Storage::disk('private')->put($path, $content);
    }

    /**
     * Get date range for report
     */
    private function getDateRange(string $range): array
    {
        $end = now();
        $start = match ($range) {
            'today' => $end->copy()->startOfDay(),
            'week' => $end->copy()->subWeek()->startOfDay(),
            'month' => $end->copy()->subMonth()->startOfDay(),
            'quarter' => $end->copy()->subQuarter()->startOfDay(),
            'year' => $end->copy()->subYear()->startOfDay(),
            default => $end->copy()->subMonth()->startOfDay(),
        };

        return [
            'start' => $start,
            'end' => $end
        ];
    }

    /**
     * Add Excel header
     */
    private function addExcelHeader($spreadsheet): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Property Name');
        $sheet->setCellValue('B1', 'Type');
        $sheet->setCellValue('C1', 'Price');
        $sheet->setCellValue('D1', 'Status');
        $sheet->setCellValue('E1', 'Created At');
    }

    /**
     * Add Excel data
     */
    private function addExcelData($spreadsheet, array $data): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $row = 2;
        // Placeholder implementation
        if (isset($data['summaries'])) {
            foreach ($data['summaries'] as $summary) {
                // ...
            }
        }
    }
}

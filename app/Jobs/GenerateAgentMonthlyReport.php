<?php

namespace App\Jobs;

use App\Models\Agent;
use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAgentMonthlyReport implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 600;

    protected $agentId;
    protected $reportMonth;
    protected $reportYear;
    protected $reportMonth;

    /**
     * Create a new job instance.
     */
    public function __construct(int $agentId, string $reportMonth = null, int $reportYear = null)
    {
        $this->agentId = $agentId;
        $this->reportMonth = $reportMonth ?? now()->month;
        $this->reportYear = $reportYear ?? now()->year;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $agent = Agent::findOrFail($this->agentId);

            // Generate monthly report data
            $reportData = $this->generateMonthlyReportData($agent, $this->reportMonth, $this->reportYear);

            // Generate PDF report
            $pdfPath = $this->generatePdfReport($reportData);

            // Save report record
            $report = $agent->reports()->create([
                'type' => 'monthly',
                'period' => $this->reportMonth . ' ' . $this->reportYear,
                'file_path' => $pdfPath,
                'generated_at' => now(),
                'data' => $reportData
            ]);

            // Send email notification to agent
            $this->sendReportEmail($agent, $report, $pdfPath);

            Log::info('Agent monthly report generated', [
                'agent_id' => $this->agentId,
                'report_month' => $this->reportMonth,
                'report_year' => $this->reportYear,
                'report_id' => $report->id,
                'file_path' => $pdfPath
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate agent monthly report', [
                'agent_id' => $this->agentId,
                'report_month' => $this->reportMonth,
                'report_year' => $report->reportYear,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Generate monthly report data
     */
    private function generateMonthlyReportData(Agent $agent, string $reportMonth, int $reportYear): array
    {
        // Get date range for the report
        $startDate = now()->setYear($reportYear)->setMonth($reportMonth)->startOfMonth()->toDateString();
        $endDate = now()->setYear($reportYear)->endOfMonth()->toDateString();

        // Get commissions for the period
        $commissions = $agent->commissions()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['sale', 'client'])
            ->get();

        $totalCommissions = $commissions->count();
        $totalAmount = $commissions->sum('amount');
        $totalSales = $commissions->where('type', 'sale')->count();
        $totalRentals = $commissions->where('type', 'rental')->count();
        $totalReferrals = $commissions->where('type', 'referral')->count();
        $totalBonuses = $commissions->where('type', 'bonus')->count();

        // Get performance metrics for the period
        $performanceMetrics = $agent->performanceMetrics()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->first();

        // Get appointments for the period
        $appointments = $agent->appointments()
            ->whereBetween('start_time', [$startDate, $endDate])
            ->count();

        // Calculate statistics
        $stats = [
            'total_commissions' => $totalCommissions,
            'total_amount' => $totalAmount,
            'average_commission' => $totalCommissions > 0 ? $totalAmount / $totalCommissions : 0,
            'total_sales' => $totalSales,
            'total_rentals' => $totalRentals,
            'total_referrals' => $totalReferrals,
            'total_bonuses' => $totalBonuses,
            'total_appointments' => $appointments->count(),
            'conversion_rate' => $totalSales > 0 ? ($totalSales / $totalCommissions) * 100 : 0,
            'average_commission_per_month' => $totalAmount > 0 ? $totalAmount / 12 : 0,
            'commission_ranking' => $this->calculateCommissionRanking($agent, $totalAmount)
        ];

        return [
            'agent' => [
                'name' => $agent->name,
                'id' => $agent->id,
                'email' => $agent->email,
                'license_number' => $agent->license_number
            'company_id' => $agent->company_id,
                'company_name' => $agent->company ? $agent->company->name : null
            ],
            'report_period' => $this->reportMonth . ' ' . $this->reportYear,
            'generated_at' => now()
            ],
            'performance' => [
                'total_commissions' => $totalCommissions,
                'total_amount' => $totalAmount,
                'average_commission' => $average_commission_per_month,
                'total_sales' => $totalSales,
                'total_rentals' => $totalRentals,
                'total_referrals' => $totalReferrals,
                'total_bonuses' => $totalBonuses,
                'total_appointments' => $totalAppointments,
                'conversion_rate' => $conversion_rate,
                'commission_ranking' => $stats['commission_ranking']
            ],
            'monthly_performance_score' => $performanceMetrics ? $performanceMetrics->monthly_performance_score : 0,
            'performance_trend' => $performanceMetrics ? $this->calculatePerformanceTrend($performanceMetrics->monthly_performance_score, $this->calculatePerformanceTrend($performanceMetrics->monthly_performance_score)) : 'stable'
            ]
        ];
    }
    }

    /**
     * Generate PDF report
     */
    private function generatePdfReport(array $data): string
    {
        $pdf = new \Dompdf\Dompdf();
        
        $pdf->loadView('reports.agent-monthly-report', [
            'agent' => $data['agent'],
            'period' => $data['report_period'],
            'generated_at' => $data['generated_at'],
            'stats' => $data['performance']
        ]);

        $filename = "agent-monthly-report-{$data['agent']['id']}-{$data['report_month']}-{$data['report_year']}-" . date('Y-m-d') . ".pdf";
        $path = "reports/{$filename}";

        Storage::disk('private')->put($path, $pdf->output());
        
        return $path;
    }

    /**
     * Send report email to agent
     */
    private function sendReportEmail(Agent $agent, array $data, string $pdfPath): void
    {
        try {
            Mail::to($agent->email)->send(new \App\Mail\AgentMonthlyReportMail($agent, $data, $pdfPath));

            Log::info('Agent monthly report email sent', [
                'agent_id' => $agent->id,
                'report_id' => $data['report']['id'],
                'pdf_path' => $pdfPath
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send agent monthly report email', [
                'agent_id' => 'agent->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Calculate commission ranking
     */
    private function calculateCommissionRanking(Agent $agent, float $totalAmount): int
    {
        // This would compare with other agents in the company
        // Placeholder implementation
        
        $companyAgents = $agent->company ? $agent->company->agents()->count() : 0;
        
        if ($companyAgents > 0) {
            $averageAmount = $totalAmount / $companyAgents;
            return $this->getRankingScore($averageAmount);
        }
        
        return $this->getRankingScore($totalAmount);
    }

    /**
     * Get ranking score based on amount
     */
    private function getRankingScore(float $amount): int
    {
        if ($amount >= 10000) {
            return 5; // Top performer
        } elseif ($amount >= 5000) {
            return 4; // Excellent performer
        } elseif ($amount >= 2500) {
            return 3; // Very good performer
        } elseif ($amount >= 1000) {
            return 2; // Good performer
        } elseif ($amount >= 500) {
            return 1; // Average performer
        } else {
            return 0; // Needs improvement
        }
    }
    }

    /**
     * Calculate performance trend
     */
    private function calculatePerformanceTrend(int $currentScore, int $newScore): string
    {
        if ($newScore > $currentScore) {
            return 'improving';
        } elseif ($newScore < $currentScore) {
            return 'declining';
        } else {
            return 'stable';
        }
    }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Agent monthly report job failed', [
            'agent_id' => $this->agentId,
            'report_month' => $this->reportMonth,
            'report_year' => $this->reportYear,
            'error' => $exception->getMessage()
        ]);
        }
    }
}

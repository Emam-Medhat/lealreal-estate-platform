<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserWallet;
use App\Models\KycVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateUserReport implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 600;

    protected $userId;
    protected $reportType;
    protected $filters;
    protected $requestedBy;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $reportType, array $filters = [], int $requestedBy = null)
    {
        $this->userId = $userId;
        $this->reportType = $reportType;
        $this->filters = $filters;
        $this->requestedBy = $requestedBy;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::with([
            'profile',
            'wallet',
            'kycVerification',
            'activities',
            'notifications',
            'favorites',
            'comparisons'
        ])->find($this->userId);
        
        if (!$user) {
            Log::error('User not found for report generation', ['user_id' => $this->userId]);
            return;
        }
        
        try {
            $reportData = $this->generateReportData($user);
            
            // Generate PDF report
            $pdfPath = $this->generatePdfReport($reportData);
            
            // Generate Excel report if requested
            $excelPath = null;
            if ($this->reportType === 'full' || $this->reportType === 'financial') {
                $excelPath = $this->generateExcelReport($reportData);
            }
            
            // Save report record
            $report = $user->reports()->create([
                'type' => $this->reportType,
                'status' => 'completed',
                'file_path' => $pdfPath,
                'excel_path' => $excelPath,
                'filters' => $this->filters,
                'requested_by' => $this->requestedBy,
                'generated_at' => now()
            ]);
            
            // Send notification to user
            $user->notifications()->create([
                'title' => 'تقريرك جاهز',
                'message' => "تم إنشاء تقرير {$this->getReportTypeName()} بنجاح",
                'type' => 'report_ready',
                'data' => [
                    'report_id' => $report->id,
                    'report_type' => $this->reportType,
                    'download_url' => route('reports.download', $report->id)
                ]
            ]);
            
            Log::info('User report generated successfully', [
                'user_id' => $this->userId,
                'report_type' => $this->reportType,
                'report_id' => $report->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to generate user report', [
                'user_id' => $this->userId,
                'report_type' => $this->reportType,
                'error' => $e->getMessage()
            ]);
            
            // Update report status to failed
            $user->reports()->create([
                'type' => $this->reportType,
                'status' => 'failed',
                'filters' => $this->filters,
                'requested_by' => $this->requestedBy,
                'error_message' => $e->getMessage(),
                'generated_at' => now()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Generate report data
     */
    private function generateReportData(User $user): array
    {
        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'status' => $user->status,
                'created_at' => $user->created_at,
                'last_activity_at' => $user->last_activity_at,
                'profile_completion' => $user->profile_completion_percentage,
                'kyc_verified' => $user->kyc_verified,
                'kyc_level' => $user->kyc_level
            ],
            'generated_at' => now()->toDateTimeString(),
            'report_type' => $this->reportType
        ];
        
        // Add profile data
        if ($user->profile) {
            $data['profile'] = $user->profile->toArray();
        }
        
        // Add wallet data for financial reports
        if ($user->wallet && in_array($this->reportType, ['financial', 'full'])) {
            $data['wallet'] = [
                'balance' => $user->wallet->balance,
                'available_balance' => $user->wallet->available_balance,
                'frozen_balance' => $user->wallet->frozen_balance,
                'currency' => $user->wallet->currency,
                'transactions_summary' => $this->getWalletTransactionsSummary($user)
            ];
        }
        
        // Add KYC data for compliance reports
        if ($user->kycVerification && in_array($this->reportType, ['compliance', 'full'])) {
            $data['kyc'] = $user->kycVerification->toArray();
        }
        
        // Add activity data
        if (in_array($this->reportType, ['activity', 'full'])) {
            $data['activities'] = $this->getFilteredActivities($user);
        }
        
        // Add favorites and comparisons
        if (in_array($this->reportType, ['engagement', 'full'])) {
            $data['engagement'] = [
                'favorites_count' => $user->favorites()->count(),
                'comparisons_count' => $user->comparisons()->count(),
                'saved_searches_count' => $user->savedSearches()->count()
            ];
        }
        
        return $data;
    }
    
    /**
     * Generate PDF report
     */
    private function generatePdfReport(array $data): string
    {
        $pdf = new \Dompdf\Dompdf();
        
        $html = view('reports.user-pdf', $data)->render();
        $pdf->loadHtml($html);
        
        $filename = "user-report-{$this->userId}-" . time() . ".pdf";
        $path = "reports/{$filename}";
        
        Storage::disk('private')->put($path, $pdf->output());
        
        return $path;
    }
    
    /**
     * Generate Excel report
     */
    private function generateExcelReport(array $data): string
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Add headers based on report type
        $this->addExcelHeaders($sheet);
        
        // Add data rows
        $this->addExcelData($sheet, $data);
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::create($spreadsheet, 'Xlsx');
        $filename = "user-report-{$this->userId}-" . time() . ".xlsx";
        $path = "reports/{$filename}";
        
        Storage::disk('private')->put($path, $writer->save('php://output'));
        
        return $path;
    }
    
    /**
     * Get wallet transactions summary
     */
    private function getWalletTransactionsSummary(User $user): array
    {
        $transactions = $user->wallet->transactions();
        
        // Apply date filters
        if (isset($this->filters['date_from'])) {
            $transactions->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        
        if (isset($this->filters['date_to'])) {
            $transactions->whereDate('created_at', '<=', $this->filters['date_to']);
        }
        
        $transactions = $transactions->get();
        
        return [
            'total_transactions' => $transactions->count(),
            'total_deposits' => $transactions->where('amount', '>', 0)->sum('amount'),
            'total_withdrawals' => abs($transactions->where('amount', '<', 0)->sum('amount')),
            'net_change' => $transactions->sum('amount'),
            'recent_transactions' => $transactions->take(10)->toArray()
        ];
    }
    
    /**
     * Get filtered activities
     */
    private function getFilteredActivities(User $user): \Illuminate\Support\Collection
    {
        $activities = $user->activities();
        
        // Apply filters
        if (isset($this->filters['activity_type'])) {
            $activities->where('activity_type', $this->filters['activity_type']);
        }
        
        if (isset($this->filters['date_from'])) {
            $activities->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        
        if (isset($this->filters['date_to'])) {
            $activities->whereDate('created_at', '<=', $this->filters['date_to']);
        }
        
        return $activities->orderBy('created_at', 'desc')->get();
    }
    
    /**
     * Add Excel headers
     */
    private function addExcelHeaders($sheet): void
    {
        $headers = [
            'A1' => 'المعرف',
            'B1' => 'الاسم',
            'C1' => 'البريد الإلكتروني',
            'D1' => 'الهاتف',
            'E1' => 'الدور',
            'F1' => 'الحالة',
            'G1' => 'تاريخ الإنشاء',
            'H1' => 'آخر نشاط'
        ];
        
        if ($this->reportType === 'financial') {
            $headers = array_merge($headers, [
                'I1' => 'رصيد المحفظة',
                'J1' => 'الرصيد المتاح',
                'K1' => 'الرصيد المجمد'
            ]);
        }
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
    }
    
    /**
     * Add Excel data
     */
    private function addExcelData($sheet, array $data): void
    {
        $row = 2;
        
        $sheet->setCellValue('A' . $row, $data['user']['id']);
        $sheet->setCellValue('B' . $row, $data['user']['name']);
        $sheet->setCellValue('C' . $row, $data['user']['email']);
        $sheet->setCellValue('D' . $row, $data['user']['phone']);
        $sheet->setCellValue('E' . $row, $data['user']['role']);
        $sheet->setCellValue('F' . $row, $data['user']['status']);
        $sheet->setCellValue('G' . $row, $data['user']['created_at']);
        $sheet->setCellValue('H' . $row, $data['user']['last_activity_at']);
        
        if ($this->reportType === 'financial' && isset($data['wallet'])) {
            $sheet->setCellValue('I' . $row, $data['wallet']['balance']);
            $sheet->setCellValue('J' . $row, $data['wallet']['available_balance']);
            $sheet->setCellValue('K' . $row, $data['wallet']['frozen_balance']);
        }
    }
    
    /**
     * Get report type name in Arabic
     */
    private function getReportTypeName(): string
    {
        $types = [
            'full' => 'شامل',
            'profile' => 'الملف الشخصي',
            'financial' => 'المالي',
            'activity' => 'النشاط',
            'compliance' => 'الامتثال',
            'engagement' => 'التفاعل'
        ];
        
        return $types[$this->reportType] ?? $this->reportType;
    }
    
    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('User report generation job failed', [
            'user_id' => $this->userId,
            'report_type' => $this->reportType,
            'error' => $exception->getMessage()
        ]);
    }
}

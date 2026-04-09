<?php

namespace App\Jobs;

use App\Services\LeadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ExportLeadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    protected $filters;
    protected $format;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param array $filters
     * @param string $format
     * @param int $userId
     */
    public function __construct(array $filters, string $format, int $userId)
    {
        $this->filters = $filters;
        $this->format = $format;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LeadService $leadService): void
    {
        try {
            $filename = "leads_export_{$this->format}_" . now()->format('Y-m-d_H-i-s') . ".{$this->format}";
            $path = "exports/{$filename}";

            // Create export file
            $this->createExportFile($leadService, $path);

            // Notify user (you can implement notification system)
            $this->notifyUser($filename, $path);

            Log::info("Leads export completed", [
                'format' => $this->format,
                'filename' => $filename,
                'user_id' => $this->userId,
            ]);

        } catch (\Exception $e) {
            Log::error("Leads export failed", [
                'error' => $e->getMessage(),
                'format' => $this->format,
                'user_id' => $this->userId,
            ]);

            $this->fail($e);
        }
    }

    /**
     * Create export file
     *
     * @param LeadService $leadService
     * @param string $path
     * @return void
     */
    private function createExportFile(LeadService $leadService, string $path): void
    {
        $generator = $leadService->getLeadsForExport($this->filters);

        switch ($this->format) {
            case 'csv':
                $this->createCsvExport($generator, $path);
                break;
            case 'excel':
                $this->createExcelExport($generator, $path);
                break;
            case 'json':
                $this->createJsonExport($generator, $path);
                break;
            default:
                throw new \InvalidArgumentException("Unsupported format: {$this->format}");
        }
    }

    /**
     * Create CSV export
     *
     * @param \Generator $generator
     * @param string $path
     * @return void
     */
    private function createCsvExport(\Generator $generator, string $path): void
    {
        $handle = fopen('php://temp', 'w+');

        // CSV header
        fputcsv($handle, [
            'ID', 'UUID', 'First Name', 'Last Name', 'Email', 'Phone', 'Company',
            'Status', 'Priority', 'Source', 'Assigned To', 'Created At', 'Updated At',
            'Estimated Value', 'Notes'
        ]);

        // CSV data
        foreach ($generator as $chunk) {
            foreach ($chunk as $lead) {
                fputcsv($handle, [
                    $lead->id,
                    $lead->uuid,
                    $lead->first_name,
                    $lead->last_name,
                    $lead->email,
                    $lead->phone,
                    $lead->company,
                    $lead->lead_status,
                    $lead->priority,
                    $lead->lead_source,
                    $lead->assignedTo?->full_name ?? 'Unassigned',
                    $lead->created_at->format('Y-m-d H:i:s'),
                    $lead->updated_at->format('Y-m-d H:i:s'),
                    $lead->estimated_value,
                    $lead->notes,
                ]);
            }
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        Storage::put($path, $csvContent);
    }

    /**
     * Create Excel export
     *
     * @param \Generator $generator
     * @param string $path
     * @return void
     */
    private function createExcelExport(\Generator $generator, string $path): void
    {
        // For now, create a CSV file with .xlsx extension
        // In production, you would use Laravel Excel or similar package
        $this->createCsvExport($generator, $path);
        
        // Rename to .xlsx (placeholder for actual Excel implementation)
        Storage::move($path, str_replace('.csv', '.xlsx', $path));
    }

    /**
     * Create JSON export
     *
     * @param \Generator $generator
     * @param string $path
     * @return void
     */
    private function createJsonExport(\Generator $generator, string $path): void
    {
        $data = [
            'export_info' => [
                'format' => 'json',
                'exported_at' => now()->toISOString(),
                'filters' => $this->filters,
                'total_count' => 0,
            ],
            'leads' => []
        ];

        foreach ($generator as $chunk) {
            foreach ($chunk as $lead) {
                $data['leads'][] = [
                    'id' => $lead->id,
                    'uuid' => $lead->uuid,
                    'first_name' => $lead->first_name,
                    'last_name' => $lead->last_name,
                    'full_name' => $lead->first_name . ' ' . $lead->last_name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'company' => $lead->company,
                    'job_title' => $lead->job_title,
                    'lead_status' => $lead->lead_status,
                    'priority' => $lead->priority,
                    'lead_source' => $lead->lead_source,
                    'assigned_to' => $lead->assignedTo ? [
                        'id' => $lead->assignedTo->id,
                        'name' => $lead->assignedTo->full_name,
                        'email' => $lead->assignedTo->email,
                    ] : null,
                    'estimated_value' => $lead->estimated_value,
                    'notes' => $lead->notes,
                    'created_at' => $lead->created_at->toISOString(),
                    'updated_at' => $lead->updated_at->toISOString(),
                    'created_by' => $lead->created_by,
                ];
                
                $data['export_info']['total_count']++;
            }
        }

        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Notify user about export completion
     *
     * @param string $filename
     * @param string $path
     * @return void
     */
    private function notifyUser(string $filename, string $path): void
    {
        // You can implement notification system here
        // For example: email notification, database notification, etc.
        
        $downloadUrl = Storage::url($path);
        
        // Log notification (replace with actual notification system)
        Log::info("Export ready for user {$this->userId}", [
            'filename' => $filename,
            'download_url' => $downloadUrl,
        ]);
    }

    /**
     * The job failed to process.
     *
     * @param \Exception $exception
     * @return void
     */
    public function failed(\Exception $exception): void
    {
        Log::error("ExportLeadsJob failed", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => $this->userId,
            'format' => $this->format,
        ]);

        // Notify user about failure
        // Implement your notification system here
    }
}

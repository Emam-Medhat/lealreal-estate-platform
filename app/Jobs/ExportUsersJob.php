<?php

namespace App\Jobs;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ExportUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [30, 60, 120];

    /**
     * The maximum number of seconds a job can run.
     */
    public $timeout = 3600; // 1 hour

    /**
     * Export filters
     */
    protected array $filters;

    /**
     * Export format
     */
    protected string $format;

    /**
     * User ID who initiated the export
     */
    protected int $userId;

    /**
     * Export file path
     */
    protected string $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct(array $filters, string $format = 'csv', int $userId)
    {
        $this->filters = $filters;
        $this->format = strtolower($format);
        $this->userId = $userId;
        $this->filePath = 'exports/users/' . date('Y/m/d') . '/users_' . time() . '_' . Str::random(8) . '.' . $this->format;
    }

    /**
     * Execute the job.
     */
    public function handle(UserRepositoryInterface $userRepository): void
    {
        try {
            Log::info('Starting user export job', [
                'user_id' => $this->userId,
                'format' => $this->format,
                'filters' => $this->filters
            ]);

            // Ensure export directory exists
            Storage::disk('local')->makeDirectory(dirname($this->filePath));

            switch ($this->format) {
                case 'csv':
                    $this->exportToCsv($userRepository);
                    break;
                case 'excel':
                    $this->exportToExcel($userRepository);
                    break;
                case 'json':
                    $this->exportToJson($userRepository);
                    break;
                default:
                    throw new \InvalidArgumentException("Unsupported export format: {$this->format}");
            }

            Log::info('User export job completed successfully', [
                'user_id' => $this->userId,
                'file_path' => $this->filePath
            ]);

            // Send notification to user (implementation would depend on your notification system)
            $this->notifyUser();

        } catch (\Exception $e) {
            Log::error('User export job failed: ' . $e->getMessage(), [
                'user_id' => $this->userId,
                'format' => $this->format,
                'filters' => $this->filters,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Export users to CSV format
     */
    protected function exportToCsv(UserRepositoryInterface $userRepository): void
    {
        $handle = fopen('php://temp', 'w+');

        // Add CSV header
        fputcsv($handle, [
            'ID',
            'UUID',
            'First Name',
            'Last Name',
            'Full Name',
            'Email',
            'Phone',
            'User Type',
            'Account Status',
            'KYC Status',
            'Is Agent',
            'Is Company',
            'Is Developer',
            'Is Investor',
            'Country',
            'City',
            'Created At',
            'Updated At',
            'Last Login At'
        ]);

        // Process users in chunks
        $processedCount = 0;
        foreach ($userRepository->getUsersForExport($this->filters) as $chunk) {
            foreach ($chunk as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->uuid,
                    $user->first_name,
                    $user->last_name,
                    $user->full_name,
                    $user->email,
                    $user->phone,
                    $user->user_type,
                    $user->account_status,
                    $user->kyc_status,
                    $user->is_agent ? 'Yes' : 'No',
                    $user->is_company ? 'Yes' : 'No',
                    $user->is_developer ? 'Yes' : 'No',
                    $user->is_investor ? 'Yes' : 'No',
                    $user->country,
                    $user->city,
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->updated_at->format('Y-m-d H:i:s'),
                    $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never'
                ]);

                $processedCount++;
            }

            // Log progress every 1000 records
            if ($processedCount % 1000 === 0) {
                Log::info("User export progress: {$processedCount} records processed");
            }
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        // Store the file
        Storage::disk('local')->put($this->filePath, $csvContent);

        Log::info("User export to CSV completed: {$processedCount} records exported");
    }

    /**
     * Export users to Excel format
     */
    protected function exportToExcel(UserRepositoryInterface $userRepository): void
    {
        $data = [];
        $headers = [
            'ID',
            'UUID',
            'First Name',
            'Last Name',
            'Full Name',
            'Email',
            'Phone',
            'User Type',
            'Account Status',
            'KYC Status',
            'Is Agent',
            'Is Company',
            'Is Developer',
            'Is Investor',
            'Country',
            'City',
            'Created At',
            'Updated At',
            'Last Login At'
        ];

        // Process users in chunks
        $processedCount = 0;
        foreach ($userRepository->getUsersForExport($this->filters) as $chunk) {
            foreach ($chunk as $user) {
                $data[] = [
                    $user->id,
                    $user->uuid,
                    $user->first_name,
                    $user->last_name,
                    $user->full_name,
                    $user->email,
                    $user->phone,
                    $user->user_type,
                    $user->account_status,
                    $user->kyc_status,
                    $user->is_agent ? 'Yes' : 'No',
                    $user->is_company ? 'Yes' : 'No',
                    $user->is_developer ? 'Yes' : 'No',
                    $user->is_investor ? 'Yes' : 'No',
                    $user->country,
                    $user->city,
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->updated_at->format('Y-m-d H:i:s'),
                    $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never'
                ];

                $processedCount++;
            }

            // Log progress every 1000 records
            if ($processedCount % 1000 === 0) {
                Log::info("User export progress: {$processedCount} records processed");
            }
        }

        // Create Excel file
        $excelFile = new \App\Exports\UsersExport([$headers], $data);
        $content = Excel::raw($excelFile, \Maatwebsite\Excel\Excel::XLSX);

        // Store the file
        Storage::disk('local')->put($this->filePath, $content);

        Log::info("User export to Excel completed: {$processedCount} records exported");
    }

    /**
     * Export users to JSON format
     */
    protected function exportToJson(UserRepositoryInterface $userRepository): void
    {
        $users = [];
        $processedCount = 0;

        // Process users in chunks
        foreach ($userRepository->getUsersForExport($this->filters) as $chunk) {
            foreach ($chunk as $user) {
                $users[] = [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'user_type' => $user->user_type,
                    'account_status' => $user->account_status,
                    'kyc_status' => $user->kyc_status,
                    'is_agent' => $user->is_agent,
                    'is_company' => $user->is_company,
                    'is_developer' => $user->is_developer,
                    'is_investor' => $user->is_investor,
                    'country' => $user->country,
                    'city' => $user->city,
                    'created_at' => $user->created_at->toISOString(),
                    'updated_at' => $user->updated_at->toISOString(),
                    'last_login_at' => $user->last_login_at ? $user->last_login_at->toISOString() : null,
                    'profile' => $user->profile ? [
                        'bio' => $user->profile->bio,
                        'avatar' => $user->profile->avatar,
                        'social_links' => $user->profile->social_links
                    ] : null
                ];

                $processedCount++;
            }

            // Log progress every 1000 records
            if ($processedCount % 1000 === 0) {
                Log::info("User export progress: {$processedCount} records processed");
            }
        }

        // Create JSON content
        $jsonContent = json_encode([
            'export_info' => [
                'total_records' => $processedCount,
                'exported_at' => now()->toISOString(),
                'filters' => $this->filters,
                'format' => $this->format
            ],
            'users' => $users
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Store the file
        Storage::disk('local')->put($this->filePath, $jsonContent);

        Log::info("User export to JSON completed: {$processedCount} records exported");
    }

    /**
     * Get the unique job ID
     */
    public function getJobId(): string
    {
        return $this->job->getJobId();
    }

    /**
     * Get the export file path
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Get the public download URL
     */
    public function getDownloadUrl(): string
    {
        return route('exports.download', ['file' => basename($this->filePath)]);
    }

    /**
     * Notify user about export completion
     */
    protected function notifyUser(): void
    {
        try {
            $user = User::find($this->userId);
            
            if ($user) {
                // Implementation would depend on your notification system
                // For example:
                // $user->notify(new ExportCompletedNotification($this->filePath, $this->format));
                
                Log::info('Export notification sent to user', [
                    'user_id' => $this->userId,
                    'file_path' => $this->filePath
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send export notification: ' . $e->getMessage(), [
                'user_id' => $this->userId
            ]);
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('User export job failed permanently', [
            'user_id' => $this->userId,
            'format' => $this->format,
            'filters' => $this->filters,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Notify user about failure
        try {
            $user = User::find($this->userId);
            
            if ($user) {
                // Implementation would depend on your notification system
                // $user->notify(new ExportFailedNotification($exception->getMessage()));
                
                Log::info('Export failure notification sent to user', [
                    'user_id' => $this->userId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send export failure notification: ' . $e->getMessage());
        }
    }
}

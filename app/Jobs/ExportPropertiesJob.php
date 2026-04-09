<?php

namespace App\Jobs;

use App\Services\PropertyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ExportPropertiesJob implements ShouldQueue
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
     * Get the job ID.
     *
     * @return string|null
     */
    public function getJobId(): ?string
    {
        return $this->job ? $this->job->getJobId() : null;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PropertyService $propertyService): void
    {
        try {
            $filename = "properties_export_{$this->format}_" . now()->format('Y-m-d_H-i-s') . ".{$this->format}";
            $path = "exports/{$filename}";

            // Create export file
            $this->createExportFile($propertyService, $path);

            // Notify user (you can implement notification system)
            $this->notifyUser($filename, $path);

            Log::info("Properties export completed", [
                'format' => $this->format,
                'filename' => $filename,
                'user_id' => $this->userId,
            ]);

        } catch (\Exception $e) {
            Log::error("Properties export failed", [
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
     * @param PropertyService $propertyService
     * @param string $path
     * @return void
     */
    private function createExportFile(PropertyService $propertyService, string $path): void
    {
        $generator = $propertyService->getPropertiesForExport($this->filters);

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
            'ID',
            'Title',
            'Type',
            'Price',
            'Area',
            'Bedrooms',
            'Bathrooms',
            'Address',
            'City',
            'State',
            'Country',
            'Latitude',
            'Longitude',
            'Status',
            'Featured',
            'Agent',
            'Created At',
            'Updated At',
            'Views Count',
            'Inquiries Count'
        ]);

        // CSV data
        foreach ($generator as $chunk) {
            foreach ($chunk as $property) {
                fputcsv($handle, [
                    $property->id,
                    $property->title,
                    $property->type,
                    $property->price,
                    $property->area,
                    $property->bedrooms,
                    $property->bathrooms,
                    $property->address,
                    $property->city,
                    $property->state,
                    $property->country,
                    $property->latitude,
                    $property->longitude,
                    $property->status,
                    $property->featured ? 'Yes' : 'No',
                    $property->agent?->full_name ?? 'N/A',
                    $property->created_at->format('Y-m-d H:i:s'),
                    $property->updated_at->format('Y-m-d H:i:s'),
                    $property->views_count ?? 0,
                    $property->inquiries_count ?? 0,
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
            'properties' => []
        ];

        foreach ($generator as $chunk) {
            foreach ($chunk as $property) {
                $data['properties'][] = [
                    'id' => $property->id,
                    'title' => $property->title,
                    'slug' => $property->slug,
                    'type' => $property->type,
                    'price' => $property->price,
                    'area' => $property->area,
                    'bedrooms' => $property->bedrooms,
                    'bathrooms' => $property->bathrooms,
                    'address' => $property->address,
                    'city' => $property->city,
                    'state' => $property->state,
                    'country' => $property->country,
                    'latitude' => $property->latitude,
                    'longitude' => $property->longitude,
                    'status' => $property->status,
                    'featured' => $property->featured,
                    'featured_at' => $property->featured_at?->toISOString(),
                    'agent' => $property->agent ? [
                        'id' => $property->agent->id,
                        'name' => $property->agent->full_name,
                        'email' => $property->agent->email,
                    ] : null,
                    'images' => $property->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->url,
                            'title' => $image->title,
                            'sort_order' => $image->sort_order,
                        ];
                    }),
                    'amenities' => $property->amenities ?? [],
                    'features' => $property->features ?? [],
                    'nearby_facilities' => $property->nearby_facilities ?? [],
                    'views_count' => $property->views_count ?? 0,
                    'inquiries_count' => $property->inquiries_count ?? 0,
                    'created_at' => $property->created_at->toISOString(),
                    'updated_at' => $property->updated_at->toISOString(),
                    'created_by' => $property->created_by,
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
        Log::error("ExportPropertiesJob failed", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => $this->userId,
            'format' => $this->format,
        ]);

        // Notify user about failure
        // Implement your notification system here
    }
}

<?php

namespace App\Jobs\Fraud;

use App\Models\Property;
use App\Models\User;
use App\Services\PropertyFraudDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * AnalyzePropertyFraudJob
 *
 * Queued job that runs the full fraud detection pipeline on a property.
 * Dispatched asynchronously when a property is created or updated.
 */
class AnalyzePropertyFraudJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries;
    public int $timeout;
    public int $backoff = 30; // seconds between retries

    public function __construct(
        public readonly int $propertyId,
        public readonly ?int $userId = null,
        public readonly string $ipAddress = '',
    ) {
        $queueConfig = config('fraud_detection.queue');
        $this->tries = $queueConfig['tries'] ?? 3;
        $this->timeout = $queueConfig['timeout'] ?? 120;
        $this->onQueue($queueConfig['queue_name'] ?? 'fraud-detection');
        $this->onConnection($queueConfig['connection'] ?? 'database');
    }

    public function handle(PropertyFraudDetectionService $service): void
    {
        $property = Property::find($this->propertyId);
        if (!$property) {
            Log::warning("AnalyzePropertyFraudJob: Property #{$this->propertyId} not found");
            return;
        }

        $user = $this->userId ? User::find($this->userId) : null;

        Log::info("AnalyzePropertyFraudJob: Starting analysis for property #{$this->propertyId}");

        $result = $service->analyzeProperty($property, $user, $this->ipAddress);

        Log::info("AnalyzePropertyFraudJob: Completed", [
            'property_id' => $this->propertyId,
            'score' => $result['total_fraud_score'],
            'status' => $result['final_status'],
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("AnalyzePropertyFraudJob: Failed for property #{$this->propertyId}", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Unique job key to prevent duplicate jobs for the same property.
     */
    public function uniqueId(): string
    {
        return "property_fraud_{$this->propertyId}";
    }
}

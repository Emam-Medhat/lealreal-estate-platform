<?php

namespace App\Jobs\Fraud;

use App\Models\User;
use App\Services\FakeAccountDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * AnalyzeUserFraudJob
 *
 * Queued job that runs fake account detection on a user.
 */
class AnalyzeUserFraudJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    public int $backoff = 20;

    public function __construct(
        public readonly int $userId,
        public readonly string $ipAddress = '',
    ) {
        $queueConfig = config('fraud_detection.queue');
        $this->onQueue($queueConfig['queue_name'] ?? 'fraud-detection');
        $this->onConnection($queueConfig['connection'] ?? 'database');
    }

    public function handle(FakeAccountDetectionService $service): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            Log::warning("AnalyzeUserFraudJob: User #{$this->userId} not found");
            return;
        }

        Log::info("AnalyzeUserFraudJob: Starting analysis for user #{$this->userId}");

        $result = $service->analyzeUser($user, $this->ipAddress);

        Log::info("AnalyzeUserFraudJob: Completed", [
            'user_id' => $this->userId,
            'risk_score' => $result['risk_score'],
            'action' => $result['action'],
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("AnalyzeUserFraudJob: Failed for user #{$this->userId}", [
            'error' => $exception->getMessage(),
        ]);
    }

    public function uniqueId(): string
    {
        return "user_fraud_{$this->userId}";
    }
}

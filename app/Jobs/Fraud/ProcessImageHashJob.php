<?php

namespace App\Jobs\Fraud;

use App\Models\PropertyMedia;
use App\Services\PropertyFraudDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * ProcessImageHashJob
 *
 * Processes a single uploaded image:
 *  1. Generates perceptual hash
 *  2. Checks against existing hashes for duplicates
 *  3. Updates fraud scores accordingly
 */
class ProcessImageHashJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly int $mediaId,
    ) {
        $queueConfig = config('fraud_detection.queue');
        $this->onQueue($queueConfig['queue_name'] ?? 'fraud-detection');
        $this->onConnection($queueConfig['connection'] ?? 'database');
    }

    public function handle(PropertyFraudDetectionService $service): void
    {
        $media = PropertyMedia::find($this->mediaId);

        if (!$media) {
            Log::warning("ProcessImageHashJob: Media #{$this->mediaId} not found");
            return;
        }

        if ($media->media_type !== 'image') {
            return; // Only process images
        }

        Log::info("ProcessImageHashJob: Processing media #{$this->mediaId}");

        $result = $service->processUploadedImage($media);

        Log::info("ProcessImageHashJob: Completed", [
            'media_id' => $this->mediaId,
            'hash' => $result['hash'] ? substr($result['hash'], 0, 8) . '...' : null,
            'duplicates' => count($result['duplicates']),
            'fraud_score' => $result['fraud_score'],
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessImageHashJob: Failed for media #{$this->mediaId}", [
            'error' => $exception->getMessage(),
        ]);
    }
}

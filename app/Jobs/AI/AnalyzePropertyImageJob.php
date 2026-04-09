<?php

namespace App\Jobs\AI;

use App\Services\AI\ImageAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyzePropertyImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;
    public int $backoff = 30;

    public function __construct(
        private readonly int $recordId,
        private readonly string $imagePath
    ) {
    }

    public function handle(ImageAnalysisService $service): void
    {
        DB::table('property_image_analyses')
            ->where('id', $this->recordId)
            ->update(['status' => 'processing', 'updated_at' => now()]);

        try {
            $result = $service->analyze($this->imagePath);

            DB::table('property_image_analyses')
                ->where('id', $this->recordId)
                ->update([
                    'status' => $result['success'] ? 'completed' : 'failed',
                    'room_type' => $result['room_type'] ?? null,
                    'furniture_quality' => $result['furniture_quality'] ?? null,
                    'brightness_level' => $result['brightness_level'] ?? null,
                    'quality_score' => $result['quality_score'] ?? null,
                    'issues' => json_encode($result['issues'] ?? []),
                    'source' => $result['source'] ?? null,
                    'updated_at' => now(),
                ]);

        } catch (\Throwable $e) {
            Log::error('AnalyzePropertyImageJob failed', [
                'record_id' => $this->recordId,
                'error' => $e->getMessage(),
            ]);

            DB::table('property_image_analyses')
                ->where('id', $this->recordId)
                ->update(['status' => 'failed', 'updated_at' => now()]);

            throw $e; // trigger retry
        }
    }

    public function failed(\Throwable $e): void
    {
        DB::table('property_image_analyses')
            ->where('id', $this->recordId)
            ->update(['status' => 'failed', 'updated_at' => now()]);
    }
}

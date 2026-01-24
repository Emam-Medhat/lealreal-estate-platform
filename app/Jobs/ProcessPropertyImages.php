<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\PropertyMediaService;

class ProcessPropertyImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $propertyId;
    protected $imagePaths;

    /**
     * Create a new job instance.
     */
    public function __construct($propertyId, array $imagePaths)
    {
        $this->propertyId = $propertyId;
        $this->imagePaths = $imagePaths;
    }

    /**
     * Execute the job.
     */
    public function handle(PropertyMediaService $mediaService): void
    {
        // $mediaService->processImages($this->propertyId, $this->imagePaths);
        // Placeholder Logic
    }
}

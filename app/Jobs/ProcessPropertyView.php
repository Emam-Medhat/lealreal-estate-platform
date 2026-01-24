<?php

namespace App\Jobs;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessPropertyView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $propertyId;
    public string $userAgent;
    public string $ipAddress;
    public ?int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $propertyId, string $userAgent, string $ipAddress, ?int $userId = null)
    {
        $this->propertyId = $propertyId;
        $this->userAgent = $userAgent;
        $this->ipAddress = $ipAddress;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function execute(): void
    {
        // Increment view count
        Property::where('id', $this->propertyId)->increment('views_count');

        // Clear relevant caches
        Cache::forget("property_details_{$this->propertyId}");
        Cache::tags(['properties'])->flush();

        // Log the view for analytics (optional)
        $this->logView();
    }

    /**
     * Log the view for analytics
     */
    private function logView(): void
    {
        // You can implement analytics logging here
        // For example, store in a separate views table or send to analytics service
    }
}

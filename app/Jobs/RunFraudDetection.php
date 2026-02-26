<?php

namespace App\Jobs;

use App\Models\Property;
use App\Services\Intelligence\FraudDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Queue job: run fraud detection for a batch of properties.
 */
class RunFraudDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $propertyIds;
    public array $contextByProperty;

    public function __construct(array $propertyIds, array $contextByProperty = [])
    {
        $this->propertyIds = $propertyIds;
        $this->contextByProperty = $contextByProperty;
        $this->onQueue('intelligence');
    }

    public function handle(FraudDetectionService $service): void
    {
        $props = Property::whereIn('id', $this->propertyIds)->get();
        foreach ($props as $p) {
            $ctx = $this->contextByProperty[$p->id] ?? [];
            $result = $service->evaluate($p, $ctx);
            $service->persist($p, $result);
        }
    }
}


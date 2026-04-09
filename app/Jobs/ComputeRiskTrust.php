<?php

namespace App\Jobs;

use App\Models\Property;
use App\Services\Intelligence\RiskTrustService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Queue job: compute trust index/level for a batch of properties.
 */
class ComputeRiskTrust implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $propertyIds;

    public function __construct(array $propertyIds)
    {
        $this->propertyIds = $propertyIds;
        $this->onQueue('intelligence');
    }

    public function handle(RiskTrustService $service): void
    {
        $props = Property::whereIn('id', $this->propertyIds)->get();
        foreach ($props as $p) {
            $trust = $service->compute($p);
            $service->persist($p, $trust);
        }
    }
}

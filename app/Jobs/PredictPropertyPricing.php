<?php

namespace App\Jobs;

use App\Models\Property;
use App\Services\Intelligence\PricingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Queue job: predict pricing for a batch of properties.
 */
class PredictPropertyPricing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $propertyIds;
    public array $comparablesByProperty;

    public function __construct(array $propertyIds, array $comparablesByProperty = [])
    {
        $this->propertyIds = $propertyIds;
        $this->comparablesByProperty = $comparablesByProperty;
        $this->onQueue('intelligence');
    }

    public function handle(PricingService $service): void
    {
        $props = Property::whereIn('id', $this->propertyIds)->get();
        foreach ($props as $p) {
            $comps = $this->comparablesByProperty[$p->id] ?? [];
            $pricing = $service->predict($p, $comps);
            $service->persist($p, $pricing);
        }
    }
}


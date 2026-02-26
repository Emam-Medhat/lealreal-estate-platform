<?php

namespace App\Jobs;

use App\Models\Property;
use App\Services\Intelligence\InvestmentScoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Queue job: compute investment scores for a batch of properties.
 */
class ComputeInvestmentScores implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array<int> Property IDs
     */
    public array $propertyIds;

    /**
     * @var array Input signals indexed by property id
     */
    public array $inputsByProperty;

    public function __construct(array $propertyIds, array $inputsByProperty = [])
    {
        $this->propertyIds = $propertyIds;
        $this->inputsByProperty = $inputsByProperty;
        $this->onQueue('intelligence');
    }

    public function handle(InvestmentScoringService $service): void
    {
        $props = Property::whereIn('id', $this->propertyIds)->get();
        foreach ($props as $p) {
            $inputs = $this->inputsByProperty[$p->id] ?? [];
            $metrics = $service->score($p, $inputs);
            $service->persist($p, $metrics);
        }
    }
}


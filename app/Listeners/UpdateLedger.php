<?php

namespace App\Listeners;

use App\Events\PropertySold;
use App\Models\PropertyAsset;
use App\Services\FractionalOwnershipService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateLedger implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Queue connection for this listener.
     */
    public string $connection = 'database';
    public string $queue = 'blockchain';

    public function __construct(private FractionalOwnershipService $ownershipService)
    {
    }

    /**
     * Handle the PropertySold event.
     * Records the sale permanently in the immutable TransactionLedger.
     */
    public function handle(PropertySold $event): void
    {
        try {
            $property = $event->property;
            $buyer = $event->buyer;
            $salePrice = $event->salePrice;

            // Find the linked PropertyAsset for this property (if tokenised)
            $asset = PropertyAsset::where('property_id', $property->id)
                ->whereIn('status', ['active', 'locked'])
                ->first();

            $this->ownershipService->recordToLedger(
                'property_sold',
                [
                    'property_id' => $property->id,
                    'property_title' => $property->title,
                    'buyer_id' => $buyer->id,
                    'buyer_name' => $buyer->name,
                    'sale_price' => $salePrice,
                    'currency' => $property->currency ?? 'USD',
                    'asset_id' => $asset?->id,
                    'token_id' => $asset?->token_id,
                    'sold_at' => now()->toISOString(),
                ],
                \App\Models\Property::class,
                $property->id,
                $buyer->id
            );

            // If there's a PropertyAsset, lock it
            if ($asset) {
                $asset->update(['status' => 'sold']);
            }

            Log::info('UpdateLedger: property_sold recorded', [
                'property_id' => $property->id,
                'buyer_id' => $buyer->id,
                'sale_price' => $salePrice,
            ]);
        } catch (\Throwable $e) {
            Log::error('UpdateLedger: failed to record property_sold', [
                'error' => $e->getMessage(),
                'property_id' => $event->property->id,
            ]);
            throw $e; // Re-throw so the queue marks the job as failed
        }
    }
}

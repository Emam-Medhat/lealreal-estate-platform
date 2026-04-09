<?php

namespace App\Listeners;

use App\Events\PropertySold;
use App\Models\PropertyAsset;
use App\Models\UserNotification;
use App\Services\FractionalOwnershipService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DividendReceived;

class NotifyShareholders implements ShouldQueue
{
    use InteractsWithQueue;

    public string $connection = 'database';
    public string $queue = 'blockchain';

    public function __construct(private FractionalOwnershipService $ownershipService)
    {
    }

    /**
     * Handle the PropertySold event.
     * Distributes dividends to all shareholders and notifies each one.
     */
    public function handle(PropertySold $event): void
    {
        try {
            $property = $event->property;
            $salePrice = $event->salePrice;

            // Only run for tokenised properties with a PropertyAsset
            $asset = PropertyAsset::where('property_id', $property->id)->first();

            if (!$asset) {
                Log::info("NotifyShareholders: property {$property->id} has no token asset, skipping.");
                return;
            }

            // Distribute 100% of sale proceeds to shareholders proportionally
            $result = $this->ownershipService->distributeDividends($asset, $salePrice);

            // Notify each shareholder (database notification)
            if ($result['shareholders_paid'] > 0) {
                $ownerships = \App\Models\FractionalOwnership::where('property_asset_id', $asset->id)
                    ->with('user')
                    ->get();

                foreach ($ownerships as $ownership) {
                    $userDividend = collect($result['breakdown'])
                        ->firstWhere('user_id', $ownership->user_id);

                    if (!$userDividend || $userDividend['dividend'] <= 0) {
                        continue;
                    }

                    // Store an in-app notification
                    UserNotification::create([
                        'user_id' => $ownership->user_id,
                        'title' => 'Dividend Received 💰',
                        'message' => "Property \"{$property->title}\" was sold. You received "
                            . number_format($userDividend['dividend'], 2)
                            . " {$property->currency} as your share dividend.",
                        'type' => 'dividend',
                        'data' => json_encode([
                            'property_id' => $property->id,
                            'asset_id' => $asset->id,
                            'shares_owned' => $ownership->shares_owned,
                            'dividend' => $userDividend['dividend'],
                        ]),
                    ]);
                }
            }

            Log::info('NotifyShareholders: dividends distributed', [
                'property_id' => $property->id,
                'asset_id' => $asset->id,
                'total_distributed' => $result['total_distributed'],
                'shareholders_paid' => $result['shareholders_paid'],
            ]);
        } catch (\Throwable $e) {
            Log::error('NotifyShareholders: failed to distribute dividends', [
                'error' => $e->getMessage(),
                'property_id' => $event->property->id,
            ]);
            throw $e;
        }
    }
}

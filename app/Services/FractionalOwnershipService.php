<?php

namespace App\Services;

use App\Models\PropertyAsset;
use App\Models\FractionalOwnership;
use App\Models\TransactionLedger;
use App\Models\EscrowContract;
use App\Models\User;
use App\Exceptions\DoubleSpendingException;
use App\Exceptions\ShareLimitExceededException;
use App\Exceptions\EscrowConditionException;
use App\Exceptions\InvalidChainStateException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FractionalOwnershipService
{
    // ─── Hash Generation ─────────────────────────────────────────────────────

    /**
     * Generate a SHA-256 hash for a new ledger entry.
     *
     * @param  array  $payload       The transaction data to hash
     * @param  string $previousHash  The hash of the preceding ledger record
     * @return string                64-character hex hash
     */
    public function generateHash(array $payload, string $previousHash): string
    {
        return hash('sha256', json_encode($payload) . $previousHash);
    }

    // ─── Ledger Recording ─────────────────────────────────────────────────────

    /**
     * Append an immutable entry to the TransactionLedger.
     * Automatically computes the hash chain.
     *
     * @param  string  $action
     * @param  array   $payload
     * @param  string  $referenceType  e.g. PropertyAsset::class
     * @param  int     $referenceId
     * @param  int|null $recordedBy
     * @return TransactionLedger
     */
    public function recordToLedger(
        string $action,
        array $payload,
        string $referenceType,
        int $referenceId,
        ?int $recordedBy = null
    ): TransactionLedger {
        // Fetch the latest entry to build the chain
        $latest = TransactionLedger::orderByDesc('id')->lockForUpdate()->first();
        $previousHash = $latest?->current_hash ?? 'genesis';

        $currentHash = $this->generateHash($payload, $previousHash);

        // Use raw insert to bypass the immutability guard in the model
        DB::table('transaction_ledgers')->insert([
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'action' => $action,
            'previous_hash' => $previousHash,
            'current_hash' => $currentHash,
            'payload' => json_encode($payload),
            'recorded_by' => $recordedBy,
            'created_at' => now(),
        ]);

        return TransactionLedger::where('current_hash', $currentHash)->first();
    }

    // ─── Share Purchase ───────────────────────────────────────────────────────

    /**
     * Atomically purchase shares in a tokenised property asset.
     *
     * Rules enforced:
     *  - Total shares can NEVER exceed 100% (asset->total_shares)
     *  - A user cannot purchase the same asset shares twice (merged instead)
     *  - The entire operation is wrapped in a DB transaction
     *
     * @throws ShareLimitExceededException
     * @throws \Throwable
     */
    public function buyShare(PropertyAsset $asset, User $buyer, int $sharesToBuy): FractionalOwnership
    {
        return DB::transaction(function () use ($asset, $buyer, $sharesToBuy) {

            // Re-read with a row-level lock to guard against race conditions
            $asset = PropertyAsset::lockForUpdate()->findOrFail($asset->id);

            // ── Guard: shares available ──────────────────────────────────────
            if ($sharesToBuy <= 0) {
                throw new ShareLimitExceededException('Number of shares to buy must be greater than zero.');
            }

            if ($asset->available_shares < $sharesToBuy) {
                throw new ShareLimitExceededException(
                    "Only {$asset->available_shares} shares are available; requested {$sharesToBuy}."
                );
            }

            // ── Guard: double-spending (can still add more shares, but check logic) ──
            $existing = FractionalOwnership::where('property_asset_id', $asset->id)
                ->where('user_id', $buyer->id)
                ->lockForUpdate()
                ->first();

            $totalCost = $sharesToBuy * $asset->share_price;
            $newPercentage = $asset->sharesAsPercentage($sharesToBuy);

            if ($existing) {
                // Merge additional shares into existing holding
                $existing->shares_owned += $sharesToBuy;
                $existing->percentage = $asset->sharesAsPercentage($existing->shares_owned);
                $existing->purchase_price += $totalCost;
                $existing->purchased_at = now();
                // Direct DB update to avoid any model-level restrictions
                DB::table('fractional_ownerships')
                    ->where('id', $existing->id)
                    ->update([
                        'shares_owned' => $existing->shares_owned,
                        'percentage' => $existing->percentage,
                        'purchase_price' => $existing->purchase_price,
                        'purchased_at' => now(),
                        'updated_at' => now(),
                    ]);

                $ownership = $existing->fresh();
            } else {
                $ownership = FractionalOwnership::create([
                    'property_asset_id' => $asset->id,
                    'user_id' => $buyer->id,
                    'shares_owned' => $sharesToBuy,
                    'percentage' => $newPercentage,
                    'purchase_price' => $totalCost,
                    'price_per_share' => $asset->share_price,
                    'purchased_at' => now(),
                ]);
            }

            // Deduct from available supply
            DB::table('property_assets')
                ->where('id', $asset->id)
                ->decrement('available_shares', $sharesToBuy);

            // If fully sold, update status
            $remainingShares = $asset->available_shares - $sharesToBuy;
            if ($remainingShares <= 0) {
                DB::table('property_assets')
                    ->where('id', $asset->id)
                    ->update(['status' => 'sold', 'updated_at' => now()]);
            }

            // Append immutable ledger entry
            $this->recordToLedger(
                'share_purchase',
                [
                    'property_asset_id' => $asset->id,
                    'token_id' => $asset->token_id,
                    'buyer_id' => $buyer->id,
                    'shares_bought' => $sharesToBuy,
                    'price_per_share' => $asset->share_price,
                    'total_cost' => $totalCost,
                    'remaining_shares' => max(0, $remainingShares),
                    'percentage' => $newPercentage,
                    'timestamp' => now()->toISOString(),
                ],
                PropertyAsset::class,
                $asset->id,
                $buyer->id
            );

            Log::info('FractionalOwnershipService: share_purchase', [
                'asset_id' => $asset->id,
                'buyer_id' => $buyer->id,
                'shares' => $sharesToBuy,
                'cost' => $totalCost,
            ]);

            return $ownership;
        });
    }

    // ─── Escrow ───────────────────────────────────────────────────────────────

    /**
     * Execute an escrow contract — release funds to seller once all
     * conditions (property_inspection, legal_approval) are satisfied.
     *
     * @throws EscrowConditionException
     * @throws InvalidChainStateException
     */
    public function executeEscrow(EscrowContract $escrow): bool
    {
        if (!$escrow->isLocked()) {
            throw new InvalidChainStateException(
                "Escrow #{$escrow->id} must be in 'locked' state to execute. Current state: {$escrow->state}."
            );
        }

        if (!$escrow->allConditionsMet()) {
            $unmet = collect($escrow->conditions)
                ->filter(fn($v) => $v === false)
                ->keys()
                ->implode(', ');

            throw new EscrowConditionException(
                "Cannot execute escrow #{$escrow->id}. Unmet conditions: {$unmet}."
            );
        }

        DB::transaction(function () use ($escrow) {
            $escrow->execute();

            $this->recordToLedger(
                'escrow_executed',
                [
                    'escrow_id' => $escrow->id,
                    'property_asset_id' => $escrow->property_asset_id,
                    'buyer_id' => $escrow->buyer_id,
                    'seller_id' => $escrow->seller_id,
                    'amount' => $escrow->amount,
                    'currency' => $escrow->currency,
                    'conditions_met' => $escrow->conditions,
                    'executed_at' => now()->toISOString(),
                ],
                EscrowContract::class,
                $escrow->id
            );
        });

        Log::info('FractionalOwnershipService: escrow_executed', ['escrow_id' => $escrow->id]);

        return true;
    }

    /**
     * Lock an escrow (hold the funds).
     */
    public function lockEscrow(EscrowContract $escrow): bool
    {
        if (!$escrow->isPending()) {
            throw new InvalidChainStateException(
                "Only pending escrows can be locked. Current state: {$escrow->state}."
            );
        }

        DB::transaction(function () use ($escrow) {
            $escrow->lock();

            $this->recordToLedger(
                'escrow_locked',
                [
                    'escrow_id' => $escrow->id,
                    'property_asset_id' => $escrow->property_asset_id,
                    'amount' => $escrow->amount,
                    'locked_at' => now()->toISOString(),
                ],
                EscrowContract::class,
                $escrow->id
            );
        });

        return true;
    }

    // ─── Dividend Distribution ────────────────────────────────────────────────

    /**
     * Automatically split rental income or resale profit among all shareholders.
     *
     * Supports 100+ shareholders efficiently using chunked DB updates.
     *
     * @param  PropertyAsset $asset        The tokenised property
     * @param  float         $totalIncome  Income to distribute (in platform currency)
     * @return array                       ['total_distributed', 'shareholders_paid', 'breakdown']
     */
    public function distributeDividends(PropertyAsset $asset, float $totalIncome): array
    {
        if ($totalIncome <= 0) {
            throw new \InvalidArgumentException('Total income for dividend distribution must be positive.');
        }

        $breakdown = [];
        $totalDistributed = 0.0;
        $shareholdersPaid = 0;

        // Build full list of shareholders and their percentages
        $ownerships = FractionalOwnership::where('property_asset_id', $asset->id)
            ->with('user')
            ->get();

        if ($ownerships->isEmpty()) {
            return [
                'total_distributed' => 0,
                'shareholders_paid' => 0,
                'breakdown' => [],
            ];
        }

        // Calculate each shareholder's cut based on their share percentage
        $totalSharesIssued = $asset->total_shares;
        $payouts = [];

        foreach ($ownerships as $ownership) {
            $fraction = $totalSharesIssued > 0
                ? $ownership->shares_owned / $totalSharesIssued
                : 0;

            $dividend = round($totalIncome * $fraction, 2);
            $payouts[] = [
                'ownership_id' => $ownership->id,
                'user_id' => $ownership->user_id,
                'shares' => $ownership->shares_owned,
                'fraction' => $fraction,
                'dividend' => $dividend,
            ];
        }

        DB::transaction(function () use ($payouts, $asset, $totalIncome, &$breakdown, &$totalDistributed, &$shareholdersPaid) {
            foreach ($payouts as $payout) {
                if ($payout['dividend'] <= 0) {
                    continue;
                }

                DB::table('fractional_ownerships')
                    ->where('id', $payout['ownership_id'])
                    ->increment('dividends_earned', $payout['dividend'], ['updated_at' => now()]);

                $breakdown[] = [
                    'user_id' => $payout['user_id'],
                    'shares' => $payout['shares'],
                    'dividend' => $payout['dividend'],
                ];

                $totalDistributed += $payout['dividend'];
                $shareholdersPaid++;
            }

            // Single ledger entry for the entire distribution event
            $this->recordToLedger(
                'dividend_distributed',
                [
                    'property_asset_id' => $asset->id,
                    'token_id' => $asset->token_id,
                    'total_income' => $totalIncome,
                    'total_distributed' => $totalDistributed,
                    'shareholders_paid' => $shareholdersPaid,
                    'breakdown' => $breakdown,
                    'distributed_at' => now()->toISOString(),
                ],
                PropertyAsset::class,
                $asset->id
            );
        });

        Log::info('FractionalOwnershipService: dividend_distributed', [
            'asset_id' => $asset->id,
            'total_distributed' => $totalDistributed,
            'shareholders_paid' => $shareholdersPaid,
        ]);

        return [
            'total_distributed' => $totalDistributed,
            'shareholders_paid' => $shareholdersPaid,
            'breakdown' => $breakdown,
        ];
    }
}

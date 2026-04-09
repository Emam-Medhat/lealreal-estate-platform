<?php

namespace App\Services;

use App\Models\TransactionLedger;
use App\Exceptions\InvalidChainStateException;

class VerificationService
{
    /**
     * Verify the entire TransactionLedger hash chain.
     *
     * Iterates every record in chronological order, re-computes the expected
     * hash from (payload + previous_hash), and compares it to the stored hash.
     * Any discrepancy means the entry was tampered with directly in the DB.
     *
     * @return array{
     *     valid: bool,
     *     total_checked: int,
     *     broken_at: int|null,
     *     broken_entry_id: int|null,
     *     message: string
     * }
     */
    public function verifyChain(): array
    {
        $total = 0;
        $brokenAt = null;
        $brokenId = null;

        // Iterate in insertion order — use lazy() to support millions of rows
        TransactionLedger::orderBy('id', 'asc')
            ->lazy()
            ->each(function (TransactionLedger $entry) use (&$total, &$brokenAt, &$brokenId) {
                $total++;

                if ($brokenAt !== null) {
                    return; // Already found a break — keep counting total
                }

                $expectedHash = hash(
                    'sha256',
                    json_encode($entry->payload) . $entry->previous_hash
                );

                if (!hash_equals($expectedHash, $entry->current_hash)) {
                    $brokenAt = $total;       // Position in chain (1-indexed)
                    $brokenId = $entry->id;   // DB primary key for easy lookup
                }
            });

        $valid = $brokenAt === null;

        return [
            'valid' => $valid,
            'total_checked' => $total,
            'broken_at' => $brokenAt,
            'broken_entry_id' => $brokenId,
            'message' => $valid
                ? "Chain integrity verified. All {$total} entries are valid."
                : "⚠ Chain broken at position {$brokenAt} (ledger entry ID: {$brokenId}). Manual tampering detected!",
        ];
    }

    /**
     * Verify only from a specific ledger entry ID onwards.
     * Useful for partial audits without scanning the full history.
     *
     * @param  int $fromId  Start from this row ID (inclusive)
     */
    public function verifyChainFrom(int $fromId): array
    {
        $total = 0;
        $brokenAt = null;
        $brokenId = null;

        // Find the entry just before fromId to get the expected previous_hash
        $predecessor = TransactionLedger::where('id', '<', $fromId)
            ->orderByDesc('id')
            ->first();

        $expectedPreviousHash = $predecessor?->current_hash ?? 'genesis';

        TransactionLedger::where('id', '>=', $fromId)
            ->orderBy('id', 'asc')
            ->lazy()
            ->each(function (TransactionLedger $entry) use (&$total, &$brokenAt, &$brokenId, &$expectedPreviousHash) {
                $total++;

                if ($brokenAt !== null) {
                    return;
                }

                // Check that previous_hash points to the right predecessor
                if (!hash_equals($expectedPreviousHash, $entry->previous_hash)) {
                    $brokenAt = $total;
                    $brokenId = $entry->id;
                    return;
                }

                $expectedHash = hash(
                    'sha256',
                    json_encode($entry->payload) . $entry->previous_hash
                );

                if (!hash_equals($expectedHash, $entry->current_hash)) {
                    $brokenAt = $total;
                    $brokenId = $entry->id;
                    return;
                }

                $expectedPreviousHash = $entry->current_hash;
            });

        $valid = $brokenAt === null;

        return [
            'valid' => $valid,
            'total_checked' => $total,
            'from_id' => $fromId,
            'broken_at' => $brokenAt,
            'broken_entry_id' => $brokenId,
            'message' => $valid
                ? "Partial chain verified from ID {$fromId}. All {$total} entries are valid."
                : "⚠ Chain broken at position {$brokenAt} (ledger entry ID: {$brokenId}).",
        ];
    }

    /**
     * Return a summary count of ledger entries grouped by action type.
     */
    public function getLedgerSummary(): array
    {
        return TransactionLedger::selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->get()
            ->keyBy('action')
            ->map(fn($row) => $row->count)
            ->toArray();
    }
}

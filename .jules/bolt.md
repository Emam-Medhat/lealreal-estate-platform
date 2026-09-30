## 2026-09-30 - Batching Dividend Distribution Updates in FractionalOwnershipService

**Learning:** `FractionalOwnershipService::distributeDividends` previously executed $N$ separate `DB::table('fractional_ownerships')->where(...)->increment(...)` update statements within a transaction loop when processing dividend payments to shareholders. By batching payout updates into a single SQL statement using a `CASE id WHEN ... THEN dividends_earned + ... END` expression, database roundtrips and transaction lock duration are reduced from $O(N)$ down to $O(1)$.

**Action:** When updating numeric balances or column values across multiple records in a collection, construct a single batched SQL `UPDATE` statement with `CASE` expressions instead of running individual model/table `where()->increment()` calls in a loop.

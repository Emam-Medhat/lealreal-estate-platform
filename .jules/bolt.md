## 2026-03-19 - Dashboard Query Consolidation
**Learning:** Consolidated multiple redundant database count calls into single selectRaw queries using conditional aggregation (e.g., COUNT(CASE WHEN ... THEN 1 END)). This significantly reduces database round-trips for high-traffic views like the user dashboard. Also confirmed that replacing whereDate with range comparisons (>= $today) improves query performance by better utilizing column indexes.
**Action:** Always check for opportunities to consolidate simple count/sum queries on the same table into a single selectRaw statement.

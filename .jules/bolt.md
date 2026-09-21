## 2026-09-21 - Enterprise Dashboard Stats Optimization
**Learning:** Multiple individual `count()` queries on `EnterpriseAccount` (`count()`, `where('status', 'active')->count()`, and `whereMonth('created_at', ...)`) cause redundant round-trips and prevent index usage when using date functions like `whereMonth`.
**Action:** Consolidate multiple count queries into a single SQL `selectRaw` with conditional aggregations (`COUNT(CASE WHEN ... THEN 1 END)`) and use explicit start-of-month timestamp range comparisons (`created_at >= ?`) for efficient index lookup.

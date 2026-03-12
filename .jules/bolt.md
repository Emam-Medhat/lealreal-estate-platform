## 2026-03-12 - [Query Stacking and Consolidation]
**Learning:** In Laravel, consecutive `where()` calls on a shared Query Builder instance modify the instance in-place and stack filters (e.g., `WHERE status='available' AND status='sold'`), leading to incorrect results when trying to get multiple counts from the same base query.
**Action:** Use `clone $query` for separate counts or, preferably, use conditional aggregation (`COUNT(CASE WHEN ... THEN 1 END)`) to consolidate multiple counts, sums, and averages into a single database query for optimal performance.

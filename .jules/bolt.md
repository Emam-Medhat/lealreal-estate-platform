
## 2025-03-26 - Optimized Dashboard Query Performance
**Learning:** Consolidating multiple database counts into a single query using `selectRaw` with conditional aggregation (CASE WHEN) significantly reduces database round-trips. However, it's critical to use range comparisons (e.g., `created_at >= ?`) instead of functional transformations like `whereDate()` to maintain index efficiency. Additionally, polymorphic relationships often hide N+1 issues in accessors; eager loading nested dependencies (e.g., `favoritable.media`) is essential.
**Action:** Always check for N+1 patterns in loop-based mappings and prioritize consolidated queries with index-friendly WHERE clauses.

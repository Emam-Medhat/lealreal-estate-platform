
## 2026-05-27 - Optimized Property Statistics and Fixed Query Stacking Bug
**Learning:** Reusing the same Eloquent/Query Builder instance for multiple counts/aggregates with `where()` calls causes filters to stack (e.g., `WHERE status='A' AND status='B'`), leading to incorrect zero results. Also, calculating stats individually leads to unnecessary database round-trips.
**Action:** Use conditional aggregation (`COUNT(CASE WHEN...)`) to consolidate multiple statistics into a single database query. This both improves performance (6 queries -> 1) and avoids the stateful query builder stacking bug. Always verify schema-specific values like status enums (e.g., 'active' vs 'available') and column names (e.g., 'property_type' vs 'type').

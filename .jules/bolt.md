## 2026-03-24 - Optimization of Property Statistics Aggregation

**Learning:** Chaining multiple `where()` calls on a shared Laravel Query Builder instance results in stacked filters (e.g., `WHERE status='active' AND status='sold'`), which is a common anti-pattern when performing multiple counts or aggregations on the same base query. Additionally, using `selectRaw` with conditional aggregation (`COUNT(CASE WHEN...)`) is significantly more efficient than multiple `count()` or `sum()` calls, as it reduces database round-trips to exactly one.

**Action:** Always use `clone $query` if multiple independent operations are needed on a base query builder, or prefer `selectRaw` with conditional aggregation to consolidate multiple metrics into a single database trip. Always verify the actual database schema for status enums (e.g., 'active' vs 'available') as they may differ from initial assumptions.

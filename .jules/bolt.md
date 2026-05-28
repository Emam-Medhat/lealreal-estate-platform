## 2025-05-28 - Optimized Property Statistics Query

**Learning:** Using conditional aggregation with `selectRaw` is significantly more efficient than multiple separate Eloquent queries (e.g., `count`, `sum`, `avg`) as it reduces database round-trips to a single call. Additionally, reusing an Eloquent query builder instance without cloning it when applying multiple `where` filters causes them to stack, leading to incorrect results (e.g., `status = 'active' AND status = 'sold'`).

**Action:** Always prefer conditional aggregation for multi-metric statistical queries. If separate queries must be used on a base query, always `clone $query` before applying filters to avoid unintended filter stacking. Verify schema enum values (e.g., 'active' vs 'available') directly from migrations or database inspection rather than assuming common naming conventions.

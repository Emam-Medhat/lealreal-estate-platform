## 2026-05-02 - Optimize Property Statistics Calculation

**Learning:** Laravel Query Builder methods like `where()` modify the instance in-place. Sequential calls on the same variable (e.g., `$query->where('status', 'A')->count()` then `$query->where('status', 'B')->count()`) result in stacked filters (e.g., `WHERE status = 'A' AND status = 'B'`), leading to incorrect results. Also discovered schema mismatches where the code used 'available' but the DB used 'active', and 'type' instead of 'property_type'.

**Action:** Use conditional aggregation (`COUNT(CASE WHEN...)`) to perform multiple counts/sums in a single query. This avoids state mutation issues, improves performance by reducing database round-trips, and simplifies the code. Always verify DB schema enums against service logic.

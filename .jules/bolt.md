## 2025-04-02 - Query Consolidation in Property Statistics

**Learning:** Replacing multiple `count()` and `sum()` calls on a Query Builder with a single `selectRaw` query using conditional aggregation (e.g., `COUNT(CASE WHEN status = 'active' THEN 1 END)`) reduced database round-trips from 6 down to 1. This also resolved a subtle bug where sequential `where()` calls on the same Query Builder instance were stacking filters, leading to incorrect zero results for subsequent counts.

**Action:** Always prefer conditional aggregation for summary statistics or dashboard data where multiple counts or sums from the same table are required. Avoid reusing the same Query Builder instance for multiple independent queries without cloning it.

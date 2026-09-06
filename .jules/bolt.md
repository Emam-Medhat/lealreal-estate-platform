## 2026-03-31 - System Error Log Controller Query Aggregation

**Learning:** `SystemErrorLogController::index` was executing 4 sequential SQL queries (`total`, `resolved`, `unresolved`, `today`) for stats calculation. Using `selectRaw` with conditional aggregations (`COUNT(CASE WHEN ... THEN 1 END)`) reduces 4 database queries down to 1. In addition, replacing `whereDate('created_at', ...)` with `created_at >= ?` ($todayStart) allows database engines to use indexes on `created_at`.

**Action:** Look for admin index/dashboard actions running multiple separate `COUNT()` or `whereDate()` queries on the same table and consolidate them into a single `selectRaw` conditional query.

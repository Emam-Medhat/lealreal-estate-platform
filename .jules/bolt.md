## 2026-10-26 - Consolidating Appointment Dashboard Statistics Queries
**Learning:** Using `whereDate('start_datetime', ...)` forces SQL `DATE()` function application on timestamps, preventing B-tree index usage. Additionally, executing multiple count helper calls in controller dashboards generates N database queries.
**Action:** Replace `whereDate` with index-friendly date range filtering (`start_datetime >= $todayStart AND start_datetime <= $todayEnd`) and consolidate individual `COUNT` operations into a single `selectRaw` SQL conditional aggregation (`COUNT(CASE WHEN ... THEN 1 END)`).

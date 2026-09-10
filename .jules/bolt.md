## 2026-02-27 - Consolidating Monthly/Quarterly Cash Flow Aggregations

**Learning:** Loop-based month-by-month database queries (e.g. 12 queries for monthly inflows, 12 for monthly outflows, plus 8 for quarterly totals) generate massive query overhead (~82 DB queries per report). Performing bulk conditional aggregations (`SUM(CASE WHEN ... THEN amount ELSE 0 END)`) grouped by database driver month functions reduces queries from ~82 to 4 while preserving data structure and allowing downstream quarterly/yearly methods to reuse pre-calculated monthly arrays.
**Action:** When building financial or time-series reports, fetch all 12 monthly aggregations in a single query per table using driver-aware month expressions, and pass pre-aggregated arrays to quarterly and yearly calculation methods.

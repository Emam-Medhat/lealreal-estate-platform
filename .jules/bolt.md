
## 2026-04-11 - Conditional Aggregation for Statistics
**Learning:** Collapsing multiple count/sum/avg queries into a single `selectRaw` with `CASE WHEN` significantly reduces database round-trips (from 6 to 1 in this case). Reusing a Laravel Query Builder instance for multiple `count()` calls is a bug, as `where` clauses are cumulative.
**Action:** Always use conditional aggregation for summary statistics. If queries must be separate, use `clone $query` to avoid filter leakage between operations.

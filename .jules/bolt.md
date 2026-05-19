## 2026-05-19 - Property Statistics Optimization
**Learning:** Chaining multiple `where()` calls on a shared Query Builder instance modifies it in-place, leading to stacked filters (e.g., WHERE status='A' AND status='B') which returns zero results. Conditional aggregation is a superior pattern for multiple counts on the same base query.
**Action:** Use `selectRaw` with `COUNT(CASE WHEN ... THEN 1 END)` to perform multiple conditional counts in a single query while avoiding query builder pollution.

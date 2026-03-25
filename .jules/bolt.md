## 2025-03-25 - Query Builder Stacking vs Conditional Aggregation
**Learning:** Chaining multiple `count()` or `where()` calls on the same Eloquent Query Builder instance modifies it in-place. This leads to "stacked" filters (e.g., `WHERE status='active' AND status='sold'`) which return incorrect zero results.
**Action:** Use `clone $query` or refactor multiple counts into a single `selectRaw` with conditional aggregation (`COUNT(CASE WHEN...)`) to improve performance and ensure correctness.

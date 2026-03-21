## 2025-03-21 - Query Builder Mutation in Aggregate Functions

**Learning:** In Laravel, calling `where()` on an Eloquent/Query Builder instance mutates the instance in-place. If multiple `count()` or `sum()` calls are made on the same builder variable with different `where()` filters, they stack together (e.g., `WHERE status = 'active' AND status = 'sold'`), leading to incorrect results (usually zero).

**Action:** Use `clone $query` for each separate aggregate call, or better yet, use a single `selectRaw` query with conditional aggregation (e.g., `COUNT(CASE WHEN status = 'active' THEN 1 END)`) to reduce database round-trips and avoid mutation bugs.

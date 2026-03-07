## 2025-03-03 - [Consolidated Dashboard Queries]
**Learning:** Consolidated multiple model counts into single `selectRaw` queries with conditional aggregates. This significantly reduces database round-trips for high-traffic pages like dashboards. Also, resolved N+1 issues by eager loading nested relationships on polymorphic models.
**Action:** When building dashboards or summary pages, prioritize conditional aggregation over multiple `count()` calls. Always check if accessors on related models (like `main_image` on `Property`) perform additional queries and eager load them accordingly.

## 2025-03-11 - Optimized Dashboard Queries

**Learning:** Consolidating multiple `count()` calls into a single query using `selectRaw` with conditional aggregation and leveraging Laravel's `withCount` relationship method significantly reduces database round-trips. Additionally, eager loading nested polymorphic relationships (e.g., `favoritable.media`) is crucial to avoid N+1 queries when model accessors (like `main_image`) perform secondary lookups.

**Action:** Always check for repeated `count()` calls on the same table or related tables that can be consolidated. Use `withCount` when fetching the main model and ensure N+1 issues are resolved by eager loading deeply nested media or related models used in common accessors.

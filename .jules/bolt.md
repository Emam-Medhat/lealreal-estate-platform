## 2025-03-18 - [Stacked Filters in Laravel Query Builder]
**Learning:** Chaining multiple methods like `count()` or `sum()` on the same Query Builder instance that includes `where()` calls will stack those filters. For example, `$query->where('status', 'A')->count()` followed by `$query->where('status', 'B')->count()` results in the second count having `WHERE status = 'A' AND status = 'B'`, which is usually not intended.
**Action:** Use `clone $query` for consecutive distinct operations on a base query, or better yet, use conditional aggregation in a single `selectRaw` call to perform all calculations in one database round-trip.

## 2025-03-18 - [Inconsistent Database Schema and Status Values]
**Learning:** The `properties` table uses `property_type` instead of `type` and `active` instead of `available` for status, despite some service methods using the old names.
**Action:** Always verify the actual schema in migrations when encountering suspected column name or status value issues, and apply fixes consistently across the entire service class.

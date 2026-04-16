## 2026-04-16 - Optimization of Property Statistics

**Learning:** Consolidated 6 database queries into 1 using conditional aggregation in `PropertyService::getPropertyStatistics`. Discovered a logic bug where stacked `where()` filters on a shared Query Builder instance resulted in zero counts for sub-categories. Also corrected a schema mismatch where 'available' was used instead of 'active' for property status.

**Action:** Always check if a Query Builder instance is being reused for multiple counts/aggregations. Prefer `selectRaw` with conditional aggregation (`COUNT(CASE WHEN...)`) to reduce database round-trips and avoid filter stacking issues. Always verify status enum values against the database schema/migrations.

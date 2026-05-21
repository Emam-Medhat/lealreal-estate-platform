## 2025-01-25 - Analytics Query Consolidation
**Learning:** Consolidating multiple `count()` and `sum()` calls into a single `selectRaw` query with conditional aggregation (`COUNT(CASE WHEN...)`) significantly reduces database round-trips. Grouping by month for trend data instead of looping in PHP eliminates N queries (where N is the number of months).
**Action:** Always look for patterns where multiple queries are run against the same table with different filters; these are prime candidates for conditional aggregation.

## 2025-01-25 - Database Portability in Raw Queries
**Learning:** Using raw SQL functions like `DATE_FORMAT` (MySQL) or `strftime` (SQLite) breaks portability.
**Action:** Detect the database driver using `DB::connection()->getDriverName()` to apply the correct syntax in raw queries.

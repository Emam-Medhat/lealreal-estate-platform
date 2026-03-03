## 2026-03-03 - [Consolidated Dashboard Queries]
**Learning:** Multiple individual `count()` queries in the `DashboardService` were causing unnecessary database round-trips. Consolidating these into single `selectRaw` queries with conditional aggregation (e.g., `COUNT(CASE WHEN ... THEN 1 END)`) significantly reduced query count. Additionally, using `>=` range comparisons instead of `whereDate()` allows the database to utilize composite indexes on the `created_at` column.
**Action:** Always look for opportunities to group aggregate queries for the same model and use index-friendly date comparisons.

## 2026-03-03 - [Avoid Staging Build Artifacts]
**Learning:** Running `php artisan migrate` or tests can generate files in `bootstrap/cache/` and `storage/framework/views/`. These are environment-specific artifacts and should never be staged or committed.
**Action:** Check `git status` carefully and use `git restore` on any generated artifacts before committing.

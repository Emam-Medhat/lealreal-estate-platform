## 2025-05-15 - Dashboard Query Consolidation
**Learning:** Consolidated multiple redundant database `count()` calls in `DashboardService.php` into single `selectRaw` queries using conditional aggregation (e.g., `COUNT(CASE WHEN ... THEN 1 END)`), reducing database round-trips. Also, using range comparisons like `where('created_at', '>=', $date)` instead of `whereDate()` ensures the database utilizes indexes on timestamp columns. Eager loading nested polymorphic relationships like `favoritable.media` is crucial to prevent N+1 issues when model accessors (like `main_image`) perform additional queries.
**Action:** Always check for repeated queries on the same table within a single method and use conditional aggregation to fetch multiple metrics at once. Avoid functional column transformations in WHERE clauses to maintain SARGability.

## 2025-05-15 - Environment Hygiene
**Learning:** Running `php artisan test` or other commands can generate transient artifacts in `bootstrap/cache/` and `storage/framework/views/` that may be automatically staged.
**Action:** Before submitting a PR, always check `git status` and use `php artisan view:clear && php artisan config:clear && php artisan route:clear && php artisan clear-compiled` followed by `git restore --staged bootstrap/cache/*.php storage/framework/views/*.php` to ensure a clean submission.

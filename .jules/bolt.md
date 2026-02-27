## 2026-02-27 - [Query Consolidation vs. Index Usage]
**Learning:** Consolidating multiple `COUNT` queries into a single `selectRaw` with conditional counting (SQL `CASE`) can be a performance anti-pattern if it requires removing `WHERE` clauses that allow the database to use indexes. Removing a `WHERE created_at >= ?` filter forces a full scan of a user's records instead of a targeted index lookup.
**Action:** Always prioritize keeping restrictive `WHERE` clauses in database queries. Only consolidate queries when the consolidated query can still benefit from the same indexes as the original separate queries.

## 2026-02-27 - [Transient Files in PRs]
**Learning:** Laravel environment-specific files like compiled Blade views (`storage/framework/views/*`) and bootstrap caches (`bootstrap/cache/*`) can be accidentally included in changes if they were generated during testing or exploration.
**Action:** Ensure transient files are removed or ignored before submission to maintain PR hygiene and avoid environment conflicts.

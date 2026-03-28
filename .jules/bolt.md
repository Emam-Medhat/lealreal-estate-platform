# Bolt's Journal - Performance Learnings

## 2025-03-28 - Dashboard Query Consolidation
**Learning:** Consolidating multiple `count()` calls into a single `selectRaw` with conditional aggregation (`COUNT(CASE WHEN...)`) significantly reduces database roundtrips. Additionally, replacing `whereDate()` with range comparisons (`>=`) ensures that database indexes on timestamp columns are utilized.
**Action:** Always look for multiple counts or statistics gathered from the same table and consolidate them. Avoid `whereDate()` on large tables.

## 2025-03-28 - Polymorphic N+1 and Accessors
**Learning:** Eager loading polymorphic relationships (e.g., `favoritable`) is not enough if the target model's accessors (like `main_image`) perform further queries on their own relationships (like `media`).
**Action:** Use nested eager loading (e.g., `with('favoritable.media')`) when accessors on the related model are known to use those relationships.

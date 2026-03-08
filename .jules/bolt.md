## 2025-03-08 - Dashboard Query Optimization
**Learning:** Consolidating multiple redundant database `count()` calls into single `selectRaw` queries using conditional aggregation (e.g., `COUNT(CASE WHEN ... THEN 1 END)`) significantly reduces database round-trips. Also, using range comparisons like `>=` for timestamps instead of `whereDate()` ensures that the database can utilize indexes.
**Action:** Always look for opportunities to merge related count queries on the same table and prefer range comparisons for indexed timestamp columns.

## 2025-03-08 - Polymorphic N+1 and Media Eager Loading
**Learning:** When dealing with polymorphic relationships (like `UserFavorite` -> `favoritable`), accessing attributes that trigger further queries (like `Property::main_image` accessing `media` relationship) causes N+1 problems. Eager loading nested relationships (e.g., `with(['favoritable.media'])`) is essential.
**Action:** Audit polymorphic relationship mappings for hidden N+1 queries in model accessors and eager load them.

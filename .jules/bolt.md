## 2026-10-27 - Geospatial Location Intelligence Query Consolidation

**Learning:** `LocationIntelligenceController::index` executed 4 separate database queries (`count` and `avg`) for statistics calculations and queried a non-existent column name `score` instead of `location_score`.
**Action:** Consolidate multiple aggregate calls into a single `selectRaw` query with conditional aggregations (`COUNT(CASE WHEN ... THEN 1 END)`) and ensure SQL column names match the database migration schema (`location_score`).

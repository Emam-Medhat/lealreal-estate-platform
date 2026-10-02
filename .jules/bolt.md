## 2026-10-02 - UserRepository Stats Query Optimization
**Learning:** Consolidating individual database aggregate queries (`COUNT`, `SUM`) into a single `selectRaw` statement with conditional aggregations (`COUNT(CASE WHEN ... THEN 1 END)`) dramatically reduces query count (from 5+ down to 1). When calculating SQL date ranges, conditional logic based on `DB::getDriverName()` ensures cross-database compatibility across SQLite and MySQL.
**Action:** Always check repository statistics methods for sequential query builder calls and combine them into unified `selectRaw` expressions.

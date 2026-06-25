## 2024-06-25 - SQLite HAVING clause limitation
**Learning:** SQLite strictly prohibits using a `HAVING` clause on a non-aggregate query in many environments, even if the alias is defined in the `SELECT` clause.
**Action:** Use a driver check (`DB::connection()->getDriverName() === 'sqlite'`) and fallback to collection-based filtering after `get()` for SQLite, while preserving the SQL-level `HAVING` for MySQL/PostgreSQL.

## 2024-06-25 - Geospatial optimization with Bounding Box
**Learning:** Calculating Haversine distance for every row in a table is an O(n) operation that cannot leverage standard B-tree indices.
**Action:** Implement a bounding box pre-filter using `whereBetween` on `latitude` and `longitude` to leverage a composite index and reduce the rows processed by trigonometric functions.

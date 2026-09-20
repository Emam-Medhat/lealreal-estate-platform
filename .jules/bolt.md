## 2026-09-20 - Database-Agnostic Conditional Aggregations in Eloquent
**Learning:** When optimizing count/sum queries using selectRaw conditional aggregations, date/time filters must use standard range bounds (e.g., created_at >= ? AND created_at <= ?) instead of dialect-specific SQL functions (e.g. strftime or MONTH()) to ensure full compatibility across SQLite, MySQL, and PostgreSQL.
**Action:** Use Carbon start/end date bounds with parameter binding for date-range conditional aggregations.

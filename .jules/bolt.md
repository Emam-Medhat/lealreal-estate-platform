## 2025-05-23 - Property Statistics Optimization
**Learning:** Database queries that use multiple `count()` or `sum()` on the same Query Builder instance can lead to incorrect results if the builder is modified (stacked filters) and causes unnecessary database round-trips. Conditional aggregation (`COUNT(CASE WHEN...)`) allows consolidating these into a single query.
**Action:** Always check if multiple aggregate queries on the same table can be consolidated. Be careful with Eloquent's in-place modification of Query Builder instances.

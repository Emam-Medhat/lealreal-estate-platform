## 2025-05-22 - Aggregate Statistics Optimization
**Learning:** Performing aggregate calculations (SUM, AVG, COUNT) by loading all Eloquent models into memory is a significant performance bottleneck that scales poorly. This pattern was identified in `AiChatbotController`.
**Action:** Use database-level aggregations (`selectRaw`, `sum`, `avg`) instead of collection methods like `all()->sum()` or `all()->count()`. Ensure denormalized columns used for these aggregations (like `message_count`) are kept in sync during write operations.

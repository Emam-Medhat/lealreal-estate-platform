## 2025-05-15 - Optimizing Property Statistics with Conditional Aggregation

**Learning:** Database query roundtrips can be a major bottleneck. Consolidating multiple `count()`, `sum()`, and `avg()` calls into a single query using conditional aggregation (`COUNT(CASE WHEN...)`) significantly improves performance by reducing database roundtrips from N to 1. Additionally, ensuring that internal status strings match the database schema (e.g., 'active' vs 'available') is crucial for both correctness and utilizing indexes.

**Action:** Always look for patterns where multiple queries are performed on the same table with different filters, and consolidate them into a single raw select with conditional aggregates.

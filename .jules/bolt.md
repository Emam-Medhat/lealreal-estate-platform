## 2026-08-16 - Agent CRM Stats Query Reduction
**Learning:** Calling sequential `$agent->leads()->count()` or filtered query methods executes separate database roundtrips. In `AgentCrmService::getCrmStats`, replacing 6 individual counts with a single `selectRaw` using conditional aggregation (`COUNT(CASE WHEN ... THEN 1 END)`) reduced database queries from 6 to 1 while preserving exact result types.
**Action:** Always check CRM and analytics statistics methods for multiple `$relation()->where(...)->count()` calls and consolidate them into single `selectRaw` queries.

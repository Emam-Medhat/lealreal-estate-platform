## 2026-04-07 - AgentPerformanceService Optimization

**Learning:** Database queries in `AgentPerformanceService` were inefficient due to N+1 problems in `getRanking` and redundant query logic in `getMetrics`. An infinite recursion bug existed where `calculatePerformanceScore` called `getMetrics` without passing data.

**Action:** Consolidate multiple aggregate counts/sums into single queries using `selectRaw` with `COUNT(CASE WHEN...)` or `SUM(CASE WHEN...)`. Always pass already-fetched data to calculation methods to prevent recursive API/service calls. Eager load or bulk fetch relationships for lists of entities.

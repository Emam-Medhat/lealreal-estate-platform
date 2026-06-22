## 2026-06-21 - AgentPerformanceService Optimization
**Learning:** The AgentPerformanceService had an infinite recursion bug where `getMetrics` called `calculatePerformanceScore` which in turn called `getMetrics`. It also suffered from an N+1 query problem in `getRanking` because it fetched metrics for each agent in a loop.
**Action:** Implement a non-recursive metrics fetching method for internal score calculations and use batch aggregation (groupBy agent_id) when calculating rankings for multiple agents.

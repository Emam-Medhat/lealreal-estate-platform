## 2026-03-05 - Bulk Lead Status Funnel Aggregation

**Learning:** `LeadService::getConversionFunnel()` suffered from an $O(N)$ N+1 query pattern where `countByStatusName` and `countTotal` were executed inside a `foreach` loop over `$statuses`. Grouping counts via `Lead::select('lead_status', DB::raw('COUNT(*) as count'))->groupBy('lead_status')->pluck('count', 'lead_status')` reduces the funnel calculation from $1 + 2N$ database queries down to 3 queries regardless of status count.

**Action:** When calculating stage distributions or funnels over predefined status entities, aggregate all record counts with a single `GROUP BY` query upfront instead of querying count per status in a loop.

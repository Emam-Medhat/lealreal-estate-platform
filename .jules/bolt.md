## 2026-09-09 - Consolidating Property and Review Aggregations in AgentDetails

**Learning:** `AgentService::getAgentDetails` was performing 4 separate database count and average queries for `total_properties`, `sold_properties`, `total_reviews`, and `average_rating`. Consolidating property statistics into a single SQL conditional aggregation (`COUNT(*)` and `COUNT(CASE WHEN status = ? THEN 1 END)`) and review statistics into a single aggregate query (`COUNT(*)` and `AVG(rating)`) reduces calculation queries from 4 down to 2.

**Action:** Whenever calculating multiple stats over the same Eloquent relationship (e.g. counts per status, average ratings), combine them into single `selectRaw` aggregate queries instead of running multiple independent queries against the relationship.

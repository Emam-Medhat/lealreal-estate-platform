## 2026-02-23 - Agent Appointment Controller Stats Optimization
**Learning:** Consolidating multiple `count()` calls in `AgentAppointmentController::getAppointmentStats` into a single `selectRaw` query with conditional aggregations (`COUNT(CASE WHEN ... THEN 1 END)`) and index-friendly date parameters reduced stats database queries from 7 down to 1.
**Action:** When calculating grouped model metrics over multiple statuses and date ranges, use a single `selectRaw` query with `CASE` statements and date range parameters instead of multiple individual Eloquent count queries.

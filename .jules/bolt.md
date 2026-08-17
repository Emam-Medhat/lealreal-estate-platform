## 2026-08-17 - Carbon Date Range Mutation Bug in Analytics Services
**Learning:** Calling `$now->subMonth()->startOfMonth()` on a shared `$now = now()` Carbon instance in Laravel mutates the object in place, causing subsequent date calls (like `$now->endOfMonth()`) to calculate boundaries based on the mutated date rather than the current date.
**Action:** Always use `$now->copy()` or fresh `now()` instances when computing date boundaries in service helpers.

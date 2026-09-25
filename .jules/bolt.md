## 2026-09-25 - Public Controller Method vs Private Helper Recursion
**Learning:** In PHP, defining a private helper method with the exact same name as a public controller route action (`getAnalyticsData`) causes infinite recursion when called within the method or controller instance.
**Action:** When creating or refactoring controller helper functions, use explicit helper names (e.g., `fetchAnalyticsData`) to avoid route method collisions.

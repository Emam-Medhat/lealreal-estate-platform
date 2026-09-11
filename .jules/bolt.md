
## 2026-09-11 - Direct Column Operations in Property Analytics
**Learning:** Instantiating full Eloquent models for basic count increments or simple metric fetching incurs unnecessary SELECT queries and model hydration overhead. Utilizing `Property::where('id', $propertyId)->increment('views_count')` and `Property::where('id', $propertyId)->select(['views_count', 'favorites_count', 'inquiries_count'])->first()` optimizes performance by performing atomic database updates and reducing memory footprint.
**Action:** Always check if scalar retrieval or direct atomic updates can replace full Eloquent model hydration in high-frequency service operations.

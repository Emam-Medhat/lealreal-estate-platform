<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Models\Property;
use App\Services\CacheService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PropertyRepository extends BaseRepository implements PropertyRepositoryInterface
{
    protected $defaultRelations = [
        'agent',
        'media',
        'location:id,city,state,country',
        'price:id,price,currency',
        'propertyType'
    ];

    public function __construct(Property $model)
    {
        parent::__construct($model);
    }

    /**
     * Get featured properties with optimized caching and eager loading
     */
    public function getFeatured(int $limit = 6): Collection
    {
        return $this->remember("properties_featured_{$limit}", function () use ($limit) {
            return $this->model->with([
                'agent:id,name',
                'media',
                'location:id,city,state,country',
                'price:id,price,currency',
                'propertyType'
            ])
                ->where('status', 'published')
                ->where('featured', true)
                ->orderBy('featured_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        }, ['properties'], 1800);
    }

    /**
     * Get latest active properties with optimized queries
     */
    public function getLatestActive(int $limit = 6): Collection
    {
        return $this->remember("properties_latest_{$limit}", function () use ($limit) {
            return $this->model->with([
                'agent:id,name',
                'media',
                'location:id,city,state,country',
                'propertyType'
            ])
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        }, ['properties'], 900);
    }

    /**
     * Get properties by type slug with caching
     */
    public function getByTypeSlug(string $slug, int $limit = 3): Collection
    {
        return $this->remember("properties_type_{$slug}_{$limit}", function () use ($slug, $limit) {
            return $this->model->with([
                'agent:id,name',
                'media',
                'propertyType'
            ])
                ->where('property_type', $slug)
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        }, ['properties'], 1800);
    }

    /**
     * Get filtered properties with advanced filtering and optimization
     */
    public function getFilteredProperties(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $page = request()->get('page', 1);
        $cacheKey = 'properties_filtered_' . md5(serialize($filters) . $perPage . $page);

        return $this->remember($cacheKey, function () use ($filters, $perPage) {
            $query = $this->model->with([
                'agent:id,name,email',
                'media',
                'location:id,city,state,country',
                'price:id,price,currency',
                'propertyType'
            ]);

            // Apply filters efficiently
            $this->applyPropertyFilters($query, $filters);

            // Apply search if provided
            if (!empty($filters['search'])) {
                $this->applyPropertySearch($query, $filters['search']);
            }

            // Apply sorting
            $this->applyPropertySorting($query, $filters);

            return $query->paginate($perPage);
        }, ['properties'], 600);
    }

    /**
     * Get active properties (Alias for getFilteredProperties)
     */
    public function getActiveProperties(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->getFilteredProperties($filters, $perPage);
    }

    /**
     * Get properties for a specific agent
     */
    public function getAgentPropertiesPaginated(int $agentId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $filters['agent_id'] = $agentId;
        return $this->getFilteredProperties($filters, $perPage);
    }

    /**
     * Get popular properties
     */
    public function getPopular(int $limit = 6, array $relations = []): Collection
    {
        return $this->remember("properties_popular_{$limit}", function () use ($limit, $relations) {
            return $this->model->with($relations)
                ->where('status', 'published')
                ->orderBy('views_count', 'desc')
                ->take($limit)
                ->get();
        }, ['properties'], 3600);
    }

    public function getMarketMetrics(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate, ?string $marketArea = null): array
    {
        // Not using cache here as params vary wildly, but should be added if needed
        $query = $this->model->whereBetween('created_at', [$startDate, $endDate]);

        if ($marketArea) {
             $query->where(function($q) use ($marketArea) {
                $q->where('city', 'LIKE', "%{$marketArea}%")
                  ->orWhere('state', 'LIKE', "%{$marketArea}%");
             });
        }

        $stats = $query->selectRaw('
            COALESCE(AVG(price), 0) as average_price,
            COUNT(*) as total_listings,
            SUM(CASE WHEN status = "sold" THEN 1 ELSE 0 END) as total_sales,
            AVG(CASE WHEN status = "sold" THEN DATEDIFF(updated_at, created_at) ELSE NULL END) as average_days_on_market
        ')->first();
        
        // Calculate median price separately (MySQL doesn't have MEDIAN function)
        // For performance, we might approximate or skip. Here is a simple implementation:
        // $medianPrice = ... 

        return [
            'average_price' => (float) $stats->average_price,
            'median_price' => (float) $stats->average_price, // Fallback to avg for now or implement robust median
            'total_listings' => (int) $stats->total_listings,
            'total_sales' => (int) $stats->total_sales,
            'average_days_on_market' => (float) $stats->average_days_on_market,
            'price_per_square_foot' => 0, // Need sqft column
            'inventory_level' => 0, 
            'price_trends' => [],
            'market_segments' => [],
            'neighborhood_data' => [],
            'market_indicators' => []
        ];
    }

    /**
     * Get latest properties
     */
    public function getLatest(int $limit = 6, array $relations = []): Collection
    {
        $relations = !empty($relations) ? $relations : $this->defaultRelations;

        return $this->remember("properties_latest_standard_{$limit}", function () use ($limit, $relations) {
            return $this->model->with($relations)
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        }, ['properties'], 900);
    }

    /**
     * Find property by ID with relations
     */
    public function find(int $id, array $columns = ['*'], array $relations = []): ?\Illuminate\Database\Eloquent\Model
    {
        $relations = !empty($relations) ? $relations : $this->defaultRelations;
        return $this->findById($id, $columns, $relations);
    }

    /**
     * Search properties with full-text search optimization
     */
    public function searchProperties(string $query, array $filters = [], int $limit = 50): Collection
    {
        $cacheKey = "properties_search_" . md5($query . serialize($filters) . $limit);

        return $this->remember($cacheKey, function () use ($query, $filters, $limit) {
            $propertyQuery = $this->model->with([
                'agent:id,name',
                'media',
                'location:id,city,state,country',
                'propertyType'
            ]);

            // Use full-text search if available
            if (DB::getDriverName() === 'mysql') {
                $propertyQuery->whereRaw("MATCH(title, description, address) AGAINST(? IN BOOLEAN MODE)", [$query]);
            } else {
                // Fallback to LIKE search
                $propertyQuery->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%")
                        ->orWhere('address', 'LIKE', "%{$query}%")
                        ->orWhere('city', 'LIKE', "%{$query}%");
                });
            }

            // Apply additional filters
            $this->applyPropertyFilters($propertyQuery, $filters);

            return $propertyQuery->orderBy('featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        }, ['properties'], 300);
    }

    /**
     * Get properties by location with geo-optimization
     */
    public function getPropertiesByLocation(string $city, string $state = null, int $limit = 20): Collection
    {
        $cacheKey = "properties_location_{$city}_{$state}_{$limit}";

        return $this->remember($cacheKey, function () use ($city, $state, $limit) {
            $query = $this->model->with([
                'agent:id,name',
                'media',
                'location:id,city,state,country',
                'propertyType'
            ])
                ->where('city', $city)
                ->where('status', 'published');

            if ($state) {
                $query->where('state', $state);
            }

            return $query->orderBy('featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        }, ['properties'], 1800);
    }

    /**
     * Get property statistics with single query optimization
     */
    public function getPropertyStats(): array
    {
        return $this->remember('property_stats', function () {
            $stats = $this->model->selectRaw('
                COUNT(*) as total_properties,
                COUNT(CASE WHEN status = "published" THEN 1 END) as published_properties,
                COUNT(CASE WHEN status = "draft" THEN 1 END) as draft_properties,
                COUNT(CASE WHEN featured = 1 THEN 1 END) as featured_properties,
                COUNT(CASE WHEN property_type = "apartment" THEN 1 END) as apartments,
                COUNT(CASE WHEN property_type = "villa" THEN 1 END) as villas,
                COUNT(CASE WHEN property_type = "house" THEN 1 END) as houses,
                COUNT(CASE WHEN property_type = "land" THEN 1 END) as lands,
                COALESCE(AVG(price), 0) as average_price,
                MIN(price) as min_price,
                MAX(price) as max_price,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as properties_this_week,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as properties_this_month,
                SUM(views_count) as total_views,
                SUM(inquiries_count) as total_inquiries
            ')->first();

            return [
                'total_properties' => (int) $stats->total_properties,
                'published_properties' => (int) $stats->published_properties,
                'draft_properties' => (int) $stats->draft_properties,
                'featured_properties' => (int) $stats->featured_properties,
                'apartments' => (int) $stats->apartments,
                'villas' => (int) $stats->villas,
                'houses' => (int) $stats->houses,
                'lands' => (int) $stats->lands,
                'average_price' => (float) $stats->average_price,
                'min_price' => (float) $stats->min_price,
                'max_price' => (float) $stats->max_price,
                'properties_this_week' => (int) $stats->properties_this_week,
                'properties_this_month' => (int) $stats->properties_this_month,
                'total_views' => (int) $stats->total_views,
                'total_inquiries' => (int) $stats->total_inquiries,
            ];
        }, ['analytics'], 1800);
    }

    /**
     * Get properties for export with memory-efficient chunking
     */
    public function getPropertiesForExport(array $filters = []): \Generator
    {
        $query = $this->model->with([
            'agent:id,name,email',
            'location:id,city,state,country',
            'price:id,price,currency',
            'media',
            'propertyType'
        ]);

        // Apply filters
        $this->applyPropertyFilters($query, $filters);

        // Use lazy collection for memory efficiency
        foreach ($query->orderBy('created_at', 'desc')->lazy() as $property) {
            yield $property;
        }
    }

    /**
     * Get property performance metrics
     */
    public function getPropertyPerformanceMetrics(): array
    {
        return $this->remember('property_performance', function () {
            $properties = $this->model->select(['id', 'views_count', 'inquiries_count', 'created_at', 'status'])
                ->where('created_at', '>=', now()->subDays(30))
                ->get();

            return [
                'average_views' => $properties->avg('views_count'),
                'average_inquiries' => $properties->avg('inquiries_count'),
                'conversion_rate' => $properties->where('inquiries_count', '>', 0)->count() > 0
                    ? ($properties->where('inquiries_count', '>', 0)->count() / $properties->count()) * 100
                    : 0,
                'total_views' => $properties->sum('views_count'),
                'total_inquiries' => $properties->sum('inquiries_count'),
                'most_viewed' => $properties->sortByDesc('views_count')->take(5)->pluck('id')->toArray(),
                'most_inquired' => $properties->sortByDesc('inquiries_count')->take(5)->pluck('id')->toArray(),
                'views_growth_rate' => $this->calculateGrowthRate('views_count'),
                'inquiries_growth_rate' => $this->calculateGrowthRate('inquiries_count'),
            ];
        }, ['analytics'], 3600);
    }

    /**
     * Apply property filters efficiently
     */
    private function applyPropertyFilters($query, array $filters): void
    {
        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Type filter
        if (!empty($filters['type'])) {
            $query->where('property_type', $filters['type']);
        }

        // Price range filter
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Area filter
        if (!empty($filters['min_area'])) {
            $query->where('area', '>=', $filters['min_area']);
        }

        if (!empty($filters['max_area'])) {
            $query->where('area', '<=', $filters['max_area']);
        }

        // Bedrooms filter
        if (!empty($filters['bedrooms'])) {
            $query->where('bedrooms', $filters['bedrooms']);
        }

        // Bathrooms filter
        if (!empty($filters['bathrooms'])) {
            $query->where('bathrooms', $filters['bathrooms']);
        }

        // City filter
        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        // State filter
        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        // Agent filter
        if (!empty($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }

        // Featured filter
        if (isset($filters['featured'])) {
            $query->where('featured', $filters['featured']);
        }

        // Date range filter
        if (!empty($filters['date_range'])) {
            $this->applyDateRangeFilter($query, $filters['date_range']);
        }
    }

    /**
     * Apply property search
     */
    private function applyPropertySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('address', 'LIKE', "%{$search}%")
                ->orWhere('city', 'LIKE', "%{$search}%")
                ->orWhere('state', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Apply property sorting
     */
    private function applyPropertySorting($query, array $filters): void
    {
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        $allowedSorts = [
            'created_at',
            'price',
            'area',
            'bedrooms',
            'bathrooms',
            'views_count',
            'inquiries_count',
            'featured_at'
        ];

        if (in_array($sortBy, $allowedSorts) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
        }
    }

    /**
     * Apply date range filter
     */
    private function applyDateRangeFilter($query, array $dateRange): void
    {
        if (isset($dateRange['start'])) {
            $query->whereDate('created_at', '>=', $dateRange['start']);
        }

        if (isset($dateRange['end'])) {
            $query->whereDate('created_at', '<=', $dateRange['end']);
        }
    }

    /**
     * Calculate growth rate for a specific metric
     */
    private function calculateGrowthRate(string $metric): float
    {
        $thisMonth = $this->model->whereMonth('created_at', now()->month)->sum($metric);
        $lastMonth = $this->model->whereMonth('created_at', now()->subMonth())->sum($metric);

        return $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    /**
     * Get property recommendations based on user preferences
     */
    public function getPropertyRecommendations(array $preferences, int $limit = 10): Collection
    {
        $cacheKey = 'property_recommendations_' . md5(serialize($preferences) . $limit);

        return $this->remember($cacheKey, function () use ($preferences, $limit) {
            $query = $this->model->with([
                'agent:id,name',
                'media',
                'propertyType'
            ])
                ->where('status', 'published');

            // Apply user preferences
            if (!empty($preferences['property_types'])) {
                $query->whereIn('property_type', $preferences['property_types']);
            }

            if (!empty($preferences['price_range'])) {
                $query->whereBetween('price', $preferences['price_range']);
            }

            if (!empty($preferences['cities'])) {
                $query->whereIn('city', $preferences['cities']);
            }

            return $query->orderBy('featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();
        }, ['properties'], 1800);
    }

    /**
     * Count properties by date.
     *
     * @param string $date
     * @return int
     */
    public function countByDate(string $date): int
    {
        return $this->model->whereDate('created_at', $date)->count();
    }

    /**
     * Count properties by status.
     *
     * @param string $status
     * @return int
     */
    public function countByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }
}

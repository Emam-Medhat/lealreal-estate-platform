<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Models\Lead;
use App\Services\CacheService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LeadRepository extends BaseRepository implements LeadRepositoryInterface
{
    /**
     * Default relations for eager loading
     */
    protected $defaultRelations;

    public function __construct(Lead $model)
    {
        parent::__construct($model);
        
        $this->defaultRelations = [
            'source:id,name',
            'status:id,name,color',
            'assignedTo:id,full_name,email,profile_image',
            'createdBy:id,full_name,email',
            'tags:id,name,color',
            'activities' => function ($query) {
                return $query->latest()->take(5);
            }
        ];
    }

    /**
     * Get filtered leads with optimized eager loading and caching
     */
    public function getFilteredLeads(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $cacheKey = 'leads_filtered_' . md5(serialize($filters) . $perPage);
        
        return $this->remember('getFilteredLeads', function () use ($filters, $perPage) {
            $query = $this->model->with($this->defaultRelations);

            // Apply filters efficiently
            $this->applyLeadFilters($query, $filters);

            // Apply search if provided
            if (!empty($filters['search'])) {
                $this->applyLeadSearch($query, $filters['search']);
            }

            // Apply sorting
            $this->applyLeadSorting($query, $filters);

            return $query->paginate($perPage, [
                'id', 'uuid', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                'company', 'lead_status', 'priority', 'source_id', 'assigned_to', 'budget',
                'property_type', 'location', 'created_at', 'updated_at'
            ]);
        }, ['leads'], 600);
    }

    /**
     * Search leads with full-text search optimization
     */
    public function searchLeads(string $query, array $filters = [], int $limit = 50): Collection
    {
        $cacheKey = 'leads_search_' . md5($query . serialize($filters) . $limit);
        
        return $this->remember('searchLeads', function () use ($query, $filters, $limit) {
            $leadQuery = $this->model->with($this->defaultRelations);

            // Use full-text search if available
            if (DB::getDriverName() === 'mysql') {
                $leadQuery->whereRaw("MATCH(first_name, last_name, full_name, email, company) AGAINST(? IN BOOLEAN MODE)", [$query]);
            } else {
                // Fallback to LIKE search
                $leadQuery->where(function ($q) use ($query) {
                    $q->where('first_name', 'LIKE', "%{$query}%")
                      ->orWhere('last_name', 'LIKE', "%{$query}%")
                      ->orWhere('full_name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%")
                      ->orWhere('company', 'LIKE', "%{$query}%")
                      ->orWhere('phone', 'LIKE', "%{$query}%");
                });
            }

            // Apply additional filters
            $this->applyLeadFilters($leadQuery, $filters);

            return $leadQuery->orderBy('created_at', 'desc')
                           ->take($limit)
                           ->get([
                               'id', 'uuid', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                               'company', 'lead_status', 'priority', 'source_id', 'assigned_to',
                               'created_at', 'updated_at'
                           ]);
        }, ['leads'], 300);
    }

    /**
     * Get leads for export with memory-efficient chunking
     */
    public function getLeadsForExport(array $filters = []): \Generator
    {
        $query = $this->model->with([
            'source:id,name',
            'status:id,name',
            'assignedTo:id,full_name,email',
            'tags:id,name,color'
        ]);

        // Apply filters
        $this->applyLeadFilters($query, $filters);

        // Use chunking for memory efficiency
        foreach ($query->orderBy('created_at', 'desc')->chunk(1000) as $chunk) {
            yield $chunk;
        }
    }

    /**
     * Get lead statistics with single query optimization
     */
    public function getLeadStats(): array
    {
        return $this->remember('getLeadStats', function () {
            $stats = $this->model->selectRaw('
                COUNT(*) as total_leads,
                COUNT(CASE WHEN lead_status = "new" THEN 1 END) as new_leads,
                COUNT(CASE WHEN lead_status = "contacted" THEN 1 END) as contacted_leads,
                COUNT(CASE WHEN lead_status = "qualified" THEN 1 END) as qualified_leads,
                COUNT(CASE WHEN lead_status = "converted" THEN 1 END) as converted_leads,
                COUNT(CASE WHEN lead_status = "lost" THEN 1 END) as lost_leads,
                COUNT(CASE WHEN priority = "high" THEN 1 END) as high_priority_leads,
                COUNT(CASE WHEN priority = "medium" THEN 1 END) as medium_priority_leads,
                COUNT(CASE WHEN priority = "low" THEN 1 END) as low_priority_leads,
                COUNT(CASE WHEN assigned_to IS NOT NULL THEN 1 END) as assigned_leads,
                COUNT(CASE WHEN assigned_to IS NULL THEN 1 END) as unassigned_leads,
                AVG(CASE WHEN budget > 0 THEN budget END) as average_budget,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as leads_this_week,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as leads_this_month,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY) THEN 1 END) as leads_this_quarter,
                COUNT(CASE WHEN lead_status = "converted" AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as conversions_this_month
            ')->first();

            return [
                'total_leads' => (int) $stats->total_leads,
                'new_leads' => (int) $stats->new_leads,
                'contacted_leads' => (int) $stats->contacted_leads,
                'qualified_leads' => (int) $stats->qualified_leads,
                'converted_leads' => (int) $stats->converted_leads,
                'lost_leads' => (int) $stats->lost_leads,
                'high_priority_leads' => (int) $stats->high_priority_leads,
                'medium_priority_leads' => (int) $stats->medium_priority_leads,
                'low_priority_leads' => (int) $stats->low_priority_leads,
                'assigned_leads' => (int) $stats->assigned_leads,
                'unassigned_leads' => (int) $stats->unassigned_leads,
                'average_budget' => round($stats->average_budget ?? 0, 2),
                'leads_this_week' => (int) $stats->leads_this_week,
                'leads_this_month' => (int) $stats->leads_this_month,
                'leads_this_quarter' => (int) $stats->leads_this_quarter,
                'conversions_this_month' => (int) $stats->conversions_this_month,
                'conversion_rate' => $this->calculateConversionRate(),
                'growth_rate' => $this->calculateGrowthRate(),
                'assignment_rate' => $this->calculateAssignmentRate(),
            ];
        }, ['analytics'], 1800);
    }

    /**
     * Get leads by status with caching
     */
    public function getLeadsByStatus(string $status, int $limit = 50): Collection
    {
        return $this->remember('getLeadsByStatus', function () use ($status, $limit) {
            return $this->model->where('lead_status', $status)
                ->with(['assignedTo:id,full_name,email', 'source:id,name'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get([
                    'id', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                    'lead_status', 'priority', 'source_id', 'assigned_to', 'created_at'
                ]);
        }, ['leads'], 900);
    }

    /**
     * Get leads by priority with caching
     */
    public function getLeadsByPriority(string $priority, int $limit = 50): Collection
    {
        return $this->remember('getLeadsByPriority', function () use ($priority, $limit) {
            return $this->model->where('priority', $priority)
                ->with(['assignedTo:id,full_name,email', 'status:id,name,color'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get([
                    'id', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                    'lead_status', 'priority', 'assigned_to', 'created_at'
                ]);
        }, ['leads'], 900);
    }

    /**
     * Get leads by agent with caching
     */
    public function getLeadsByAgent(int $agentId, int $limit = 50): Collection
    {
        return $this->remember('getLeadsByAgent', function () use ($agentId, $limit) {
            return $this->model->where('assigned_to', $agentId)
                ->with(['source:id,name', 'status:id,name,color', 'tags:id,name,color'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get([
                    'id', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                    'lead_status', 'priority', 'source_id', 'created_at'
                ]);
        }, ['leads'], 900);
    }

    /**
     * Get recent leads with caching
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->remember('getRecent', function () use ($limit) {
            return $this->model->with(['assignedTo:id,full_name,email', 'status:id,name,color'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get([
                    'id', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                    'lead_status', 'priority', 'assigned_to', 'created_at'
                ]);
        }, ['leads'], 300);
    }

    /**
     * Count leads by status name
     */
    public function countByStatusName(string $statusName): int
    {
        return $this->model->where('lead_status', $statusName)->count();
    }

    /**
     * Count converted leads
     */
    public function countConverted(): int
    {
        return $this->model->where('lead_status', 'converted')->count();
    }

    /**
     * Sum estimated value of all leads
     */
    public function sumEstimatedValue(): float
    {
        return $this->model->where('estimated_value', '>', 0)->sum('estimated_value') ?? 0;
    }

    /**
     * Count total leads
     */
    public function countTotal(): int
    {
        return $this->model->count();
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return $this->remember('getDashboardStats', function () {
            return [
                'total_leads' => $this->countTotal(),
                'new_leads' => $this->countByStatusName('new'),
                'qualified_leads' => $this->countByStatusName('qualified'),
                'converted_leads' => $this->countConverted(),
                'total_estimated_value' => $this->sumEstimatedValue(),
                'conversion_rate' => $this->countTotal() > 0 ? ($this->countConverted() / $this->countTotal()) * 100 : 0,
            ];
        }, ['analytics'], 600);
    }

    /**
     * Get leads for export with memory-efficient chunking
     */
    public function getForExport(array $filters = [], int $chunkSize = 1000): \Generator
    {
        $query = $this->model->with([
            'source:id,name',
            'status:id,name',
            'assignedTo:id,full_name,email',
            'tags:id,name,color'
        ]);

        // Apply filters
        $this->applyLeadFilters($query, $filters);

        // Use chunking for memory efficiency
        foreach ($query->orderBy('created_at', 'desc')->chunk($chunkSize) as $chunk) {
            yield $chunk;
        }
    }

    /**
     * Get recent leads with caching (legacy method)
     */
    public function getRecentLeads(int $limit = 10): Collection
    {
        return $this->remember('getRecentLeads', function () use ($limit) {
            return $this->model->with(['assignedTo:id,full_name,email', 'status:id,name,color'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get([
                    'id', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                    'lead_status', 'priority', 'assigned_to', 'created_at'
                ]);
        }, ['leads'], 300);
    }

    /**
     * Get unassigned leads with caching
     */
    public function getUnassignedLeads(int $limit = 50): Collection
    {
        return $this->remember('getUnassignedLeads', function () use ($limit) {
            return $this->model->whereNull('assigned_to')
                ->with(['source:id,name', 'status:id,name,color'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get([
                    'id', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                    'lead_status', 'priority', 'source_id', 'created_at'
                ]);
        }, ['leads'], 900);
    }

    /**
     * Get high priority leads with caching
     */
    public function getHighPriorityLeads(int $limit = 50): Collection
    {
        return $this->remember('getHighPriorityLeads', function () use ($limit) {
            return $this->model->where('priority', 'high')
                ->with(['assignedTo:id,full_name,email', 'status:id,name,color'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get([
                    'id', 'first_name', 'last_name', 'full_name', 'email', 'phone',
                    'lead_status', 'priority', 'assigned_to', 'created_at'
                ]);
        }, ['leads'], 900);
    }

    /**
     * Get lead performance metrics
     */
    public function getLeadPerformanceMetrics(): array
    {
        return $this->remember('getLeadPerformanceMetrics', function () {
            $metrics = $this->model->select([
                'lead_status', 'priority', 'assigned_to', 'created_at'
            ])->where('created_at', '>=', now()->subDays(30))->get();

            return [
                'average_conversion_time' => $this->calculateAverageConversionTime($metrics),
                'best_conversion_source' => $this->getBestConversionSource(),
                'top_performing_agent' => $this->getTopPerformingAgent(),
                'most_common_status' => $this->getMostCommonStatus($metrics),
                'priority_distribution' => $this->getPriorityDistribution($metrics),
                'status_flow' => $this->getStatusFlow($metrics),
                'lead_sources_performance' => $this->getLeadSourcesPerformance(),
            ];
        }, ['analytics'], 3600);
    }

    /**
     * Apply lead filters efficiently
     */
    private function applyLeadFilters($query, array $filters): void
    {
        // Status filter
        if (!empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $query->whereIn('lead_status', $filters['status']);
            } else {
                $query->where('lead_status', $filters['status']);
            }
        }

        // Priority filter
        if (!empty($filters['priority'])) {
            if (is_array($filters['priority'])) {
                $query->whereIn('priority', $filters['priority']);
            } else {
                $query->where('priority', $filters['priority']);
            }
        }

        // Source filter
        if (!empty($filters['source'])) {
            $query->where('source_id', $filters['source']);
        }

        // Assigned to filter
        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        // Unassigned filter
        if (isset($filters['unassigned']) && $filters['unassigned']) {
            $query->whereNull('assigned_to');
        }

        // Created date range filter
        if (!empty($filters['created_at'])) {
            $this->applyDateRangeFilter($query, $filters['created_at'], 'created_at');
        }

        // Budget range filter
        if (!empty($filters['budget'])) {
            $this->applyRangeFilter($query, $filters['budget'], 'budget');
        }

        // Property type filter
        if (!empty($filters['property_type'])) {
            $query->where('property_type', $filters['property_type']);
        }

        // Location filter
        if (!empty($filters['location'])) {
            $query->where('location', 'LIKE', "%{$filters['location']}%");
        }

        // Company filter
        if (!empty($filters['company'])) {
            $query->where('company', 'LIKE', "%{$filters['company']}%");
        }
    }

    /**
     * Apply lead search
     */
    private function applyLeadSearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('full_name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%")
              ->orWhere('company', 'LIKE', "%{$search}%")
              ->orWhere('location', 'LIKE', "%{$search}%")
              ->orWhere('property_type', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Apply lead sorting
     */
    private function applyLeadSorting($query, array $filters): void
    {
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        $allowedSorts = [
            'created_at', 'updated_at', 'first_name', 'last_name', 'full_name',
            'email', 'lead_status', 'priority', 'budget', 'company'
        ];

        if (in_array($sortBy, $allowedSorts) && in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Apply date range filter
     */
    private function applyDateRangeFilter($query, array $dateRange, string $field): void
    {
        if (isset($dateRange['start'])) {
            $query->whereDate($field, '>=', $dateRange['start']);
        }
        
        if (isset($dateRange['end'])) {
            $query->whereDate($field, '<=', $dateRange['end']);
        }
    }

    /**
     * Apply range filter
     */
    private function applyRangeFilter($query, array $range, string $field): void
    {
        if (isset($range['min'])) {
            $query->where($field, '>=', $range['min']);
        }
        
        if (isset($range['max'])) {
            $query->where($field, '<=', $range['max']);
        }
    }

    /**
     * Calculate conversion rate
     */
    private function calculateConversionRate(): float
    {
        $totalLeads = $this->model->count();
        $convertedLeads = $this->model->where('lead_status', 'converted')->count();
        
        return $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;
    }

    /**
     * Calculate growth rate
     */
    private function calculateGrowthRate(): float
    {
        $thisMonth = $this->model->whereMonth('created_at', now()->month)->count();
        $lastMonth = $this->model->whereMonth('created_at', now()->subMonth())->count();

        return $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    /**
     * Calculate assignment rate
     */
    private function calculateAssignmentRate(): float
    {
        $totalLeads = $this->model->count();
        $assignedLeads = $this->model->whereNotNull('assigned_to')->count();
        
        return $totalLeads > 0 ? ($assignedLeads / $totalLeads) * 100 : 0;
    }

    /**
     * Calculate average conversion time
     */
    private function calculateAverageConversionTime(Collection $leads): float
    {
        $convertedLeads = $leads->where('lead_status', 'converted');
        
        if ($convertedLeads->isEmpty()) {
            return 0;
        }

        $totalDays = $convertedLeads->map(function ($lead) {
            return $lead->created_at->diffInDays(now());
        })->sum();

        return $totalDays / $convertedLeads->count();
    }

    /**
     * Get best conversion source
     */
    private function getBestConversionSource(): array
    {
        $bestSource = $this->model->select('source_id')
            ->selectRaw('COUNT(*) as total, COUNT(CASE WHEN lead_status = "converted" THEN 1 END) as converted')
            ->with('source:id,name')
            ->groupBy('source_id')
            ->orderByRaw('(converted / total) DESC')
            ->first();

        return [
            'source_id' => $bestSource->source_id ?? null,
            'source_name' => $bestSource->source->name ?? 'Unknown',
            'conversion_rate' => $bestSource ? ($bestSource->total > 0 ? ($bestSource->converted / $bestSource->total) * 100 : 0) : 0
        ];
    }

    /**
     * Get top performing agent
     */
    private function getTopPerformingAgent(): array
    {
        $topAgent = $this->model->select('assigned_to')
            ->selectRaw('COUNT(*) as total, COUNT(CASE WHEN lead_status = "converted" THEN 1 END) as converted')
            ->with('assignedTo')
            ->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->orderByRaw('(converted / total) DESC')
            ->first();

        return [
            'agent_id' => $topAgent->assigned_to ?? null,
            'agent_name' => $topAgent->assignedTo->name ?? $topAgent->assignedTo->full_name ?? 'Unknown',
            'conversion_rate' => $topAgent ? ($topAgent->total > 0 ? ($topAgent->converted / $topAgent->total) * 100 : 0) : 0
        ];
    }

    /**
     * Get most common status
     */
    private function getMostCommonStatus(Collection $leads): string
    {
        return $leads->groupBy('lead_status')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first() ?? 'unknown';
    }

    /**
     * Get priority distribution
     */
    private function getPriorityDistribution(Collection $leads): array
    {
        return $leads->groupBy('priority')
            ->map->count()
            ->toArray();
    }

    /**
     * Get status flow
     */
    private function getStatusFlow(Collection $leads): array
    {
        return $leads->groupBy('lead_status')
            ->map(function ($statusLeads) {
                return [
                    'count' => $statusLeads->count(),
                    'average_budget' => $statusLeads->avg('budget'),
                    'conversion_rate' => $statusLeads->where('lead_status', 'converted')->count() / $statusLeads->count() * 100
                ];
            })
            ->toArray();
    }

    /**
     * Get lead sources performance
     */
    private function getLeadSourcesPerformance(): array
    {
        return $this->model->select('source_id')
            ->selectRaw('COUNT(*) as total, COUNT(CASE WHEN lead_status = "converted" THEN 1 END) as converted')
            ->with('source:id,name')
            ->groupBy('source_id')
            ->get()
            ->map(function ($source) {
                return [
                    'source_name' => $source->source->name ?? 'Unknown',
                    'total_leads' => $source->total,
                    'converted_leads' => $source->converted,
                    'conversion_rate' => $source->total > 0 ? ($source->converted / $source->total) * 100 : 0
                ];
            })
            ->toArray();
    }
}

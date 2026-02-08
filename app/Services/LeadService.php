<?php

namespace App\Services;

use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use App\Models\LeadActivity;
use App\Models\LeadConversion;
use App\Models\LeadScore;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class LeadService
{
    protected $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    /**
     * Get paginated leads with filtering and optimized caching.
     */
    public function getLeads(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return CacheService::rememberLeads('filtered_' . md5(serialize($filters) . $perPage), function () use ($filters, $perPage) {
            return $this->leadRepository->getFilteredLeads($filters, $perPage);
        }, 'medium');
    }

    /**
     * Get recent leads with optimized caching.
     */
    public function getRecentLeads(int $limit = 10)
    {
        return CacheService::rememberLeads("recent_{$limit}", function () use ($limit) {
            return $this->leadRepository->getRecent($limit);
        }, 'short');
    }

    /**
     * Get lead sources with caching.
     */
    public function getActiveSources()
    {
        return CacheService::remember('lead_sources_active', function () {
            return LeadSource::where('is_active', 1)->orderBy('name')->get(['id', 'name']);
        }, 'extended');
    }

    /**
     * Get lead statuses with caching.
     */
    public function getActiveStatuses()
    {
        return CacheService::remember('lead_statuses_active', function () {
            return LeadStatus::where('is_active', 1)->orderBy('order')->get(['id', 'name', 'color']);
        }, 'extended');
    }

    /**
     * Get available agents with optimized caching.
     */
    public function getAvailableAgents()
    {
        return CacheService::rememberUsers('available_agents', function () {
            return User::whereIn('role', ['agent', 'admin'])
                ->where('account_status', 'active')
                ->get(['id', 'full_name', 'email']);
        }, 'long');
    }

    /**
     * Get dashboard statistics with single query and caching.
     */
    public function getDashboardStats(): array
    {
        return CacheService::rememberDashboard('leads_stats', function () {
            return $this->leadRepository->getDashboardStats();
        }, 'short');
    }

    /**
     * Get lead sources statistics with caching.
     */
    public function getLeadSourcesStats(int $limit = 5)
    {
        return CacheService::rememberAnalytics("lead_sources_stats_{$limit}", function () use ($limit) {
            return LeadSource::withCount('leads')
                ->orderBy('leads_count', 'desc')
                ->take($limit)
                ->get(['id', 'name', 'leads_count']);
        }, 'medium');
    }

    /**
     * Get lead statuses statistics with caching.
     */
    public function getLeadStatusesStats()
    {
        return CacheService::rememberAnalytics('lead_statuses_stats', function () {
            return LeadStatus::withCount('leads')
                ->orderBy('order')
                ->get(['id', 'name', 'color', 'leads_count']);
        }, 'medium');
    }

    /**
     * Get pipeline data with optimized eager loading and caching.
     */
    public function getPipelineData()
    {
        return CacheService::rememberDashboard('leads_pipeline', function () {
            return Lead::with(['source:id,name', 'status:id,name,color', 'assignedTo:id,full_name'])
                ->select(['id', 'uuid', 'first_name', 'last_name', 'email', 'lead_status', 'priority', 'lead_source', 'assigned_to', 'created_at'])
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }, 'short');
    }

    /**
     * Create lead with optimized operations and cache clearing.
     */
    public function createLead(array $data, ?array $tagIds = null): Lead
    {
        DB::beginTransaction();

        try {
            $lead = $this->leadRepository->create($data);

            if (!empty($tagIds)) {
                $lead->tags()->attach($tagIds);
            }

            // Create initial activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'type' => 'created',
                'description' => 'تم إنشاء العميل المحتمل',
                'user_id' => Auth::id() ?? $data['created_by'] ?? null,
            ]);

            // Calculate initial score
            $this->calculateLeadScore($lead);

            DB::commit();
            $this->clearLeadCaches();

            return $lead;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update lead with cache clearing.
     */
    public function updateLead(Lead $lead, array $data, ?array $tagIds = null): Lead
    {
        DB::beginTransaction();

        try {
            $oldStatusId = $lead->status_id;
            $lead->update($data);

            if ($tagIds !== null) {
                $lead->tags()->sync($tagIds);
            }

            // Create activity if status changed
            if ($oldStatusId != $lead->status_id) {
                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'type' => 'status_changed',
                    'description' => 'تم تغيير الحالة إلى ' . ($lead->status->name ?? $lead->status_id),
                    'user_id' => Auth::id(),
                ]);
            }

            // Calculate score
            $this->calculateLeadScore($lead);

            DB::commit();
            $this->clearLeadCaches();

            return $lead;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete lead with cache clearing.
     */
    public function deleteLead(Lead $lead): bool
    {
        DB::beginTransaction();

        try {
            $result = $this->leadRepository->deleteById($lead->id);
            $this->clearLeadCaches();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Convert lead to another entity.
     */
    public function convertLead(Lead $lead, array $data): LeadConversion
    {
        DB::beginTransaction();
        try {
            $conversion = LeadConversion::create([
                'lead_id' => $lead->id,
                'converted_to_type' => $data['converted_to_type'],
                'converted_to_id' => $data['converted_to_id'],
                'conversion_value' => $data['conversion_value'] ?? 0,
                'conversion_date' => now(),
                'notes' => $data['notes'] ?? null,
                'converted_by' => Auth::id(),
            ]);

            $convertedStatus = LeadStatus::where('name', 'محول')->first();
            $lead->update([
                'converted_at' => now(),
                'status_id' => $convertedStatus ? $convertedStatus->id : $lead->status_id,
            ]);

            LeadActivity::create([
                'lead_id' => $lead->id,
                'type' => 'converted',
                'description' => 'تم تحويل العميل المحتمل إلى ' . $data['converted_to_type'],
                'user_id' => Auth::id(),
            ]);

            DB::commit();
            $this->clearLeadCaches();
            return $conversion;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Score a lead.
     */
    public function scoreLead(Lead $lead, array $data): LeadScore
    {
        $score = LeadScore::create([
            'lead_id' => $lead->id,
            'score' => $data['score'],
            'factors' => $data['factors'] ?? [],
            'calculated_by' => Auth::id(),
        ]);

        $lead->update(['score' => $data['score']]);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => 'scored',
            'description' => 'تم تقييم العميل المحتمل بدرجة ' . $data['score'],
            'user_id' => Auth::id(),
        ]);

        $this->clearLeadCaches();
        return $score;
    }

    /**
     * Calculate and store lead score automatically based on factors.
     */
    public function calculateLeadScore(Lead $lead)
    {
        $score = 0;
        $factors = [];

        // Base score based on source
        if ($lead->source) {
            $weight = $lead->source->weight ?? 0;
            $score += $weight;
            $factors[] = 'المصدر: ' . $lead->source->name . ' (+' . $weight . ')';
        }

        // Score based on completeness
        $completeness = 0;
        if ($lead->email)
            $completeness += 20;
        if ($lead->phone)
            $completeness += 20;
        if ($lead->company)
            $completeness += 15;
        if ($lead->job_title || $lead->position)
            $completeness += 15;
        if ($lead->estimated_value)
            $completeness += 30;

        $score += $completeness;
        $factors[] = 'اكتمال البيانات: ' . $completeness . '%';

        // Score based on priority
        $priorityScores = [
            'low' => 5,
            'medium' => 10,
            'high' => 20,
            'critical' => 30,
        ];

        $priority = strtolower($lead->priority);
        if (isset($priorityScores[$priority])) {
            $score += $priorityScores[$priority];
            $factors[] = 'الأولوية: ' . $priority . ' (+' . $priorityScores[$priority] . ')';
        }

        LeadScore::updateOrCreate(
            ['lead_id' => $lead->id],
            [
                'score' => $score,
                'factors' => $factors,
                'calculated_by' => Auth::id() ?? 1,
            ]
        );

        $lead->update(['score' => $score]);
    }

    /**
     * Clear lead-related caches.
     */
    public function clearLeadCaches(): void
    {
        CacheService::clearTags([CacheService::TAGS['leads'], CacheService::TAGS['dashboard'], CacheService::TAGS['analytics']]);
    }

    /**
     * Get lead conversion funnel data.
     */
    public function getConversionFunnel()
    {
        return CacheService::rememberAnalytics('lead_conversion_funnel', function () {
            $statuses = LeadStatus::orderBy('order')->get(['id', 'name']);
            $funnel = [];

            foreach ($statuses as $status) {
                $count = $this->leadRepository->countByStatusName($status->name);
                $funnel[] = [
                    'status' => $status->name,
                    'count' => $count,
                    'percentage' => $this->calculateConversionPercentage($count)
                ];
            }

            return $funnel;
        }, 'medium');
    }

    private function calculateConversionPercentage(int $count): float
    {
        $totalLeads = $this->leadRepository->countTotal();
        return $totalLeads > 0 ? round(($count / $totalLeads) * 100, 2) : 0;
    }

    public function getLeadsForExport(array $filters = []): \Generator
    {
        return $this->leadRepository->getForExport($filters);
    }

    public function getTotalEstimatedValue(): float
    {
        return $this->leadRepository->sumEstimatedValue();
    }
}

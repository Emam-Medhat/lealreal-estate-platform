<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Services\LeadService;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeadApiController extends ApiController
{
    protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
        $this->middleware('auth:api');
    }

    /**
     * Get paginated leads with filtering
     */
    public function index(Request $request): JsonResponse
    {
        $this->rateLimit($request, 100, 5); // 100 requests per 5 minutes

        $filters = $request->only(['search', 'status', 'source', 'assigned_to', 'priority', 'date_range']);
        $perPage = min($request->get('per_page', 20), 100); // Max 100 per page

        $leads = $this->getCachedData(
            'leads:' . md5(serialize($filters) . $perPage),
            function () use ($filters, $perPage) {
                return $this->leadService->getLeads($filters, $perPage);
            },
            'short'
        );

        return $this->paginatedResponse($leads, 'Leads retrieved successfully');
    }

    /**
     * Get a specific lead
     */
    public function show(Lead $lead): JsonResponse
    {
        $this->rateLimit(request(), 200, 5);

        $leadData = $this->getCachedData(
            "lead:{$lead->id}",
            function () use ($lead) {
                return $lead->load([
                    'source:id,name',
                    'status:id,name,color',
                    'assignedTo:id,full_name,email',
                    'activities' => function ($query) {
                        return $query->with('user:id,full_name')
                              ->latest()
                              ->take(10);
                    },
                    'notes' => function ($query) {
                        return $query->with('user:id,full_name')
                              ->latest()
                              ->take(10);
                    }
                ]);
            },
            'medium'
        );

        return $this->apiResponse($leadData, 'Lead retrieved successfully');
    }

    /**
     * Create a new lead
     */
    public function store(StoreLeadRequest $request): JsonResponse
    {
        $this->rateLimit($request, 30, 5); // Lower rate for writes

        $validated = $request->validated();
        
        // Add UUID and other required fields
        $leadData = array_merge($validated, [
            'uuid' => \Illuminate\Support\Str::uuid(),
            'lead_status' => $this->getStatusName($validated['status_id'] ?? null),
            'priority' => $this->getPriorityName($validated['priority'] ?? 2),
            'created_by' => auth()->id(),
        ]);

        $lead = $this->leadService->createLead($leadData);
        
        // Clear relevant caches
        $this->clearApiCache('leads');
        $this->clearApiCache('dashboard');

        return $this->apiResponse($lead, 'Lead created successfully', 201);
    }

    /**
     * Update a lead
     */
    public function update(Request $request, Lead $lead): JsonResponse
    {
        $this->rateLimit($request, 50, 5);

        $validated = $this->validateApiRequest($request, [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:leads,email,' . $lead->id,
            'phone' => 'sometimes|string|max:20',
            'company' => 'sometimes|string|max:255',
            'job_title' => 'sometimes|string|max:255',
            'lead_source' => 'sometimes|nullable|string',
            'lead_status' => 'sometimes|string',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'priority' => 'sometimes|integer|in:1,2,3',
            'estimated_value' => 'sometimes|numeric|min:0',
            'notes' => 'sometimes|string|max:2000',
        ]);

        // Convert status_id to lead_status if provided
        if (isset($validated['status_id'])) {
            $validated['lead_status'] = $this->getStatusName($validated['status_id']);
            unset($validated['status_id']);
        }

        // Convert priority to integer
        if (isset($validated['priority'])) {
            $validated['priority'] = (int) $validated['priority'];
        }

        $this->leadService->updateLead($lead, $validated);
        
        // Clear relevant caches
        $this->clearApiCache('leads');
        $this->clearApiCache("lead:{$lead->id}");

        return $this->apiResponse($lead->fresh(), 'Lead updated successfully');
    }

    /**
     * Delete a lead
     */
    public function destroy(Lead $lead): JsonResponse
    {
        $this->rateLimit(request(), 30, 5);

        $this->leadService->deleteLead($lead);
        
        // Clear relevant caches
        $this->clearApiCache('leads');
        $this->clearApiCache("lead:{$lead->id}");

        return $this->apiResponse(null, 'Lead deleted successfully');
    }

    /**
     * Search leads
     */
    public function search(Request $request): JsonResponse
    {
        $this->rateLimit($request, 100, 5);

        $query = $request->get('q', '');
        $filters = $request->only(['status', 'assigned_to', 'priority']);
        $limit = min($request->get('limit', 50), 100);

        if (empty($query)) {
            return $this->errorResponse('Search query is required', 400);
        }

        $leads = $this->getCachedData(
            'search:' . md5($query . serialize($filters) . $limit),
            function () use ($query, $filters, $limit) {
                return $this->leadService->searchLeads($query, $filters, $limit);
            },
            'short'
        );

        return $this->apiResponse($leads, 'Search results retrieved successfully');
    }

    /**
     * Get dashboard statistics
     */
    public function dashboard(): JsonResponse
    {
        $this->rateLimit(request(), 60, 5);

        $stats = $this->getCachedData(
            'dashboard_stats',
            function () {
                return $this->leadService->getDashboardStats();
            },
            'short'
        );

        return $this->apiResponse($stats, 'Dashboard statistics retrieved successfully');
    }

    /**
     * Get conversion funnel
     */
    public function funnel(): JsonResponse
    {
        $this->rateLimit(request(), 60, 10);

        $funnel = $this->getCachedData(
            'conversion_funnel',
            function () {
                return $this->leadService->getConversionFunnel();
            },
            'medium'
        );

        return $this->apiResponse($funnel, 'Conversion funnel retrieved successfully');
    }

    /**
     * Bulk update leads
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->rateLimit($request, 10, 5); // Very low rate for bulk operations

        $validated = $this->validateApiRequest($request, [
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:leads,id',
            'data' => 'required|array',
            'data.lead_status' => 'sometimes|string',
            'data.assigned_to' => 'sometimes|nullable|exists:users,id',
            'data.priority' => 'sometimes|integer|in:1,2,3',
        ]);

        $updated = $this->leadService->bulkUpdateLeads($validated['ids'], $validated['data']);
        
        // Clear relevant caches
        $this->clearApiCache('leads');
        $this->clearApiCache('dashboard');

        return $this->apiResponse(['updated_count' => $updated], 'Leads updated successfully');
    }

    /**
     * Export leads
     */
    public function export(Request $request): JsonResponse
    {
        $this->rateLimit($request, 5, 10); // Very low rate for exports

        $filters = $request->only(['status', 'assigned_to', 'date_range']);
        $format = $request->get('format', 'csv');

        if (!in_array($format, ['csv', 'excel', 'json'])) {
            return $this->errorResponse('Invalid format. Use csv, excel, or json', 400);
        }

        // Generate export job
        $job = new \App\Jobs\ExportLeadsJob($filters, $format, auth()->id());
        dispatch($job);

        return $this->apiResponse(
            ['job_id' => $job->getJobId()],
            'Export job started successfully',
            202
        );
    }

    /**
     * Get lead activities
     */
    public function activities(Lead $lead): JsonResponse
    {
        $this->rateLimit(request(), 100, 5);

        $activities = $this->getCachedData(
            "lead_activities:{$lead->id}",
            function () use ($lead) {
                return $lead->activities()
                    ->with('user:id,full_name,email')
                    ->latest()
                    ->paginate(20);
            },
            'short'
        );

        return $this->paginatedResponse($activities, 'Activities retrieved successfully');
    }

    /**
     * Get available statistics
     */
    public function stats(): JsonResponse
    {
        $this->rateLimit(request(), 60, 5);

        $stats = $this->getCachedData(
            'lead_stats',
            function () {
                return [
                    'total_leads' => $this->leadService->getRepository()->countTotal(),
                    'new_leads' => $this->leadService->getRepository()->countByStatusName('new'),
                    'converted_leads' => $this->leadService->getRepository()->countConverted(),
                    'total_value' => $this->leadService->getRepository()->sumEstimatedValue(),
                    'sources' => $this->leadService->getLeadSourcesStats(10),
                    'statuses' => $this->leadService->getLeadStatusesStats(),
                ];
            },
            'medium'
        );

        return $this->apiResponse($stats, 'Statistics retrieved successfully');
    }

    /**
     * Helper method to get status name from ID
     */
    private function getStatusName(?int $statusId): string
    {
        if (!$statusId) {
            return 'new';
        }

        $status = \App\Models\LeadStatus::find($statusId);
        return $status ? $status->name : 'new';
    }

    /**
     * Helper method to get priority name from value
     */
    private function getPriorityName(int $priority): string
    {
        $priorities = [
            1 => 'low',
            2 => 'medium',
            3 => 'high'
        ];

        return $priorities[$priority] ?? 'medium';
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadTag;
use App\Models\LeadCampaign;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\ConvertLeadRequest;
use App\Http\Requests\ScoreLeadRequest;
use App\Services\LeadService;
use App\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class LeadController extends BaseController
{
    protected $leadService;
    protected $leadRepository;

    public function __construct(
        LeadService $leadService,
        LeadRepositoryInterface $leadRepository
    ) {
        $this->leadService = $leadService;
        $this->leadRepository = $leadRepository;
    }

    /**
     * Display a listing of leads with caching and performance optimization
     */
    public function index(Request $request)
    {
        $this->rateLimit($request, 100, 5);

        $filters = $request->only(['search', 'status', 'source', 'assigned_to', 'date_range', 'priority']);
        $perPage = $this->getPerPage($request, 20, 100);

        $leads = $this->getCachedData(
            'leads_index_' . md5(serialize($filters) . $perPage),
            function () use ($filters, $perPage) {
                return $this->leadRepository->getFilteredLeads($filters, $perPage);
            },
            'short'
        );

        $sources = $this->getCachedData(
            'lead_sources',
            function () {
                return $this->leadService->getActiveSources();
            },
            'medium'
        );

        $statuses = $this->getCachedData(
            'lead_statuses',
            function () {
                return $this->leadService->getActiveStatuses();
            },
            'medium'
        );

        $users = $this->getCachedData(
            'available_agents',
            function () {
                return $this->leadService->getAvailableAgents();
            },
            'medium'
        );

        return view('leads.index', compact('leads', 'sources', 'statuses', 'users'));
    }

    /**
     * Display lead dashboard with optimized caching
     */
    public function dashboard(Request $request)
    {
        $this->rateLimit($request, 60, 5);

        $stats = $this->getCachedData(
            'lead_dashboard_stats',
            function () {
                return $this->leadService->getDashboardStats();
            },
            'short'
        );

        $recentLeads = $this->getCachedData(
            'recent_leads_10',
            function () {
                return $this->leadService->getRecentLeads(10);
            },
            'short'
        );

        $leadSources = $this->getCachedData(
            'lead_sources_stats_5',
            function () {
                return $this->leadService->getLeadSourcesStats(5);
            },
            'medium'
        );

        $leadStatuses = $this->getCachedData(
            'lead_statuses_stats',
            function () {
                return $this->leadService->getLeadStatusesStats();
            },
            'medium'
        );

        return view('leads.dashboard', compact('stats', 'recentLeads', 'leadSources', 'leadStatuses'));
    }

    /**
     * Display lead pipeline with optimized data loading
     */
    public function pipeline(Request $request)
    {
        $this->rateLimit($request, 60, 5);

        $statuses = $this->getCachedData(
            'lead_pipeline_data',
            function () {
                return $this->leadService->getPipelineData();
            },
            'short'
        );

        $stats = $this->getCachedData(
            'lead_dashboard_stats',
            function () {
                return $this->leadService->getDashboardStats();
            },
            'short'
        );

        $totalLeads = $stats['total_leads'] ?? 0;
        $convertedLeads = $stats['converted_leads'] ?? 0;
        $totalValue = $this->getCachedData(
            'total_estimated_value',
            function () {
                return $this->leadService->getTotalEstimatedValue();
            },
            'medium'
        );
        $conversionRate = $stats['conversion_rate'] ?? 0;

        return view('leads.pipeline', compact('statuses', 'totalLeads', 'convertedLeads', 'totalValue', 'conversionRate'));
    }

    /**
     * Show the form for creating a new lead with optimized data loading
     */
    public function create(Request $request)
    {
        $this->rateLimit($request, 50, 5);

        $sources = $this->getCachedData(
            'lead_sources',
            function () {
                return $this->leadService->getActiveSources();
            },
            'medium'
        );

        $statuses = $this->getCachedData(
            'lead_statuses',
            function () {
                return $this->leadService->getActiveStatuses();
            },
            'medium'
        );

        $campaigns = $this->getCachedData(
            'lead_campaigns_active',
            function () {
                return LeadCampaign::active()->get(['id', 'name', 'description']);
            },
            'medium'
        );

        $tags = $this->getCachedData(
            'lead_tags',
            function () {
                return LeadTag::get(['id', 'name', 'color']);
            },
            'medium'
        );

        $users = $this->getCachedData(
            'available_agents',
            function () {
                return $this->leadService->getAvailableAgents();
            },
            'medium'
        );

        return view('leads.create', compact('sources', 'statuses', 'users'));
    }

    /**
     * Store a newly created lead with validation and caching
     */
    public function store(StoreLeadRequest $request)
    {
        $this->rateLimit($request, 30, 5);

        try {
            $data = $request->except(['tags', '_token']);
            $data['full_name'] = $request->first_name . ' ' . $request->last_name;
            $data['lead_status'] = $this->getStatusName($request->status_id);
            $data['priority'] = $this->getPriorityName($request->priority);
            $data['created_by'] = auth()->id();
            $data['uuid'] = (string) \Illuminate\Support\Str::uuid();

            $tagIds = [];
            if ($request->has('tags')) {
                // If tags are sent as comma-separated string of IDs
                if (is_string($request->tags)) {
                    $tagIds = array_filter(explode(',', $request->tags), function($value) {
                        return is_numeric($value);
                    });
                } 
                // If tags are sent as array of IDs
                elseif (is_array($request->tags)) {
                    $tagIds = array_filter($request->tags, function($value) {
                        return is_numeric($value);
                    });
                }
            }
            
            $lead = $this->leadService->createLead($data, $tagIds);

            // Clear relevant caches
            $this->clearCache('lead_dashboard_stats');
            $this->clearCache('recent_leads');
            $this->clearCache('lead_pipeline_data');
            $this->clearCache('total_estimated_value');

            return redirect()->route('leads.show', $lead)
                ->with('success', 'تم إضافة العميل المحتمل بنجاح');

        } catch (\Exception $e) {
            Log::error('Lead creation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            // For debugging - show actual error in development
            if (app()->environment('local')) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'حدث خطأ أثناء إضافة العميل المحتمل: ' . $e->getMessage());
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة العميل المحتمل');
        }
    }

    /**
     * Display the specified lead with optimized loading
     */
    public function show(Request $request, $id)
    {
        $this->rateLimit($request, 100, 5);

        $lead = $this->getCachedData(
            "lead_show_{$id}",
            function () use ($id) {
                return $this->leadRepository->findById($id, ['*'], [
                    'tags',
                    'campaign',
                    'assignedUser:id,full_name,email',
                    'activities' => function ($query) {
                        return $query->latest()->take(20);
                    },
                    'notes' => function ($query) {
                        return $query->latest()->take(10);
                    }
                ]);
            },
            'medium'
        );

        if (!$lead) {
            return $this->errorResponse('Lead not found', 404);
        }

        return view('leads.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified lead
     */
    public function edit(Request $request, $id)
    {
        $this->rateLimit($request, 50, 5);

        $lead = $this->leadRepository->findById($id, ['*'], ['tags', 'campaign']);
        
        if (!$lead) {
            return $this->errorResponse('Lead not found', 404);
        }

        $sources = $this->getCachedData(
            'lead_sources',
            function () {
                return $this->leadService->getActiveSources();
            },
            'medium'
        );

        $statuses = $this->getCachedData(
            'lead_statuses',
            function () {
                return $this->leadService->getActiveStatuses();
            },
            'medium'
        );

        $campaigns = $this->getCachedData(
            'lead_campaigns_active',
            function () {
                return LeadCampaign::active()->get(['id', 'name', 'description']);
            },
            'medium'
        );

        $tags = $this->getCachedData(
            'lead_tags',
            function () {
                return LeadTag::get(['id', 'name', 'color']);
            },
            'medium'
        );

        $users = $this->getCachedData(
            'available_agents',
            function () {
                return $this->leadService->getAvailableAgents();
            },
            'medium'
        );

        return view('leads.edit', compact('lead', 'sources', 'statuses', 'campaigns', 'tags', 'users'));
    }

    /**
     * Update the specified lead with validation and cache clearing
     */
    public function update(StoreLeadRequest $request, $id)
    {
        $this->rateLimit($request, 50, 5);

        try {
            $lead = $this->leadRepository->findById($id);
            
            if (!$lead) {
                return $this->errorResponse('Lead not found', 404);
            }

            $data = $request->except(['tags', '_token', '_method']);
            $data['full_name'] = $request->first_name . ' ' . $request->last_name;
            $data['lead_status'] = $this->getStatusName($request->status_id);
            $data['priority'] = $this->getPriorityName($request->priority);
            $data['updated_by'] = auth()->id();

            $tagIds = $request->tags ? array_filter(explode(',', $request->tags), function($value) {
                return is_numeric(trim($value));
            }) : [];
            
            // Debug: Show what data we're trying to update
            if (app()->environment('local')) {
                \Log::info('Lead Update Data:', [
                    'lead_id' => $id,
                    'data' => $data,
                    'tagIds' => $tagIds,
                    'request_all' => $request->all()
                ]);
            }
            
            $updatedLead = $this->leadService->updateLead($lead, $data, $tagIds);

            // Clear relevant caches
            $this->clearCache("lead_show_{$id}");
            $this->clearCache('lead_dashboard_stats');
            $this->clearCache('recent_leads');
            $this->clearCache('lead_pipeline_data');
            $this->clearCache('total_estimated_value');

            return redirect()->route('leads.show', $lead)
                ->with('success', 'تم تحديث العميل المحتمل بنجاح');

        } catch (\Exception $e) {
            // Debug: Show the actual error
            if (app()->environment('local')) {
                \Log::error('Lead Update Error:', [
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'lead_id' => $id ?? 'unknown',
                    'request_data' => $request->all(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // For development - show actual error
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'حدث خطأ: ' . $e->getMessage() . ' في الملف: ' . $e->getFile() . ' السطر: ' . $e->getLine());
            }

            // For production - show generic error
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث العميل المحتمل. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Remove the specified lead with proper cleanup
     */
    public function destroy(Request $request, $id)
    {
        $this->rateLimit($request, 30, 5);

        try {
            $success = $this->leadService->deleteLead($id);

            if (!$success) {
                return $this->errorResponse('Lead not found', 404);
            }

            // Clear relevant caches
            $this->clearCache("lead_show_{$id}");
            $this->clearCache('lead_dashboard_stats');
            $this->clearCache('recent_leads');
            $this->clearCache('lead_pipeline_data');
            $this->clearCache('total_estimated_value');

            return redirect()->route('leads.index')
                ->with('success', 'تم حذف العميل المحتمل بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف العميل المحتمل');
        }
    }

    public function storeActivity(Request $request, Lead $lead)
    {
        $request->validate([
            'type' => 'required|string',
            'activity_date' => 'required|date',
            // Conditional validation based on type
            'title' => 'nullable|string',
            'activity_title' => 'nullable|string',
            'description' => 'nullable|string',
            'activity_description' => 'nullable|string',
            'to_email' => 'nullable|email',
            'attachments.*' => 'nullable|file|max:10240', // Max 10MB per file
        ]);

        $title = $request->type === 'email' ? $request->title : $request->activity_title;
        $description = $request->type === 'email' ? $request->description : $request->activity_description;

        if ($request->type === 'email') {
            try {
                // Ensure email is provided
                $toEmail = $request->to_email ?? $lead->email;
                if (!$toEmail) {
                    throw new \Exception('No email address provided.');
                }

                $attachments = [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $attachments[] = [
                            'path' => $file->getRealPath(),
                            'name' => $file->getClientOriginalName(),
                            'mime' => $file->getMimeType(),
                        ];
                    }
                }

                // Send using a closure to handle attachments
                $senderName = auth()->user()->name;
                $subject = $title ?? 'No Subject';
                $content = $description ?? ' ';

                \Illuminate\Support\Facades\Mail::send('emails.leads.contact', [
                    'subject' => $subject,
                    'messageContent' => $content,
                    'senderName' => $senderName
                ], function($message) use ($toEmail, $subject, $attachments) {
                    $message->to($toEmail)
                            ->subject($subject);
                    
                    foreach ($attachments as $attachment) {
                        $message->attach($attachment['path'], [
                            'as' => $attachment['name'],
                            'mime' => $attachment['mime'],
                        ]);
                    }
                });

            } catch (\Exception $e) {
                \Log::error('Failed to send email to lead: ' . $e->getMessage());
                return redirect()->back()->with('error', 'فشل إرسال البريد الإلكتروني: ' . $e->getMessage());
            }
        }

        \App\Models\LeadActivity::create([
            'lead_id' => $lead->id,
            'type' => $request->type,
            'title' => $title ?? 'نشاط',
            'description' => $description,
            'activity_date' => $request->activity_date,
            'user_id' => auth()->id(),
        ]);

        $message = 'تم إضافة النشاط بنجاح';
        if ($request->type === 'email') {
            $message = 'تم إرسال البريد الإلكتروني وتسجيل النشاط بنجاح';
        }

        return redirect()->back()->with('success', $message);
    }

    public function storeNote(Request $request, Lead $lead)
    {
        $request->validate([
            'note_content' => 'required|string',
        ]);

        \App\Models\LeadNote::create([
            'lead_id' => $lead->id,
            'content' => $request->note_content,
            'title' => 'ملاحظة سريعة',
            'type' => 'general',
            'agent_id' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'تم إضافة الملاحظة بنجاح');
    }

    /**
     * Convert lead to opportunity with validation
     */
    public function convert(ConvertLeadRequest $request, $id)
    {
        $this->rateLimit($request, 30, 5);

        try {
            $lead = $this->leadService->convertLead($id, $request->validated());

            if (!$lead) {
                return $this->errorResponse('Lead not found', 404);
            }

            // Clear relevant caches
            $this->clearCache("lead_show_{$id}");
            $this->clearCache('lead_dashboard_stats');
            $this->clearCache('lead_pipeline_data');

            return redirect()->route('leads.show', $lead)
                ->with('success', 'تم تحويل العميل المحتمل بنجاح');

        } catch (\Exception $e) {
            Log::error('Lead conversion failed: ' . $e->getMessage(), [
                'lead_id' => $id,
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحويل العميل المحتمل');
        }
    }

    /**
     * Score lead with AI-powered scoring
     */
    public function score(ScoreLeadRequest $request, $id)
    {
        $this->rateLimit($request, 50, 5);

        try {
            $lead = $this->leadService->scoreLead($id, $request->validated());

            if (!$lead) {
                return $this->errorResponse('Lead not found', 404);
            }

            // Clear relevant caches
            $this->clearCache("lead_show_{$id}");
            $this->clearCache('lead_dashboard_stats');

            return $this->jsonResponse($lead, 'Lead scored successfully');

        } catch (\Exception $e) {
            Log::error('Lead scoring failed: ' . $e->getMessage(), [
                'lead_id' => $id,
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            return $this->errorResponse('Lead scoring failed', 500);
        }
    }

    /**
     * Get lead statistics for API
     */
    public function statistics(Request $request)
    {
        $this->rateLimit($request, 60, 5);

        $stats = $this->getCachedData(
            'lead_statistics',
            function () {
                return $this->leadService->getComprehensiveStats();
            },
            'short'
        );

        return $this->jsonResponse($stats, 'Lead statistics retrieved successfully');
    }

    /**
     * Export leads with memory-efficient processing
     */
    public function export(Request $request)
    {
        $this->rateLimit($request, 10, 5);

        $filters = $request->only(['search', 'status', 'source', 'assigned_to', 'date_range', 'priority']);
        $format = $request->get('format', 'csv');

        try {
            $job = new \App\Jobs\ExportLeadsJob($filters, $format, auth()->id());
            dispatch($job);

            return $this->jsonResponse(['job_id' => $job->getJobId()], 'Export job started successfully');

        } catch (\Exception $e) {
            Log::error('Lead export failed: ' . $e->getMessage(), [
                'filters' => $filters,
                'format' => $format,
                'user_id' => auth()->id()
            ]);

            return $this->errorResponse('Export failed', 500);
        }
    }

    /**
     * Get status name from ID with caching
     */
    private function getStatusName($statusId)
    {
        static $statusCache = [];
        
        if (!isset($statusCache[$statusId])) {
            $status = \App\Models\LeadStatus::find($statusId);
            $statusCache[$statusId] = $status ? $status->name : 'new';
        }

        return $statusCache[$statusId];
    }

    /**
     * Get priority name from value
     */
    private function getPriorityName($priorityValue)
    {
        $priorities = [
            1 => 'low',
            2 => 'medium',
            3 => 'high'
        ];

        return $priorities[$priorityValue] ?? 'medium';
    }
}

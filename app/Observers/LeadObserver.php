<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadScore;
use App\Services\LeadScoringService;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LeadAssignedNotification;
use App\Notifications\LeadStatusChangedNotification;

class LeadObserver
{
    /**
     * Handle the Lead "creating" event.
     */
    public function creating(Lead $lead): void
    {
        // Generate UUID if not set
        if (empty($lead->uuid)) {
            $lead->uuid = (string) \Illuminate\Support\Str::uuid();
        }

        // Set default values
        $lead->created_by = $lead->created_by ?? auth()->id();
        $lead->lead_status = $lead->lead_status ?? 'new';
        $lead->priority = $lead->priority ?? 'medium';
    }

    /**
     * Handle the Lead "created" event.
     */
    public function created(Lead $lead): void
    {
        try {
            // Clear lead-related caches
            $this->clearLeadCaches();

            // Create initial activity
            $this->createActivity($lead, 'created', 'Lead created successfully');

            // Calculate initial score asynchronously
            dispatch(function () use ($lead) {
                $this->calculateLeadScore($lead);
            });

            // Send notification if assigned to agent
            if ($lead->assigned_to) {
                dispatch(function () use ($lead) {
                    $this->sendAssignmentNotification($lead);
                });
            }

            // Log creation
            Log::info('Lead created', [
                'lead_id' => $lead->id,
                'uuid' => $lead->uuid,
                'created_by' => $lead->created_by,
                'assigned_to' => $lead->assigned_to
            ]);

        } catch (\Exception $e) {
            Log::error('Lead observer created event failed: ' . $e->getMessage(), [
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Lead "updating" event.
     */
    public function updating(Lead $lead): void
    {
        // Track important changes before update
        $changes = $lead->getDirty();
        
        // Log status changes for post-update processing
        if (isset($changes['lead_status'])) {
            $lead->_old_status = $lead->getOriginal('lead_status');
        }

        if (isset($changes['assigned_to'])) {
            $lead->_old_assigned_to = $lead->getOriginal('assigned_to');
        }
    }

    /**
     * Handle the Lead "updated" event.
     */
    public function updated(Lead $lead): void
    {
        try {
            // Clear lead-related caches
            $this->clearLeadCaches();

            $changes = $lead->getDirty();
            
            // Handle status change
            if (isset($changes['lead_status']) && isset($lead->_old_status)) {
                $this->handleStatusChange($lead, $lead->_old_status, $lead->lead_status);
            }

            // Handle assignment change
            if (isset($changes['assigned_to']) && isset($lead->_old_assigned_to)) {
                $this->handleAssignmentChange($lead, $lead->_old_assigned_to, $lead->assigned_to);
            }

            // Handle priority change
            if (isset($changes['priority'])) {
                $this->createActivity($lead, 'priority_changed', "Priority changed to {$lead->priority}");
            }

            // Recalculate score if important fields changed
            $scoreFields = ['first_name', 'last_name', 'email', 'phone', 'budget', 'property_type', 'location'];
            if (array_intersect_key($changes, array_flip($scoreFields))) {
                dispatch(function () use ($lead) {
                    $this->calculateLeadScore($lead);
                });
            }

            // Log update
            Log::info('Lead updated', [
                'lead_id' => $lead->id,
                'changes' => array_keys($changes),
                'updated_by' => auth()->id()
            ]);

        } catch (\Exception $e) {
            Log::error('Lead observer updated event failed: ' . $e->getMessage(), [
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Lead "deleted" event.
     */
    public function deleted(Lead $lead): void
    {
        try {
            // Clear lead-related caches
            $this->clearLeadCaches();

            // Create activity log
            $this->createActivity($lead, 'deleted', 'Lead deleted');

            // Log deletion
            Log::info('Lead deleted', [
                'lead_id' => $lead->id,
                'deleted_by' => auth()->id()
            ]);

        } catch (\Exception $e) {
            Log::error('Lead observer deleted event failed: ' . $e->getMessage(), [
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Lead "restored" event.
     */
    public function restored(Lead $lead): void
    {
        try {
            // Clear lead-related caches
            $this->clearLeadCaches();

            // Create activity log
            $this->createActivity($lead, 'restored', 'Lead restored');

            // Recalculate score
            dispatch(function () use ($lead) {
                $this->calculateLeadScore($lead);
            });

            Log::info('Lead restored', [
                'lead_id' => $lead->id,
                'restored_by' => auth()->id()
            ]);

        } catch (\Exception $e) {
            Log::error('Lead observer restored event failed: ' . $e->getMessage(), [
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Lead "force deleted" event.
     */
    public function forceDeleted(Lead $lead): void
    {
        try {
            // Clear all caches
            $this->clearLeadCaches();

            // Log force deletion
            Log::warning('Lead force deleted', [
                'lead_id' => $lead->id,
                'deleted_by' => auth()->id()
            ]);

        } catch (\Exception $e) {
            Log::error('Lead observer force deleted event failed: ' . $e->getMessage(), [
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle status change with notifications and scoring
     */
    private function handleStatusChange(Lead $lead, string $oldStatus, string $newStatus): void
    {
        // Create activity
        $this->createActivity($lead, 'status_changed', "Status changed from {$oldStatus} to {$newStatus}");

        // Send notification if converted to opportunity
        if ($newStatus === 'converted' && $oldStatus !== 'converted') {
            dispatch(function () use ($lead) {
                $this->sendConversionNotification($lead);
            });
        }

        // Update analytics
        dispatch(function () use ($lead, $oldStatus, $newStatus) {
            $this->updateLeadAnalytics($lead, $oldStatus, $newStatus);
        });

        // Send status change notification
        if ($lead->assigned_to) {
            dispatch(function () use ($lead, $oldStatus, $newStatus) {
                $this->sendStatusChangeNotification($lead, $oldStatus, $newStatus);
            });
        }
    }

    /**
     * Handle assignment change with notifications
     */
    private function handleAssignmentChange(Lead $lead, ?int $oldAssignee, ?int $newAssignee): void
    {
        if ($newAssignee && $newAssignee !== $oldAssignee) {
            // Create activity
            $this->createActivity($lead, 'assigned', "Lead assigned to agent {$newAssignee}");

            // Send notification to new assignee
            dispatch(function () use ($lead) {
                $this->sendAssignmentNotification($lead);
            });

            // Update agent performance metrics
            dispatch(function () use ($newAssignee) {
                $this->updateAgentMetrics($newAssignee);
            });
        }
    }

    /**
     * Create lead activity log
     */
    private function createActivity(Lead $lead, string $action, string $description): void
    {
        try {
            LeadActivity::create([
                'lead_id' => $lead->id,
                'action' => $action,
                'description' => $description,
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create lead activity: ' . $e->getMessage(), [
                'lead_id' => $lead->id,
                'action' => $action
            ]);
        }
    }

    /**
     * Calculate lead score
     */
    private function calculateLeadScore(Lead $lead): void
    {
        try {
            if (class_exists(LeadScoringService::class)) {
                $scoreService = app(LeadScoringService::class);
                $score = $scoreService->calculateLeadScore($lead);

                // Save score
                LeadScore::updateOrCreate(
                    ['lead_id' => $lead->id],
                    [
                        'score' => $score,
                        'calculated_at' => now(),
                        'factors' => $scoreService->getScoreFactors($lead)
                    ]
                );

                Log::info('Lead score calculated', [
                    'lead_id' => $lead->id,
                    'score' => $score
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to calculate lead score: ' . $e->getMessage(), [
                'lead_id' => $lead->id
            ]);
        }
    }

    /**
     * Send assignment notification
     */
    private function sendAssignmentNotification(Lead $lead): void
    {
        try {
            if ($lead->assignedTo) {
                $lead->assignedTo->notify(new LeadAssignedNotification($lead));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send assignment notification: ' . $e->getMessage(), [
                'lead_id' => $lead->id,
                'assigned_to' => $lead->assigned_to
            ]);
        }
    }

    /**
     * Send conversion notification
     */
    private function sendConversionNotification(Lead $lead): void
    {
        try {
            // Notify assigned agent and admin users
            $users = collect([$lead->assignedTo])
                ->filter()
                ->merge(\App\Models\User::where('role', 'admin')->get());

            foreach ($users as $user) {
                $user->notify(new \App\Notifications\LeadConvertedNotification($lead));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send conversion notification: ' . $e->getMessage(), [
                'lead_id' => $lead->id
            ]);
        }
    }

    /**
     * Send status change notification
     */
    private function sendStatusChangeNotification(Lead $lead, string $oldStatus, string $newStatus): void
    {
        try {
            if ($lead->assignedTo) {
                $lead->assignedTo->notify(new LeadStatusChangedNotification($lead, $oldStatus, $newStatus));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send status change notification: ' . $e->getMessage(), [
                'lead_id' => $lead->id
            ]);
        }
    }

    /**
     * Update lead analytics
     */
    private function updateLeadAnalytics(Lead $lead, string $oldStatus, string $newStatus): void
    {
        try {
            // Update status transition analytics
            \App\Models\LeadAnalytics::updateOrCreate(
                ['date' => now()->toDateString()],
                [
                    'total_leads' => \DB::raw('total_leads + 1'),
                    'converted_leads' => $newStatus === 'converted' ? \DB::raw('converted_leads + 1') : 'converted_leads',
                    'updated_at' => now()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to update lead analytics: ' . $e->getMessage());
        }
    }

    /**
     * Update agent performance metrics
     */
    private function updateAgentMetrics(int $agentId): void
    {
        try {
            $agent = \App\Models\User::find($agentId);
            if ($agent) {
                $agent->update([
                    'leads_assigned' => $agent->leads()->count(),
                    'leads_converted' => $agent->leads()->where('lead_status', 'converted')->count(),
                    'conversion_rate' => $this->calculateConversionRate($agent)
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update agent metrics: ' . $e->getMessage(), [
                'agent_id' => $agentId
            ]);
        }
    }

    /**
     * Calculate conversion rate for agent
     */
    private function calculateConversionRate(\App\Models\User $agent): float
    {
        $totalLeads = $agent->leads()->count();
        $convertedLeads = $agent->leads()->where('lead_status', 'converted')->count();
        
        return $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;
    }

    /**
     * Clear lead-related caches
     */
    private function clearLeadCaches(): void
    {
        try {
            $cacheKeys = [
                'lead_dashboard_stats',
                'lead_statistics',
                'lead_pipeline_data',
                'total_estimated_value',
                'recent_leads',
                'lead_sources_stats',
                'lead_statuses_stats'
            ];

            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }

            // Clear tagged caches if using CacheService
            if (class_exists(CacheService::class)) {
                app(CacheService::class)->clearTags(['leads', 'dashboard', 'analytics']);
            }

        } catch (\Exception $e) {
            Log::error('Failed to clear lead caches: ' . $e->getMessage());
        }
    }
}

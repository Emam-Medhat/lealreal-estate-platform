<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\StoreMilestoneRequest;
use App\Http\Requests\Developer\UpdateMilestoneRequest;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\DeveloperMilestone;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperMilestoneController extends Controller
{
    public function index(Request $request)
    {
        $developer = Auth::user()->developer;
        
        $milestones = $developer->milestones()
            ->with(['project'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->project_id, function ($query, $projectId) {
                $query->where('project_id', $projectId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest('target_date')
            ->paginate(20);

        $projects = $developer->projects()->pluck('name', 'id');

        return view('developer.milestones.index', compact('milestones', 'projects'));
    }

    public function create()
    {
        $developer = Auth::user()->developer;
        $projects = $developer->projects()->pluck('name', 'id');
        
        return view('developer.milestones.create', compact('developer', 'projects'));
    }

    public function store(StoreMilestoneRequest $request)
    {
        $developer = Auth::user()->developer;
        
        $milestone = DeveloperMilestone::create([
            'developer_id' => $developer->id,
            'project_id' => $request->project_id,
            'title' => $request->title,
            'description' => $request->description,
            'milestone_type' => $request->milestone_type,
            'target_date' => $request->target_date,
            'actual_date' => $request->actual_date,
            'status' => $request->status ?? 'pending',
            'priority_level' => $request->priority_level,
            'progress_percentage' => $request->progress_percentage ?? 0,
            'budget_allocated' => $request->budget_allocated,
            'actual_cost' => $request->actual_cost,
            'deliverables' => $request->deliverables ?? [],
            'dependencies' => $request->dependencies ?? [],
            'assigned_team' => $request->assigned_team ?? [],
            'stakeholders' => $request->stakeholders ?? [],
            'success_criteria' => $request->success_criteria ?? [],
            'risk_factors' => $request->risk_factors ?? [],
            'mitigation_strategies' => $request->mitigation_strategies ?? [],
            'quality_standards' => $request->quality_standards ?? [],
            'approval_required' => $request->approval_required ?? false,
            'approved_by' => $request->approved_by,
            'approval_date' => $request->approval_date,
            'completion_notes' => $request->completion_notes,
            'lessons_learned' => $request->lessons_learned,
            'next_milestones' => $request->next_milestones ?? [],
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle milestone documents
        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('milestone-documents', 'public');
                $documents[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $milestone->update(['documents' => $documents]);
        }

        // Handle milestone images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('milestone-images', 'public');
                $images[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'caption' => '',
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $milestone->update(['images' => $images]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_developer_milestone',
            'details' => "Created milestone: {$milestone->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.milestones.show', $milestone)
            ->with('success', 'Milestone created successfully.');
    }

    public function show(DeveloperMilestone $milestone)
    {
        $this->authorize('view', $milestone);
        
        $milestone->load(['project', 'creator', 'updater']);
        
        return view('developer.milestones.show', compact('milestone'));
    }

    public function edit(DeveloperMilestone $milestone)
    {
        $this->authorize('update', $milestone);
        
        $developer = Auth::user()->developer;
        $projects = $developer->projects()->pluck('name', 'id');
        
        return view('developer.milestones.edit', compact('milestone', 'projects'));
    }

    public function update(UpdateMilestoneRequest $request, DeveloperMilestone $milestone)
    {
        $this->authorize('update', $milestone);
        
        $milestone->update([
            'project_id' => $request->project_id,
            'title' => $request->title,
            'description' => $request->description,
            'milestone_type' => $request->milestone_type,
            'target_date' => $request->target_date,
            'actual_date' => $request->actual_date,
            'status' => $request->status,
            'priority_level' => $request->priority_level,
            'progress_percentage' => $request->progress_percentage,
            'budget_allocated' => $request->budget_allocated,
            'actual_cost' => $request->actual_cost,
            'deliverables' => $request->deliverables ?? [],
            'dependencies' => $request->dependencies ?? [],
            'assigned_team' => $request->assigned_team ?? [],
            'stakeholders' => $request->stakeholders ?? [],
            'success_criteria' => $request->success_criteria ?? [],
            'risk_factors' => $request->risk_factors ?? [],
            'mitigation_strategies' => $request->mitigation_strategies ?? [],
            'quality_standards' => $request->quality_standards ?? [],
            'approval_required' => $request->approval_required,
            'approved_by' => $request->approved_by,
            'approval_date' => $request->approval_date,
            'completion_notes' => $request->completion_notes,
            'lessons_learned' => $request->lessons_learned,
            'next_milestones' => $request->next_milestones ?? [],
            'updated_by' => Auth::id(),
        ]);

        // Handle new documents
        if ($request->hasFile('documents')) {
            $existingDocuments = $milestone->documents ?? [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('milestone-documents', 'public');
                $existingDocuments[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $milestone->update(['documents' => $existingDocuments]);
        }

        // Handle new images
        if ($request->hasFile('images')) {
            $existingImages = $milestone->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('milestone-images', 'public');
                $existingImages[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'caption' => '',
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $milestone->update(['images' => $existingImages]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer_milestone',
            'details' => "Updated milestone: {$milestone->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.milestones.show', $milestone)
            ->with('success', 'Milestone updated successfully.');
    }

    public function destroy(DeveloperMilestone $milestone)
    {
        $this->authorize('delete', $milestone);
        
        $milestoneTitle = $milestone->title;
        
        // Delete milestone documents
        if ($milestone->documents) {
            foreach ($milestone->documents as $document) {
                Storage::disk('public')->delete($document['path']);
            }
        }
        
        // Delete milestone images
        if ($milestone->images) {
            foreach ($milestone->images as $image) {
                Storage::disk('public')->delete($image['path']);
            }
        }
        
        $milestone->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_developer_milestone',
            'details' => "Deleted milestone: {$milestoneTitle}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.milestones.index')
            ->with('success', 'Milestone deleted successfully.');
    }

    public function updateStatus(Request $request, DeveloperMilestone $milestone): JsonResponse
    {
        $this->authorize('update', $milestone);
        
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,overdue,cancelled',
        ]);

        $milestone->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_milestone_status',
            'details' => "Updated milestone '{$milestone->title}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Milestone status updated successfully'
        ]);
    }

    public function updateProgress(Request $request, DeveloperMilestone $milestone): JsonResponse
    {
        $this->authorize('update', $milestone);
        
        $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        $milestone->update(['progress_percentage' => $request->progress_percentage]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_milestone_progress',
            'details' => "Updated milestone '{$milestone->title}' progress to {$request->progress_percentage}%",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'progress_percentage' => $request->progress_percentage,
            'message' => 'Milestone progress updated successfully'
        ]);
    }

    public function completeMilestone(Request $request, DeveloperMilestone $milestone): JsonResponse
    {
        $this->authorize('update', $milestone);
        
        $request->validate([
            'completion_notes' => 'required|string|max:2000',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        $milestone->update([
            'status' => 'completed',
            'actual_date' => now(),
            'progress_percentage' => 100,
            'completion_notes' => $request->completion_notes,
            'actual_cost' => $request->actual_cost,
            'updated_by' => Auth::id(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'completed_milestone',
            'details' => "Completed milestone: {$milestone->title}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => 'completed',
            'actual_date' => $milestone->actual_date,
            'message' => 'Milestone completed successfully'
        ]);
    }

    public function getUpcomingMilestones(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $upcoming = $developer->milestones()
            ->whereIn('status', ['pending', 'in_progress'])
            ->where('target_date', '>', now())
            ->where('target_date', '<=', now()->addDays(30))
            ->orderBy('target_date')
            ->get(['id', 'title', 'target_date', 'status', 'priority_level']);

        return response()->json([
            'success' => true,
            'milestones' => $upcoming
        ]);
    }

    public function getOverdueMilestones(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $overdue = $developer->milestones()
            ->whereIn('status', ['pending', 'in_progress'])
            ->where('target_date', '<', now())
            ->orderBy('target_date')
            ->get(['id', 'title', 'target_date', 'status', 'priority_level']);

        return response()->json([
            'success' => true,
            'milestones' => $overdue
        ]);
    }

    public function getProjectMilestones(DeveloperProject $project): JsonResponse
    {
        $this->authorize('view', $project);
        
        $milestones = $project->milestones()
            ->orderBy('target_date')
            ->get(['id', 'title', 'target_date', 'status', 'progress_percentage']);

        return response()->json([
            'success' => true,
            'milestones' => $milestones
        ]);
    }

    public function getMilestoneStats(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $stats = [
            'total_milestones' => $developer->milestones()->count(),
            'pending_milestones' => $developer->milestones()->where('status', 'pending')->count(),
            'in_progress_milestones' => $developer->milestones()->where('status', 'in_progress')->count(),
            'completed_milestones' => $developer->milestones()->where('status', 'completed')->count(),
            'overdue_milestones' => $developer->milestones()
                ->whereIn('status', ['pending', 'in_progress'])
                ->where('target_date', '<', now())
                ->count(),
            'upcoming_milestones' => $developer->milestones()
                ->whereIn('status', ['pending', 'in_progress'])
                ->where('target_date', '>', now())
                ->where('target_date', '<=', now()->addDays(30))
                ->count(),
            'by_type' => $developer->milestones()
                ->groupBy('milestone_type')
                ->map(function ($group) {
                    return $group->count();
                }),
            'by_priority' => $developer->milestones()
                ->groupBy('priority_level')
                ->map(function ($group) {
                    return $group->count();
                }),
            'average_progress' => $developer->milestones()->avg('progress_percentage'),
            'total_budget_allocated' => $developer->milestones()->sum('budget_allocated'),
            'total_actual_cost' => $developer->milestones()->sum('actual_cost'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportMilestones(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:pending,in_progress,completed,overdue,cancelled',
            'project_id' => 'nullable|exists:developer_projects,id',
        ]);

        $developer = Auth::user()->developer;
        
        $query = $developer->milestones()->with(['project']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $milestones = $query->get();

        $filename = "developer_milestones_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $milestones,
            'filename' => $filename,
            'message' => 'Milestones exported successfully'
        ]);
    }
}

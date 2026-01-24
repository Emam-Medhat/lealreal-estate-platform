<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\StoreConstructionUpdateRequest;
use App\Http\Requests\Developer\UpdateConstructionUpdateRequest;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\DeveloperConstructionUpdate;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperConstructionUpdateController extends Controller
{
    public function index(Request $request)
    {
        $developer = Auth::user()->developer;
        
        $updates = $developer->constructionUpdates()
            ->with(['project'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->project_id, function ($query, $projectId) {
                $query->where('project_id', $projectId);
            })
            ->when($request->update_type, function ($query, $updateType) {
                $query->where('update_type', $updateType);
            })
            ->latest('update_date')
            ->paginate(20);

        $projects = $developer->projects()->pluck('name', 'id');

        return view('developer.construction-updates.index', compact('updates', 'projects'));
    }

    public function create()
    {
        $developer = Auth::user()->developer;
        $projects = $developer->projects()->pluck('name', 'id');
        
        return view('developer.construction-updates.create', compact('developer', 'projects'));
    }

    public function store(StoreConstructionUpdateRequest $request)
    {
        $developer = Auth::user()->developer;
        
        $update = DeveloperConstructionUpdate::create([
            'developer_id' => $developer->id,
            'project_id' => $request->project_id,
            'title' => $request->title,
            'description' => $request->description,
            'update_type' => $request->update_type,
            'update_date' => $request->update_date,
            'progress_percentage' => $request->progress_percentage,
            'phase_name' => $request->phase_name,
            'weather_conditions' => $request->weather_conditions,
            'work_force' => $request->work_force,
            'equipment_on_site' => $request->equipment_on_site ?? [],
            'materials_delivered' => $request->materials_delivered ?? [],
            'work_completed' => $request->work_completed ?? [],
            'work_planned' => $request->work_planned ?? [],
            'issues_encountered' => $request->issues_encountered ?? [],
            'safety_incidents' => $request->safety_incidents ?? [],
            'quality_inspections' => $request->quality_inspections ?? [],
            'next_steps' => $request->next_steps ?? [],
            'milestones_achieved' => $request->milestones_achieved ?? [],
            'budget_status' => $request->budget_status,
            'schedule_status' => $request->schedule_status,
            'cost_variances' => $request->cost_variances ?? [],
            'time_variances' => $request->time_variances ?? [],
            'stakeholders_notified' => $request->stakeholders_notified ?? [],
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle update images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('construction-updates', 'public');
                $images[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'caption' => '',
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $update->update(['images' => $images]);
        }

        // Handle documents
        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('construction-documents', 'public');
                $documents[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $update->update(['documents' => $documents]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_construction_update',
            'details' => "Created construction update: {$update->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.construction-updates.show', $update)
            ->with('success', 'Construction update created successfully.');
    }

    public function show(DeveloperConstructionUpdate $update)
    {
        $this->authorize('view', $update);
        
        $update->load(['project', 'creator', 'updater']);
        
        return view('developer.construction-updates.show', compact('update'));
    }

    public function edit(DeveloperConstructionUpdate $update)
    {
        $this->authorize('update', $update);
        
        $developer = Auth::user()->developer;
        $projects = $developer->projects()->pluck('name', 'id');
        
        return view('developer.construction-updates.edit', compact('update', 'projects'));
    }

    public function update(UpdateConstructionUpdateRequest $request, DeveloperConstructionUpdate $update)
    {
        $this->authorize('update', $update);
        
        $update->update([
            'project_id' => $request->project_id,
            'title' => $request->title,
            'description' => $request->description,
            'update_type' => $request->update_type,
            'update_date' => $request->update_date,
            'progress_percentage' => $request->progress_percentage,
            'phase_name' => $request->phase_name,
            'weather_conditions' => $request->weather_conditions,
            'work_force' => $request->work_force,
            'equipment_on_site' => $request->equipment_on_site ?? [],
            'materials_delivered' => $request->materials_delivered ?? [],
            'work_completed' => $request->work_completed ?? [],
            'work_planned' => $request->work_planned ?? [],
            'issues_encountered' => $request->issues_encountered ?? [],
            'safety_incidents' => $request->safety_incidents ?? [],
            'quality_inspections' => $request->quality_inspections ?? [],
            'next_steps' => $request->next_steps ?? [],
            'milestones_achieved' => $request->milestones_achieved ?? [],
            'budget_status' => $request->budget_status,
            'schedule_status' => $request->schedule_status,
            'cost_variances' => $request->cost_variances ?? [],
            'time_variances' => $request->time_variances ?? [],
            'stakeholders_notified' => $request->stakeholders_notified ?? [],
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        // Handle new images
        if ($request->hasFile('images')) {
            $existingImages = $update->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('construction-updates', 'public');
                $existingImages[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'caption' => '',
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $update->update(['images' => $existingImages]);
        }

        // Handle new documents
        if ($request->hasFile('documents')) {
            $existingDocuments = $update->documents ?? [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('construction-documents', 'public');
                $existingDocuments[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $update->update(['documents' => $existingDocuments]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_construction_update',
            'details' => "Updated construction update: {$update->title}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.construction-updates.show', $update)
            ->with('success', 'Construction update updated successfully.');
    }

    public function destroy(DeveloperConstructionUpdate $update)
    {
        $this->authorize('delete', $update);
        
        $updateTitle = $update->title;
        
        // Delete update images
        if ($update->images) {
            foreach ($update->images as $image) {
                Storage::disk('public')->delete($image['path']);
            }
        }
        
        // Delete documents
        if ($update->documents) {
            foreach ($update->documents as $document) {
                Storage::disk('public')->delete($document['path']);
            }
        }
        
        $update->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_construction_update',
            'details' => "Deleted construction update: {$updateTitle}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.construction-updates.index')
            ->with('success', 'Construction update deleted successfully.');
    }

    public function getProjectUpdates(DeveloperProject $project): JsonResponse
    {
        $this->authorize('view', $project);
        
        $updates = $project->constructionUpdates()
            ->latest('update_date')
            ->take(20)
            ->get(['id', 'title', 'update_type', 'update_date', 'progress_percentage']);

        return response()->json([
            'success' => true,
            'updates' => $updates
        ]);
    }

    public function getProgressTimeline(DeveloperProject $project): JsonResponse
    {
        $this->authorize('view', $project);
        
        $timeline = $project->constructionUpdates()
            ->orderBy('update_date')
            ->get(['id', 'title', 'update_date', 'progress_percentage', 'update_type'])
            ->map(function ($update) {
                return [
                    'id' => $update->id,
                    'title' => $update->title,
                    'date' => $update->update_date,
                    'progress' => $update->progress_percentage,
                    'type' => $update->update_type,
                ];
            });

        return response()->json([
            'success' => true,
            'timeline' => $timeline
        ]);
    }

    public function getUpdateStats(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $stats = [
            'total_updates' => $developer->constructionUpdates()->count(),
            'this_month_updates' => $developer->constructionUpdates()
                ->whereMonth('update_date', now()->month)
                ->whereYear('update_date', now()->year)
                ->count(),
            'by_type' => $developer->constructionUpdates()
                ->groupBy('update_type')
                ->map(function ($group) {
                    return $group->count();
                }),
            'by_project' => $developer->constructionUpdates()
                ->with('project')
                ->get()
                ->groupBy('project.name')
                ->map(function ($group) {
                    return $group->count();
                }),
            'average_progress' => $developer->constructionUpdates()->avg('progress_percentage'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportUpdates(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'update_type' => 'nullable|in:daily,weekly,monthly,milestone,issue,safety,quality',
            'project_id' => 'nullable|exists:developer_projects,id',
        ]);

        $developer = Auth::user()->developer;
        
        $query = $developer->constructionUpdates()->with(['project']);

        if ($request->update_type) {
            $query->where('update_type', $request->update_type);
        }

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $updates = $query->get();

        $filename = "construction_updates_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $updates,
            'filename' => $filename,
            'message' => 'Construction updates exported successfully'
        ]);
    }
}

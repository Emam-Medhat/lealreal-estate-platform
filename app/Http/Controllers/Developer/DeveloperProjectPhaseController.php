<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\AddPhaseRequest;
use App\Http\Requests\Developer\UpdatePhaseRequest;
use App\Models\DeveloperProject;
use App\Models\DeveloperProjectPhase;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperProjectPhaseController extends Controller
{
    public function index(Request $request, DeveloperProject $project)
    {
        $this->authorize('view', $project);
        
        $phases = $project->phases()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('start_date')
            ->paginate(20);

        return view('developer.phases.index', compact('project', 'phases'));
    }

    public function create(DeveloperProject $project)
    {
        $this->authorize('update', $project);
        
        return view('developer.phases.create', compact('project'));
    }

    public function store(AddPhaseRequest $request, DeveloperProject $project)
    {
        $this->authorize('update', $project);
        
        $phase = DeveloperProjectPhase::create([
            'project_id' => $project->id,
            'name' => $request->name,
            'description' => $request->description,
            'phase_type' => $request->phase_type,
            'status' => $request->status ?? 'planned',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'duration_days' => $request->duration_days,
            'budget' => $request->budget,
            'actual_cost' => $request->actual_cost ?? 0,
            'completion_percentage' => $request->completion_percentage ?? 0,
            'contractor_id' => $request->contractor_id,
            'supervisor_id' => $request->supervisor_id,
            'deliverables' => $request->deliverables ?? [],
            'materials' => $request->materials ?? [],
            'equipment' => $request->equipment ?? [],
            'requirements' => $request->requirements ?? [],
            'risks' => $request->risks ?? [],
            'mitigation_plan' => $request->mitigation_plan ?? [],
            'quality_standards' => $request->quality_standards ?? [],
            'safety_measures' => $request->safety_measures ?? [],
            'inspections' => $request->inspections ?? [],
            'dependencies' => $request->dependencies ?? [],
        ]);

        // Handle phase documents
        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('phase-documents', 'public');
                $documents[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $phase->update(['documents' => $documents]);
        }

        // Handle phase images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('phase-images', 'public');
                $images[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $phase->update(['images' => $images]);
        }

        // Update project completion percentage
        $this->updateProjectCompletion($project);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_project_phase',
            'details' => "Created phase '{$phase->name}' for project: {$project->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.phases.show', [$project, $phase])
            ->with('success', 'Phase created successfully.');
    }

    public function show(DeveloperProject $project, DeveloperProjectPhase $phase)
    {
        $this->authorize('view', $project);
        
        $phase->load(['contractor', 'supervisor', 'inspections']);
        
        return view('developer.phases.show', compact('project', 'phase'));
    }

    public function edit(DeveloperProject $project, DeveloperProjectPhase $phase)
    {
        $this->authorize('update', $project);
        
        return view('developer.phases.edit', compact('project', 'phase'));
    }

    public function update(UpdatePhaseRequest $request, DeveloperProject $project, DeveloperProjectPhase $phase)
    {
        $this->authorize('update', $project);
        
        $phase->update([
            'name' => $request->name,
            'description' => $request->description,
            'phase_type' => $request->phase_type,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'duration_days' => $request->duration_days,
            'budget' => $request->budget,
            'actual_cost' => $request->actual_cost,
            'completion_percentage' => $request->completion_percentage,
            'contractor_id' => $request->contractor_id,
            'supervisor_id' => $request->supervisor_id,
            'deliverables' => $request->deliverables ?? [],
            'materials' => $request->materials ?? [],
            'equipment' => $request->equipment ?? [],
            'requirements' => $request->requirements ?? [],
            'risks' => $request->risks ?? [],
            'mitigation_plan' => $request->mitigation_plan ?? [],
            'quality_standards' => $request->quality_standards ?? [],
            'safety_measures' => $request->safety_measures ?? [],
            'inspections' => $request->inspections ?? [],
            'dependencies' => $request->dependencies ?? [],
        ]);

        // Handle new documents
        if ($request->hasFile('documents')) {
            $existingDocuments = $phase->documents ?? [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('phase-documents', 'public');
                $existingDocuments[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $phase->update(['documents' => $existingDocuments]);
        }

        // Handle new images
        if ($request->hasFile('images')) {
            $existingImages = $phase->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('phase-images', 'public');
                $existingImages[] = [
                    'path' => $path,
                    'name' => $image->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $phase->update(['images' => $existingImages]);
        }

        // Update project completion percentage
        $this->updateProjectCompletion($project);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_project_phase',
            'details' => "Updated phase '{$phase->name}' for project: {$project->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.phases.show', [$project, $phase])
            ->with('success', 'Phase updated successfully.');
    }

    public function destroy(DeveloperProject $project, DeveloperProjectPhase $phase)
    {
        $this->authorize('update', $project);
        
        $phaseName = $phase->name;
        
        // Delete phase documents
        if ($phase->documents) {
            foreach ($phase->documents as $document) {
                Storage::disk('public')->delete($document['path']);
            }
        }
        
        // Delete phase images
        if ($phase->images) {
            foreach ($phase->images as $image) {
                Storage::disk('public')->delete($image['path']);
            }
        }
        
        $phase->delete();

        // Update project completion percentage
        $this->updateProjectCompletion($project);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_project_phase',
            'details' => "Deleted phase '{$phaseName}' from project: {$project->name}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.phases.index', $project)
            ->with('success', 'Phase deleted successfully.');
    }

    public function updateStatus(Request $request, DeveloperProject $project, DeveloperProjectPhase $phase): JsonResponse
    {
        $this->authorize('update', $project);
        
        $request->validate([
            'status' => 'required|in:planned,in_progress,completed,on_hold,cancelled',
        ]);

        $phase->update(['status' => $request->status]);

        // Update project completion percentage
        $this->updateProjectCompletion($project);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_phase_status',
            'details' => "Updated phase '{$phase->name}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Phase status updated successfully'
        ]);
    }

    public function updateProgress(Request $request, DeveloperProject $project, DeveloperProjectPhase $phase): JsonResponse
    {
        $this->authorize('update', $project);
        
        $request->validate([
            'completion_percentage' => 'required|integer|min:0|max:100',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        $phase->update([
            'completion_percentage' => $request->completion_percentage,
            'actual_cost' => $request->actual_cost ?? $phase->actual_cost,
        ]);

        // Update project completion percentage
        $this->updateProjectCompletion($project);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_phase_progress',
            'details' => "Updated phase '{$phase->name}' progress to {$request->completion_percentage}%",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'completion_percentage' => $request->completion_percentage,
            'actual_cost' => $request->actual_cost,
            'message' => 'Phase progress updated successfully'
        ]);
    }

    public function getPhaseTimeline(DeveloperProject $project, DeveloperProjectPhase $phase): JsonResponse
    {
        $this->authorize('view', $project);
        
        $timeline = [
            'phase' => [
                'name' => $phase->name,
                'start_date' => $phase->start_date,
                'end_date' => $phase->end_date,
                'duration_days' => $phase->duration_days,
                'completion_percentage' => $phase->completion_percentage,
                'status' => $phase->status,
            ],
            'dependencies' => $phase->dependencies ?? [],
            'deliverables' => $phase->deliverables ?? [],
            'risks' => $phase->risks ?? [],
            'inspections' => $phase->inspections ?? [],
        ];

        return response()->json([
            'success' => true,
            'timeline' => $timeline
        ]);
    }

    public function exportPhases(Request $request, DeveloperProject $project): JsonResponse
    {
        $this->authorize('view', $project);
        
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:planned,in_progress,completed,on_hold,cancelled',
        ]);

        $query = $project->phases();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $phases = $query->get();

        $filename = "project_{$project->id}_phases_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $phases,
            'filename' => $filename,
            'message' => 'Phases exported successfully'
        ]);
    }

    private function updateProjectCompletion(DeveloperProject $project): void
    {
        $phases = $project->phases;
        $totalPhases = $phases->count();
        
        if ($totalPhases === 0) {
            $project->update(['completion_percentage' => 0]);
            return;
        }

        $totalCompletion = $phases->sum('completion_percentage');
        $averageCompletion = round($totalCompletion / $totalPhases, 2);
        
        $project->update(['completion_percentage' => $averageCompletion]);
    }
}

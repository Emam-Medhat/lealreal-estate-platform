<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\StoreBimModelRequest;
use App\Http\Requests\Developer\UpdateBimModelRequest;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\DeveloperBimModel;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperBimController extends Controller
{
    public function index(Request $request)
    {
        $developer = Auth::user()->developer;
        
        $bimModels = $developer->bimModels()
            ->with(['project'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->project_id, function ($query, $projectId) {
                $query->where('project_id', $projectId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $projects = $developer->projects()->pluck('name', 'id');

        return view('developer.bim.index', compact('bimModels', 'projects'));
    }

    public function create()
    {
        $developer = Auth::user()->developer;
        $projects = $developer->projects()->pluck('name', 'id');
        
        return view('developer.bim.create', compact('developer', 'projects'));
    }

    public function store(StoreBimModelRequest $request)
    {
        $developer = Auth::user()->developer;
        
        $bimModel = DeveloperBimModel::create([
            'developer_id' => $developer->id,
            'project_id' => $request->project_id,
            'name' => $request->name,
            'description' => $request->description,
            'model_type' => $request->model_type,
            'software_used' => $request->software_used,
            'version' => $request->version,
            'status' => $request->status ?? 'draft',
            'complexity_level' => $request->complexity_level,
            'lod_level' => $request->lod_level,
            'discipline' => $request->discipline,
            'coordinates' => $request->coordinates ?? [],
            'metadata' => $request->metadata ?? [],
            'parameters' => $request->parameters ?? [],
            'materials' => $request->materials ?? [],
            'components' => $request->components ?? [],
            'conflicts' => $request->conflicts ?? [],
            'issues' => $request->issues ?? [],
            'clash_results' => $request->clash_results ?? [],
            'quantities' => $request->quantities ?? [],
            'specifications' => $request->specifications ?? [],
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle BIM model file upload
        if ($request->hasFile('model_file')) {
            $modelPath = $request->file('model_file')->store('bim-models', 'public');
            $bimModel->update(['model_file' => $modelPath]);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('bim-thumbnails', 'public');
            $bimModel->update(['thumbnail' => $thumbnailPath]);
        }

        // Handle additional files
        if ($request->hasFile('additional_files')) {
            $files = [];
            foreach ($request->file('additional_files') as $file) {
                $path = $file->store('bim-additional-files', 'public');
                $files[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $bimModel->update(['additional_files' => $files]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_bim_model',
            'details' => "Created BIM model: {$bimModel->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.bim.show', $bimModel)
            ->with('success', 'BIM model created successfully.');
    }

    public function show(DeveloperBimModel $bimModel)
    {
        $this->authorize('view', $bimModel);
        
        $bimModel->load(['project', 'creator', 'updater']);
        
        return view('developer.bim.show', compact('bimModel'));
    }

    public function edit(DeveloperBimModel $bimModel)
    {
        $this->authorize('update', $bimModel);
        
        $developer = Auth::user()->developer;
        $projects = $developer->projects()->pluck('name', 'id');
        
        return view('developer.bim.edit', compact('bimModel', 'projects'));
    }

    public function update(UpdateBimModelRequest $request, DeveloperBimModel $bimModel)
    {
        $this->authorize('update', $bimModel);
        
        $bimModel->update([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'description' => $request->description,
            'model_type' => $request->model_type,
            'software_used' => $request->software_used,
            'version' => $request->version,
            'status' => $request->status,
            'complexity_level' => $request->complexity_level,
            'lod_level' => $request->lod_level,
            'discipline' => $request->discipline,
            'coordinates' => $request->coordinates ?? [],
            'metadata' => $request->metadata ?? [],
            'parameters' => $request->parameters ?? [],
            'materials' => $request->materials ?? [],
            'components' => $request->components ?? [],
            'conflicts' => $request->conflicts ?? [],
            'issues' => $request->issues ?? [],
            'clash_results' => $request->clash_results ?? [],
            'quantities' => $request->quantities ?? [],
            'specifications' => $request->specifications ?? [],
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        // Handle BIM model file update
        if ($request->hasFile('model_file')) {
            if ($bimModel->model_file) {
                Storage::disk('public')->delete($bimModel->model_file);
            }
            $modelPath = $request->file('model_file')->store('bim-models', 'public');
            $bimModel->update(['model_file' => $modelPath]);
        }

        // Handle thumbnail update
        if ($request->hasFile('thumbnail')) {
            if ($bimModel->thumbnail) {
                Storage::disk('public')->delete($bimModel->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('bim-thumbnails', 'public');
            $bimModel->update(['thumbnail' => $thumbnailPath]);
        }

        // Handle new additional files
        if ($request->hasFile('additional_files')) {
            $existingFiles = $bimModel->additional_files ?? [];
            foreach ($request->file('additional_files') as $file) {
                $path = $file->store('bim-additional-files', 'public');
                $existingFiles[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $bimModel->update(['additional_files' => $existingFiles]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_bim_model',
            'details' => "Updated BIM model: {$bimModel->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.bim.show', $bimModel)
            ->with('success', 'BIM model updated successfully.');
    }

    public function destroy(DeveloperBimModel $bimModel)
    {
        $this->authorize('delete', $bimModel);
        
        $modelName = $bimModel->name;
        
        // Delete model file
        if ($bimModel->model_file) {
            Storage::disk('public')->delete($bimModel->model_file);
        }
        
        // Delete thumbnail
        if ($bimModel->thumbnail) {
            Storage::disk('public')->delete($bimModel->thumbnail);
        }
        
        // Delete additional files
        if ($bimModel->additional_files) {
            foreach ($bimModel->additional_files as $file) {
                Storage::disk('public')->delete($file['path']);
            }
        }
        
        $bimModel->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_bim_model',
            'details' => "Deleted BIM model: {$modelName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.bim.index')
            ->with('success', 'BIM model deleted successfully.');
    }

    public function updateStatus(Request $request, DeveloperBimModel $bimModel): JsonResponse
    {
        $this->authorize('update', $bimModel);
        
        $request->validate([
            'status' => 'required|in:draft,in_review,approved,published,archived',
        ]);

        $bimModel->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_bim_model_status',
            'details' => "Updated BIM model '{$bimModel->name}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'BIM model status updated successfully'
        ]);
    }

    public function runClashDetection(Request $request, DeveloperBimModel $bimModel): JsonResponse
    {
        $this->authorize('update', $bimModel);
        
        // Simulate clash detection process
        $clashResults = [
            'total_clashes' => rand(0, 50),
            'hard_clashes' => rand(0, 20),
            'soft_clashes' => rand(0, 30),
            'clashes_by_discipline' => [
                'architectural' => rand(0, 15),
                'structural' => rand(0, 15),
                'mechanical' => rand(0, 10),
                'electrical' => rand(0, 10),
                'plumbing' => rand(0, 10),
            ],
            'clash_details' => [],
        ];

        $bimModel->update([
            'clash_results' => $clashResults,
            'updated_by' => Auth::id(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'ran_clash_detection',
            'details' => "Ran clash detection on BIM model: {$bimModel->name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'clash_results' => $clashResults,
            'message' => 'Clash detection completed successfully'
        ]);
    }

    public function generateQuantities(Request $request, DeveloperBimModel $bimModel): JsonResponse
    {
        $this->authorize('view', $bimModel);
        
        // Simulate quantity takeoff
        $quantities = [
            'concrete' => [
                'volume' => rand(1000, 5000) . ' m³',
                'strength' => 'C30/37',
            ],
            'steel' => [
                'weight' => rand(500, 2000) . ' kg',
                'grade' => 'S500',
            ],
            'floor_area' => rand(1000, 10000) . ' m²',
            'wall_area' => rand(2000, 8000) . ' m²',
            'door_count' => rand(50, 200),
            'window_count' => rand(100, 400),
        ];

        $bimModel->update(['quantities' => $quantities]);

        return response()->json([
            'success' => true,
            'quantities' => $quantities,
            'message' => 'Quantities generated successfully'
        ]);
    }

    public function downloadModel(DeveloperBimModel $bimModel)
    {
        $this->authorize('view', $bimModel);
        
        if (!$bimModel->model_file) {
            return back()->with('error', 'No model file available for download.');
        }

        $filePath = storage_path('app/public/' . $bimModel->model_file);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Model file not found.');
        }

        return response()->download($filePath, $bimModel->name . '_bim_model.' . pathinfo($filePath, PATHINFO_EXTENSION));
    }

    public function getBimStats(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $stats = [
            'total_models' => $developer->bimModels()->count(),
            'draft_models' => $developer->bimModels()->where('status', 'draft')->count(),
            'published_models' => $developer->bimModels()->where('status', 'published')->count(),
            'by_discipline' => $developer->bimModels()
                ->groupBy('discipline')
                ->map(function ($group) {
                    return $group->count();
                }),
            'by_lod_level' => $developer->bimModels()
                ->groupBy('lod_level')
                ->map(function ($group) {
                    return $group->count();
                }),
            'by_software' => $developer->bimModels()
                ->groupBy('software_used')
                ->map(function ($group) {
                    return $group->count();
                }),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportBimModels(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:draft,in_review,approved,published,archived',
            'project_id' => 'nullable|exists:developer_projects,id',
        ]);

        $developer = Auth::user()->developer;
        
        $query = $developer->bimModels()->with(['project']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $bimModels = $query->get();

        $filename = "developer_bim_models_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $bimModels,
            'filename' => $filename,
            'message' => 'BIM models exported successfully'
        ]);
    }
}

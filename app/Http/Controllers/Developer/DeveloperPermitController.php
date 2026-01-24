<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\StorePermitRequest;
use App\Http\Requests\Developer\UpdatePermitRequest;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\DeveloperPermit;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperPermitController extends Controller
{
    public function index(Request $request)
    {
        $developer = Auth::user()->developer;
        
        $permits = $developer->permits()
            ->with(['project'])
            ->when($request->search, function ($query, $search) {
                $query->where('permit_number', 'like', "%{$search}%")
                    ->orWhere('permit_type', 'like', "%{$search}%")
                    ->orWhere('issuing_authority', 'like', "%{$search}%");
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

        return view('developer.permits.index', compact('permits', 'projects'));
    }

    public function create()
    {
        $developer = Auth::user()->developer;
        $projects = $developer->projects()->pluck('name', 'id');
        
        return view('developer.permits.create', compact('developer', 'projects'));
    }

    public function store(StorePermitRequest $request)
    {
        $developer = Auth::user()->developer;
        
        $permit = DeveloperPermit::create([
            'developer_id' => $developer->id,
            'project_id' => $request->project_id,
            'permit_number' => $request->permit_number,
            'permit_type' => $request->permit_type,
            'description' => $request->description,
            'issuing_authority' => $request->issuing_authority,
            'application_date' => $request->application_date,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'status' => $request->status ?? 'pending',
            'priority_level' => $request->priority_level,
            'estimated_cost' => $request->estimated_cost,
            'actual_cost' => $request->actual_cost,
            'validity_period' => $request->validity_period,
            'renewal_required' => $request->renewal_required ?? false,
            'conditions' => $request->conditions ?? [],
            'requirements' => $request->requirements ?? [],
            'inspections_required' => $request->inspections_required ?? [],
            'approvals_needed' => $request->approvals_needed ?? [],
            'contact_person' => $request->contact_person,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle permit document upload
        if ($request->hasFile('permit_document')) {
            $documentPath = $request->file('permit_document')->store('permit-documents', 'public');
            $permit->update(['permit_document' => $documentPath]);
        }

        // Handle supporting documents
        if ($request->hasFile('supporting_documents')) {
            $documents = [];
            foreach ($request->file('supporting_documents') as $document) {
                $path = $document->store('permit-supporting-docs', 'public');
                $documents[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $permit->update(['supporting_documents' => $documents]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_developer_permit',
            'details' => "Created permit: {$permit->permit_number}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.permits.show', $permit)
            ->with('success', 'Permit created successfully.');
    }

    public function show(DeveloperPermit $permit)
    {
        $this->authorize('view', $permit);
        
        $permit->load(['project', 'creator', 'updater']);
        
        return view('developer.permits.show', compact('permit'));
    }

    public function edit(DeveloperPermit $permit)
    {
        $this->authorize('update', $permit);
        
        $developer = Auth::user()->developer;
        $projects = $developer->projects()->pluck('name', 'id');
        
        return view('developer.permits.edit', compact('permit', 'projects'));
    }

    public function update(UpdatePermitRequest $request, DeveloperPermit $permit)
    {
        $this->authorize('update', $permit);
        
        $permit->update([
            'project_id' => $request->project_id,
            'permit_number' => $request->permit_number,
            'permit_type' => $request->permit_type,
            'description' => $request->description,
            'issuing_authority' => $request->issuing_authority,
            'application_date' => $request->application_date,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'status' => $request->status,
            'priority_level' => $request->priority_level,
            'estimated_cost' => $request->estimated_cost,
            'actual_cost' => $request->actual_cost,
            'validity_period' => $request->validity_period,
            'renewal_required' => $request->renewal_required,
            'conditions' => $request->conditions ?? [],
            'requirements' => $request->requirements ?? [],
            'inspections_required' => $request->inspections_required ?? [],
            'approvals_needed' => $request->approvals_needed ?? [],
            'contact_person' => $request->contact_person,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        // Handle permit document update
        if ($request->hasFile('permit_document')) {
            if ($permit->permit_document) {
                Storage::disk('public')->delete($permit->permit_document);
            }
            $documentPath = $request->file('permit_document')->store('permit-documents', 'public');
            $permit->update(['permit_document' => $documentPath]);
        }

        // Handle new supporting documents
        if ($request->hasFile('supporting_documents')) {
            $existingDocuments = $permit->supporting_documents ?? [];
            foreach ($request->file('supporting_documents') as $document) {
                $path = $document->store('permit-supporting-docs', 'public');
                $existingDocuments[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $permit->update(['supporting_documents' => $existingDocuments]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer_permit',
            'details' => "Updated permit: {$permit->permit_number}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.permits.show', $permit)
            ->with('success', 'Permit updated successfully.');
    }

    public function destroy(DeveloperPermit $permit)
    {
        $this->authorize('delete', $permit);
        
        $permitNumber = $permit->permit_number;
        
        // Delete permit document
        if ($permit->permit_document) {
            Storage::disk('public')->delete($permit->permit_document);
        }
        
        // Delete supporting documents
        if ($permit->supporting_documents) {
            foreach ($permit->supporting_documents as $document) {
                Storage::disk('public')->delete($document['path']);
            }
        }
        
        $permit->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_developer_permit',
            'details' => "Deleted permit: {$permitNumber}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.permits.index')
            ->with('success', 'Permit deleted successfully.');
    }

    public function updateStatus(Request $request, DeveloperPermit $permit): JsonResponse
    {
        $this->authorize('update', $permit);
        
        $request->validate([
            'status' => 'required|in:pending,approved,issued,rejected,expired,renewed',
        ]);

        $permit->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_permit_status',
            'details' => "Updated permit '{$permit->permit_number}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Permit status updated successfully'
        ]);
    }

    public function getExpiringSoon(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $expiringSoon = $developer->permits()
            ->where('status', 'issued')
            ->where('expiry_date', '<=', now()->addDays(90))
            ->where('expiry_date', '>', now())
            ->orderBy('expiry_date')
            ->get();

        return response()->json([
            'success' => true,
            'permits' => $expiringSoon
        ]);
    }

    public function getExpired(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $expired = $developer->permits()
            ->where('status', 'expired')
            ->orWhere('expiry_date', '<', now())
            ->orderBy('expiry_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'permits' => $expired
        ]);
    }

    public function getProjectPermits(DeveloperProject $project): JsonResponse
    {
        $this->authorize('view', $project);
        
        $permits = $project->permits()
            ->latest()
            ->get(['id', 'permit_number', 'permit_type', 'status', 'expiry_date']);

        return response()->json([
            'success' => true,
            'permits' => $permits
        ]);
    }

    public function getPermitStats(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $stats = [
            'total_permits' => $developer->permits()->count(),
            'pending_permits' => $developer->permits()->where('status', 'pending')->count(),
            'approved_permits' => $developer->permits()->where('status', 'approved')->count(),
            'issued_permits' => $developer->permits()->where('status', 'issued')->count(),
            'expired_permits' => $developer->permits()->where('status', 'expired')->count(),
            'expiring_soon' => $developer->permits()
                ->where('status', 'issued')
                ->where('expiry_date', '<=', now()->addDays(90))
                ->where('expiry_date', '>', now())
                ->count(),
            'by_type' => $developer->permits()
                ->groupBy('permit_type')
                ->map(function ($group) {
                    return $group->count();
                }),
            'total_estimated_cost' => $developer->permits()->sum('estimated_cost'),
            'total_actual_cost' => $developer->permits()->sum('actual_cost'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportPermits(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:pending,approved,issued,rejected,expired,renewed',
            'project_id' => 'nullable|exists:developer_projects,id',
        ]);

        $developer = Auth::user()->developer;
        
        $query = $developer->permits()->with(['project']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $permits = $query->get();

        $filename = "developer_permits_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $permits,
            'filename' => $filename,
            'message' => 'Permits exported successfully'
        ]);
    }
}

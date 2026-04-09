<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\StoreCertificationRequest;
use App\Http\Requests\Agent\UpdateCertificationRequest;
use App\Models\Agent;
use App\Models\AgentCertification;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AgentCertificationController extends Controller
{
    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $certifications = $agent->certifications()
            ->withPivot(['issued_date', 'expiry_date', 'certificate_number'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'active') {
                    $query->wherePivot('expiry_date', '>', now());
                } elseif ($status === 'expired') {
                    $query->wherePivot('expiry_date', '<=', now());
                }
            })
            ->orderByPivot('issued_date', 'desc')
            ->paginate(20);

        return view('agent.certifications.index', compact('certifications'));
    }

    public function create()
    {
        return view('agent.certifications.create');
    }

    public function store(StoreCertificationRequest $request)
    {
        $agent = Auth::user()->agent;
        
        // Create or find certification
        $certification = AgentCertification::firstOrCreate([
            'name' => $request->name,
            'issuing_organization' => $request->issuing_organization,
            'description' => $request->description,
            'category' => $request->category,
            'validity_period_years' => $request->validity_period_years,
            'is_required' => $request->is_required ?? false,
        ]);

        // Attach to agent with pivot data
        $agent->certifications()->attach($certification->id, [
            'certificate_number' => $request->certificate_number,
            'issued_date' => $request->issued_date,
            'expiry_date' => $request->expiry_date,
            'status' => 'active',
            'notes' => $request->notes,
        ]);

        // Handle certificate document upload
        if ($request->hasFile('certificate_document')) {
            $document = $request->file('certificate_document');
            $path = $document->store('certifications', 'public');
            
            // Update pivot with document path
            $agent->certifications()->updateExistingPivot($certification->id, [
                'document_path' => $path,
                'document_name' => $document->getClientOriginalName(),
            ]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'added_certification',
            'details' => "Added certification: {$certification->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.certifications.index')
            ->with('success', 'Certification added successfully.');
    }

    public function show($certificationId)
    {
        $agent = Auth::user()->agent;
        
        $certification = $agent->certifications()
            ->wherePivot('certification_id', $certificationId)
            ->withPivot(['issued_date', 'expiry_date', 'certificate_number', 'status', 'notes', 'document_path', 'document_name'])
            ->firstOrFail();

        return view('agent.certifications.show', compact('certification'));
    }

    public function edit($certificationId)
    {
        $agent = Auth::user()->agent;
        
        $certification = $agent->certifications()
            ->wherePivot('certification_id', $certificationId)
            ->withPivot(['issued_date', 'expiry_date', 'certificate_number', 'status', 'notes', 'document_path', 'document_name'])
            ->firstOrFail();

        return view('agent.certifications.edit', compact('certification'));
    }

    public function update(UpdateCertificationRequest $request, $certificationId)
    {
        $agent = Auth::user()->agent;
        
        $pivotData = [
            'certificate_number' => $request->certificate_number,
            'issued_date' => $request->issued_date,
            'expiry_date' => $request->expiry_date,
            'status' => $request->status,
            'notes' => $request->notes,
        ];

        // Handle document upload if new file provided
        if ($request->hasFile('certificate_document')) {
            // Delete old document if exists
            $currentPivot = $agent->certifications()->wherePivot('certification_id', $certificationId)->first()->pivot;
            if ($currentPivot->document_path) {
                Storage::disk('public')->delete($currentPivot->document_path);
            }
            
            // Upload new document
            $document = $request->file('certificate_document');
            $path = $document->store('certifications', 'public');
            
            $pivotData['document_path'] = $path;
            $pivotData['document_name'] = $document->getClientOriginalName();
        }

        $agent->certifications()->updateExistingPivot($certificationId, $pivotData);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_certification',
            'details' => "Updated certification details",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.certifications.show', $certificationId)
            ->with('success', 'Certification updated successfully.');
    }

    public function destroy($certificationId)
    {
        $agent = Auth::user()->agent;
        
        $certification = $agent->certifications()
            ->wherePivot('certification_id', $certificationId)
            ->firstOrFail();

        // Delete document if exists
        if ($certification->pivot->document_path) {
            Storage::disk('public')->delete($certification->pivot->document_path);
        }

        // Detach certification
        $agent->certifications()->detach($certificationId);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'removed_certification',
            'details' => "Removed certification: {$certification->name}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('agent.certifications.index')
            ->with('success', 'Certification removed successfully.');
    }

    public function downloadDocument($certificationId): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $certification = $agent->certifications()
            ->wherePivot('certification_id', $certificationId)
            ->firstOrFail();

        $documentPath = $certification->pivot->document_path;
        
        if (!$documentPath || !Storage::disk('public')->exists($documentPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ]);
        }

        $filePath = storage_path("app/public/{$documentPath}");
        
        return response()->json([
            'success' => true,
            'download_url' => asset("storage/{$documentPath}"),
            'filename' => $certification->pivot->document_name ?? 'certificate.pdf',
        ]);
    }

    public function updateStatus(Request $request, $certificationId): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $request->validate([
            'status' => 'required|in:active,expired,suspended,renewal_pending',
        ]);

        $agent->certifications()->updateExistingPivot($certificationId, [
            'status' => $request->status,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_certification_status',
            'details' => "Updated certification status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Certification status updated successfully'
        ]);
    }

    public function getExpiringCertifications(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $expiringSoon = $agent->certifications()
            ->wherePivot('expiry_date', '>', now())
            ->wherePivot('expiry_date', '<=', now()->addDays(90))
            ->wherePivot('status', 'active')
            ->withPivot(['expiry_date', 'certificate_number'])
            ->get(['id', 'name']);

        $expired = $agent->certifications()
            ->wherePivot('expiry_date', '<=', now())
            ->wherePivot('status', '!=', 'expired')
            ->withPivot(['expiry_date', 'certificate_number'])
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'expiring_soon' => $expiringSoon,
            'expired' => $expired,
        ]);
    }

    public function getCertificationStats(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $stats = [
            'total_certifications' => $agent->certifications()->count(),
            'active_certifications' => $agent->certifications()
                ->wherePivot('status', 'active')
                ->wherePivot('expiry_date', '>', now())
                ->count(),
            'expired_certifications' => $agent->certifications()
                ->wherePivot('expiry_date', '<=', now())
                ->count(),
            'expiring_soon' => $agent->certifications()
                ->wherePivot('expiry_date', '>', now())
                ->wherePivot('expiry_date', '<=', now()->addDays(90))
                ->count(),
            'required_certifications' => $agent->certifications()
                ->where('is_required', true)
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'certification_ids' => 'required|array',
            'certification_ids.*' => 'exists:agent_certifications,id',
            'action' => 'required|in:activate,deactivate,renew,expire',
        ]);

        $agent = Auth::user()->agent;
        $certificationIds = $request->certification_ids;
        $action = $request->action;

        $updateData = match($action) {
            'activate' => ['status' => 'active'],
            'deactivate' => ['status' => 'suspended'],
            'renew' => ['status' => 'active', 'expiry_date' => now()->addYears(1)],
            'expire' => ['status' => 'expired'],
            default => [],
        };

        foreach ($certificationIds as $certificationId) {
            $agent->certifications()->updateExistingPivot($certificationId, $updateData);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'bulk_updated_certifications',
            'details' => "Bulk {$action} on " . count($certificationIds) . " certifications",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Certifications {$action}d successfully"
        ]);
    }

    public function exportCertifications(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:active,expired,suspended,renewal_pending',
        ]);

        $agent = Auth::user()->agent;
        
        $query = $agent->certifications()->withPivot([
            'issued_date', 'expiry_date', 'certificate_number', 'status', 'notes'
        ]);

        if ($request->status) {
            if ($request->status === 'active') {
                $query->wherePivot('status', 'active')->wherePivot('expiry_date', '>', now());
            } else {
                $query->wherePivot('status', $request->status);
            }
        }

        $certifications = $query->get();

        $filename = "agent_certifications_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $certifications,
            'filename' => $filename,
            'message' => 'Certifications exported successfully'
        ]);
    }
}

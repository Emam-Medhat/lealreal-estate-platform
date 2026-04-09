<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\StoreLicenseRequest;
use App\Http\Requests\Agent\UpdateLicenseRequest;
use App\Models\Agent;
use App\Models\AgentLicense;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AgentLicenseController extends Controller
{
    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $licenses = $agent->licenses()
            ->when($request->search, function ($query, $search) {
                $query->where('license_number', 'like', "%{$search}%")
                    ->orWhere('issuing_authority', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->latest('issued_date')
            ->paginate(20);

        return view('agent.licenses.index', compact('licenses'));
    }

    public function create()
    {
        return view('agent.licenses.create');
    }

    public function store(StoreLicenseRequest $request)
    {
        $agent = Auth::user()->agent;
        
        $license = AgentLicense::create([
            'agent_id' => $agent->id,
            'license_number' => $request->license_number,
            'type' => $request->type,
            'issuing_authority' => $request->issuing_authority,
            'state' => $request->state,
            'country' => $request->country,
            'issued_date' => $request->issued_date,
            'expiry_date' => $request->expiry_date,
            'status' => $request->status ?? 'active',
            'restrictions' => $request->restrictions ?? [],
            'endorsements' => $request->endorsements ?? [],
            'specializations' => $request->specializations ?? [],
            'notes' => $request->notes,
            'verification_code' => $request->verification_code,
            'verification_url' => $request->verification_url,
        ]);

        // Handle license document upload
        if ($request->hasFile('license_document')) {
            $document = $request->file('license_document');
            $path = $document->store('licenses', 'public');
            
            $license->update([
                'document_path' => $path,
                'document_name' => $document->getClientOriginalName(),
            ]);
        }

        // Handle verification document upload
        if ($request->hasFile('verification_document')) {
            $document = $request->file('verification_document');
            $path = $document->store('license-verifications', 'public');
            
            $license->update([
                'verification_document_path' => $path,
                'verification_document_name' => $document->getClientOriginalName(),
            ]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_license',
            'details' => "Created license: {$license->license_number}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.licenses.show', $license)
            ->with('success', 'License created successfully.');
    }

    public function show(AgentLicense $license)
    {
        $this->authorize('view', $license);
        
        return view('agent.licenses.show', compact('license'));
    }

    public function edit(AgentLicense $license)
    {
        $this->authorize('update', $license);
        
        return view('agent.licenses.edit', compact('license'));
    }

    public function update(UpdateLicenseRequest $request, AgentLicense $license)
    {
        $this->authorize('update', $license);
        
        $license->update([
            'license_number' => $request->license_number,
            'type' => $request->type,
            'issuing_authority' => $request->issuing_authority,
            'state' => $request->state,
            'country' => $request->country,
            'issued_date' => $request->issued_date,
            'expiry_date' => $request->expiry_date,
            'status' => $request->status,
            'restrictions' => $request->restrictions ?? [],
            'endorsements' => $request->endorsements ?? [],
            'specializations' => $request->specializations ?? [],
            'notes' => $request->notes,
            'verification_code' => $request->verification_code,
            'verification_url' => $request->verification_url,
        ]);

        // Handle document uploads if new files provided
        if ($request->hasFile('license_document')) {
            if ($license->document_path) {
                Storage::disk('public')->delete($license->document_path);
            }
            
            $document = $request->file('license_document');
            $path = $document->store('licenses', 'public');
            
            $license->update([
                'document_path' => $path,
                'document_name' => $document->getClientOriginalName(),
            ]);
        }

        if ($request->hasFile('verification_document')) {
            if ($license->verification_document_path) {
                Storage::disk('public')->delete($license->verification_document_path);
            }
            
            $document = $request->file('verification_document');
            $path = $document->store('license-verifications', 'public');
            
            $license->update([
                'verification_document_path' => $path,
                'verification_document_name' => $document->getClientOriginalName(),
            ]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_license',
            'details' => "Updated license: {$license->license_number}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.licenses.show', $license)
            ->with('success', 'License updated successfully.');
    }

    public function destroy(AgentLicense $license)
    {
        $this->authorize('delete', $license);
        
        $licenseNumber = $license->license_number;
        
        // Delete documents
        if ($license->document_path) {
            Storage::disk('public')->delete($license->document_path);
        }
        
        if ($license->verification_document_path) {
            Storage::disk('public')->delete($license->verification_document_path);
        }
        
        $license->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_license',
            'details' => "Deleted license: {$licenseNumber}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('agent.licenses.index')
            ->with('success', 'License deleted successfully.');
    }

    public function updateStatus(Request $request, AgentLicense $license): JsonResponse
    {
        $this->authorize('update', $license);
        
        $request->validate([
            'status' => 'required|in:active,expired,suspended,revoked,renewal_pending',
        ]);

        $license->update([
            'status' => $request->status,
            'status_updated_at' => now(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_license_status',
            'details' => "Updated license {$license->license_number} status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'License status updated successfully'
        ]);
    }

    public function renew(Request $request, AgentLicense $license): JsonResponse
    {
        $this->authorize('update', $license);
        
        $request->validate([
            'new_expiry_date' => 'required|date|after:today',
            'renewal_notes' => 'nullable|string|max:1000',
        ]);

        $license->update([
            'expiry_date' => $request->new_expiry_date,
            'status' => 'active',
            'renewal_date' => now(),
            'renewal_notes' => $request->renewal_notes,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'renewed_license',
            'details' => "Renewed license {$license->license_number} until {$request->new_expiry_date}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'License renewed successfully'
        ]);
    }

    public function verify(Request $request, AgentLicense $license): JsonResponse
    {
        $this->authorize('update', $license);
        
        $request->validate([
            'verification_result' => 'required|in:verified,invalid,expired',
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        $license->update([
            'verification_status' => $request->verification_result,
            'verification_date' => now(),
            'verification_notes' => $request->verification_notes,
        ]);

        if ($request->verification_result === 'verified') {
            $license->update(['status' => 'active']);
        } elseif ($request->verification_result === 'invalid') {
            $license->update(['status' => 'suspended']);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'verified_license',
            'details' => "Verified license {$license->license_number}: {$request->verification_result}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'verification_status' => $request->verification_result,
            'message' => 'License verification completed'
        ]);
    }

    public function downloadDocument(AgentLicense $license, string $type): JsonResponse
    {
        $this->authorize('view', $license);
        
        $documentPath = $type === 'license' ? $license->document_path : $license->verification_document_path;
        $documentName = $type === 'license' ? $license->document_name : $license->verification_document_name;
        
        if (!$documentPath || !Storage::disk('public')->exists($documentPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ]);
        }

        return response()->json([
            'success' => true,
            'download_url' => asset("storage/{$documentPath}"),
            'filename' => $documentName ?? 'document.pdf',
        ]);
    }

    public function getExpiringLicenses(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $expiringSoon = $agent->licenses()
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(90))
            ->where('status', 'active')
            ->get(['id', 'license_number', 'type', 'expiry_date']);

        $expired = $agent->licenses()
            ->where('expiry_date', '<=', now())
            ->where('status', '!=', 'expired')
            ->get(['id', 'license_number', 'type', 'expiry_date']);

        return response()->json([
            'success' => true,
            'expiring_soon' => $expiringSoon,
            'expired' => $expired,
        ]);
    }

    public function getLicenseStats(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        $stats = [
            'total_licenses' => $agent->licenses()->count(),
            'active_licenses' => $agent->licenses()->where('status', 'active')->count(),
            'expired_licenses' => $agent->licenses()->where('status', 'expired')->count(),
            'suspended_licenses' => $agent->licenses()->where('status', 'suspended')->count(),
            'expiring_soon' => $agent->licenses()
                ->where('expiry_date', '>', now())
                ->where('expiry_date', '<=', now()->addDays(90))
                ->count(),
            'verified_licenses' => $agent->licenses()
                ->where('verification_status', 'verified')
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportLicenses(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:active,expired,suspended,revoked,renewal_pending',
        ]);

        $agent = Auth::user()->agent;
        
        $query = $agent->licenses();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $licenses = $query->get();

        $filename = "agent_licenses_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $licenses,
            'filename' => $filename,
            'message' => 'Licenses exported successfully'
        ]);
    }
}

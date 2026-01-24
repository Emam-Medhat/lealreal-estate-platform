<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\StoreCertificationRequest;
use App\Http\Requests\Developer\UpdateCertificationRequest;
use App\Models\Developer;
use App\Models\DeveloperCertification;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperCertificationController extends Controller
{
    public function index(Request $request)
    {
        $developer = Auth::user()->developer;
        
        $certifications = $developer->certifications()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('issuing_organization', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return view('developer.certifications.index', compact('certifications'));
    }

    public function create()
    {
        $developer = Auth::user()->developer;
        return view('developer.certifications.create', compact('developer'));
    }

    public function store(StoreCertificationRequest $request)
    {
        $developer = Auth::user()->developer;
        
        $certification = DeveloperCertification::create([
            'developer_id' => $developer->id,
            'name' => $request->name,
            'issuing_organization' => $request->issuing_organization,
            'description' => $request->description,
            'category' => $request->category,
            'validity_period_years' => $request->validity_period_years,
            'is_required' => $request->is_required ?? false,
            'certificate_number' => $request->certificate_number,
            'issued_date' => $request->issued_date,
            'expiry_date' => $request->expiry_date,
            'status' => $request->status ?? 'active',
            'notes' => $request->notes,
        ]);

        // Handle certificate document upload
        if ($request->hasFile('certificate_document')) {
            $documentPath = $request->file('certificate_document')->store('developer-certifications', 'public');
            $certification->update(['certificate_document' => $documentPath]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_developer_certification',
            'details' => "Created certification: {$certification->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.certifications.show', $certification)
            ->with('success', 'Certification created successfully.');
    }

    public function show(DeveloperCertification $certification)
    {
        $this->authorize('view', $certification);
        
        return view('developer.certifications.show', compact('certification'));
    }

    public function edit(DeveloperCertification $certification)
    {
        $this->authorize('update', $certification);
        
        return view('developer.certifications.edit', compact('certification'));
    }

    public function update(UpdateCertificationRequest $request, DeveloperCertification $certification)
    {
        $this->authorize('update', $certification);
        
        $certification->update([
            'name' => $request->name,
            'issuing_organization' => $request->issuing_organization,
            'description' => $request->description,
            'category' => $request->category,
            'validity_period_years' => $request->validity_period_years,
            'is_required' => $request->is_required,
            'certificate_number' => $request->certificate_number,
            'issued_date' => $request->issued_date,
            'expiry_date' => $request->expiry_date,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        // Handle certificate document update
        if ($request->hasFile('certificate_document')) {
            if ($certification->certificate_document) {
                Storage::disk('public')->delete($certification->certificate_document);
            }
            $documentPath = $request->file('certificate_document')->store('developer-certifications', 'public');
            $certification->update(['certificate_document' => $documentPath]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer_certification',
            'details' => "Updated certification: {$certification->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.certifications.show', $certification)
            ->with('success', 'Certification updated successfully.');
    }

    public function destroy(DeveloperCertification $certification)
    {
        $this->authorize('delete', $certification);
        
        $certificationName = $certification->name;
        
        // Delete certificate document
        if ($certification->certificate_document) {
            Storage::disk('public')->delete($certification->certificate_document);
        }
        
        $certification->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_developer_certification',
            'details' => "Deleted certification: {$certificationName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.certifications.index')
            ->with('success', 'Certification deleted successfully.');
    }

    public function updateStatus(Request $request, DeveloperCertification $certification): JsonResponse
    {
        $this->authorize('update', $certification);
        
        $request->validate([
            'status' => 'required|in:active,expired,suspended,renewal_pending',
        ]);

        $certification->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_certification_status',
            'details' => "Updated certification '{$certification->name}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Certification status updated successfully'
        ]);
    }

    public function downloadDocument(DeveloperCertification $certification)
    {
        $this->authorize('view', $certification);
        
        if (!$certification->certificate_document) {
            return back()->with('error', 'No document available for download.');
        }

        $filePath = storage_path('app/public/' . $certification->certificate_document);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Document file not found.');
        }

        return response()->download($filePath, $certification->name . '_certificate.' . pathinfo($filePath, PATHINFO_EXTENSION));
    }

    public function getExpiringSoon(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $expiringSoon = $developer->certifications()
            ->where('status', 'active')
            ->where('expiry_date', '<=', now()->addDays(90))
            ->where('expiry_date', '>', now())
            ->orderBy('expiry_date')
            ->get();

        return response()->json([
            'success' => true,
            'certifications' => $expiringSoon
        ]);
    }

    public function getExpired(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $expired = $developer->certifications()
            ->where('status', 'expired')
            ->orWhere('expiry_date', '<', now())
            ->orderBy('expiry_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'certifications' => $expired
        ]);
    }

    public function getCertificationStats(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $stats = [
            'total_certifications' => $developer->certifications()->count(),
            'active_certifications' => $developer->certifications()->where('status', 'active')->count(),
            'expired_certifications' => $developer->certifications()->where('status', 'expired')->count(),
            'expiring_soon' => $developer->certifications()
                ->where('status', 'active')
                ->where('expiry_date', '<=', now()->addDays(90))
                ->where('expiry_date', '>', now())
                ->count(),
            'required_certifications' => $developer->certifications()->where('is_required', true)->count(),
            'by_category' => $developer->certifications()
                ->groupBy('category')
                ->map(function ($group) {
                    return $group->count();
                }),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportCertifications(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:active,expired,suspended,renewal_pending',
        ]);

        $developer = Auth::user()->developer;
        
        $query = $developer->certifications();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $certifications = $query->get();

        $filename = "developer_certifications_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $certifications,
            'filename' => $filename,
            'message' => 'Certifications exported successfully'
        ]);
    }
}

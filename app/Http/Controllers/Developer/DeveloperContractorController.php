<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\StoreContractorRequest;
use App\Http\Requests\Developer\UpdateContractorRequest;
use App\Models\Developer;
use App\Models\DeveloperContractor;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperContractorController extends Controller
{
    public function index(Request $request)
    {
        $developer = Auth::user()->developer;
        
        $contractors = $developer->contractors()
            ->when($request->search, function ($query, $search) {
                $query->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('specialization', 'like', "%{$search}%");
            })
            ->when($request->specialization, function ($query, $specialization) {
                $query->where('specialization', $specialization);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return view('developer.contractors.index', compact('contractors'));
    }

    public function create()
    {
        $developer = Auth::user()->developer;
        return view('developer.contractors.create', compact('developer'));
    }

    public function store(StoreContractorRequest $request)
    {
        $developer = Auth::user()->developer;
        
        $contractor = DeveloperContractor::create([
            'developer_id' => $developer->id,
            'company_name' => $request->company_name,
            'contact_person' => $request->contact_person,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'specialization' => $request->specialization,
            'services_offered' => $request->services_offered ?? [],
            'company_size' => $request->company_size,
            'established_year' => $request->established_year,
            'license_number' => $request->license_number,
            'tax_id' => $request->tax_id,
            'insurance_details' => $request->insurance_details ?? [],
            'certifications' => $request->certifications ?? [],
            'experience_years' => $request->experience_years,
            'completed_projects' => $request->completed_projects ?? [],
            'ongoing_projects' => $request->ongoing_projects ?? [],
            'team_members' => $request->team_members ?? [],
            'equipment_available' => $request->equipment_available ?? [],
            'payment_terms' => $request->payment_terms,
            'hourly_rate' => $request->hourly_rate,
            'project_rate' => $request->project_rate,
            'rating' => $request->rating ?? 0,
            'status' => $request->status ?? 'active',
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle company logo upload
        if ($request->hasFile('company_logo')) {
            $logoPath = $request->file('company_logo')->store('contractor-logos', 'public');
            $contractor->update(['company_logo' => $logoPath]);
        }

        // Handle documents upload
        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('contractor-documents', 'public');
                $documents[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $contractor->update(['documents' => $documents]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_developer_contractor',
            'details' => "Created contractor: {$contractor->company_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.contractors.show', $contractor)
            ->with('success', 'Contractor created successfully.');
    }

    public function show(DeveloperContractor $contractor)
    {
        $this->authorize('view', $contractor);
        
        return view('developer.contractors.show', compact('contractor'));
    }

    public function edit(DeveloperContractor $contractor)
    {
        $this->authorize('update', $contractor);
        
        return view('developer.contractors.edit', compact('contractor'));
    }

    public function update(UpdateContractorRequest $request, DeveloperContractor $contractor)
    {
        $this->authorize('update', $contractor);
        
        $contractor->update([
            'company_name' => $request->company_name,
            'contact_person' => $request->contact_person,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'specialization' => $request->specialization,
            'services_offered' => $request->services_offered ?? [],
            'company_size' => $request->company_size,
            'established_year' => $request->established_year,
            'license_number' => $request->license_number,
            'tax_id' => $request->tax_id,
            'insurance_details' => $request->insurance_details ?? [],
            'certifications' => $request->certifications ?? [],
            'experience_years' => $request->experience_years,
            'completed_projects' => $request->completed_projects ?? [],
            'ongoing_projects' => $request->ongoing_projects ?? [],
            'team_members' => $request->team_members ?? [],
            'equipment_available' => $request->equipment_available ?? [],
            'payment_terms' => $request->payment_terms,
            'hourly_rate' => $request->hourly_rate,
            'project_rate' => $request->project_rate,
            'rating' => $request->rating,
            'status' => $request->status,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        // Handle company logo update
        if ($request->hasFile('company_logo')) {
            if ($contractor->company_logo) {
                Storage::disk('public')->delete($contractor->company_logo);
            }
            $logoPath = $request->file('company_logo')->store('contractor-logos', 'public');
            $contractor->update(['company_logo' => $logoPath]);
        }

        // Handle new documents
        if ($request->hasFile('documents')) {
            $existingDocuments = $contractor->documents ?? [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('contractor-documents', 'public');
                $existingDocuments[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $contractor->update(['documents' => $existingDocuments]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer_contractor',
            'details' => "Updated contractor: {$contractor->company_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.contractors.show', $contractor)
            ->with('success', 'Contractor updated successfully.');
    }

    public function destroy(DeveloperContractor $contractor)
    {
        $this->authorize('delete', $contractor);
        
        $companyName = $contractor->company_name;
        
        // Delete company logo
        if ($contractor->company_logo) {
            Storage::disk('public')->delete($contractor->company_logo);
        }
        
        // Delete documents
        if ($contractor->documents) {
            foreach ($contractor->documents as $document) {
                Storage::disk('public')->delete($document['path']);
            }
        }
        
        $contractor->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_developer_contractor',
            'details' => "Deleted contractor: {$companyName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.contractors.index')
            ->with('success', 'Contractor deleted successfully.');
    }

    public function updateStatus(Request $request, DeveloperContractor $contractor): JsonResponse
    {
        $this->authorize('update', $contractor);
        
        $request->validate([
            'status' => 'required|in:active,inactive,suspended,blacklisted',
        ]);

        $contractor->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_contractor_status',
            'details' => "Updated contractor '{$contractor->company_name}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Contractor status updated successfully'
        ]);
    }

    public function updateRating(Request $request, DeveloperContractor $contractor): JsonResponse
    {
        $this->authorize('update', $contractor);
        
        $request->validate([
            'rating' => 'required|numeric|min:0|max:5',
        ]);

        $contractor->update(['rating' => $request->rating]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_contractor_rating',
            'details' => "Updated contractor '{$contractor->company_name}' rating to {$request->rating}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'rating' => $request->rating,
            'message' => 'Contractor rating updated successfully'
        ]);
    }

    public function getContractorStats(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $stats = [
            'total_contractors' => $developer->contractors()->count(),
            'active_contractors' => $developer->contractors()->where('status', 'active')->count(),
            'inactive_contractors' => $developer->contractors()->where('status', 'inactive')->count(),
            'suspended_contractors' => $developer->contractors()->where('status', 'suspended')->count(),
            'blacklisted_contractors' => $developer->contractors()->where('status', 'blacklisted')->count(),
            'by_specialization' => $developer->contractors()
                ->groupBy('specialization')
                ->map(function ($group) {
                    return $group->count();
                }),
            'by_company_size' => $developer->contractors()
                ->groupBy('company_size')
                ->map(function ($group) {
                    return $group->count();
                }),
            'average_rating' => $developer->contractors()->avg('rating'),
            'average_experience' => $developer->contractors()->avg('experience_years'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getAvailableContractors(Request $request): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $contractors = $developer->contractors()
            ->where('status', 'active')
            ->when($request->specialization, function ($query, $specialization) {
                $query->where('specialization', $specialization);
            })
            ->orderBy('rating', 'desc')
            ->get(['id', 'company_name', 'specialization', 'rating', 'contact_person', 'contact_phone']);

        return response()->json([
            'success' => true,
            'contractors' => $contractors
        ]);
    }

    public function exportContractors(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:active,inactive,suspended,blacklisted',
            'specialization' => 'nullable|string|max:100',
        ]);

        $developer = Auth::user()->developer;
        
        $query = $developer->contractors();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->specialization) {
            $query->where('specialization', $request->specialization);
        }

        $contractors = $query->get();

        $filename = "developer_contractors_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $contractors,
            'filename' => $filename,
            'message' => 'Contractors exported successfully'
        ]);
    }
}

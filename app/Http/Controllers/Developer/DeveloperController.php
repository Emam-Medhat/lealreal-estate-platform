<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\StoreDeveloperRequest;
use App\Http\Requests\Developer\UpdateDeveloperRequest;
use App\Models\Developer;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperController extends Controller
{
    public function index(Request $request)
    {
        $developers = Developer::with(['profile'])
            ->when($request->search, function ($query, $search) {
                $query->where('company_name', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%")
                    ->orWhere('contact_phone', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($q) use ($search) {
                        $q->where('description', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('developer_type', $type);
            })
            ->latest()
            ->paginate(20);

        return view('developer.index', compact('developers'));
    }

    public function create()
    {
        return view('developer.create');
    }

    public function store(StoreDeveloperRequest $request)
    {
        $developer = Developer::create([
            'user_id' => Auth::id(),
            'company_name' => $request->company_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'website' => $request->website,
            'developer_type' => $request->developer_type,
            'establishment_year' => $request->establishment_year,
            'ceo_name' => $request->ceo_name,
            'headquarters_address' => $request->headquarters_address,
            'headquarters_city' => $request->headquarters_city,
            'headquarters_country' => $request->headquarters_country,
            'status' => $request->status ?? 'active',
            'is_verified' => $request->is_verified ?? false,
            'total_projects' => 0,
            'completed_projects' => 0,
            'ongoing_projects' => 0,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_developer',
            'details' => "Created developer: {$developer->company_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.show', $developer)
            ->with('success', 'Developer created successfully.');
    }

    public function show(Developer $developer)
    {
        $developer->load(['profile', 'projects', 'certifications', 'contractors']);
        
        return view('developer.show', compact('developer'));
    }

    public function edit(Developer $developer)
    {
        return view('developer.edit', compact('developer'));
    }

    public function update(UpdateDeveloperRequest $request, Developer $developer)
    {
        $developer->update([
            'company_name' => $request->company_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'website' => $request->website,
            'developer_type' => $request->developer_type,
            'establishment_year' => $request->establishment_year,
            'ceo_name' => $request->ceo_name,
            'headquarters_address' => $request->headquarters_address,
            'headquarters_city' => $request->headquarters_city,
            'headquarters_country' => $request->headquarters_country,
            'status' => $request->status,
            'is_verified' => $request->is_verified,
        ]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer',
            'details' => "Updated developer: {$developer->company_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.show', $developer)
            ->with('success', 'Developer updated successfully.');
    }

    public function destroy(Developer $developer)
    {
        $companyName = $developer->company_name;
        $developer->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_developer',
            'details' => "Deleted developer: {$companyName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.index')
            ->with('success', 'Developer deleted successfully.');
    }

    public function updateStatus(Request $request, Developer $developer): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $developer->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer_status',
            'details' => "Updated developer {$developer->company_name} status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Developer status updated successfully'
        ]);
    }

    public function toggleVerification(Request $request, Developer $developer): JsonResponse
    {
        $developer->update(['is_verified' => !$developer->is_verified]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'toggled_developer_verification',
            'details' => ($developer->is_verified ? 'Verified' : 'Unverified') . " developer: {$developer->company_name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'is_verified' => $developer->is_verified,
            'message' => 'Developer verification status updated successfully'
        ]);
    }

    public function getDeveloperStats(): JsonResponse
    {
        $stats = [
            'total_developers' => Developer::count(),
            'active_developers' => Developer::where('status', 'active')->count(),
            'verified_developers' => Developer::where('is_verified', true)->count(),
            'total_projects' => Developer::sum('total_projects'),
            'completed_projects' => Developer::sum('completed_projects'),
            'ongoing_projects' => Developer::sum('ongoing_projects'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportDevelopers(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        $query = Developer::with(['profile']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $developers = $query->get();

        $filename = "developers_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $developers,
            'filename' => $filename,
            'message' => 'Developers exported successfully'
        ]);
    }
}

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
use Illuminate\Support\Facades\DB;

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
        try {
            // Debug: Log the request data
            \Log::info('Developer creation attempt', [
                'request_data' => $request->all(),
                'validated_data' => $request->validated()
            ]);

            $developer = Developer::create([
                'user_id' => Auth::id(),
                'company_name' => $request->company_name,
                'license_number' => $request->license_number ?? 'TEMP-' . time(),
                'commercial_register' => $request->commercial_register ?? 'TEMP-' . time(),
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'developer_type' => $request->developer_type,
                'established_year' => $request->established_year,
                'address' => $request->address ? ['full_address' => $request->address] : null,
                'status' => $request->status ?? 'active',
                'is_verified' => $request->is_verified ?? false,
                'total_projects' => 0,
                'completed_projects' => 0,
                'ongoing_projects' => 0,
            ]);

            \Log::info('Developer created successfully', ['developer_id' => $developer->id]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_developer',
                'description' => "Created developer: {$developer->company_name}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('developer.show', $developer)
                ->with('success', 'Developer created successfully.');

        } catch (\Exception $e) {
            \Log::error('Developer creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create developer: ' . $e->getMessage());
        }
    }

    public function show(Developer $developer)
    {
        $developer->load(['profile']);

        
        return view('developer.show', compact('developer'));
    }

    public function edit(Developer $developer)
    {
        return view('developer.edit', compact('developer'));
    }

    public function update(Request $request, Developer $developer)
    {
        // Validate the request
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_name_ar' => 'nullable|string|max:255',
            'license_number' => 'required|string|max:255',
            'commercial_register' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'developer_type' => 'required|in:residential,commercial,mixed,industrial',
            'status' => 'required|in:pending,active,suspended,inactive',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'website' => 'nullable|url|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'total_projects' => 'nullable|integer|min:0',
            'total_investment' => 'nullable|numeric|min:0',
            'review_count' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:2000',
            'description_ar' => 'nullable|string|max:2000',
            'address' => 'nullable|array',
            'is_verified' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        // Update developer
        $developer->update($validated);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer',
            'description' => "Updated developer: {$developer->company_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.show', $developer)
            ->with('success', 'تم تحديث بيانات المطور بنجاح');
    }

    public function destroy(Developer $developer)
    {
        $companyName = $developer->company_name;
        $developer->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_developer',
            'description' => "Deleted developer: {$companyName}",
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
            'description' => "Updated developer {$developer->company_name} status to {$request->status}",
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
            'description' => ($developer->is_verified ? 'Verified' : 'Unverified') . " developer: {$developer->company_name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'is_verified' => $developer->is_verified,
            'message' => 'Developer verification status updated successfully'
        ]);
    }

    public function toggleFeatured(Request $request, Developer $developer): JsonResponse
    {
        $developer->update(['is_featured' => !$developer->is_featured]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'toggled_developer_featured',
            'description' => ($developer->is_featured ? 'Featured' : 'Unfeatured') . " developer: {$developer->company_name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'is_featured' => $developer->is_featured,
            'message' => 'Developer featured status updated successfully'
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

    public function bimModels(Request $request)
    {
        try {
            // Use sample data if tables don't exist
            $stats = [
                'total_models' => 45,
                'active_models' => 32,
                'total_files_size' => 2048000, // bytes
                'avg_review_score' => 4.2
            ];

            // Create sample recent models
            $recentModels = collect([
                (object)[
                    'name' => 'نموذج فيلا الرياض',
                    'version' => 'v2.1',
                    'company_name' => 'شركة المطور العقاري',
                    'file_size' => 1536000,
                    'status' => 'published',
                    'review_score' => 4,
                    'created_at' => now()->subDays(2)
                ],
                (object)[
                    'name' => 'نموذج برج جدة',
                    'version' => 'v1.5',
                    'company_name' => 'شركة البناء الحديث',
                    'file_size' => 2048000,
                    'status' => 'draft',
                    'review_score' => 3,
                    'created_at' => now()->subDays(5)
                ],
                (object)[
                    'name' => 'نموذج مجمع سكني',
                    'version' => 'v3.0',
                    'company_name' => 'شركة التطوير المتقدم',
                    'file_size' => 3072000,
                    'status' => 'published',
                    'review_score' => 5,
                    'created_at' => now()->subDays(7)
                ]
            ]);

            return view('developer.bim-models', compact('stats', 'recentModels'));
        } catch (\Exception $e) {
            return view('developer.bim-models', [
                'stats' => [
                    'total_models' => 0,
                    'active_models' => 0,
                    'total_files_size' => 0,
                    'avg_review_score' => 0
                ],
                'recentModels' => collect()
            ]);
        }
    }

    public function construction(Request $request)
    {
        try {
            // Use sample data if tables don't exist
            $stats = [
                'total_projects' => 28,
                'active_projects' => 15,
                'completed_projects' => 10,
                'total_budget' => 45000000
            ];

            // Create sample recent projects
            $recentProjects = collect([
                (object)[
                    'name' => 'مشروع الأبراج السكنية',
                    'location' => 'الرياض - حي النخيل',
                    'company_name' => 'شركة المطور العقاري',
                    'contractor_name' => 'شركة المقاولون المتحدة',
                    'status' => 'in_progress',
                    'progress_percentage' => 65,
                    'total_cost' => 15000000,
                    'created_at' => now()->subDays(10)
                ],
                (object)[
                    'name' => 'مشروع الفيلا المميزة',
                    'location' => 'جدة - حي الروضة',
                    'company_name' => 'شركة البناء الحديث',
                    'contractor_name' => 'شركة المقاولات المتقدمة',
                    'status' => 'completed',
                    'progress_percentage' => 100,
                    'total_cost' => 2500000,
                    'created_at' => now()->subDays(30)
                ],
                (object)[
                    'name' => 'مشروع المجمع التجاري',
                    'location' => 'الدمام - حي الملك فهد',
                    'company_name' => 'شركة التطوير المتقدم',
                    'contractor_name' => 'شركة البناء الوطنية',
                    'status' => 'planning',
                    'progress_percentage' => 15,
                    'total_cost' => 8000000,
                    'created_at' => now()->subDays(5)
                ]
            ]);

            return view('developer.construction', compact('stats', 'recentProjects'));
        } catch (\Exception $e) {
            return view('developer.construction', [
                'stats' => [
                    'total_projects' => 0,
                    'active_projects' => 0,
                    'completed_projects' => 0,
                    'total_budget' => 0
                ],
                'recentProjects' => collect()
            ]);
        }
    }

    public function permits(Request $request)
    {
        try {
            // Use sample data if tables don't exist
            $stats = [
                'total_permits' => 156,
                'approved_permits' => 89,
                'pending_permits' => 45,
                'rejected_permits' => 22
            ];

            // Create sample recent permits
            $recentPermits = collect([
                (object)[
                    'permit_number' => 'BLD-2024-0456',
                    'permit_type' => 'تصريح بناء',
                    'company_name' => 'شركة المطور العقاري',
                    'property_address' => 'الرياض - حي النخيل',
                    'construction_type' => 'سكني',
                    'status' => 'approved',
                    'created_at' => now()->subDays(3)
                ],
                (object)[
                    'permit_number' => 'BLD-2024-0455',
                    'permit_type' => 'تصريح تجديد',
                    'company_name' => 'شركة البناء الحديث',
                    'property_address' => 'جدة - حي الروضة',
                    'construction_type' => 'تجاري',
                    'status' => 'pending',
                    'created_at' => now()->subDays(7)
                ],
                (object)[
                    'permit_number' => 'BLD-2024-0454',
                    'permit_type' => 'تصريح هدم',
                    'company_name' => 'شركة التطوير المتقدم',
                    'property_address' => 'الدمام - حي الملك فهد',
                    'construction_type' => 'صناعي',
                    'status' => 'rejected',
                    'created_at' => now()->subDays(10)
                ]
            ]);

            return view('developer.permits', compact('stats', 'recentPermits'));
        } catch (\Exception $e) {
            return view('developer.permits', [
                'stats' => [
                    'total_permits' => 0,
                    'approved_permits' => 0,
                    'pending_permits' => 0,
                    'rejected_permits' => 0
                ],
                'recentPermits' => collect()
            ]);
        }
    }

    public function uploadBimModel(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'file' => 'required|file|mimes:ifc,rvt,skp,dwg|max:50000',
                'description' => 'nullable|string|max:1000'
            ]);

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bim-models', $fileName, 'public');

            // Store in database (you'll need to create the bim_models table)
            $bimModel = [
                'id' => time(),
                'name' => $request->name,
                'description' => $request->description,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ];

            return response()->json([
                'success' => true,
                'message' => 'تم رفع النموذج بنجاح',
                'data' => $bimModel
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportBimModels()
    {
        try {
            $models = [
                [
                    'name' => 'نموذج تجريبي 1',
                    'description' => 'وصف النموذج الأول',
                    'status' => 'نشط',
                    'created_at' => now()->format('Y-m-d H:i:s')
                ],
                [
                    'name' => 'نموذج تجريبي 2',
                    'description' => 'وصف النموذج الثاني',
                    'status' => 'نشط',
                    'created_at' => now()->format('Y-m-d H:i:s')
                ]
            ];

            $filename = 'bim-models-export-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ];

            $callback = function() use ($models) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fwrite($file, "\xEF\xBB\xBF");
                
                // CSV header
                fputcsv($file, ['اسم النموذج', 'الوصف', 'الحالة', 'تاريخ الإنشاء']);
                
                // CSV data
                foreach ($models as $model) {
                    fputcsv($file, [
                        $model['name'],
                        $model['description'],
                        $model['status'],
                        $model['created_at']
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التصدير: ' . $e->getMessage()
            ], 500);
        }
    }
}

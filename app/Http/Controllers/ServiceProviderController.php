<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use App\Services\ServiceProviderService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ServiceProviderController extends Controller
{
    protected $providerService;

    public function __construct(ServiceProviderService $providerService)
    {
        $this->providerService = $providerService;
    }

    public function index(Request $request)
    {
        $providers = $this->providerService->getAllProviders($request->all());
        return view('maintenance.providers', compact('providers'));
    }

    public function create()
    {
        return view('maintenance.providers-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:service_providers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'service_type' => 'required|in:plumbing,electrical,hvac,structural,general,all',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:100',
            'license_number' => 'nullable|string|max:100',
            'insurance_number' => 'nullable|string|max:100',
            'hourly_rate' => 'required|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        try {
            $attachments = $request->file('attachments', []);
            $provider = $this->providerService->createProvider($validated, $attachments);

            return redirect()->route('maintenance.providers.show', $provider)
                ->with('success', 'تم إنشاء مقدم الخدمة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إنشاء مقدم الخدمة: ' . $e->getMessage());
        }
    }

    public function show(ServiceProvider $provider)
    {
        $provider = $this->providerService->getProviderDetails($provider->id);

        $stats = [
            'total_requests' => $provider->maintenanceRequests()->count(),
            'completed_requests' => $provider->maintenanceRequests()->where('status', 'completed')->count(),
            'pending_requests' => $provider->maintenanceRequests()->where('status', 'pending')->count(),
            'in_progress_requests' => $provider->maintenanceRequests()->where('status', 'in_progress')->count(),
            'total_revenue' => $provider->maintenanceRequests()->where('status', 'completed')->sum('actual_cost'),
            'average_rating' => $provider->rating,
            'upcoming_schedules' => $provider->schedules()->where('scheduled_date', '>=', now())->count(),
        ];

        return view('maintenance.providers-show', compact('provider', 'stats'));
    }

    public function edit(ServiceProvider $provider)
    {
        $provider->specializations = json_decode($provider->specializations ?? '[]', true);
        return view('maintenance.providers-edit', compact('provider'));
    }

    public function update(Request $request, ServiceProvider $provider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:service_providers,email,' . $provider->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'service_type' => 'required|in:plumbing,electrical,hvac,structural,general,all',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:100',
            'license_number' => 'nullable|string|max:100',
            'insurance_number' => 'nullable|string|max:100',
            'hourly_rate' => 'required|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->providerService->updateProvider($provider, $validated);
            return redirect()->route('maintenance.providers.show', $provider)
                ->with('success', 'تم تحديث مقدم الخدمة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث مقدم الخدمة');
        }
    }

    public function destroy(ServiceProvider $provider)
    {
        try {
            $this->providerService->deleteProvider($provider);
            return redirect()->route('maintenance.providers.index')
                ->with('success', 'تم حذف مقدم الخدمة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(ServiceProvider $provider)
    {
        $isActive = $this->providerService->toggleProviderStatus($provider);
        $status = $isActive ? 'تفعيل' : 'تعطيل';

        return redirect()->route('maintenance.providers.show', $provider)
            ->with('success', 'تم ' . $status . ' مقدم الخدمة بنجاح');
    }

    public function updateRating(ServiceProvider $provider, Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric|min:0|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $this->providerService->updateProviderRating($provider, $validated['rating'], $validated['review']);

        return redirect()->route('maintenance.providers.show', $provider)
            ->with('success', 'تم تحديث تقييم مقدم الخدمة بنجاح');
    }

    public function getAvailableProviders(Request $request)
    {
        $serviceType = $request->service_type;
        $date = $request->date;
        $duration = $request->duration ?? 60;

        $providers = $this->providerService->getAvailableProviders($serviceType, $date, $duration);

        return response()->json($providers);
    }

    public function performance(ServiceProvider $provider)
    {
        $performanceData = $this->providerService->getProviderPerformance($provider);
        $stats = $performanceData['stats'];
        $monthlyData = $performanceData['monthlyData'];

        return view('maintenance.providers-performance', compact('provider', 'stats', 'monthlyData'));
    }

    public function export(Request $request)
    {
        $providers = ServiceProvider::withCount(['maintenanceRequests', 'schedules'])
            ->when($request->is_active, function ($query, $isActive) {
                $query->where('is_active', $isActive);
            })
            ->when($request->service_type, function ($query, $serviceType) {
                $query->where('service_type', $serviceType);
            })
            ->get();

        $filename = 'service_providers_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($providers) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'الكود',
                'الاسم',
                'الشخص المسؤول',
                'البريد الإلكتروني',
                'الهاتف',
                'نوع الخدمة',
                'التقييم',
                'الساعة بالريال',
                'الحالة',
                'إجمالي الطلبات',
                'الجداول المجدولة',
            ]);

            // CSV Data
            foreach ($providers as $provider) {
                fputcsv($file, [
                    $provider->provider_code,
                    $provider->name,
                    $provider->contact_person,
                    $provider->email,
                    $provider->phone,
                    $this->getServiceTypeLabel($provider->service_type),
                    $provider->rating,
                    $provider->hourly_rate,
                    $provider->is_active ? 'نشط' : 'غير نشط',
                    $provider->maintenance_requests_count,
                    $provider->schedules_count,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getServiceTypeLabel($type)
    {
        $labels = [
            'plumbing' => 'سباكة',
            'electrical' => 'كهرباء',
            'hvac' => 'تكييف',
            'structural' => 'إنشائي',
            'general' => 'عام',
            'all' => 'جميع الخدمات',
        ];

        return $labels[$type] ?? $type;
    }
}

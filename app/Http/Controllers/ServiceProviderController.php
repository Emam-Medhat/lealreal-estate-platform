<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiceProviderController extends Controller
{
    public function index()
    {
        $providers = ServiceProvider::with(['maintenanceRequests', 'schedules'])
            ->when(request('is_active'), function ($query, $isActive) {
                $query->where('is_active', $isActive);
            })
            ->when(request('service_type'), function ($query, $serviceType) {
                $query->where('service_type', $serviceType);
            })
            ->when(request('rating'), function ($query, $rating) {
                $query->where('rating', '>=', $rating);
            })
            ->latest()->paginate(15);

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

        $validated['provider_code'] = 'PROV-' . date('Y') . '-' . str_pad(ServiceProvider::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['specializations'] = json_encode($validated['specializations'] ?? []);
        $validated['is_active'] = $validated['is_active'] ?? true;

        DB::beginTransaction();
        try {
            $provider = ServiceProvider::create($validated);

            // Handle attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('provider_attachments', 'public');
                    // You might want to create an attachments table
                }
            }

            DB::commit();

            return redirect()->route('maintenance.providers.show', $provider)
                ->with('success', 'تم إنشاء مقدم الخدمة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إنشاء مقدم الخدمة');
        }
    }

    public function show(ServiceProvider $provider)
    {
        $provider->load([
            'maintenanceRequests' => function ($query) {
                $query->latest()->take(10);
            },
            'schedules' => function ($query) {
                $query->where('scheduled_date', '>=', now())->orderBy('scheduled_date')->take(10);
            }
        ]);

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

        $validated['specializations'] = json_encode($validated['specializations'] ?? []);
        $validated['is_active'] = $validated['is_active'] ?? $provider->is_active;

        $provider->update($validated);

        return redirect()->route('maintenance.providers.show', $provider)
            ->with('success', 'تم تحديث مقدم الخدمة بنجاح');
    }

    public function destroy(ServiceProvider $provider)
    {
        if ($provider->maintenanceRequests()->where('status', '!=', 'completed')->exists()) {
            return back()->with('error', 'لا يمكن حذف مقدم الخدمة الذي لديه طلبات صيانة نشطة');
        }

        $provider->delete();

        return redirect()->route('maintenance.providers.index')
            ->with('success', 'تم حذف مقدم الخدمة بنجاح');
    }

    public function toggleStatus(ServiceProvider $provider)
    {
        $provider->update(['is_active' => !$provider->is_active]);

        $status = $provider->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()->route('maintenance.providers.show', $provider)
            ->with('success', 'تم ' . $status . ' مقدم الخدمة بنجاح');
    }

    public function updateRating(ServiceProvider $provider, Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric|min:0|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $provider->update([
            'rating' => $validated['rating'],
            'last_review' => $validated['review'],
            'last_review_date' => now(),
        ]);

        return redirect()->route('maintenance.providers.show', $provider)
            ->with('success', 'تم تحديث تقييم مقدم الخدمة بنجاح');
    }

    public function getAvailableProviders(Request $request)
    {
        $serviceType = $request->service_type;
        $date = $request->date;
        $duration = $request->duration ?? 60;

        $providers = ServiceProvider::where('is_active', true)
            ->where(function ($query) use ($serviceType) {
                $query->where('service_type', $serviceType)
                    ->orWhere('service_type', 'all');
            })
            ->whereDoesntHave('schedules', function ($query) use ($date, $duration) {
                $query->where('status', 'in_progress')
                    ->where('scheduled_date', '<=', Carbon::parse($date)->addMinutes($duration))
                    ->where('scheduled_date', '>=', Carbon::parse($date)->subMinutes($duration));
            })
            ->withCount([
                'maintenanceRequests' => function ($query) {
                    $query->where('status', 'completed');
                }
            ])
            ->orderBy('rating', 'desc')
            ->get();

        return response()->json($providers);
    }

    public function performance(ServiceProvider $provider)
    {
        $stats = [
            'monthly_requests' => $provider->maintenanceRequests()
                ->whereMonth('created_at', now()->month)
                ->count(),
            'monthly_completed' => $provider->maintenanceRequests()
                ->where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->count(),
            'monthly_revenue' => $provider->maintenanceRequests()
                ->where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->sum('actual_cost'),
            'average_completion_time' => $provider->maintenanceRequests()
                ->where('status', 'completed')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, started_at, completed_at)) as avg_time')
                ->value('avg_time'),
            'completion_rate' => $provider->maintenanceRequests()
                ->where('status', 'completed')
                ->count() > 0 ?
                ($provider->maintenanceRequests()->where('status', 'completed')->count() /
                    $provider->maintenanceRequests()->count()) * 100 : 0,
        ];

        $monthlyData = $provider->maintenanceRequests()
            ->where('status', 'completed')
            ->selectRaw('MONTH(completed_at) as month, YEAR(completed_at) as year, COUNT(*) as count, SUM(actual_cost) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

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

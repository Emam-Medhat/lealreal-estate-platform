<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use App\Models\WarrantyClaim;
use App\Models\Property;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarrantyController extends Controller
{
    public function index()
    {
        $warranties = Warranty::with(['property', 'serviceProvider'])
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('property_id'), function ($query, $propertyId) {
                $query->where('property_id', $propertyId);
            })
            ->when(request('service_provider_id'), function ($query, $providerId) {
                $query->where('service_provider_id', $providerId);
            })
            ->when(request('expiry_status'), function ($query, $expiryStatus) {
                if ($expiryStatus === 'expired') {
                    $query->where('end_date', '<', now());
                } elseif ($expiryStatus === 'expiring_soon') {
                    $query->where('end_date', '>', now())
                        ->where('end_date', '<=', now()->addDays(30));
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('maintenance.warranties', compact('warranties'));
    }

    public function policiesIndex()
    {
        $warranties = Warranty::with(['property', 'serviceProvider'])
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('property_id'), function ($query, $propertyId) {
                $query->where('property_id', $propertyId);
            })
            ->when(request('service_provider_id'), function ($query, $providerId) {
                $query->where('service_provider_id', $providerId);
            })
            ->when(request('expiry_status'), function ($query, $expiryStatus) {
                if ($expiryStatus === 'expired') {
                    $query->where('end_date', '<', now());
                } elseif ($expiryStatus === 'expiring_soon') {
                    $query->where('end_date', '>', now())
                           ->where('end_date', '<=', now()->addDays(30));
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $properties = Property::all();
        $serviceProviders = ServiceProvider::all();

        return view('warranties.policies.index', compact('warranties', 'properties', 'serviceProviders'));
    }

    public function policiesCreate()
    {
        $properties = Property::all();
        $serviceProviders = ServiceProvider::all();

        return view('warranties.policies.create', compact('properties', 'serviceProviders'));
    }

    public function policiesStore(Request $request)
    {
        $validated = $request->validate([
            'warranty_number' => 'required|string|max:255|unique:warranties,warranty_number',
            'warranty_type' => 'required|in:product,labor,combined,extended',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'coverage_details' => 'required|string',
            'property_id' => 'required|exists:properties,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'duration_months' => 'required|integer|min:1',
            'coverage_amount' => 'required|numeric|min:0',
            'deductible_amount' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'notes' => 'nullable|string',
        ]);

        $validated['warranty_code'] = 'WAR-' . date('Y') . '-' . str_pad(Warranty::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'active';
        $validated['created_by'] = auth()->id();

        $warranty = Warranty::create($validated);

        return redirect()->route('warranties.policies.show', $warranty)
            ->with('success', 'تم إنشاء الضمان بنجاح');
    }

    public function policiesShow(Warranty $policy)
    {
        $policy->load(['property', 'serviceProvider', 'createdBy']);
        
        $stats = [
            'total_claims' => $policy->claims()->count(),
            'approved_claims' => $policy->claims()->where('status', 'approved')->count(),
            'rejected_claims' => $policy->claims()->where('status', 'rejected')->count(),
            'pending_claims' => $policy->claims()->where('status', 'pending')->count(),
            'total_claimed_amount' => $policy->claims()->where('status', 'approved')->sum('amount'),
            'remaining_coverage' => $policy->coverage_amount - $policy->claims()->where('status', 'approved')->sum('amount'),
            'days_remaining' => $policy->end_date->diffInDays(now()),
            'is_expired' => $policy->end_date < now(),
            'is_expiring_soon' => $policy->end_date <= now()->addDays(30) && $policy->end_date > now(),
        ];

        return view('warranties.policies.show', compact('policy', 'stats'));
    }

    public function policiesEdit(Warranty $policy)
    {
        if ($policy->status === 'expired') {
            return back()->with('error', 'لا يمكن تعديل الضمان المنتهي');
        }

        $properties = Property::all();
        $serviceProviders = ServiceProvider::all();

        return view('warranties.policies.edit', compact('policy', 'properties', 'serviceProviders'));
    }

    public function policiesUpdate(Request $request, Warranty $policy)
    {
        if ($policy->status === 'expired') {
            return back()->with('error', 'لا يمكن تعديل الضمان المنتهي');
        }

        $validated = $request->validate([
            'warranty_number' => 'required|string|max:255|unique:warranties,warranty_number,' . $policy->id,
            'warranty_type' => 'required|in:product,labor,combined,extended',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'coverage_details' => 'required|string',
            'property_id' => 'required|exists:properties,id',
            'service_provider_id' => 'nullable|exists:service_providers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'duration_months' => 'required|integer|min:1',
            'coverage_amount' => 'required|numeric|min:0',
            'deductible_amount' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'notes' => 'nullable|string',
        ]);

        $policy->update($validated);

        return redirect()->route('warranties.policies.show', $policy)
            ->with('success', 'تم تحديث الضمان بنجاح');
    }

    public function policiesDestroy(Warranty $policy)
    {
        if ($policy->claims()->where('status', 'pending')->exists()) {
            return back()->with('error', 'لا يمكن حذف الضمان الذي لديه مطالبات معلقة');
        }

        $policy->delete();

        return redirect()->route('warranties.policies.index')
            ->with('success', 'تم حذف الضمان بنجاح');
    }

    public function create()
    {
        $properties = Property::all();
        $serviceProviders = ServiceProvider::all();

        return view('maintenance.warranties-create', compact('properties', 'serviceProviders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'service_provider_id' => 'required|exists:service_providers,id',
            'warranty_number' => 'required|string|max:255',
            'warranty_type' => 'required|in:product,labor,combined,extended',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'coverage_details' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'duration_months' => 'required|integer|min:1',
            'coverage_amount' => 'required|numeric|min:0',
            'deductible_amount' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $validated['warranty_code'] = 'WAR-' . date('Y') . '-' . str_pad(Warranty::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'active';
        $validated['created_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $warranty = Warranty::create($validated);

            // Handle attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('warranty_attachments', 'public');
                    // You might want to create an attachments table
                }
            }

            DB::commit();

            return redirect()->route('maintenance.warranties.show', $warranty)
                ->with('success', 'تم إنشاء الضمان بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إنشاء الضمان');
        }
    }

    public function show(Warranty $warranty)
    {
        $warranty->load(['property', 'serviceProvider', 'claims']);

        $stats = [
            'total_claims' => $warranty->claims()->count(),
            'approved_claims' => $warranty->claims()->where('status', 'approved')->count(),
            'rejected_claims' => $warranty->claims()->where('status', 'rejected')->count(),
            'pending_claims' => $warranty->claims()->where('status', 'pending')->count(),
            'total_claimed_amount' => $warranty->claims()->where('status', 'approved')->sum('amount'),
            'remaining_coverage' => $warranty->coverage_amount - $warranty->claims()->where('status', 'approved')->sum('amount'),
            'days_remaining' => $warranty->end_date->diffInDays(now()),
            'is_expired' => $warranty->end_date < now(),
            'is_expiring_soon' => $warranty->end_date <= now()->addDays(30) && $warranty->end_date > now(),
        ];

        return view('maintenance.warranties-show', compact('warranty', 'stats'));
    }

    public function edit(Warranty $warranty)
    {
        if ($warranty->status === 'expired') {
            return back()->with('error', 'لا يمكن تعديل الضمان المنتهي');
        }

        $properties = Property::all();
        $serviceProviders = ServiceProvider::all();

        return view('maintenance.warranties-edit', compact('warranty', 'properties', 'serviceProviders'));
    }

    public function update(Request $request, Warranty $warranty)
    {
        if ($warranty->status === 'expired') {
            return back()->with('error', 'لا يمكن تعديل الضمان المنتهي');
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'service_provider_id' => 'required|exists:service_providers,id',
            'warranty_number' => 'required|string|max:255',
            'warranty_type' => 'required|in:product,labor,combined,extended',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'coverage_details' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'duration_months' => 'required|integer|min:1',
            'coverage_amount' => 'required|numeric|min:0',
            'deductible_amount' => 'nullable|numeric|min:0',
            'terms_conditions' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'notes' => 'nullable|string',
        ]);

        $warranty->update($validated);

        return redirect()->route('maintenance.warranties.show', $warranty)
            ->with('success', 'تم تحديث الضمان بنجاح');
    }

    public function destroy(Warranty $warranty)
    {
        if ($warranty->claims()->where('status', 'pending')->exists()) {
            return back()->with('error', 'لا يمكن حذف الضمان الذي لديه مطالبات معلقة');
        }

        $warranty->delete();

        return redirect()->route('maintenance.warranties.index')
            ->with('success', 'تم حذف الضمان بنجاح');
    }

    public function extend(Warranty $warranty, Request $request)
    {
        if ($warranty->status === 'expired') {
            return back()->with('error', 'لا يمكن تمديد الضمان المنتهي');
        }

        $validated = $request->validate([
            'extension_months' => 'required|integer|min:1|max:60',
            'extension_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $newEndDate = $warranty->end_date->addMonths($validated['extension_months']);
        $newDuration = $warranty->duration_months + $validated['extension_months'];

        $warranty->update([
            'end_date' => $newEndDate,
            'duration_months' => $newDuration,
            'extension_cost' => $validated['extension_cost'],
            'extended_at' => now(),
            'extended_by' => auth()->id(),
            'extension_notes' => $validated['notes'],
        ]);

        return redirect()->route('maintenance.warranties.show', $warranty)
            ->with('success', 'تم تمديد الضمان بنجاح');
    }

    public function expire(Warranty $warranty, Request $request)
    {
        if ($warranty->status === 'expired') {
            return back()->with('error', 'الضمان منتهي بالفعل');
        }

        $validated = $request->validate([
            'expiry_reason' => 'required|string|max:500',
        ]);

        $warranty->update([
            'status' => 'expired',
            'expired_at' => now(),
            'expiry_reason' => $validated['expiry_reason'],
            'expired_by' => auth()->id(),
        ]);

        return redirect()->route('maintenance.warranties.show', $warranty)
            ->with('success', 'تم إنهاء الضمان بنجاح');
    }

    public function reactivate(Warranty $warranty, Request $request)
    {
        if ($warranty->status !== 'expired') {
            return back()->with('error', 'يجب أن يكون الضمان منتهيًا لإعادة تفعيله');
        }

        $validated = $request->validate([
            'new_end_date' => 'required|date|after:today',
            'reactivation_reason' => 'required|string|max:500',
        ]);

        $warranty->update([
            'status' => 'active',
            'end_date' => $validated['new_end_date'],
            'reactivated_at' => now(),
            'reactivated_by' => auth()->id(),
        ]);

        return redirect()->route('maintenance.warranties.show', $warranty)
            ->with('success', 'تم إعادة تفعيل الضمان بنجاح');
    }

    public function createClaim(Warranty $warranty, Request $request)
    {
        if ($warranty->status !== 'active') {
            return back()->with('error', 'لا يمكن إنشاء مطالبة على ضمان غير نشط');
        }

        $validated = $request->validate([
            'claim_number' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'claim_date' => 'required|date',
            'incident_date' => 'required|date|before_or_equal:claim_date',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $claim = $warranty->claims()->create([
            'claim_number' => $validated['claim_number'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'claim_date' => $validated['claim_date'],
            'incident_date' => $validated['incident_date'],
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('maintenance.warranty-claims.show', $claim)
            ->with('success', 'تم إنشاء المطالبة بنجاح');
    }

    public function dashboard()
    {
        $stats = [
            'total_warranties' => Warranty::count(),
            'active_warranties' => Warranty::where('status', 'active')->count(),
            'expired_warranties' => Warranty::where('status', 'expired')->count(),
            'expiring_soon' => Warranty::where('end_date', '>', now())
                ->where('end_date', '<=', now()->addDays(30))->count(),
            'total_coverage' => Warranty::where('status', 'active')->sum('coverage_amount'),
            'total_claims' => \App\Models\WarrantyClaim::count(),
            'pending_claims' => \App\Models\WarrantyClaim::where('status', 'pending')->count(),
            'approved_claims' => \App\Models\WarrantyClaim::where('status', 'approved')->count(),
        ];

        $expiringSoon = Warranty::with(['property', 'serviceProvider'])
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->orderBy('end_date')
            ->take(5)
            ->get();

        $recentClaims = \App\Models\WarrantyClaim::with(['warranty.property'])
            ->latest()
            ->take(5)
            ->get();

        return view('maintenance.warranties-dashboard', compact('stats', 'expiringSoon', 'recentClaims'));
    }

    public function calendar()
    {
        $warranties = Warranty::with(['property', 'serviceProvider'])
            ->where('end_date', '>=', now()->subMonth())
            ->where('end_date', '<=', now()->addYear())
            ->get();

        return view('maintenance.warranties-calendar', compact('warranties'));
    }

    public function export(Request $request)
    {
        $warranties = Warranty::with(['property', 'serviceProvider'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->property_id, function ($query, $propertyId) {
                $query->where('property_id', $propertyId);
            })
            ->when($request->service_provider_id, function ($query, $providerId) {
                $query->where('service_provider_id', $providerId);
            })
            ->get();

        $filename = 'warranties_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($warranties) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'كود الضمان',
                'رقم الضمان',
                'العنوان',
                'العقار',
                'مقدم الخدمة',
                'نوع الضمان',
                'تاريخ البدء',
                'تاريخ الانتهاء',
                'المدة بالأشهر',
                'مبلغ التغطية',
                'الحالة',
            ]);

            // CSV Data
            foreach ($warranties as $warranty) {
                fputcsv($file, [
                    $warranty->warranty_code,
                    $warranty->warranty_number,
                    $warranty->title,
                    $warranty->property->title ?? '',
                    $warranty->serviceProvider->name ?? '',
                    $this->getWarrantyTypeLabel($warranty->warranty_type),
                    $warranty->start_date->format('Y-m-d'),
                    $warranty->end_date->format('Y-m-d'),
                    $warranty->duration_months,
                    $warranty->coverage_amount,
                    $this->getStatusLabel($warranty->status),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getWarrantyTypeLabel($type)
    {
        $labels = [
            'product' => 'منتج',
            'labor' => 'عمالة',
            'combined' => 'مجمع',
            'extended' => 'ممدد',
        ];

        return $labels[$type] ?? $type;
    }

    // Claims Methods
    public function claimsIndex()
    {
        $claims = WarrantyClaim::with(['warranty.property', 'warranty.serviceProvider', 'creator'])
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('warranty_id'), function ($query, $warrantyId) {
                $query->where('warranty_id', $warrantyId);
            })
            ->when(request('date_from'), function ($query, $dateFrom) {
                $query->whereDate('claim_date', '>=', $dateFrom);
            })
            ->when(request('date_to'), function ($query, $dateTo) {
                $query->whereDate('claim_date', '<=', $dateTo);
            })
            ->orderBy('claim_date', 'desc')
            ->paginate(10);

        return view('warranties.claims.index', compact('claims'));
    }

    public function claimsCreate()
    {
        $warranties = Warranty::where('status', 'active')->get();
        return view('warranties.claims.create', compact('warranties'));
    }

    public function claimsStore(Request $request)
    {
        $request->validate([
            'warranty_id' => 'required|exists:warranties,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'claim_date' => 'required|date',
            'incident_date' => 'nullable|date',
        ]);

        $claimNumber = 'CLM-' . date('Y') . '-' . str_pad(WarrantyClaim::count() + 1, 4, '0', STR_PAD_LEFT);

        WarrantyClaim::create([
            'warranty_id' => $request->warranty_id,
            'claim_number' => $claimNumber,
            'description' => $request->description,
            'amount' => $request->amount,
            'claim_date' => $request->claim_date,
            'incident_date' => $request->incident_date,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('warranties.claims.index')
            ->with('success', 'تم إنشاء المطالبة بنجاح');
    }

    public function claimsShow(WarrantyClaim $claim)
    {
        $claim->load(['warranty.property', 'warranty.serviceProvider', 'creator']);
        return view('warranties.claims.show', compact('claim'));
    }

    public function claimsEdit(WarrantyClaim $claim)
    {
        $warranties = Warranty::where('status', 'active')->get();
        return view('warranties.claims.edit', compact('claim', 'warranties'));
    }

    public function claimsUpdate(Request $request, WarrantyClaim $claim)
    {
        $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'claim_date' => 'required|date',
            'incident_date' => 'nullable|date',
            'status' => 'required|in:pending,approved,rejected,processing,completed',
            'resolution' => 'nullable|string',
        ]);

        $claim->update($request->all());

        return redirect()->route('warranties.claims.show', $claim)
            ->with('success', 'تم تحديث المطالبة بنجاح');
    }

    public function claimsDestroy(WarrantyClaim $claim)
    {
        $claim->delete();
        return redirect()->route('warranties.claims.index')
            ->with('success', 'تم حذف المطالبة بنجاح');
    }

    public function claimsApprove(WarrantyClaim $claim)
    {
        $claim->update([
            'status' => 'approved',
            'resolved_at' => now(),
        ]);

        return redirect()->route('warranties.claims.show', $claim)
            ->with('success', 'تم قبول المطالبة بنجاح');
    }

    public function claimsReject(WarrantyClaim $claim)
    {
        $claim->update([
            'status' => 'rejected',
            'resolved_at' => now(),
        ]);

        return redirect()->route('warranties.claims.show', $claim)
            ->with('success', 'تم رفض المطالبة');
    }

    public function claimsProcess(WarrantyClaim $claim)
    {
        $claim->update(['status' => 'processing']);

        return redirect()->route('warranties.claims.show', $claim)
            ->with('success', 'تم بدء معالجة المطالبة');
    }

    public function claimsComplete(WarrantyClaim $claim)
    {
        $claim->update([
            'status' => 'completed',
            'resolved_at' => now(),
        ]);

        return redirect()->route('warranties.claims.show', $claim)
            ->with('success', 'تم إكمال المطالبة بنجاح');
    }

    public function claimsAssign(Request $request, WarrantyClaim $claim)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $claim->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'processing',
        ]);

        return redirect()->route('warranties.claims.show', $claim)
            ->with('success', 'تم تعيين المطالبة بنجاح');
    }

    // Service Providers Methods
    public function providersIndex()
    {
        $providers = ServiceProvider::when(request('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('warranties.providers.index', compact('providers'));
    }

    public function providersCreate()
    {
        return view('warranties.providers.create');
    }

    public function providersStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:service_providers,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'services' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'license_number' => 'nullable|string|max:255',
        ]);

        ServiceProvider::create($request->all());

        return redirect()->route('warranties.providers.index')
            ->with('success', 'تم إضافة مقدم الخدمة بنجاح');
    }

    public function providersShow(ServiceProvider $provider)
    {
        $provider->load(['warranties' => function($query) {
            $query->with('property')->latest();
        }]);
        
        return view('warranties.providers.show', compact('provider'));
    }

    public function providersEdit(ServiceProvider $provider)
    {
        return view('warranties.providers.edit', compact('provider'));
    }

    public function providersUpdate(Request $request, ServiceProvider $provider)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:service_providers,email,' . $provider->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'services' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $provider->update($request->all());

        return redirect()->route('warranties.providers.show', $provider)
            ->with('success', 'تم تحديث مقدم الخدمة بنجاح');
    }

    public function providersDestroy(ServiceProvider $provider)
    {
        $provider->delete();
        return redirect()->route('warranties.providers.index')
            ->with('success', 'تم حذف مقدم الخدمة بنجاح');
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'active' => 'نشط',
            'expired' => 'منتهي',
            'suspended' => 'موقف',
        ];

        return $labels[$status] ?? $status;
    }
}

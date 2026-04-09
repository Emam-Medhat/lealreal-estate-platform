<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Lease;
use App\Models\RentPayment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class RentalController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_properties' => Property::where('is_rental', true)->count(),
            'occupied_properties' => Property::where('is_rental', true)->where('status', 'occupied')->count(),
            'vacant_properties' => Property::where('is_rental', true)->where('status', 'vacant')->count(),
            'total_tenants' => Tenant::count(),
            'active_leases' => Lease::where('status', 'active')->count(),
            'monthly_revenue' => RentPayment::whereMonth('payment_date', Carbon::now()->month)
                ->where('status', 'paid')->sum('amount'),
            'pending_payments' => RentPayment::where('status', 'pending')->count(),
            'overdue_payments' => RentPayment::where('status', 'overdue')->count(),
        ];

        $recentActivities = [];
        $upcomingLeaseExpirations = Lease::where('end_date', '<=', Carbon::now()->addDays(30))
            ->where('status', 'active')
            ->with(['tenant', 'property'])
            ->get();

        return view('rentals.dashboard', compact('stats', 'recentActivities', 'upcomingLeaseExpirations'));
    }

    public function index(Request $request)
    {
        $query = Property::where('is_rental', true);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        $properties = $query->with(['currentLease.tenant', 'currentLease'])->paginate(10);

        return view('rentals.index', compact('properties'));
    }

    public function properties()
    {
        $properties = Property::where('is_rental', true)
            ->with(['currentLease.tenant', 'maintenanceRequests'])
            ->paginate(15);

        return view('rentals.properties', compact('properties'));
    }

    public function createProperty()
    {
        return view('rentals.properties.create');
    }

    public function storeProperty(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'property_type' => 'required|string',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'area' => 'required|numeric|min:0',
            'rent_amount' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'available_date' => 'required|date',
            'amenities' => 'nullable|array',
            'photos' => 'nullable|array',
            'documents' => 'nullable|array',
        ]);

        $validated['is_rental'] = true;
        $validated['status'] = 'vacant';
        $validated['user_id'] = auth()->id();

        $property = Property::create($validated);

        return redirect()->route('rentals.properties.show', $property)
            ->with('success', 'تم إضافة العقار بنجاح');
    }

    public function showProperty(Property $property)
    {
        $property->load(['currentLease.tenant', 'maintenanceRequests', 'documents']);
        return view('rentals.properties.show', compact('property'));
    }

    public function editProperty(Property $property)
    {
        return view('rentals.properties.edit', compact('property'));
    }

    public function updateProperty(Request $request, Property $property)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'property_type' => 'required|string',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'area' => 'required|numeric|min:0',
            'rent_amount' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'available_date' => 'required|date',
            'amenities' => 'nullable|array',
            'photos' => 'nullable|array',
            'documents' => 'nullable|array',
        ]);

        $property->update($validated);

        return redirect()->route('rentals.properties.show', $property)
            ->with('success', 'تم تحديث العقار بنجاح');
    }

    public function destroyProperty(Property $property)
    {
        if ($property->currentLease) {
            return redirect()->back()->with('error', 'لا يمكن حذف عقار مستأجر');
        }

        $property->delete();

        return redirect()->route('rentals.properties')
            ->with('success', 'تم حذف العقار بنجاح');
    }

    public function toggleAvailability(Property $property)
    {
        $property->update([
            'is_available' => !$property->is_available
        ]);

        return response()->json([
            'success' => true,
            'is_available' => $property->is_available
        ]);
    }

    public function updateRent(Request $request, Property $property)
    {
        $validated = $request->validate([
            'rent_amount' => 'required|numeric|min:0',
            'effective_date' => 'required|date|after_or_equal:today',
        ]);

        // Create rent adjustment record
        $property->rentAdjustments()->create([
            'old_rent' => $property->rent_amount,
            'new_rent' => $validated['rent_amount'],
            'effective_date' => $validated['effective_date'],
            'reason' => $request->reason,
            'user_id' => auth()->id(),
        ]);

        $property->update(['rent_amount' => $validated['rent_amount']]);

        return redirect()->back()->with('success', 'تم تحديث الإيجار بنجاح');
    }

    public function propertyAnalytics(Property $property)
    {
        $analytics = [
            'occupancy_rate' => $this->calculateOccupancyRate($property),
            'total_revenue' => $this->calculateTotalRevenue($property),
            'maintenance_costs' => $this->calculateMaintenanceCosts($property),
            'rent_history' => $property->rentAdjustments()->orderBy('created_at', 'desc')->get(),
            'lease_history' => $property->leases()->with('tenant')->orderBy('created_at', 'desc')->get(),
        ];

        return view('rentals.properties.analytics', compact('property', 'analytics'));
    }

    public function exportProperties(Request $request)
    {
        $properties = Property::where('is_rental', true)
            ->with(['currentLease.tenant'])
            ->get();

        $csvData = [];
        $csvData[] = ['العنوان', 'النوع', 'الإيجار', 'الحالة', 'المستأجر', 'تاريخ البدء'];

        foreach ($properties as $property) {
            $csvData[] = [
                $property->title,
                $property->property_type,
                $property->rent_amount,
                $property->status,
                $property->currentLease?->tenant?->name ?? '-',
                $property->currentLease?->start_date ?? '-',
            ];
        }

        $filename = 'properties_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    public function reports()
    {
        return view('rentals.reports');
    }

    public function analytics()
    {
        $analytics = [
            'occupancy_trends' => $this->getOccupancyTrends(),
            'revenue_trends' => $this->getRevenueTrends(),
            'tenant_demographics' => $this->getTenantDemographics(),
            'property_performance' => $this->getPropertyPerformance(),
        ];

        return view('rentals.analytics', compact('analytics'));
    }

    public function performance()
    {
        $performance = [
            'top_performing_properties' => $this->getTopPerformingProperties(),
            'tenant_retention_rate' => $this->getTenantRetentionRate(),
            'average_rent_growth' => $this->getAverageRentGrowth(),
            'maintenance_response_time' => $this->getMaintenanceResponseTime(),
        ];

        return view('rentals.performance', compact('performance'));
    }

    public function occupancy()
    {
        $occupancyData = [
            'current_occupancy' => $this->getCurrentOccupancy(),
            'occupancy_by_property_type' => $this->getOccupancyByPropertyType(),
            'occupancy_by_location' => $this->getOccupancyByLocation(),
            'occupancy_forecast' => $this->getOccupancyForecast(),
        ];

        return view('rentals.occupancy', compact('occupancyData'));
    }

    public function revenue()
    {
        $revenueData = [
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'revenue_by_property' => $this->getRevenueByProperty(),
            'revenue_growth' => $this->getRevenueGrowth(),
            'revenue_forecast' => $this->getRevenueForecast(),
        ];

        return view('rentals.revenue', compact('revenueData'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $properties = Property::where('is_rental', true)
            ->where(function($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                  ->orWhere('address', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%');
            })
            ->with(['currentLease.tenant'])
            ->limit(10)
            ->get();

        return response()->json($properties);
    }

    public function dashboardStats(): JsonResponse
    {
        $stats = [
            'total_properties' => Property::where('is_rental', true)->count(),
            'occupied_properties' => Property::where('is_rental', true)->where('status', 'occupied')->count(),
            'vacant_properties' => Property::where('is_rental', true)->where('status', 'vacant')->count(),
            'total_tenants' => Tenant::count(),
            'active_leases' => Lease::where('status', 'active')->count(),
            'monthly_revenue' => RentPayment::whereMonth('payment_date', Carbon::now()->month)
                ->where('status', 'paid')->sum('amount'),
            'occupancy_rate' => $this->calculateOverallOccupancyRate(),
        ];

        return response()->json($stats);
    }

    public function calendarEvents(Request $request)
    {
        $events = [];
        
        // Lease expirations
        $leaseExpirations = Lease::whereBetween('end_date', [
            $request->start,
            $request->end
        ])->with(['tenant', 'property'])->get();

        foreach ($leaseExpirations as $lease) {
            $events[] = [
                'title' => 'انتهاء عقد: ' . $lease->property->title,
                'start' => $lease->end_date,
                'color' => '#ff6b6b',
                'url' => route('rentals.leases.show', $lease),
            ];
        }

        // Rent due dates
        $rentPayments = RentPayment::whereBetween('due_date', [
            $request->start,
            $request->end
        ])->with(['lease.tenant', 'lease.property'])->get();

        foreach ($rentPayments as $payment) {
            $events[] = [
                'title' => 'استحقاق إيجار: ' . $payment->lease->property->title,
                'start' => $payment->due_date,
                'color' => $payment->status === 'paid' ? '#51cf66' : '#ff6b6b',
                'url' => route('rentals.payments.show', $payment),
            ];
        }

        return response()->json($events);
    }

    public function settings()
    {
        return view('rentals.settings');
    }

    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'default_rent_period' => 'required|string',
            'late_fee_percentage' => 'required|numeric|min:0|max:100',
            'grace_period_days' => 'required|integer|min:0|max:30',
            'security_deposit_percentage' => 'required|numeric|min:0|max:100',
            'notification_settings' => 'nullable|array',
        ]);

        // Save settings to database or config
        // Implementation depends on your settings storage approach

        return redirect()->back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    // Helper methods
    private function calculateOccupancyRate(Property $property): float
    {
        $totalDays = Carbon::now()->daysInMonth;
        $occupiedDays = $property->leases()
            ->where('status', 'active')
            ->where(function($q) {
                $q->where('start_date', '<=', Carbon::now())
                  ->where('end_date', '>=', Carbon::now());
            })
            ->count();

        return $totalDays > 0 ? ($occupiedDays / $totalDays) * 100 : 0;
    }

    private function calculateTotalRevenue(Property $property): float
    {
        return $property->rentPayments()
            ->where('status', 'paid')
            ->sum('amount');
    }

    private function calculateMaintenanceCosts(Property $property): float
    {
        return $property->maintenanceRequests()
            ->where('status', 'completed')
            ->sum('cost');
    }

    private function getOccupancyTrends(): array
    {
        // Implementation for occupancy trends
        return [];
    }

    private function getRevenueTrends(): array
    {
        // Implementation for revenue trends
        return [];
    }

    private function getTenantDemographics(): array
    {
        // Implementation for tenant demographics
        return [];
    }

    private function getPropertyPerformance(): array
    {
        // Implementation for property performance
        return [];
    }

    private function getTopPerformingProperties(): array
    {
        // Implementation for top performing properties
        return [];
    }

    private function getTenantRetentionRate(): float
    {
        // Implementation for tenant retention rate
        return 0;
    }

    private function getAverageRentGrowth(): float
    {
        // Implementation for average rent growth
        return 0;
    }

    private function getMaintenanceResponseTime(): float
    {
        // Implementation for maintenance response time
        return 0;
    }

    private function getCurrentOccupancy(): array
    {
        // Implementation for current occupancy
        return [];
    }

    private function getOccupancyByPropertyType(): array
    {
        // Implementation for occupancy by property type
        return [];
    }

    private function getOccupancyByLocation(): array
    {
        // Implementation for occupancy by location
        return [];
    }

    private function getOccupancyForecast(): array
    {
        // Implementation for occupancy forecast
        return [];
    }

    private function getMonthlyRevenue(): array
    {
        // Implementation for monthly revenue
        return [];
    }

    private function getRevenueByProperty(): array
    {
        // Implementation for revenue by property
        return [];
    }

    private function getRevenueGrowth(): array
    {
        // Implementation for revenue growth
        return [];
    }

    private function getRevenueForecast(): array
    {
        // Implementation for revenue forecast
        return [];
    }

    private function calculateOverallOccupancyRate(): float
    {
        $totalProperties = Property::where('is_rental', true)->count();
        $occupiedProperties = Property::where('is_rental', true)->where('status', 'occupied')->count();
        
        return $totalProperties > 0 ? ($occupiedProperties / $totalProperties) * 100 : 0;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Lease;
use App\Models\RentAdjustment;
use Illuminate\Http\Request;

class RentAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = RentAdjustment::with(['property', 'lease', 'approvedBy', 'appliedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('rentals.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $properties = Property::all();
        $leases = Lease::where('status', 'active')->get();
        return view('rentals.adjustments.create', compact('properties', 'leases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'lease_id' => 'nullable|exists:leases,id',
            'old_rent' => 'required|numeric|min:0',
            'new_rent' => 'required|numeric|min:0',
            'adjustment_type' => 'required|in:increase,decrease',
            'effective_date' => 'required|date|after_or_equal:today',
            'reason' => 'required|in:market_rate,maintenance,improvements,inflation,negotiation,other',
            'description' => 'required|string|max:1000',
        ]);

        $adjustmentAmount = abs($request->new_rent - $request->old_rent);
        $adjustmentPercentage = $request->old_rent > 0 ? ($adjustmentAmount / $request->old_rent) * 100 : 0;

        $adjustment = RentAdjustment::create([
            'property_id' => $request->property_id,
            'lease_id' => $request->lease_id,
            'adjustment_number' => 'RADJ-' . date('Y') . '-' . str_pad(RentAdjustment::count() + 1, 6, '0', STR_PAD_LEFT),
            'old_rent' => $request->old_rent,
            'new_rent' => $request->new_rent,
            'adjustment_type' => $request->adjustment_type,
            'adjustment_amount' => $adjustmentAmount,
            'adjustment_percentage' => $adjustmentPercentage,
            'effective_date' => $request->effective_date,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('rentals.adjustments.show', $adjustment)
            ->with('success', 'تم إنشاء تعديل الإيجار بنجاح');
    }

    public function show(RentAdjustment $adjustment)
    {
        $adjustment->load(['property', 'lease', 'approvedBy', 'appliedBy', 'user']);
        return view('rentals.adjustments.show', compact('adjustment'));
    }

    public function edit(RentAdjustment $adjustment)
    {
        if ($adjustment->status !== 'pending') {
            return back()->with('error', 'لا يمكن تعديل تعديل إيجار غير معلق');
        }
        
        $properties = Property::all();
        $leases = Lease::where('status', 'active')->get();
        return view('rentals.adjustments.edit', compact('adjustment', 'properties', 'leases'));
    }

    public function update(Request $request, RentAdjustment $adjustment)
    {
        if ($adjustment->status !== 'pending') {
            return back()->with('error', 'لا يمكن تعديل تعديل إيجار غير معلق');
        }

        $request->validate([
            'old_rent' => 'required|numeric|min:0',
            'new_rent' => 'required|numeric|min:0',
            'effective_date' => 'required|date|after_or_equal:today',
            'reason' => 'required|in:market_rate,maintenance,improvements,inflation,negotiation,other',
            'description' => 'required|string|max:1000',
        ]);

        $adjustmentAmount = abs($request->new_rent - $request->old_rent);
        $adjustmentPercentage = $request->old_rent > 0 ? ($adjustmentAmount / $request->old_rent) * 100 : 0;

        $adjustment->update([
            'old_rent' => $request->old_rent,
            'new_rent' => $request->new_rent,
            'adjustment_amount' => $adjustmentAmount,
            'adjustment_percentage' => $adjustmentPercentage,
            'effective_date' => $request->effective_date,
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        return redirect()->route('rentals.adjustments.show', $adjustment)
            ->with('success', 'تم تحديث تعديل الإيجار بنجاح');
    }

    public function approve(RentAdjustment $adjustment)
    {
        if ($adjustment->status !== 'pending') {
            return back()->with('error', 'لا يمكن الموافقة على هذا التعديل');
        }

        $adjustment->approve();

        return redirect()->route('rentals.adjustments.show', $adjustment)
            ->with('success', 'تم الموافقة على تعديل الإيجار بنجاح');
    }

    public function apply(RentAdjustment $adjustment)
    {
        if (!$adjustment->canBeApplied()) {
            return back()->with('error', 'لا يمكن تطبيق هذا التعديل');
        }

        $adjustment->apply();

        return redirect()->route('rentals.adjustments.show', $adjustment)
            ->with('success', 'تم تطبيق تعديل الإيجار بنجاح');
    }

    public function reject(Request $request, RentAdjustment $adjustment)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($adjustment->status !== 'pending') {
            return back()->with('error', 'لا يمكن رفض هذا التعديل');
        }

        $adjustment->reject($request->reason);

        return redirect()->route('rentals.adjustments.show', $adjustment)
            ->with('success', 'تم رفض تعديل الإيجار بنجاح');
    }

    public function destroy(RentAdjustment $adjustment)
    {
        if (!in_array($adjustment->status, ['pending', 'rejected'])) {
            return back()->with('error', 'لا يمكن حذف تعديل الإيجار');
        }

        $adjustment->delete();

        return redirect()->route('rentals.adjustments.index')
            ->with('success', 'تم حذف تعديل الإيجار بنجاح');
    }

    public function export()
    {
        $adjustments = RentAdjustment::with(['property', 'lease', 'approvedBy', 'appliedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = ['رقم التعديل', 'العقار', 'العقد', 'نوع التعديل', 'الإيجار القديم', 'الإيجار الجديد', 'الفرق', 'النسبة المئوية', 'الحالة', 'تاريخ التطبيق'];

        foreach ($adjustments as $adjustment) {
            $csvData[] = [
                $adjustment->adjustment_number,
                $adjustment->property->title,
                $adjustment->lease?->lease_number,
                $adjustment->adjustment_type,
                $adjustment->old_rent,
                $adjustment->new_rent,
                $adjustment->adjustment_amount,
                $adjustment->adjustment_percentage . '%',
                $adjustment->status,
                $adjustment->effective_date->format('Y-m-d'),
            ];
        }

        $filename = 'rent_adjustments_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 200, $headers);
    }

    public function dashboard()
    {
        $pendingAdjustments = RentAdjustment::pending()->count();
        $approvedAdjustments = RentAdjustment::approved()->count();
        $appliedAdjustments = RentAdjustment::applied()->count();
        $rejectedAdjustments = RentAdjustment::where('status', 'rejected')->count();

        $increaseAdjustments = RentAdjustment::increase()->count();
        $decreaseAdjustments = RentAdjustment::decrease()->count();

        $upcomingAdjustments = RentAdjustment::with(['property', 'lease'])
            ->where('status', 'approved')
            ->where('effective_date', '>=', now())
            ->where('effective_date', '<=', now()->addDays(30))
            ->orderBy('effective_date')
            ->get();

        $recentAdjustments = RentAdjustment::with(['property', 'lease'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $adjustmentsByReason = RentAdjustment::selectRaw('reason, COUNT(*) as count')
            ->groupBy('reason')
            ->get();

        return view('rentals.adjustments.dashboard', compact(
            'pendingAdjustments',
            'approvedAdjustments',
            'appliedAdjustments',
            'rejectedAdjustments',
            'increaseAdjustments',
            'decreaseAdjustments',
            'upcomingAdjustments',
            'recentAdjustments',
            'adjustmentsByReason'
        ));
    }

    public function bulkApply(Request $request)
    {
        $request->validate([
            'adjustments' => 'required|array',
            'adjustments.*' => 'exists:rent_adjustments,id',
        ]);

        $appliedCount = 0;
        
        foreach ($request->adjustments as $adjustmentId) {
            $adjustment = RentAdjustment::find($adjustmentId);
            
            if ($adjustment && $adjustment->canBeApplied()) {
                $adjustment->apply();
                $appliedCount++;
            }
        }

        return back()->with('success', "تم تطبيق {$appliedCount} تعديل بنجاح");
    }

    public function analytics()
    {
        $monthlyTrends = $this->getMonthlyTrends();
        $adjustmentTypes = $this->getAdjustmentTypesDistribution();
        $reasonDistribution = $this->getReasonDistribution();
        $propertyImpact = $this->getPropertyImpact();

        return view('rentals.adjustments.analytics', compact(
            'monthlyTrends',
            'adjustmentTypes',
            'reasonDistribution',
            'propertyImpact'
        ));
    }

    private function getMonthlyTrends()
    {
        $trends = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $increaseCount = RentAdjustment::whereMonth('created_at', $month)
                ->where('adjustment_type', 'increase')
                ->count();
                
            $decreaseCount = RentAdjustment::whereMonth('created_at', $month)
                ->where('adjustment_type', 'decrease')
                ->count();
                
            $trends[] = [
                'month' => $month->format('M Y'),
                'increase' => $increaseCount,
                'decrease' => $decreaseCount,
                'total' => $increaseCount + $decreaseCount,
            ];
        }
        
        return $trends;
    }

    private function getAdjustmentTypesDistribution()
    {
        return RentAdjustment::selectRaw('adjustment_type, COUNT(*) as count, AVG(adjustment_percentage) as avg_percentage')
            ->groupBy('adjustment_type')
            ->get();
    }

    private function getReasonDistribution()
    {
        return RentAdjustment::selectRaw('reason, COUNT(*) as count')
            ->groupBy('reason')
            ->get();
    }

    private function getPropertyImpact()
    {
        return RentAdjustment::with(['property'])
            ->selectRaw('property_id, COUNT(*) as count, AVG(adjustment_percentage) as avg_percentage')
            ->groupBy('property_id')
            ->get();
    }
}

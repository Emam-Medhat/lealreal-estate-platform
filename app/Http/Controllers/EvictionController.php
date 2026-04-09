<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Eviction;
use Illuminate\Http\Request;

class EvictionController extends Controller
{
    public function index()
    {
        $evictions = Eviction::with(['lease', 'tenant', 'property'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('rentals.evictions.index', compact('evictions'));
    }

    public function create()
    {
        $leases = Lease::where('status', 'active')->get();
        return view('rentals.evictions.create', compact('leases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'reason' => 'required|in:non_payment,lease_violation,property_damage,illegal_activity,other',
            'description' => 'required|string|max:1000',
            'notice_date' => 'required|date|before_or_equal:today',
            'notice_type' => 'required|in:pay_or_quit,cure_or_quit,unconditional',
        ]);

        $lease = Lease::find($request->lease_id);
        
        $eviction = Eviction::create([
            'lease_id' => $request->lease_id,
            'tenant_id' => $lease->tenant_id,
            'property_id' => $lease->property_id,
            'eviction_number' => 'EVI-' . date('Y') . '-' . str_pad(Eviction::count() + 1, 6, '0', STR_PAD_LEFT),
            'reason' => $request->reason,
            'description' => $request->description,
            'notice_date' => $request->notice_date,
            'notice_type' => $request->notice_type,
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('rentals.evictions.show', $eviction)
            ->with('success', 'تم إنشاء طلب الإخلاء بنجاح');
    }

    public function show(Eviction $eviction)
    {
        $eviction->load(['lease', 'tenant', 'property']);
        return view('rentals.evictions.show', compact('eviction'));
    }

    public function edit(Eviction $eviction)
    {
        if (!in_array($eviction->status, ['pending', 'notice_served'])) {
            return back()->with('error', 'لا يمكن تعديل طلب الإخلاء');
        }
        
        return view('rentals.evictions.edit', compact('eviction'));
    }

    public function update(Request $request, Eviction $eviction)
    {
        if (!in_array($eviction->status, ['pending', 'notice_served'])) {
            return back()->with('error', 'لا يمكن تعديل طلب الإخلاء');
        }

        $request->validate([
            'description' => 'required|string|max:1000',
            'legal_fees' => 'nullable|numeric|min:0',
            'damages' => 'nullable|numeric|min:0',
        ]);

        $eviction->update($request->only(['description', 'legal_fees', 'damages']));

        return redirect()->route('rentals.evictions.show', $eviction)
            ->with('success', 'تم تحديث طلب الإخلاء بنجاح');
    }

    public function serveNotice(Request $request, Eviction $eviction)
    {
        if ($eviction->status !== 'pending') {
            return back()->with('error', 'لا يمكن تسليم الإشعار');
        }

        $request->validate([
            'notice_served_method' => 'required|in:personal,certified_mail,posted,email',
            'notice_served_date' => 'required|date|before_or_equal:today',
        ]);

        $eviction->update([
            'status' => 'notice_served',
            'notice_served' => true,
            'notice_served_date' => $request->notice_served_date,
            'notice_served_method' => $request->notice_served_method,
        ]);

        return redirect()->route('rentals.evictions.show', $eviction)
            ->with('success', 'تم تسليم الإشعار بنجاح');
    }

    public function fileWithCourt(Request $request, Eviction $eviction)
    {
        if ($eviction->status !== 'notice_served') {
            return back()->with('error', 'لا يمكن رفع القضية للمحكمة');
        }

        $request->validate([
            'court_filing_date' => 'required|date|before_or_equal:today',
            'court_order_number' => 'nullable|string',
        ]);

        $eviction->update([
            'status' => 'court_filed',
            'court_filing_date' => $request->court_filing_date,
            'court_order_number' => $request->court_order_number,
        ]);

        return redirect()->route('rentals.evictions.show', $eviction)
            ->with('success', 'تم رفع القضية للمحكمة بنجاح');
    }

    public function recordJudgment(Request $request, Eviction $eviction)
    {
        if ($eviction->status !== 'court_filed') {
            return back()->with('error', 'لا يمكن تسجيل الحكم');
        }

        $request->validate([
            'judgment_date' => 'required|date|before_or_equal:today',
        ]);

        $eviction->update([
            'status' => 'judgment',
            'judgment_date' => $request->judgment_date,
        ]);

        return redirect()->route('rentals.evictions.show', $eviction)
            ->with('success', 'تم تسجيل الحكم بنجاح');
    }

    public function issueWrit(Request $request, Eviction $eviction)
    {
        if ($eviction->status !== 'judgment') {
            return back()->with('error', 'لا يمكن إصدار أمر التنفيذ');
        }

        $request->validate([
            'writ_date' => 'required|date|before_or_equal:today',
        ]);

        $eviction->update([
            'status' => 'writ_issued',
            'writ_date' => $request->writ_date,
        ]);

        return redirect()->route('rentals.evictions.show', $eviction)
            ->with('success', 'تم إصدار أمر التنفيذ بنجاح');
    }

    public function scheduleSheriff(Request $request, Eviction $eviction)
    {
        if ($eviction->status !== 'writ_issued') {
            return back()->with('error', 'لا يمكن جدولة التنفيذ');
        }

        $request->validate([
            'sheriff_date' => 'required|date|after:today',
        ]);

        $eviction->update([
            'status' => 'sheriff_scheduled',
            'sheriff_date' => $request->sheriff_date,
        ]);

        return redirect()->route('rentals.evictions.show', $eviction)
            ->with('success', 'تم جدولة التنفيذ بنجاح');
    }

    public function completeEviction(Request $request, Eviction $eviction)
    {
        if ($eviction->status !== 'sheriff_scheduled') {
            return back()->with('error', 'لا يمكن إكمال الإخلاء');
        }

        $request->validate([
            'actual_move_out_date' => 'required|date|before_or_equal:today',
            'recovery_amount' => 'nullable|numeric|min:0',
        ]);

        $eviction->update([
            'status' => 'completed',
            'actual_move_out_date' => $request->actual_move_out_date,
            'recovery_amount' => $request->recovery_amount ?? 0,
        ]);

        // Update lease status
        if ($eviction->lease) {
            $eviction->lease->terminate('Eviction completed', 'Eviction #' . $eviction->eviction_number);
        }

        return redirect()->route('rentals.evictions.show', $eviction)
            ->with('success', 'تم إكمال الإخلاء بنجاح');
    }

    public function cancel(Request $request, Eviction $eviction)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        $eviction->update([
            'status' => 'cancelled',
            'notes' => ($eviction->notes ?? '') . "\n\nCancelled: " . $request->cancellation_reason . " (" . now()->toDateString() . ")",
        ]);

        return redirect()->route('rentals.evictions.show', $eviction)
            ->with('success', 'تم إلغاء طلب الإخلاء بنجاح');
    }

    public function destroy(Eviction $eviction)
    {
        if (!in_array($eviction->status, ['pending', 'cancelled'])) {
            return back()->with('error', 'لا يمكن حذف طلب الإخلاء');
        }

        $eviction->delete();

        return redirect()->route('rentals.evictions.index')
            ->with('success', 'تم حذف طلب الإخلاء بنجاح');
    }

    public function export()
    {
        $evictions = Eviction::with(['lease', 'tenant', 'property'])
            ->orderBy('created_at', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = ['رقم الإخلاء', 'العقد', 'المستأجر', 'العقار', 'السبب', 'الحالة', 'تاريخ الإشعار', 'تاريخ المحكمة'];

        foreach ($evictions as $eviction) {
            $csvData[] = [
                $eviction->eviction_number,
                $eviction->lease->lease_number,
                $eviction->tenant->name,
                $eviction->property->title,
                $eviction->reason,
                $eviction->status,
                $eviction->notice_date->format('Y-m-d'),
                $eviction->court_filing_date?->format('Y-m-d'),
            ];
        }

        $filename = 'evictions_' . date('Y-m-d') . '.csv';
        
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
        $pendingEvictions = Eviction::where('status', 'pending')->count();
        $noticeServed = Eviction::where('status', 'notice_served')->count();
        $courtFiled = Eviction::where('status', 'court_filed')->count();
        $completedEvictions = Eviction::where('status', 'completed')->count();

        $recentEvictions = Eviction::with(['lease', 'tenant'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $upcomingCourtDates = Eviction::with(['lease', 'tenant'])
            ->where('status', 'court_filed')
            ->where('court_date', '>=', now())
            ->where('court_date', '<=', now()->addDays(30))
            ->orderBy('court_date')
            ->get();

        return view('rentals.evictions.dashboard', compact(
            'pendingEvictions',
            'noticeServed',
            'courtFiled',
            'completedEvictions',
            'recentEvictions',
            'upcomingCourtDates'
        ));
    }
}

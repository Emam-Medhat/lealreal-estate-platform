<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Lease;
use App\Models\RentalInspection;
use Illuminate\Http\Request;

class RentalInspectionController extends Controller
{
    public function index()
    {
        $inspections = RentalInspection::with(['property', 'lease', 'tenant', 'inspector'])
            ->orderBy('scheduled_date', 'desc')
            ->paginate(15);
            
        return view('rentals.inspections.index', compact('inspections'));
    }

    public function create()
    {
        $properties = Property::all();
        $leases = Lease::where('status', 'active')->get();
        return view('rentals.inspections.create', compact('properties', 'leases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'lease_id' => 'nullable|exists:leases,id',
            'tenant_id' => 'nullable|exists:tenants,id',
            'inspection_type' => 'required|in:move_in,move_out,routine,emergency,pre_renewal',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'inspector_id' => 'nullable|exists:users,id',
        ]);

        $lease = Lease::find($request->lease_id);
        
        $inspection = RentalInspection::create([
            'property_id' => $request->property_id,
            'lease_id' => $request->lease_id,
            'tenant_id' => $lease?->tenant_id ?? $request->tenant_id,
            'inspection_number' => 'INS-' . date('Y') . '-' . str_pad(RentalInspection::count() + 1, 6, '0', STR_PAD_LEFT),
            'inspection_type' => $request->inspection_type,
            'scheduled_date' => $request->scheduled_date,
            'status' => 'scheduled',
            'inspector_id' => $request->inspector_id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('rentals.inspections.show', $inspection)
            ->with('success', 'تم إنشاء المعاينة بنجاح');
    }

    public function show(RentalInspection $inspection)
    {
        $inspection->load(['property', 'lease', 'tenant', 'inspector', 'user']);
        return view('rentals.inspections.show', compact('inspection'));
    }

    public function edit(RentalInspection $inspection)
    {
        if ($inspection->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل معاينة مكتملة');
        }
        
        $properties = Property::all();
        $leases = Lease::where('status', 'active')->get();
        return view('rentals.inspections.edit', compact('inspection', 'properties', 'leases'));
    }

    public function update(Request $request, RentalInspection $inspection)
    {
        if ($inspection->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل معاينة مكتملة');
        }

        $request->validate([
            'scheduled_date' => 'required|date|after_or_equal:today',
            'inspector_id' => 'nullable|exists:users,id',
        ]);

        $inspection->update($request->only(['scheduled_date', 'inspector_id']));

        return redirect()->route('rentals.inspections.show', $inspection)
            ->with('success', 'تم تحديث المعاينة بنجاح');
    }

    public function start(RentalInspection $inspection)
    {
        if ($inspection->status !== 'scheduled') {
            return back()->with('error', 'لا يمكن بدء هذه المعاينة');
        }

        $inspection->startInspection();

        return redirect()->route('rentals.inspections.show', $inspection)
            ->with('success', 'تم بدء المعاينة بنجاح');
    }

    public function complete(Request $request, RentalInspection $inspection)
    {
        if ($inspection->status !== 'in_progress') {
            return back()->with('error', 'لا يمكن إكمال هذه المعاينة');
        }

        $request->validate([
            'inspection_notes' => 'nullable|string',
            'checklist_items' => 'nullable|array',
            'overall_condition' => 'required|in:excellent,good,fair,poor,damaged',
            'estimated_damages' => 'nullable|numeric|min:0',
            'recommendations' => 'nullable|string',
            'tenant_comments' => 'nullable|string',
            'tenant_present' => 'boolean',
            'requires_follow_up' => 'boolean',
            'follow_up_date' => 'nullable|date|after:today',
            'follow_up_notes' => 'nullable|string',
        ]);

        $data = $request->only([
            'inspection_notes', 'checklist_items', 'overall_condition',
            'estimated_damages', 'recommendations', 'tenant_comments',
            'tenant_present', 'requires_follow_up', 'follow_up_date', 'follow_up_notes'
        ]);

        $inspection->completeInspection($data);

        return redirect()->route('rentals.inspections.show', $inspection)
            ->with('success', 'تم إكمال المعاينة بنجاح');
    }

    public function cancel(Request $request, RentalInspection $inspection)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($inspection->status === 'completed') {
            return back()->with('error', 'لا يمكن إلغاء معاينة مكتملة');
        }

        $inspection->cancel($request->reason);

        return redirect()->route('rentals.inspections.show', $inspection)
            ->with('success', 'تم إلغاء المعاينة بنجاح');
    }

    public function reschedule(Request $request, RentalInspection $inspection)
    {
        $request->validate([
            'new_date' => 'required|date|after:today',
        ]);

        if ($inspection->status === 'completed') {
            return back()->with('error', 'لا يمكن إعادة جدولة معاينة مكتملة');
        }

        $inspection->reschedule($request->new_date);

        return redirect()->route('rentals.inspections.show', $inspection)
            ->with('success', 'تم إعادة جدولة المعاينة بنجاح');
    }

    public function addPhoto(Request $request, RentalInspection $inspection)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($inspection->status === 'completed') {
            return back()->with('error', 'لا يمكن إضافة صور لمعاينة مكتملة');
        }

        $photoPath = $request->file('photo')->store('inspection-photos', 'public');
        $photoUrl = asset('storage/' . $photoPath);

        $inspection->addPhoto($photoUrl);

        return back()->with('success', 'تم إضافة الصورة بنجاح');
    }

    public function addVideo(Request $request, RentalInspection $inspection)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,avi,mov|max:51200',
        ]);

        if ($inspection->status === 'completed') {
            return back()->with('error', 'لا يمكن إضافة فيديو لمعاينة مكتملة');
        }

        $videoPath = $request->file('video')->store('inspection-videos', 'public');
        $videoUrl = asset('storage/' . $videoPath);

        $inspection->addVideo($videoUrl);

        return back()->with('success', 'تم إضافة الفيديو بنجاح');
    }

    public function addDocument(Request $request, RentalInspection $inspection)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'document_type' => 'required|string|max:255',
        ]);

        if ($inspection->status === 'completed') {
            return back()->with('error', 'لا يمكن إضافة وثيقة لمعاينة مكتملة');
        }

        $documentPath = $request->file('document')->store('inspection-documents', 'public');
        $documentUrl = asset('storage/' . $documentPath);

        $inspection->addDocument($documentUrl, $request->document_type);

        return back()->with('success', 'تم إضافة الوثيقة بنجاح');
    }

    public function updateChecklist(Request $request, RentalInspection $inspection)
    {
        $request->validate([
            'item_id' => 'required|string',
            'completed' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($inspection->status === 'completed') {
            return back()->with('error', 'لا يمكن تحديث قائمة التحقق لمعاينة مكتملة');
        }

        $inspection->updateChecklistItem($request->item_id, $request->completed, $request->notes);

        return back()->with('success', 'تم تحديث قائمة التحقق بنجاح');
    }

    public function destroy(RentalInspection $inspection)
    {
        if ($inspection->status === 'completed') {
            return back()->with('error', 'لا يمكن حذف معاينة مكتملة');
        }

        $inspection->delete();

        return redirect()->route('rentals.inspections.index')
            ->with('success', 'تم حذف المعاينة بنجاح');
    }

    public function export()
    {
        $inspections = RentalInspection::with(['property', 'lease', 'tenant', 'inspector'])
            ->orderBy('scheduled_date', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = ['رقم المعاينة', 'العقار', 'العقد', 'المستأجر', 'نوع المعاينة', 'الحالة', 'تاريخ الجدولة', 'تاريخ الإنجاز'];

        foreach ($inspections as $inspection) {
            $csvData[] = [
                $inspection->inspection_number,
                $inspection->property->title,
                $inspection->lease?->lease_number,
                $inspection->tenant?->name,
                $inspection->type_label,
                $inspection->status,
                $inspection->scheduled_date->format('Y-m-d'),
                $inspection->completed_date?->format('Y-m-d'),
            ];
        }

        $filename = 'rental_inspections_' . date('Y-m-d') . '.csv';
        
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
        $scheduledInspections = RentalInspection::scheduled()->count();
        $inProgressInspections = RentalInspection::inProgress()->count();
        $completedInspections = RentalInspection::completed()->count();
        $overdueInspections = RentalInspection::overdue()->count();

        $upcomingInspections = RentalInspection::with(['property', 'tenant'])
            ->upcoming()
            ->orderBy('scheduled_date')
            ->take(5)
            ->get();

        $recentInspections = RentalInspection::with(['property', 'tenant'])
            ->orderBy('completed_date', 'desc')
            ->take(5)
            ->get();

        $inspectionsByType = RentalInspection::selectRaw('inspection_type, COUNT(*) as count')
            ->groupBy('inspection_type')
            ->get();

        return view('rentals.inspections.dashboard', compact(
            'scheduledInspections',
            'inProgressInspections',
            'completedInspections',
            'overdueInspections',
            'upcomingInspections',
            'recentInspections',
            'inspectionsByType'
        ));
    }
}

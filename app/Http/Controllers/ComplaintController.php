<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Property;
use App\Models\Agent;
use App\Models\User;
use App\Http\Requests\SubmitComplaintRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ComplaintSubmitted;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with(['user', 'complaintable', 'assignedTo'])
            ->when(!Auth::user()->isAdmin(), function($query) {
                return $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('complaints.index', compact('complaints'));
    }

    public function create($type = null, $id = null)
    {
        $complaintable = null;
        
        if ($type && $id) {
            $complaintable = $this->getComplaintableModel($type, $id);
        }

        $complaintTypes = $this->getComplaintTypes();
        $urgencyLevels = $this->getUrgencyLevels();

        return view('complaints.create', compact('complaintable', 'type', 'complaintTypes', 'urgencyLevels'));
    }

    public function store(SubmitComplaintRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $complaintable = $this->getComplaintableModel($request->complaintable_type, $request->complaintable_id);
            
            $complaint = Complaint::create([
                'user_id' => Auth::id(),
                'complaintable_type' => get_class($complaintable),
                'complaintable_id' => $complaintable->id,
                'type' => $request->type,
                'title' => $request->title,
                'description' => $request->description,
                'urgency_level' => $request->urgency_level,
                'expected_resolution' => $request->expected_resolution,
                'contact_preference' => $request->contact_preference,
                'contact_details' => $request->contact_details,
                'status' => 'pending',
                'reference_number' => $this->generateReferenceNumber()
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $fileName = time() . '_' . $attachment->getClientOriginalName();
                    $filePath = $attachment->storeAs('complaints', $fileName, 'public');
                    
                    $complaint->attachments()->create([
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_size' => $attachment->getSize(),
                        'mime_type' => $attachment->getMimeType()
                    ]);
                }
            }

            // Auto-assign complaint if possible
            $this->autoAssignComplaint($complaint);

            // Send notification emails
            $this->sendNotifications($complaint);

            DB::commit();

            return redirect()->route('complaints.show', $complaint->id)
                ->with('success', 'تم تقديم الشكوى بنجاح. رقم المرجع: ' . $complaint->reference_number);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تقديم الشكوى: ' . $e->getMessage());
        }
    }

    public function show(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $complaint->load(['user', 'complaintable', 'assignedTo', 'attachments', 'responses.user']);

        return view('complaints.show', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,closed,rejected',
            'assigned_to' => 'nullable|exists:users,id',
            'internal_notes' => 'nullable|string|max:2000',
            'resolution_details' => 'nullable|string|max:2000'
        ]);

        $complaint->update([
            'status' => $request->status,
            'assigned_to' => $request->assigned_to,
            'internal_notes' => $request->internal_notes,
            'resolution_details' => $request->resolution_details
        ]);

        // Add status change timestamp
        if ($request->status === 'resolved') {
            $complaint->update(['resolved_at' => now()]);
        } elseif ($request->status === 'closed') {
            $complaint->update(['closed_at' => now()]);
        }

        // Notify complainant about status change
        $this->notifyStatusChange($complaint);

        return back()->with('success', 'تم تحديث الشكوى بنجاح');
    }

    public function addResponse(Request $request, Complaint $complaint)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|min:10',
            'is_internal' => 'boolean'
        ]);

        $complaint->responses()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'is_internal' => $request->has('is_internal')
        ]);

        // Update last activity
        $complaint->update(['last_activity_at' => now()]);

        // Notify complainant if response is public
        if (!$request->has('is_internal')) {
            $this->notifyResponseAdded($complaint);
        }

        return back()->with('success', 'تم إضافة الرد بنجاح');
    }

    public function escalate(Complaint $complaint)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $complaint->update([
            'status' => 'escalated',
            'escalated_at' => now()
        ]);

        // Notify senior staff
        $this->notifyEscalation($complaint);

        return back()->with('success', 'تم رفع الشكوى بنجاح');
    }

    public function close(Request $request, Complaint $complaint)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'resolution_summary' => 'required|string|min:20'
        ]);

        $complaint->update([
            'status' => 'closed',
            'closed_at' => now(),
            'resolution_details' => $request->resolution_summary
        ]);

        // Notify complainant
        $this->notifyComplaintClosed($complaint);

        return back()->with('success', 'تم إغلاق الشكوى بنجاح');
    }

    public function myComplaints()
    {
        $complaints = Complaint::where('user_id', Auth::id())
            ->with(['complaintable', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('complaints.my-complaints', compact('complaints'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $complaints = Complaint::with(['user', 'complaintable'])
            ->when(!Auth::user()->isAdmin(), function($query) {
                return $query->where('user_id', Auth::id());
            })
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('reference_number', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('complaints.search', compact('complaints', 'query'));
    }

    public function getStatistics()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $stats = [
            'total' => Complaint::count(),
            'pending' => Complaint::where('status', 'pending')->count(),
            'in_progress' => Complaint::where('status', 'in_progress')->count(),
            'resolved' => Complaint::where('status', 'resolved')->count(),
            'closed' => Complaint::where('status', 'closed')->count(),
            'escalated' => Complaint::where('status', 'escalated')->count(),
            'by_type' => Complaint::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get(),
            'by_urgency' => Complaint::selectRaw('urgency_level, count(*) as count')
                ->groupBy('urgency_level')
                ->get(),
            'resolution_time' => Complaint::whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
                ->first()
        ];

        return response()->json($stats);
    }

    private function getComplaintableModel($type, $id)
    {
        switch ($type) {
            case 'property':
                return Property::findOrFail($id);
            case 'agent':
                return Agent::findOrFail($id);
            case 'user':
                return User::findOrFail($id);
            default:
                return null;
        }
    }

    private function getComplaintTypes()
    {
        return [
            'service_quality' => 'جودة الخدمة',
            'property_issue' => 'مشكلة في العقار',
            'payment_dispute' => 'نزاع دفع',
            'communication' => 'مشكلة تواصل',
            'contract_violation' => 'انتهاك العقد',
            'safety_concern' => 'قضية أمان',
            'discrimination' => 'تمييز',
            'fraud' => 'احتيال',
            'other' => 'أخرى'
        ];
    }

    private function getUrgencyLevels()
    {
        return [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'critical' => 'حرج'
        ];
    }

    private function generateReferenceNumber()
    {
        return 'CMP-' . date('Y') . '-' . str_pad(Complaint::count() + 1, 6, '0', STR_PAD_LEFT);
    }

    private function autoAssignComplaint(Complaint $complaint)
    {
        // Auto-assign logic based on complaint type and urgency
        $assigneeId = null;

        if ($complaint->urgency_level === 'critical') {
            // Assign to senior staff
            $assigneeId = User::where('role', 'senior_admin')->first()?->id;
        } elseif ($complaint->type === 'property_issue') {
            // Assign to property team
            $assigneeId = User::where('role', 'property_manager')->first()?->id;
        }

        if ($assigneeId) {
            $complaint->update(['assigned_to' => $assigneeId]);
        }
    }

    private function sendNotifications(Complaint $complaint)
    {
        // Send email to complainant
        Mail::to($complaint->user->email)->send(new ComplaintSubmitted($complaint));

        // Send notification to assigned staff
        if ($complaint->assigned_to) {
            $complaint->assignedTo->notifications()->create([
                'type' => 'complaint_assigned',
                'title' => 'شكوى جديدة مخصصة لك',
                'message' => "تم تخصيص شكوى جديدة لك: {$complaint->title}",
                'data' => ['complaint_id' => $complaint->id]
            ]);
        }
    }

    private function notifyStatusChange(Complaint $complaint)
    {
        $complaint->user->notifications()->create([
            'type' => 'complaint_status_change',
            'title' => 'تحديث حالة الشكوى',
            'message' => "تم تحديث حالة شكواك إلى: {$complaint->status}",
            'data' => ['complaint_id' => $complaint->id]
        ]);
    }

    private function notifyResponseAdded(Complaint $complaint)
    {
        $complaint->user->notifications()->create([
            'type' => 'complaint_response',
            'title' => 'رد جديد على شكواك',
            'message' => 'تم إضافة رد جديد على شكواك',
            'data' => ['complaint_id' => $complaint->id]
        ]);
    }

    private function notifyEscalation(Complaint $complaint)
    {
        User::where('role', 'senior_admin')->get()->each(function($admin) use ($complaint) {
            $admin->notifications()->create([
                'type' => 'complaint_escalated',
                'title' => 'شكوى مرفوعة',
                'message' => "تم رفع شكوى: {$complaint->title}",
                'data' => ['complaint_id' => $complaint->id]
            ]);
        });
    }

    private function notifyComplaintClosed(Complaint $complaint)
    {
        $complaint->user->notifications()->create([
            'type' => 'complaint_closed',
            'title' => 'إغلاق الشكوى',
            'message' => 'تم إغلاق شكواك بنجاح',
            'data' => ['complaint_id' => $complaint->id]
        ]);
    }
}

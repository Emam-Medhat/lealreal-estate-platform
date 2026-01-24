<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceTicket;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceTicketController extends Controller
{
    public function index()
    {
        $tickets = MaintenanceTicket::with(['maintenanceRequest.property', 'assignedTo', 'createdBy'])
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('priority'), function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when(request('assigned_to'), function ($query, $assignedTo) {
                $query->where('assigned_to', $assignedTo);
            })
            ->latest()->paginate(15);

        return view('maintenance.tickets', compact('tickets'));
    }

    public function create()
    {
        $maintenanceRequests = MaintenanceRequest::where('status', '!=', 'completed')->get();
        $users = \App\Models\User::all();

        return view('maintenance.tickets-create', compact('maintenanceRequests', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'maintenance_request_id' => 'required|exists:maintenance_requests,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,emergency',
            'assigned_to' => 'nullable|exists:users,id',
            'category' => 'nullable|in:bug,feature,request,info,other',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $validated['ticket_number'] = 'TCK-' . date('Y') . '-' . str_pad(MaintenanceTicket::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'open';
        $validated['created_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $ticket = MaintenanceTicket::create($validated);

            // Log the ticket creation
            MaintenanceLog::create([
                'maintenance_request_id' => $ticket->maintenance_request_id,
                'action' => 'ticket_created',
                'description' => 'تم إنشاء تذكرة: ' . $ticket->ticket_number,
                'new_values' => json_encode($validated),
                'user_id' => auth()->id(),
            ]);

            // Handle attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('ticket_attachments', 'public');
                    // You might want to create an attachments table
                }
            }

            DB::commit();

            return redirect()->route('maintenance.tickets.show', $ticket)
                ->with('success', 'تم إنشاء التذكرة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إنشاء التذكرة');
        }
    }

    public function show(MaintenanceTicket $ticket)
    {
        $ticket->load(['maintenanceRequest.property', 'assignedTo', 'createdBy', 'replies']);

        return view('maintenance.tickets-show', compact('ticket'));
    }

    public function edit(MaintenanceTicket $ticket)
    {
        if ($ticket->status === 'closed') {
            return back()->with('error', 'لا يمكن تعديل التذكرة المغلقة');
        }

        $maintenanceRequests = MaintenanceRequest::where('status', '!=', 'completed')->get();
        $users = \App\Models\User::all();

        return view('maintenance.tickets-edit', compact('ticket', 'maintenanceRequests', 'users'));
    }

    public function update(Request $request, MaintenanceTicket $ticket)
    {
        if ($ticket->status === 'closed') {
            return back()->with('error', 'لا يمكن تعديل التذكرة المغلقة');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,emergency',
            'assigned_to' => 'nullable|exists:users,id',
            'category' => 'nullable|in:bug,feature,request,info,other',
        ]);

        $ticket->update($validated);

        return redirect()->route('maintenance.tickets.show', $ticket)
            ->with('success', 'تم تحديث التذكرة بنجاح');
    }

    public function destroy(MaintenanceTicket $ticket)
    {
        if ($ticket->status !== 'open') {
            return back()->with('error', 'لا يمكن حذف التذكرة التي تم بدء العمل عليها');
        }

        DB::beginTransaction();
        try {
            // Log the deletion
            MaintenanceLog::create([
                'maintenance_request_id' => $ticket->maintenance_request_id,
                'action' => 'ticket_deleted',
                'description' => 'تم حذف التذكرة: ' . $ticket->ticket_number,
                'old_values' => json_encode($ticket->toArray()),
                'user_id' => auth()->id(),
            ]);

            $ticket->delete();
            DB::commit();

            return redirect()->route('maintenance.tickets.index')
                ->with('success', 'تم حذف التذكرة بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء حذف التذكرة');
        }
    }

    public function assign(MaintenanceTicket $ticket, Request $request)
    {
        if ($ticket->status === 'closed') {
            return back()->with('error', 'لا يمكن تكليف التذكرة المغلقة');
        }

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $ticket->update([
            'assigned_to' => $validated['assigned_to'],
            'status' => 'assigned',
            'assigned_at' => now(),
            'assigned_by' => auth()->id(),
        ]);

        // Log the assignment
        MaintenanceLog::create([
            'maintenance_request_id' => $ticket->maintenance_request_id,
            'action' => 'ticket_assigned',
            'description' => 'تم تكليف التذكرة: ' . $ticket->ticket_number,
            'new_values' => json_encode($validated),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('maintenance.tickets.show', $ticket)
            ->with('success', 'تم تكليف التذكرة بنجاح');
    }

    public function start(MaintenanceTicket $ticket)
    {
        if ($ticket->status !== 'assigned') {
            return back()->with('error', 'يجب تكليف التذكرة أولاً');
        }

        $ticket->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Log the start
        MaintenanceLog::create([
            'maintenance_request_id' => $ticket->maintenance_request_id,
            'action' => 'ticket_started',
            'description' => 'تم بدء العمل على التذكرة: ' . $ticket->ticket_number,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('maintenance.tickets.show', $ticket)
            ->with('success', 'تم بدء العمل على التذكرة');
    }

    public function close(MaintenanceTicket $ticket, Request $request)
    {
        if ($ticket->status !== 'in_progress') {
            return back()->with('error', 'يجب أن تكون التذكرة قيد التنفيذ');
        }

        $validated = $request->validate([
            'resolution' => 'required|string',
            'satisfaction_rating' => 'nullable|integer|min:1|max:5',
            'feedback' => 'nullable|string',
        ]);

        $ticket->update([
            'status' => 'closed',
            'resolution' => $validated['resolution'],
            'satisfaction_rating' => $validated['satisfaction_rating'],
            'feedback' => $validated['feedback'],
            'closed_at' => now(),
            'closed_by' => auth()->id(),
        ]);

        // Log the closure
        MaintenanceLog::create([
            'maintenance_request_id' => $ticket->maintenance_request_id,
            'action' => 'ticket_closed',
            'description' => 'تم إغلاق التذكرة: ' . $ticket->ticket_number,
            'new_values' => json_encode($validated),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('maintenance.tickets.show', $ticket)
            ->with('success', 'تم إغلاق التذكرة بنجاح');
    }

    public function reopen(MaintenanceTicket $ticket, Request $request)
    {
        if ($ticket->status !== 'closed') {
            return back()->with('error', 'يجب أن تكون التذكرة مغلقة لإعادة فتحها');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $ticket->update([
            'status' => 'reopened',
            'reopened_at' => now(),
            'reopened_by' => auth()->id(),
            'reopened_reason' => $validated['reason'],
        ]);

        // Log the reopening
        MaintenanceLog::create([
            'maintenance_request_id' => $ticket->maintenance_request_id,
            'action' => 'ticket_reopened',
            'description' => 'تم إعادة فتح التذكرة: ' . $ticket->ticket_number . ' - ' . $validated['reason'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('maintenance.tickets.show', $ticket)
            ->with('success', 'تم إعادة فتح التذكرة بنجاح');
    }

    public function addReply(MaintenanceTicket $ticket, Request $request)
    {
        if ($ticket->status === 'closed') {
            return back()->with('error', 'لا يمكن إضافة رد على التذكرة المغلقة');
        }

        $validated = $request->validate([
            'message' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $reply = $ticket->replies()->create([
            'message' => $validated['message'],
            'user_id' => auth()->id(),
        ]);

        // Handle attachments if any
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket_replies', 'public');
                // You might want to create an attachments table
            }
        }

        // Log the reply
        MaintenanceLog::create([
            'maintenance_request_id' => $ticket->maintenance_request_id,
            'action' => 'ticket_reply',
            'description' => 'تم إضافة رد على التذكرة: ' . $ticket->ticket_number,
            'new_values' => json_encode($validated),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('maintenance.tickets.show', $ticket)
            ->with('success', 'تم إضافة الرد بنجاح');
    }

    public function export(Request $request)
    {
        $tickets = MaintenanceTicket::with(['maintenanceRequest.property', 'assignedTo', 'createdBy'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($request->date_from, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->get();

        $filename = 'maintenance_tickets_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($tickets) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'رقم التذكرة',
                'الموضوع',
                'العقار',
                'الأولوية',
                'الحالة',
                'المكلف له',
                'المنشئ',
                'التاريخ',
            ]);

            // CSV Data
            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->ticket_number,
                    $ticket->subject,
                    $ticket->maintenanceRequest->property->title ?? '',
                    $this->getPriorityLabel($ticket->priority),
                    $this->getStatusLabel($ticket->status),
                    $ticket->assignedTo->name ?? '',
                    $ticket->createdBy->name ?? '',
                    $ticket->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getPriorityLabel($priority)
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'emergency' => 'طوارئ',
        ];

        return $labels[$priority] ?? $priority;
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'open' => 'مفتوحة',
            'assigned' => 'مكلفة',
            'in_progress' => 'قيد التنفيذ',
            'closed' => 'مغلقة',
            'reopened' => 'مفتوحة مرة أخرى',
        ];

        return $labels[$status] ?? $status;
    }
}

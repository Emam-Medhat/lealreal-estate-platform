<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\MaintenanceRequest;
use App\Models\Inventory;
use App\Models\MaintenanceTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkOrderController extends Controller
{
    public function index()
    {
        $orders = WorkOrder::with(['maintenanceRequest.property', 'assignedTo', 'createdBy'])
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('priority'), function($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when(request('assigned_to'), function($query, $assignedTo) {
                $query->where('assigned_to', $assignedTo);
            })
            ->latest()->paginate(15);

        return view('maintenance.work-orders', compact('orders'));
    }

    public function create()
    {
        $maintenanceRequests = MaintenanceRequest::where('status', 'assigned')->get();
        $users = \App\Models\User::all();
        $inventoryItems = Inventory::where('quantity', '>', 0)->get();

        return view('maintenance.work-orders-create', compact('maintenanceRequests', 'users', 'inventoryItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'maintenance_request_id' => 'required|exists:maintenance_requests,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,emergency',
            'assigned_to' => 'required|exists:users,id',
            'estimated_duration' => 'required|integer|min:30|max:480',
            'estimated_cost' => 'required|numeric|min:0',
            'scheduled_date' => 'nullable|date|after:today',
            'items' => 'nullable|array',
            'items.*.inventory_id' => 'required|exists:inventory,id',
            'items.*.quantity' => 'required|integer|min:1',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $validated['order_number'] = 'WO-' . date('Y') . '-' . str_pad(WorkOrder::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['status'] = 'pending';
        $validated['created_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $order = WorkOrder::create($validated);

            // Handle inventory items
            if (isset($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    $inventoryItem = Inventory::find($item['inventory_id']);
                    
                    if ($inventoryItem->quantity < $item['quantity']) {
                        throw new \Exception('الكمية المطلوبة غير متوفرة في المخزون');
                    }

                    // Reserve inventory
                    $inventoryItem->decrement('quantity', $item['quantity']);
                    
                    // Create work order item
                    $order->items()->create([
                        'inventory_id' => $item['inventory_id'],
                        'quantity' => $item['quantity'],
                        'unit_cost' => $inventoryItem->unit_cost,
                        'total_cost' => $item['quantity'] * $inventoryItem->unit_cost,
                    ]);
                }
            }

            // Handle attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('work_order_attachments', 'public');
                    // You might want to create an attachments table
                }
            }

            DB::commit();

            return redirect()->route('maintenance.work-orders.show', $order)
                ->with('success', 'تم إنشاء أمر العمل بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إنشاء أمر العمل: ' . $e->getMessage());
        }
    }

    public function show(WorkOrder $order)
    {
        $order->load([
            'maintenanceRequest.property', 
            'assignedTo', 
            'createdBy', 
            'items.inventory',
            'timeLogs'
        ]);
        
        return view('maintenance.work-orders-show', compact('order'));
    }

    public function edit(WorkOrder $order)
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل أمر العمل المكتمل');
        }

        $maintenanceRequests = MaintenanceRequest::where('status', 'assigned')->get();
        $users = \App\Models\User::all();
        $inventoryItems = Inventory::where('quantity', '>', 0)->get();

        return view('maintenance.work-orders-edit', compact('order', 'maintenanceRequests', 'users', 'inventoryItems'));
    }

    public function update(Request $request, WorkOrder $order)
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل أمر العمل المكتمل');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,emergency',
            'assigned_to' => 'required|exists:users,id',
            'estimated_duration' => 'required|integer|min:30|max:480',
            'estimated_cost' => 'required|numeric|min:0',
            'scheduled_date' => 'nullable|date|after:today',
        ]);

        $order->update($validated);

        return redirect()->route('maintenance.work-orders.show', $order)
            ->with('success', 'تم تحديث أمر العمل بنجاح');
    }

    public function destroy(WorkOrder $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'لا يمكن حذف أمر العمل الذي تم بدء العمل عليه');
        }

        DB::beginTransaction();
        try {
            // Return inventory items to stock
            foreach ($order->items as $item) {
                $inventoryItem = Inventory::find($item->inventory_id);
                $inventoryItem->increment('quantity', $item->quantity);
            }

            $order->delete();
            DB::commit();

            return redirect()->route('maintenance.work-orders.index')
                ->with('success', 'تم حذف أمر العمل بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء حذف أمر العمل');
        }
    }

    public function start(WorkOrder $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'يجب أن يكون أمر العمل في انتظار التنفيذ');
        }

        $order->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->route('maintenance.work-orders.show', $order)
            ->with('success', 'تم بدء العمل على أمر العمل');
    }

    public function pause(WorkOrder $order, Request $request)
    {
        if ($order->status !== 'in_progress') {
            return back()->with('error', 'يجب أن يكون أمر العمل قيد التنفيذ');
        }

        $validated = $request->validate([
            'pause_reason' => 'required|string|max:500',
        ]);

        $order->update([
            'status' => 'paused',
            'paused_at' => now(),
            'pause_reason' => $validated['pause_reason'],
        ]);

        return redirect()->route('maintenance.work-orders.show', $order)
            ->with('success', 'تم إيقاف أمر العمل مؤقتاً');
    }

    public function resume(WorkOrder $order)
    {
        if ($order->status !== 'paused') {
            return back()->with('error', 'يجب أن يكون أمر العمل موقوفاً');
        }

        $order->update([
            'status' => 'in_progress',
            'resumed_at' => now(),
        ]);

        return redirect()->route('maintenance.work-orders.show', $order)
            ->with('success', 'تم استئناف العمل على أمر العمل');
    }

    public function complete(WorkOrder $order, Request $request)
    {
        if ($order->status !== 'in_progress') {
            return back()->with('error', 'يجب أن يكون أمر العمل قيد التنفيذ');
        }

        $validated = $request->validate([
            'actual_duration' => 'required|integer|min:1',
            'actual_cost' => 'required|numeric|min:0',
            'completion_notes' => 'required|string',
            'items_used' => 'nullable|array',
            'items_used.*.inventory_id' => 'required|exists:inventory,id',
            'items_used.*.quantity' => 'required|integer|min:1',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Handle additional items used
            if (isset($validated['items_used'])) {
                foreach ($validated['items_used'] as $item) {
                    $inventoryItem = Inventory::find($item['inventory_id']);
                    
                    if ($inventoryItem->quantity < $item['quantity']) {
                        throw new \Exception('الكمية المطلوبة غير متوفرة في المخزون');
                    }

                    // Remove from inventory
                    $inventoryItem->decrement('quantity', $item['quantity']);
                    
                    // Create work order item
                    $order->items()->create([
                        'inventory_id' => $item['inventory_id'],
                        'quantity' => $item['quantity'],
                        'unit_cost' => $inventoryItem->unit_cost,
                        'total_cost' => $item['quantity'] * $inventoryItem->unit_cost,
                        'used_at_completion' => true,
                    ]);
                }
            }

            $order->update([
                'status' => 'completed',
                'actual_duration' => $validated['actual_duration'],
                'actual_cost' => $validated['actual_cost'],
                'completion_notes' => $validated['completion_notes'],
                'completed_at' => now(),
                'completed_by' => auth()->id(),
            ]);

            // Handle attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('work_order_completion', 'public');
                    // You might want to create an attachments table
                }
            }

            DB::commit();

            return redirect()->route('maintenance.work-orders.show', $order)
                ->with('success', 'تم إكمال أمر العمل بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إكمال أمر العمل: ' . $e->getMessage());
        }
    }

    public function cancel(WorkOrder $order, Request $request)
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'لا يمكن إلغاء أمر العمل المكتمل');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Return inventory items to stock
            foreach ($order->items as $item) {
                $inventoryItem = Inventory::find($item->inventory_id);
                $inventoryItem->increment('quantity', $item->quantity);
            }

            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('maintenance.work-orders.show', $order)
                ->with('success', 'تم إلغاء أمر العمل بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إلغاء أمر العمل');
        }
    }

    public function addTimeLog(WorkOrder $order, Request $request)
    {
        if ($order->status !== 'in_progress') {
            return back()->with('error', 'يجب أن يكون أمر العمل قيد التنفيذ');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:500',
            'duration' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $order->timeLogs()->create([
            'description' => $validated['description'],
            'duration' => $validated['duration'],
            'notes' => $validated['notes'],
            'user_id' => auth()->id(),
            'log_time' => now(),
        ]);

        return redirect()->route('maintenance.work-orders.show', $order)
            ->with('success', 'تم إضافة سجل الوقت بنجاح');
    }

    public function export(Request $request)
    {
        $orders = WorkOrder::with(['maintenanceRequest.property', 'assignedTo', 'createdBy'])
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($request->date_from, function($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->get();

        $filename = 'work_orders_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'رقم الأمر',
                'العنوان',
                'العقار',
                'الأولوية',
                'الحالة',
                'المكلف له',
                'المنشئ',
                'التكلفة التقديرية',
                'التكلفة الفعلية',
                'المدة التقديرية',
                'المدة الفعلية',
                'التاريخ',
            ]);

            // CSV Data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->title,
                    $order->maintenanceRequest->property->title ?? '',
                    $this->getPriorityLabel($order->priority),
                    $this->getStatusLabel($order->status),
                    $order->assignedTo->name ?? '',
                    $order->createdBy->name ?? '',
                    $order->estimated_cost,
                    $order->actual_cost,
                    $order->estimated_duration,
                    $order->actual_duration,
                    $order->created_at->format('Y-m-d H:i'),
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
            'pending' => 'في انتظار',
            'in_progress' => 'قيد التنفيذ',
            'paused' => 'موقوف',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        return $labels[$status] ?? $status;
    }
}

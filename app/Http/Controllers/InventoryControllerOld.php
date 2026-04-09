<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\MaintenanceRequest;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function index()
    {
        $items = Inventory::with(['category', 'supplier'])
            ->when(request('category_id'), function($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('stock_level'), function($query, $stockLevel) {
                if ($stockLevel === 'low') {
                    $query->whereRaw('quantity <= reorder_level');
                } elseif ($stockLevel === 'out') {
                    $query->where('quantity', 0);
                }
            })
            ->latest()->paginate(15);

        $categories = \App\Models\InventoryCategory::all();
        
        return view('maintenance.inventory', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = \App\Models\InventoryCategory::all();
        $suppliers = \App\Models\InventorySupplier::all();
        
        return view('maintenance.inventory-create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:100|unique:inventories,sku',
            'category_id' => 'nullable|exists:inventory_categories,id',
            'supplier_id' => 'nullable|exists:inventory_suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:reorder_level',
            'unit_of_measure' => 'required|string|max:50',
            'status' => 'required|in:active,inactive,discontinued',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        
        // Set default status based on quantity if not provided
        if ($validated['quantity'] > 0) {
            $validated['status'] = 'available';
        } else {
            $validated['status'] = 'out_of_stock';
        }

        DB::beginTransaction();
        try {
            $item = Inventory::create($validated);

            DB::commit();
            
            return redirect()->route('inventory.index')
                ->with('success', 'Inventory item created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error for debugging
            \Log::error('Inventory creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create inventory item. Please check all fields and try again. Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $item = Inventory::with(['category', 'supplier', 'transactions' => function($query) {
            $query->latest()->take(10);
        }])->findOrFail($id);
        
        $stats = [
            'total_transactions' => $item->transactions()->count(),
            'in_transactions' => $item->transactions()->where('type', 'in')->sum('quantity'),
            'out_transactions' => $item->transactions()->where('type', 'out')->sum('quantity'),
            'low_stock_warning' => $item->quantity <= $item->reorder_level,
            'out_of_stock' => $item->quantity == 0,
        ];

        return view('maintenance.inventory-show', compact('item', 'stats'));
    }

    public function edit($id)
    {
        $item = Inventory::findOrFail($id);
        $categories = \App\Models\InventoryCategory::all();
        $suppliers = \App\Models\InventorySupplier::all();
        
        return view('maintenance.inventory-edit', compact('item', 'categories', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $item = Inventory::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:100|unique:inventories,sku,' . $id,
            'category_id' => 'nullable|exists:inventory_categories,id',
            'supplier_id' => 'nullable|exists:inventory_suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:reorder_level',
            'unit_of_measure' => 'required|string|max:50',
            'status' => 'required|in:active,inactive,discontinued',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $item->update($validated);
        
        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item updated successfully!');
    }

    public function destroy($id)
    {
        $item = Inventory::findOrFail($id);
        $item->delete();
        
        return redirect()->route('inventory.index')
            ->with('success', 'Inventory item deleted successfully!');
    }

    public function addStock(Inventory $item, Request $request)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $oldQuantity = $item->quantity;
        $newQuantity = $oldQuantity + $validated['quantity'];

        DB::beginTransaction();
        try {
            // Update inventory
            $item->update([
                'quantity' => $newQuantity,
                'unit_cost' => $validated['unit_cost'],
                'total_value' => $newQuantity * $validated['unit_cost'],
                'status' => 'available',
                'last_stock_in' => now(),
            ]);

            // Create transaction record
            $item->transactions()->create([
                'type' => 'in',
                'quantity' => $validated['quantity'],
                'unit_cost' => $validated['unit_cost'],
                'reference' => $validated['reference'],
                'notes' => $validated['notes'],
                'user_id' => auth()->id(),
                'transaction_date' => now(),
            ]);

            DB::commit();

            return redirect()->route('maintenance.inventory.show', $item)
                ->with('success', 'تم إضافة الكمية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء إضافة الكمية');
        }
    }

    public function removeStock(Inventory $item, Request $request)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $item->quantity,
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'maintenance_request_id' => 'nullable|exists:maintenance_requests,id',
            'work_order_id' => 'nullable|exists:work_orders,id',
        ]);

        $oldQuantity = $item->quantity;
        $newQuantity = $oldQuantity - $validated['quantity'];

        DB::beginTransaction();
        try {
            // Update inventory
            $item->update([
                'quantity' => $newQuantity,
                'total_value' => $newQuantity * $item->unit_cost,
                'status' => $newQuantity == 0 ? 'out_of_stock' : 
                           ($newQuantity <= $item->reorder_level ? 'low_stock' : 'available'),
                'last_stock_out' => now(),
            ]);

            // Create transaction record
            $item->transactions()->create([
                'type' => 'out',
                'quantity' => $validated['quantity'],
                'unit_cost' => $item->unit_cost,
                'reference' => $validated['reference'],
                'notes' => $validated['notes'],
                'maintenance_request_id' => $validated['maintenance_request_id'],
                'work_order_id' => $validated['work_order_id'],
                'user_id' => auth()->id(),
                'transaction_date' => now(),
            ]);

            DB::commit();

            return redirect()->route('maintenance.inventory.show', $item)
                ->with('success', 'تم سحب الكمية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء سحب الكمية');
        }
    }

    public function adjustStock(Inventory $item, Request $request)
    {
        $validated = $request->validate([
            'new_quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string',
        ]);

        $oldQuantity = $item->quantity;
        $newQuantity = $validated['new_quantity'];
        $difference = $newQuantity - $oldQuantity;

        if ($difference == 0) {
            return back()->with('error', 'لا يوجد فرق في الكمية');
        }

        DB::beginTransaction();
        try {
            // Update inventory
            $item->update([
                'quantity' => $newQuantity,
                'total_value' => $newQuantity * $item->unit_cost,
                'status' => $newQuantity == 0 ? 'out_of_stock' : 
                           ($newQuantity <= $item->reorder_level ? 'low_stock' : 'available'),
            ]);

            // Create adjustment transaction
            $item->transactions()->create([
                'type' => 'adjustment',
                'quantity' => abs($difference),
                'unit_cost' => $item->unit_cost,
                'reference' => 'Stock Adjustment',
                'notes' => $validated['reason'] . ' - ' . ($validated['notes'] ?? ''),
                'user_id' => auth()->id(),
                'transaction_date' => now(),
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
            ]);

            DB::commit();

            return redirect()->route('maintenance.inventory.show', $item)
                ->with('success', 'تم تعديل الكمية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطأ أثناء تعديل الكمية');
        }
    }

    public function lowStockAlerts()
    {
        $items = Inventory::with(['category', 'supplier'])
            ->whereRaw('quantity <= reorder_level')
            ->where('quantity', '>', 0)
            ->orderByRaw('(reorder_level - quantity) DESC')
            ->get();

        return view('maintenance.inventory-alerts', compact('items'));
    }

    public function outOfStock()
    {
        $items = Inventory::with(['category', 'supplier'])
            ->where('quantity', 0)
            ->latest()->get();

        return view('maintenance.inventory-out', compact('items'));
    }

    public function transactions(Inventory $item)
    {
        $transactions = $item->transactions()
            ->with(['user', 'maintenanceRequest', 'workOrder'])
            ->latest()
            ->paginate(20);

        return view('maintenance.inventory-transactions', compact('item', 'transactions'));
    }

    public function export(Request $request)
    {
        $items = Inventory::with(['category', 'supplier'])
            ->when($request->category_id, function($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->get();

        $filename = 'inventory_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($items) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'الكود',
                'الاسم',
                'SKU',
                'الفئة',
                'المورد',
                'الوحدة',
                'الكمية',
                'مستوى إعادة الطلب',
                'سعر الوحدة',
                'القيمة الإجمالية',
                'الحالة',
                'الموقع',
            ]);

            // CSV Data
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->item_code,
                    $item->name,
                    $item->sku,
                    $item->category->name ?? '',
                    $item->supplier->name ?? '',
                    $item->unit,
                    $item->quantity,
                    $item->reorder_level,
                    $item->unit_cost,
                    $item->total_value,
                    $this->getStatusLabel($item->status),
                    $item->location,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'available' => 'متاح',
            'low_stock' => 'مخزون منخفض',
            'out_of_stock' => 'نفد المخزون',
            'discontinued' => 'متوقف',
        ];

        return $labels[$status] ?? $status;
    }
}

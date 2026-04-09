<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\InventorySupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    public function index()
    {
        $items = Inventory::query()
            ->when(request('search'), function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('sku', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when(request('category'), function($query, $category) {
                $query->where('category', $category);
            })
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('stock_level'), function($query, $stockLevel) {
                if ($stockLevel === 'low') {
                    $query->whereRaw('quantity <= reorder_point AND quantity > 0');
                } elseif ($stockLevel === 'out') {
                    $query->where('quantity', 0);
                } elseif ($stockLevel === 'available') {
                    $query->where('quantity', '>', 0);
                }
            })
            ->latest()->paginate(15);

        $categories = InventoryCategory::all();
        
        return view('maintenance.inventory', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = InventoryCategory::all();
        $suppliers = InventorySupplier::all();
        
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
            'max_stock' => 'nullable|integer|min:0',
            'unit_of_measure' => 'required|string|max:50',
            'status' => 'required|in:active,inactive,discontinued',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        
        // Set default status based on quantity if not provided
        if ($validated['quantity'] > 0) {
            $validated['status'] = 'active';
        } else {
            $validated['status'] = 'inactive';
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
            Log::error('Inventory creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create inventory item. Please check all fields and try again. Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $item = Inventory::findOrFail($id);
        return view('maintenance.inventory-show', compact('item'));
    }

    public function edit($id)
    {
        $item = Inventory::findOrFail($id);
        $categories = InventoryCategory::all();
        $suppliers = InventorySupplier::all();
        
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
            'max_stock' => 'nullable|integer|min:0',
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

    // Stock Movements
    public function movementsIndex()
    {
        $movements = DB::table('inventory_movements')
            ->join('inventory_items', 'inventory_movements.inventory_id', '=', 'inventory_items.id')
            ->select('inventory_movements.*', 'inventory_items.name as item_name', 'inventory_items.sku')
            ->orderBy('inventory_movements.created_at', 'desc')
            ->get()
            ->map(function ($movement) {
                $movement->created_at = \Carbon\Carbon::parse($movement->created_at);
                return $movement;
            });
            
        return view('inventory.movements.index', compact('movements'));
    }

    public function movementsCreate()
    {
        $items = Inventory::all();
        return view('inventory.movements.create', compact('items'));
    }

    public function movementsStore(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventory_items,id',
            'type' => 'required|in:in,out,transfer',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'location_from' => 'nullable|string|max:255',
            'location_to' => 'nullable|string|max:255',
            'unit_cost' => 'nullable|numeric|min:0',
            'total_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        DB::table('inventory_movements')->insert([
            'inventory_id' => $validated['inventory_id'],
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'],
            'reference' => $validated['reference'] ?? null,
            'location_from' => $validated['location_from'] ?? null,
            'location_to' => $validated['location_to'] ?? null,
            'unit_cost' => $validated['unit_cost'] ?? null,
            'total_cost' => $validated['total_cost'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('inventory.movements.index')
            ->with('success', 'Stock movement recorded successfully!');
    }

    public function movementsShow($id)
    {
        $movement = DB::table('inventory_movements')
            ->join('inventory_items', 'inventory_movements.inventory_id', '=', 'inventory_items.id')
            ->select('inventory_movements.*', 'inventory_items.name as item_name', 'inventory_items.sku')
            ->where('inventory_movements.id', $id)
            ->first();
            
        if ($movement) {
            $movement->created_at = \Carbon\Carbon::parse($movement->created_at);
        }
            
        return view('inventory.movements.show', compact('movement'));
    }

    // Reports
    public function reports()
    {
        return view('inventory.reports.index');
    }

    public function stockLevelsReport()
    {
        $items = Inventory::with(['category'])
            ->get()
            ->groupBy(function($item) {
                if ($item->quantity <= $item->min_stock_level) {
                    return 'Low Stock';
                } elseif ($item->quantity >= $item->max_stock_level) {
                    return 'Overstock';
                } else {
                    return 'Optimal';
                }
            });
            
        return view('inventory.reports.stock-levels', compact('items'));
    }

    public function movementsReport()
    {
        $movements = DB::table('inventory_movements')
            ->join('inventory', 'inventory_movements.inventory_id', '=', 'inventory.id')
            ->select('inventory_movements.*', 'inventory.name as item_name')
            ->orderBy('inventory_movements.created_at', 'desc')
            ->get();
            
        return view('inventory.reports.movements', compact('movements'));
    }

    public function valuationReport()
    {
        $items = Inventory::with(['category'])
            ->get();
            
        $totalValue = $items->sum(function($item) {
            return $item->quantity * $item->unit_price;
        });
        
        return view('inventory.reports.valuation', compact('items', 'totalValue'));
    }

    public function export()
    {
        $items = Inventory::all();
        
        $filename = 'inventory_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];
        
        $callback = function() use ($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'SKU', 'Category', 'Supplier', 'Quantity', 'Unit Price', 'Total Value']);
            
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->name,
                    $item->sku,
                    $item->category->name ?? 'N/A',
                    $item->supplier->name ?? 'N/A',
                    $item->quantity,
                    $item->unit_price,
                    $item->quantity * $item->unit_price
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    // Items Management
    public function itemsIndex()
    {
        $items = Inventory::query()
            ->when(request('search'), function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('sku', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->paginate(20);
            
        return view('inventory.items.index', compact('items'));
    }

    public function itemsCreate()
    {
        $categories = InventoryCategory::all();
        $suppliers = InventorySupplier::all();
        return view('inventory.items.create', compact('categories', 'suppliers'));
    }

    public function itemsStore(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|unique:inventory_items,item_code',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'category' => 'required|in:tools,materials,equipment,supplies,safety,other',
            'status' => 'required|in:active,inactive,discontinued,out_of_stock',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'sku' => 'nullable|string|unique:inventory_items,sku',
            'unit' => 'required|string|max:50',
            'unit_ar' => 'nullable|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'min_quantity' => 'nullable|integer|min:0',
            'max_quantity' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:0',
            'supplier' => 'nullable|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'location_ar' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'qr_code' => 'nullable|string|max:255',
            'warranty_expiry' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'requires_maintenance' => 'nullable|boolean',
            'maintenance_instructions' => 'nullable|string',
            'maintenance_instructions_ar' => 'nullable|string',
            'safety_notes' => 'nullable|string',
            'safety_notes_ar' => 'nullable|string',
            'notes' => 'nullable|string',
            'notes_ar' => 'nullable|string',
        ]);

        // Add the created_by field from the authenticated user
        $validated['created_by'] = auth()->id();

        Inventory::create($validated);

        return redirect()->route('inventory.items.index')
            ->with('success', 'Item created successfully!');
    }

    public function itemsShow($item)
    {
        $item = Inventory::findOrFail($item);
        return view('inventory.items.show', compact('item'));
    }

    public function itemsEdit($item)
    {
        $item = Inventory::findOrFail($item);
        $categories = InventoryCategory::all();
        $suppliers = InventorySupplier::all();
        return view('inventory.items.edit', compact('item', 'categories', 'suppliers'));
    }

    public function itemsUpdate(Request $request, $item)
    {
        $item = Inventory::findOrFail($item);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:inventory,sku,' . $item->id,
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:inventory_categories,id',
            'supplier_id' => 'nullable|exists:inventory_suppliers,id',
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'max_stock_level' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,discontinued'
        ]);

        $item->update($validated);

        return redirect()->route('inventory.items.index')
            ->with('success', 'Item updated successfully!');
    }

    public function itemsDestroy($item)
    {
        $item = Inventory::findOrFail($item);
        $item->delete();
        
        return redirect()->route('inventory.items.index')
            ->with('success', 'Item deleted successfully!');
    }

    public function adjustStock(Request $request, $item)
    {
        $item = Inventory::findOrFail($item);
        
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'reason' => 'required|string|max:255',
            'type' => 'required|in:in,out,adjustment'
        ]);

        DB::table('inventory_movements')->insert([
            'inventory_id' => $item->id,
            'type' => $validated['type'],
            'quantity' => abs($validated['quantity']),
            'reason' => $validated['reason'],
            'user_id' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($validated['type'] == 'out') {
            $item->quantity -= $validated['quantity'];
        } else {
            $item->quantity += $validated['quantity'];
        }
        $item->save();

        return back()->with('success', 'Stock adjusted successfully!');
    }

    public function reorderItemForm($item)
    {
        $item = Inventory::findOrFail($item);
        return view('inventory.items.reorder', compact('item'));
    }

    public function reorderItem(Request $request, $item)
    {
        $item = Inventory::findOrFail($item);
        // Logic for reordering item
        return back()->with('success', 'Reorder request sent!');
    }

    public function toggleItemStatus(Request $request, $item)
    {
        $item = Inventory::findOrFail($item);
        $item->status = $item->status == 'active' ? 'inactive' : 'active';
        $item->save();
        
        return back()->with('success', 'Item status updated!');
    }

    // Categories Management
    public function categoriesIndex()
    {
        $categories = InventoryCategory::paginate(20);
        return view('inventory.categories.index', compact('categories'));
    }

    public function categoriesCreate()
    {
        return view('inventory.categories.create');
    }

    public function categoriesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_categories,name',
            'description' => 'nullable|string'
        ]);

        InventoryCategory::create($validated);

        return redirect()->route('inventory.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function categoriesShow($category)
    {
        $category = InventoryCategory::with(['items'])->findOrFail($category);
        return view('inventory.categories.show', compact('category'));
    }

    public function categoriesEdit($category)
    {
        $category = InventoryCategory::findOrFail($category);
        return view('inventory.categories.edit', compact('category'));
    }

    public function categoriesUpdate(Request $request, $category)
    {
        $category = InventoryCategory::findOrFail($category);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        $category->update($validated);

        return redirect()->route('inventory.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function categoriesDestroy($category)
    {
        $category = InventoryCategory::findOrFail($category);
        $category->delete();
        
        return redirect()->route('inventory.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    // Suppliers Management
    public function suppliersIndex()
    {
        $suppliers = InventorySupplier::paginate(20);
        return view('inventory.suppliers.index', compact('suppliers'));
    }

    public function suppliersCreate()
    {
        return view('inventory.suppliers.create');
    }

    public function suppliersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_suppliers,name',
            'email' => 'nullable|email|unique:inventory_suppliers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        InventorySupplier::create($validated);

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'Supplier created successfully!');
    }

    public function suppliersShow($supplier)
    {
        $supplier = InventorySupplier::findOrFail($supplier);
        return view('inventory.suppliers.show', compact('supplier'));
    }

    public function suppliersEdit($supplier)
    {
        $supplier = InventorySupplier::findOrFail($supplier);
        return view('inventory.suppliers.edit', compact('supplier'));
    }

    public function suppliersUpdate(Request $request, $supplier)
    {
        $supplier = InventorySupplier::findOrFail($supplier);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:inventory_suppliers,name,' . $supplier->id,
            'email' => 'nullable|email|unique:inventory_suppliers,email,' . $supplier->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        $supplier->update($validated);

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'Supplier updated successfully!');
    }

    public function suppliersDestroy($supplier)
    {
        $supplier = InventorySupplier::findOrFail($supplier);
        $supplier->delete();
        
        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'Supplier deleted successfully!');
    }

    // Low Stock Alerts
    public function lowStock()
    {
        $items = Inventory::query()
            ->whereRaw('quantity <= reorder_point')
            ->get();
            
        return view('inventory.low-stock', compact('items'));
    }

    public function sendLowStockAlerts()
    {
        $items = Inventory::whereRaw('quantity <= min_stock_level')->get();
        
        // Logic to send alerts
        foreach ($items as $item) {
            // Send notification logic here
        }
        
        return back()->with('success', 'Low stock alerts sent successfully!');
    }
}

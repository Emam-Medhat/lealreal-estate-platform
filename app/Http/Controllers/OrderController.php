<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Metaverse\MetaverseProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = Order::with('items')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Ensure user can only see their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.itemable']);

        return view('orders.show', compact('order'));
    }

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => auth()->id(),
                'total_amount' => 0, // Will be calculated below
                'currency' => 'USD',
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $request->input('notes'),
            ]);

            // Create order items from cart
            $cart = $request->input('cart', []);
            $totalAmount = 0;

            foreach ($cart as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'itemable_type' => $item['itemable_type'],
                    'itemable_id' => $item['itemable_id'],
                    'item_name' => $item['item_name'],
                    'item_description' => $item['item_description'] ?? null,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['total'],
                    'item_data' => $item['item_data'] ?? [],
                ]);

                $totalAmount += $item['total'];

                // Update property status if it's a metaverse property
                if ($item['itemable_type'] === 'App\\Models\\Metaverse\\MetaverseProperty') {
                    $property = MetaverseProperty::find($item['itemable_id']);
                    if ($property) {
                        // You might want to mark property as sold or update its status
                        // $property->update(['status' => 'sold']);
                    }
                }
            }

            // Update order total
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'message' => 'تم إنشاء الطلب بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الطلب: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        // Ensure user can only update their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'payment_method' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $order->update($validated);

        if ($validated['payment_status'] === 'paid') {
            $order->markAsPaid();
        }

        return redirect()->route('orders.show', $order)->with('success', 'تم تحديث حالة الدفع بنجاح');
    }

    public function showPaymentStatus(Order $order)
    {
        // Ensure user can only see their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.payment-status', compact('order'));
    }

    public function cancel(Order $order)
    {
        // Ensure user can only cancel their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status === 'pending') {
            $order->markAsCancelled();
            return redirect()->back()->with('success', 'تم إلغاء الطلب بنجاح');
        }

        return redirect()->back()->with('error', 'لا يمكن إلغاء هذا الطلب');
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}

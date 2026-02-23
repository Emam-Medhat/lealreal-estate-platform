<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * Display the cart page
     */
    public function index()
    {
        $cart = session('cart', []);
        $cartItems = [];
        
        if (!empty($cart)) {
            // Get NFT details for cart items
            foreach ($cart as $id => $item) {
                if ($item['type'] === 'nft') {
                    // You can fetch NFT details from database here
                    $cartItems[] = [
                        'id' => $id,
                        'type' => 'nft',
                        'name' => 'NFT #' . $id,
                        'price' => $item['price'] ?? 0,
                        'image' => $item['image'] ?? '/placeholder-nft.jpg'
                    ];
                }
            }
        }
        
        return view('cart.index', compact('cartItems'));
    }
    
    /**
     * Add item to cart
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'nft_id' => 'required|integer',
            'type' => 'required|string'
        ]);
        
        $cart = session('cart', []);
        $nftId = $request->nft_id;
        
        // Check if item already exists in cart
        if (!isset($cart[$nftId])) {
            $cart[$nftId] = [
                'type' => $request->type,
                'price' => $request->price ?? 0,
                'added_at' => now()
            ];
            
            session(['cart' => $cart]);
            
            return response()->json([
                'success' => true,
                'message' => 'تمت إضافة العنصر إلى السلة بنجاح',
                'cart_count' => count($cart)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'العنصر موجود بالفعل في السلة'
        ]);
    }
    
    /**
     * Remove item from cart
     */
    public function remove($id): JsonResponse
    {
        $cart = session('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session(['cart' => $cart]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم حذف العنصر من السلة',
                'cart_count' => count($cart)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'العنصر غير موجود في السلة'
        ]);
    }
    
    /**
     * Clear cart
     */
    public function clear(): JsonResponse
    {
        session()->forget('cart');
        
        return response()->json([
            'success' => true,
            'message' => 'تم تفريغ السلة',
            'cart_count' => 0
        ]);
    }
    
    /**
     * Get cart count
     */
    public function count(): JsonResponse
    {
        $cart = session('cart', []);
        
        return response()->json([
            'count' => count($cart)
        ]);
    }
}

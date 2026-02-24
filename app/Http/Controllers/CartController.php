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
        // Try Laravel session first, fallback to regular session
        $cart = session('cart', []);
        if (empty($cart)) {
            $cart = $_SESSION['cart'] ?? [];
        }
        
        $cartItems = [];
        
        if (!empty($cart)) {
            // Get NFT details for cart items
            foreach ($cart as $id => $item) {
                if ($item['type'] === 'nft') {
                    // Use sample NFT data for demonstration
                    $sampleNfts = [
                        1 => [
                            'name' => 'Cosmic Ape #2341',
                            'price' => 2.5,
                            'image' => 'https://picsum.photos/seed/nft1/400/400.jpg',
                            'type' => 'Digital Art'
                        ],
                        2 => [
                            'name' => 'Pixel Punk #8888',
                            'price' => 1.8,
                            'image' => 'https://picsum.photos/seed/nft2/400/400.jpg',
                            'type' => 'Collectible'
                        ],
                        3 => [
                            'name' => 'Meta Dragon #4567',
                            'price' => 3.2,
                            'image' => 'https://picsum.photos/seed/nft3/400/400.jpg',
                            'type' => 'Gaming'
                        ],
                        4 => [
                            'name' => 'Crypto Kitty #9999',
                            'price' => 1.5,
                            'image' => 'https://picsum.photos/seed/nft4/400/400.jpg',
                            'type' => 'Pet'
                        ],
                        5 => [
                            'name' => 'Virtual Land #1234',
                            'price' => 5.0,
                            'image' => 'https://picsum.photos/seed/nft5/400/400.jpg',
                            'type' => 'Real Estate'
                        ]
                    ];
                    
                    $nftData = $sampleNfts[$id] ?? [
                        'name' => 'NFT #' . $id,
                        'price' => $item['price'] ?? 1.0,
                        'image' => 'https://picsum.photos/seed/nft' . $id . '/400/400.jpg',
                        'type' => 'Digital Asset'
                    ];
                    
                    $cartItems[] = [
                        'id' => $id,
                        'type' => 'nft',
                        'name' => $nftData['name'],
                        'price' => $item['price'] ?? $nftData['price'],
                        'image' => $item['image'] ?? $nftData['image']
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

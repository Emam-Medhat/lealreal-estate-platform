<?php

/**
 * Add Sample NFTs to Cart for Testing
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Session;

// Sample NFTs to add to cart
$sampleNfts = [
    1 => [
        'type' => 'nft',
        'price' => 2.5,
        'image' => 'https://picsum.photos/seed/nft1/400/400.jpg'
    ],
    2 => [
        'type' => 'nft',
        'price' => 1.8,
        'image' => 'https://picsum.photos/seed/nft2/400/400.jpg'
    ],
    3 => [
        'type' => 'nft',
        'price' => 3.2,
        'image' => 'https://picsum.photos/seed/nft3/400/400.jpg'
    ]
];

// Clear existing cart
Session::forget('cart');

// Add sample items to cart
$cart = [];
foreach ($sampleNfts as $id => $nft) {
    $cart[$id] = [
        'type' => $nft['type'],
        'price' => $nft['price'],
        'image' => $nft['image'],
        'added_at' => now()
    ];
}

// Save cart to session
Session::put('cart', $cart);

echo "Added " . count($cart) . " sample NFTs to cart successfully!\n";
echo "Total: " . array_sum(array_column($cart, 'price')) . " ETH\n";
echo "Visit: http://127.0.0.1:8000/cart\n";

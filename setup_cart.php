<?php

/**
 * Setup Cart with Sample Data
 */

// Start session
session_start();

// Sample NFT data
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

// Add to session
$_SESSION['cart'] = $sampleNfts;

echo "Cart setup complete!\n";
echo "Items: " . count($sampleNfts) . "\n";
echo "Total: " . array_sum(array_column($sampleNfts, 'price')) . " ETH\n";
echo "Visit: http://127.0.0.1:8000/cart\n";

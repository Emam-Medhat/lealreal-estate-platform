<?php

namespace Database\Seeders;

use App\Models\DefiPool;
use Illuminate\Database\Seeder;

class DefiPoolSeeder extends Seeder
{
    public function run(): void
    {
        $pools = [
            [
                'name' => 'ETH Staking Pool',
                'token_pair' => 'ETH',
                'type' => 'staking',
                'total_liquidity' => 1500.75,
                'total_liquidity_usd' => 3001500.00,
                'apy' => 8.2,
                'volume_24h' => 50000.00,
                'fees_24h' => 50.00,
                'min_deposit' => 0.01,
                'withdraw_fee' => 0.5,
                'is_active' => true,
                'description' => 'Stake your ETH and earn rewards while securing the network',
                'protocol' => 'ethereum'
            ],
            [
                'name' => 'ETH/USDC Liquidity Pool',
                'token_pair' => 'ETH/USDC',
                'type' => 'liquidity',
                'total_liquidity' => 2500.50,
                'total_liquidity_usd' => 5001000.00,
                'apy' => 12.5,
                'volume_24h' => 150000.00,
                'fees_24h' => 150.00,
                'min_deposit' => 0.1,
                'withdraw_fee' => 0.3,
                'is_active' => true,
                'description' => 'Provide liquidity for ETH/USDC trading pair',
                'protocol' => 'uniswap'
            ],
            [
                'name' => 'USDC/USDT Stable Pool',
                'token_pair' => 'USDC/USDT',
                'type' => 'liquidity',
                'total_liquidity' => 10000.00,
                'total_liquidity_usd' => 10000000.00,
                'apy' => 4.2,
                'volume_24h' => 250000.00,
                'fees_24h' => 25.00,
                'min_deposit' => 100.0,
                'withdraw_fee' => 0.1,
                'is_active' => true,
                'description' => 'Low-risk stablecoin pool with consistent returns',
                'protocol' => 'curve'
            ],
            [
                'name' => 'ETH/DAI Yield Farm',
                'token_pair' => 'ETH/DAI',
                'type' => 'yield',
                'total_liquidity' => 800.25,
                'total_liquidity_usd' => 1600500.00,
                'apy' => 25.8,
                'volume_24h' => 75000.00,
                'fees_24h' => 75.00,
                'min_deposit' => 0.5,
                'withdraw_fee' => 0.5,
                'is_active' => true,
                'description' => 'High-yield farming with ETH/DAI pair',
                'protocol' => 'sushiswap'
            ],
            [
                'name' => 'Lending Pool - USDC',
                'token_pair' => 'USDC',
                'type' => 'lending',
                'total_liquidity' => 5000.00,
                'total_liquidity_usd' => 5000000.00,
                'apy' => 6.8,
                'volume_24h' => 100000.00,
                'fees_24h' => 100.00,
                'min_deposit' => 10.0,
                'withdraw_fee' => 0.2,
                'is_active' => true,
                'description' => 'Lend USDC and earn competitive interest rates',
                'protocol' => 'aave'
            ],
            [
                'name' => 'BTC/ETH Liquidity Pool',
                'token_pair' => 'BTC/ETH',
                'type' => 'liquidity',
                'total_liquidity' => 25.5,
                'total_liquidity_usd' => 1275000.00,
                'apy' => 18.5,
                'volume_24h' => 85000.00,
                'fees_24h' => 85.00,
                'min_deposit' => 0.001,
                'withdraw_fee' => 0.4,
                'is_active' => true,
                'description' => 'Major crypto pair with high trading volume',
                'protocol' => 'uniswap'
            ],
            [
                'name' => 'Liquid Staking - stETH',
                'token_pair' => 'ETH',
                'type' => 'staking',
                'total_liquidity' => 2000.00,
                'total_liquidity_usd' => 4000000.00,
                'apy' => 6.5,
                'volume_24h' => 30000.00,
                'fees_24h' => 30.00,
                'min_deposit' => 0.1,
                'withdraw_fee' => 0.3,
                'is_active' => true,
                'description' => 'Liquid staking with instant withdrawal options',
                'protocol' => 'lido'
            ],
            [
                'name' => 'Multi-Asset Yield Farm',
                'token_pair' => 'ETH/USDC/DAI',
                'type' => 'yield',
                'total_liquidity' => 1200.75,
                'total_liquidity_usd' => 2401500.00,
                'apy' => 35.2,
                'volume_24h' => 45000.00,
                'fees_24h' => 45.00,
                'min_deposit' => 1.0,
                'withdraw_fee' => 0.6,
                'is_active' => true,
                'description' => 'Multi-asset farming with enhanced rewards',
                'protocol' => 'balancer'
            ]
        ];

        foreach ($pools as $pool) {
            DefiPool::create($pool);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\LiquidityPool;
use App\Models\CryptoWallet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LiquidityPoolController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $pools = LiquidityPool::with(['token0', 'token1', 'creator', 'liquidity_providers'])->latest()->paginate(20);
        
        return view('blockchain.pools.index', compact('pools'));
    }

    public function createPool(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'token0_address' => 'required|string|max:255',
            'token1_address' => 'required|string|max:255',
            'pool_address' => 'required|string|max:255|unique:liquidity_pools',
            'creator_address' => 'required|string|max:255',
            'token0_amount' => 'required|numeric|min:0',
            'token1_amount' => 'required|numeric|min:0',
            'total_supply' => 'required|numeric|min:0',
            'fee_percentage' => 'required|numeric|min:0|max:10',
            'protocol' => 'required|string|in:uniswap,sushiswap,curve,balancer',
            'pool_type' => 'required|string|in:standard,stable,volatile',
            'is_active' => 'required|boolean',
            'metadata' => 'nullable|array',
            'created_at' => 'now()',
            'updated_at' => 'now()'
        ]);

        $pool = LiquidityPool::create([
            'name' => $request->name,
            'description' => $request->description,
            'token0_address' => $request->token0_address,
            'token1_address' => $request->token1_address,
            'pool_address' => $request->pool_address,
            'creator_address' => $request->creator_address,
            'token0_amount' => $request->token0_amount,
            'token1_amount' => $request->token1_amount,
            'total_supply' => $request->total_supply,
            'fee_percentage' => $request->fee_percentage,
            'protocol' => $request->protocol,
            'pool_type' => $request->pool_type,
            'is_active' => $request->is_active,
            'metadata' => $request->metadata ?? [],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'pool' => $pool
        ]);
    }

    public function getPools(Request $request)
    {
        $query = LiquidityPool::with(['token0', 'token1', 'creator', 'liquidity_providers']);
        
        if ($request->protocol) {
            $query->where('protocol', $request->protocol);
        }
        
        if ($request->pool_type) {
            $query->where('pool_type', $request->pool_type);
        }
        
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('pool_address', 'like', "%{$search}%");
            });
        }

        $pools = $query->latest()->paginate(20);
        
        return response()->json($pools);
    }

    public function getPool(Request $request)
    {
        $pool = LiquidityPool::with(['token0', 'token1', 'creator', 'liquidity_providers'])
            ->where('id', $request->id)
            ->orWhere('pool_address', $request->pool_address)
            ->first();
        
        if (!$pool) {
            return response()->json(['error' => 'Pool not found'], 404);
        }

        return response()->json($pool);
    }

    public function updatePool(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:liquidity_pools,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'fee_percentage' => 'nullable|numeric|min:0|max:10',
            'is_active' => 'nullable|boolean',
            'metadata' => 'nullable|array'
        ]);

        $pool = LiquidityPool::findOrFail($request->id);
        
        $pool->update([
            'name' => $request->name ?? $pool->name,
            'description' => $request->description ?? $pool->description,
            'fee_percentage' => $request->fee_percentage ?? $pool->fee_percentage,
            'is_active' => $request->is_active ?? $pool->is_active,
            'metadata' => $request->metadata ?? $pool->metadata,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'pool' => $pool
        ]);
    }

    public function addLiquidity(Request $request)
    {
        $request->validate([
            'pool_id' => 'required|integer|exists:liquidity_pools,id',
            'provider_address' => 'required|string|max:255',
            'token0_amount' => 'required|numeric|min:0',
            'token1_amount' => 'required|numeric|min:0',
            'signature' => 'required|string',
            'gas_price' => 'nullable|numeric|min:0'
        ]);

        $pool = LiquidityPool::findOrFail($request->pool_id);
        
        if (!$pool->is_active) {
            return response()->json(['error' => 'Pool is not active'], 400);
        }

        $result = $this->processLiquidityAddition($pool, $request->all());

        return response()->json([
            'status' => $result['status'],
            'liquidity' => $result['liquidity']
        ]);
    }

    public function removeLiquidity(Request $request)
    {
        $request->validate([
            'pool_id' => 'required|integer|exists:liquidity_pools,id',
            'provider_address' => 'required|string|max:255',
            'token0_amount' => 'required|numeric|min:0',
            'token1_amount' => 'required|numeric|min:0',
            'signature' => 'required|string',
            'gas_price' => 'nullable|numeric|min:0'
        ]);

        $pool = LiquidityPool::findOrFail($request->pool_id);
        
        if (!$pool->is_active) {
            return response()->json(['error' => 'Pool is not active'], 400);
        }

        $result = $this->processLiquidityRemoval($pool, $request->all());

        return response()->json([
            'status' => $result['status'],
            'liquidity' => $result['liquidity']
        ]);
    }

    public function swapTokens(Request $request)
    {
        $request->validate([
            'pool_id' => 'required|integer|exists:liquidity_pools,id',
            'provider_address' => 'required|string|max:255',
            'token_in_amount' => 'required|numeric|min:0',
            'token_out_amount' => 'required|numeric|min:0',
            'signature' => 'required|string',
            'gas_price' => 'nullable|numeric|min:0'
        ]);

        $pool = LiquidityPool::findOrFail($request->pool_id);
        
        if (!$pool->is_active) {
            return response()->json(['error' => 'Pool is not active'], 400);
        }

        $result = $this->processTokenSwap($pool, $request->all());

        return response()->json([
            'status' => $result['status'],
            'swap' => $result['swap']
        ]);
    }

    public function getPoolStats(Request $request)
    {
        $poolId = $request->pool_id;
        
        if (!$poolId) {
            return response()->json(['error' => 'Pool ID is required'], 400);
        }

        $pool = LiquidityPool::findOrFail($poolId);

        $stats = [
            'pool_info' => [
                'name' => $pool->name,
                'address' => $pool->pool_address,
                'token0_address' => $pool->token0_address,
                'token1_address' => $pool->token1_address,
                'protocol' => $pool->protocol,
                'pool_type' => $pool->pool_type,
                'token0_amount' => $pool->token0_amount,
                'token1_amount' => $pool->token1_amount,
                'total_supply' => $pool->total_supply,
                'fee_percentage' => $pool->fee_percentage,
                'is_active' => $pool->is_active
            ],
            'liquidity_stats' => $this->getPoolLiquidityStats($pool),
            'provider_stats' => $this->buildProviderStatsForPool($pool),
            'performance_stats' => $this->getPoolPerformanceStats($pool),
            'volume_stats' => $this->getVolumeStats($pool)
        ];

        return response()->json($stats);
    }

    public function getDefiStats(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_pools' => LiquidityPool::count(),
            'active_pools' => LiquidityPool::where('is_active', true)->count(),
            'total_liquidity' => LiquidityPool::sum('token0_amount') + LiquidityPool::sum('token1_amount'),
            'total_volume' => $this->calculateTotalVolume($startDate),
            'total_fees' => $this->calculateTotalFees($startDate),
            'protocol_stats' => $this->getProtocolStats($startDate),
            'pool_type_stats' => $this->getPoolTypeStats($startDate),
            'provider_stats' => $this->buildProviderStatsByDate($startDate),
            'performance_stats' => $this->getPerformanceStats($startDate)
        ];

        return response()->json($stats);
    }

    public function searchPools(Request $request)
    {
        $query = LiquidityPool::with(['token0', 'token1', 'creator', 'liquidity_providers']);
        
        if ($request->protocol) {
            $query->where('protocol', $request->protocol);
        }
        
        if ($request->pool_type) {
            $query->where('pool_type', $request->pool_type);
        }
        
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->token0_address) {
            $query->where('token0_address', $request->token0_address);
        }
        
        if ($request->token1_address) {
            $query->where('token1_address', $request->token1_address);
        }
        
        if ($request->min_liquidity) {
            $query->whereRaw('(token0_amount + token1_amount) >= ?', $request->min_liquidity);
        }
        
        if ($request->max_liquidity) {
            $query->whereRaw('(token0_amount + token1_amount) <= ?', $request->max_liquidity);
        }

        if ($request->fee_percentage_min) {
            $query->where('fee_percentage', '>=', $request->fee_percentage_min);
        }
        
        if ($request->fee_percentage_max) {
            $query->where('fee_percentage', '<=', $request->fee_percentage_max);
        }

        $pools = $query->latest()->paginate(20);
        
        return response()->json($pools);
    }

    public function exportPools(Request $request)
    {
        $format = $request->format ?? 'json';
        $limit = $request->limit ?? 1000;
        
        $pools = LiquidityPool::with(['token0', 'token1', 'creator', 'liquidity_providers'])->latest()->limit($limit)->get();

        if ($format === 'csv') {
            return $this->exportPoolsToCsv($pools);
        }

        return response()->json($pools);
    }

    private function processLiquidityAddition($pool, $data)
    {
        $liquidity = [
            'pool_id' => $pool->id,
            'provider_address' => $data['provider_address'],
            'token0_amount' => $data['token0_amount'],
            'token1_amount' => $data['token1_amount'],
            'signature' => $data['signature'],
            'gas_price' => $data['gas_price'] ?? 0,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Update pool amounts
        $pool->update([
            'token0_amount' => $pool->token0_amount + $data['token0_amount'],
            'token1_amount' => $pool->token1_amount + $data['token1_amount'],
            'updated_at' => now()
        ]);

        return [
            'status' => 'success',
            'liquidity' => $liquidity
        ];
    }

    private function processLiquidityRemoval($pool, $data)
    {
        $liquidity = [
            'pool_id' => $pool->id,
            'provider_address' => $data['provider_address'],
            'token0_amount' => $data['token0_amount'],
            'token1_amount' => $data['token1_amount'],
            'signature' => $data['signature'],
            'gas_price' => $data['gas_price'] ?? 0,
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Update pool amounts
        $pool->update([
            'token0_amount' => $pool->token0_amount - $data['token0_amount'],
            'token1_amount' => $pool->token1_amount - $data['token1_amount'],
            'updated_at' => now()
        ]);

        return [
            'status' => 'success',
            'liquidity' => $liquidity
        ];
    }

    private function processTokenSwap($pool, $data)
    {
        $swap = [
            'pool_id' => $pool->id,
            'provider1_address' => $data['provider_address'],
            'token_in_amount' => $data['token_in_amount'],
            'token_out_amount' => $data['token_out_amount'],
            'signature' => $data['signature'],
            'gas_price' => $data['gas_price'] ?? 0,
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Update pool amounts
        $pool->update([
            'token0_amount' => $pool->token0_amount - $data['token_in_amount'] + $data['token_out_amount'],
            'token1_amount' => $pool->token1_amount - $data['token_in_amount'] + $data['token_out_amount'],
            'updated_at' => now()
        ]);

        return [
            'status' => 'success',
            'swap' => $swap
        ];
    }

    private function getPoolLiquidityStats($pool)
    {
        return [
            'total_liquidity' => $pool->token0_amount + $pool->token1_amount,
            'liquidity_ratio' => $this->calculateLiquidityRatio($pool),
            'depth' => $this->calculateDepth($pool),
            'spread' => $this->calculateSpread($pool),
            'price_impact' => $this->getPriceImpact($pool),
            'utilization_rate' => $this->calculateLiquidityUtilization($pool)
        ];
    }

    private function getPoolPerformanceStats($pool)
    {
        return [
            'daily_volume' => $this->getDailyVolume($pool),
            'weekly_volume' => $this->getWeeklyVolume($pool),
            'monthly_volume' => $this->getMonthlyVolume($pool),
            'price_volatility' => $this->calculatePriceVolatility($pool),
            'liquidity_utilization' => $this->calculateLiquidityUtilization($pool),
            'fee_revenue' => $this->calculateFeeRevenue($pool),
            'impermanent_loss' => $this->calculatePermanentLoss($pool)
        ];
    }

    private function getVolumeStats($pool)
    {
        return [
            '24h_volume' => $this->get24HourVolume($pool),
            '7d_volume' => $this->get7DayVolume($pool),
            '30d_volume' => $this->get30DayVolume($pool),
            'total_volume' => $pool->token0_amount + $pool->token1_amount,
            'volume_trend' => $this->getVolumeTrend($pool),
            'price_impact' => $this->getPriceImpact($pool)
        ];
    }

    private function calculateLiquidityRatio($pool)
    {
        $totalLiquidity = $pool->token0_amount + $pool->token1_amount;
        $totalSupply = $pool->total_supply;
        
        return $totalSupply > 0 ? ($totalLiquidity / $totalSupply) * 100 : 0;
    }

    private function calculateDepth($pool)
    {
        $totalLiquidity = $pool->token0_amount + $pool->token1_amount;
        $totalSupply = $pool->total_supply;
        
        if ($totalSupply === 0) return 0;
        
        return $totalLiquidity / $totalSupply * 100;
    }

    private function calculateSpread($pool)
    {
        $token0Amount = $pool->token0_amount;
        $token1Amount = $pool->token1_amount;
        
        if ($token0Amount + $token1Amount === 0) return 0;
        
        return abs($token1Amount - $token0Amount) / $token0Amount * 100;
    }

    private function getPriceImpact($pool)
    {
        return $this->calculateSpread($pool);
    }

    private function calculateLiquidityUtilization($pool)
    {
        $totalLiquidity = $pool->token0_amount + $pool->token1_amount;
        $totalSupply = $pool->total_supply;
        
        return $totalSupply > 0 ? ($totalLiquidity / $totalSupply) * 100 : 0;
    }

    private function getTopProviders($pool)
    {
        return [
            [
                'address' => '0x' . substr(hash('sha256', 'provider1'), 0, 40),
                'liquidity_amount' => rand(1000, 10000),
                'contribution' => rand(1, 100)
            ],
            [
                'address' => '0x' . substr(hash('sha256', 'provider2'), 0, 40),
                'liquidity_amount' => rand(500, 5000),
                'contribution' => rand(1, 100)
            ],
            [
                'address' => '0x' . substr(hash('sha256', 'provider3'), 0, 40),
                'liquidity_amount' => rand(100, 1000),
                'contribution' => rand(1, 100)
            ]
        ];
    }

    private function getProviderDistribution($pool)
    {
        $providers = $this->getTopProviders($pool);
        
        $distribution = [];
        
        foreach ($providers as $provider) {
            $distribution[$provider['address']] = $provider['contribution'];
        }
        
        return $distribution;
    }

    private function getDailyVolume($pool)
    {
        return $this->getVolumeByPeriod($pool, '1d');
    }

    private function getWeeklyVolume($pool)
    {
        return $this->getVolumeByPeriod($pool, '7d');
    }

    private function getMonthlyVolume($pool)
    {
        return $this->getVolumeByPeriod($pool, '30d');
    }

    private function getVolumeByPeriod($pool, $period)
    {
        $days = match($period) {
            '1d' => 1,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 30
        };

        $volume = [];
        
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $volume = rand(1000, 10000);
            
            $volume[] = [
                'date' => $date,
                'volume' => $volume
            ];
        }
        
        return $volume;
    }

    private function getVolumeTrend($pool)
    {
        $dailyVolumes = $this->getDailyVolume($pool);
        
        if (count($dailyVolumes) < 2) {
            return [
                'trend' => 'stable',
                'change' => 0,
                'direction' => 'neutral'
            ];
        }

        $firstHalf = array_slice($dailyVolumes, 0, ceil(count($dailyVolumes) / 2));
        $lastHalf = array_slice($dailyVolumes, ceil(count($dailyVolumes) / 2));
        
        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $lastAvg = array_sum($lastHalf) / count($lastHalf);
        
        $change = ($lastAvg - $firstAvg) / $firstAvg * 100;
        
        if ($change > 5) {
            return [
                'trend' => 'increasing',
                'change' => $change,
                'direction' => 'up'
            ];
        } elseif ($change < -5) {
            return [
                'trend' => 'decreasing',
                'change' => $change,
                'direction' => 'down'
            ];
        }
        
        return [
            'trend' => 'stable',
            'change' => $change,
            'direction' => 'neutral'
        ];
    }

    private function calculateTotalVolume($startDate)
    {
        return LiquidityPool::where('created_at', '>=', $startDate)
            ->selectRaw('SUM(token0_amount + token1_amount) as volume')
            ->value('volume');
    }

    private function calculateTotalFees($startDate)
    {
        return LiquidityPool::where('created_at', '>=', $startDate)
            ->selectRaw('SUM((token0_amount + token1_amount) * fee_percentage / 100) as total_fees')
            ->value('total_fees');
    }

    private function getProtocolStats($startDate)
    {
        $protocols = ['uniswap', 'sushiswap', 'curve', 'balancer'];
        $stats = [];

        foreach ($protocols as $protocol) {
            $stats[$protocol] = $this->getProtocolData($protocol, $startDate);
        }

        return $stats;
    }

    private function getPoolTypeStats($startDate)
    {
        return LiquidityPool::where('created_at', '>=', $startDate)
            ->selectRaw('pool_type, COUNT(*) as count, SUM(token0_amount + token1_amount) as total_volume')
            ->groupBy('pool_type')
            ->orderByDesc('total_volume')
            ->get();
    }

    private function buildProviderStatsByDate($startDate)
    {
        return LiquidityPool::where('created_at', '>=', $startDate)
            ->selectRaw('creator_address, COUNT(*) as count, SUM(token0_amount + token1_amount) as total_liquidity')
            ->groupBy('creator_address')
            ->orderByDesc('total_liquidity')
            ->get();
    }

    private function buildProviderStatsForPool($pool)
    {
        return [
            'total_providers' => rand(10, 100),
            'active_providers' => rand(5, 50),
            'total_liquidity' => $pool->token0_amount + $pool->token1_amount,
            'average_liquidity' => ($pool->token0_amount + $pool->token1_amount) / 2,
            'top_providers' => $this->getTopProviders($pool),
            'provider_distribution' => $this->getProviderDistribution($pool)
        ];
    }

    private function getPerformanceStats($startDate)
    {
        return [
            'daily_volume' => $this->getDailyVolume($startDate),
            'weekly_volume' => $this->getWeeklyVolume($startDate),
            'monthly_volume' => $this->getMonthlyVolume($startDate),
            'price_volatility' => $this->calculateAverageVolatility($startDate),
            'utilization_rate' => $this->getAverageUtilization($startDate)
        ];
    }

    private function getAverageUtilization($startDate)
    {
        return LiquidityPool::where('created_at', '>=', $startDate)
            ->selectRaw('(SUM(token0_amount + token1_amount) / SUM(total_supply)) as utilization')
            ->value('utilization');
    }

    private function calculateAverageVolatility($startDate)
    {
        $volumes = $this->getDailyVolume($startDate);
        
        if (count($volumes) < 2) {
            return 0;
        }

        $variances = [];
        
        for ($i = 1; $i < count($volumes); $i++) {
            $variances[] = pow($volumes[$i] - $volumes[$i - 1], 2);
        }
        
        return count($variances) > 0 ? array_sum($variances) / (count($variances) - 1) : 0;
    }

    private function exportPoolsToCsv($pools)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="liquidity_pools.csv"'
        ];

        $callback = function() use ($pools) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 'Name', 'Address', 'Token0', 'Token1', 'Protocol', 'Type', 'Total Supply', 'Token0 Amount', 'Token1 Amount', 'Fee %', 'Active', 'Created At'
            ]);
            
            foreach ($pools as $pool) {
                fputcsv($file, [
                    $pool->id,
                    $pool->name,
                    $pool->pool_address,
                    $pool->token0_address,
                    $pool->token1_address,
                    $pool->protocol,
                    $pool->pool_type,
                    $pool->total_supply,
                    $pool->token0_amount,
                    $pool->token1_amount,
                    $pool->fee_percentage,
                    $pool->is_active,
                    $pool->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStartDate($period)
    {
        return match($period) {
            '1h' => now()->subHour(),
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDay()
        };
    }
}

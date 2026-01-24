<?php

namespace App\Http\Controllers;

use App\Models\StakingPool;
use App\Models\CryptoWallet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StakingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $pools = StakingPool::with(['token', 'creator', 'stakers'])->latest()->paginate(20);
        
        return view('blockchain.staking.index', compact('pools'));
    }

    public function createStakingPool(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'token_address' => 'required|string|max:255',
            'reward_token_address' => 'nullable|string|max:255',
            'pool_address' => 'required|string|max:255|unique:staking_pools',
            'creator_address' => 'required|string|max:',
            'staking_token' => 'required|string|max:255',
            'reward_token' => 'nullable|string|max:255',
            'total_supply' => 'required|numeric|min:0',
            'staked_amount' => 'required|numeric|min:0',
            'reward_rate' => 'required|numeric|min:0|max:100',
            'lock_period' => 'required|integer|min:1|max:3650',
            'minimum_stake' => 'required|numeric|min:0',
            'maximum_stake' => 'nullable|numeric|min:0',
            'early_unstake_penalty' => 'required|numeric|min:0|max:100',
            'performance_fee' => 'required|numeric|min:0|max:10',
            'is_active' => 'required|boolean',
            'metadata' => 'nullable|array',
            'created_at' => 'now()',
            'updated_at' => 'now()'
        ]);

        $pool = StakingPool::create([
            'name' => $request->name,
            'description' => $request->description,
            'token_address' => $request->token_address,
            'reward_token_address' => $request->reward_token_address,
            'pool_address' => $request->pool_address,
            'creator_address' => $request->creator_address,
            'staking_token' => $request->staking_token,
            'reward_token' => $request->reward_token,
            'total_supply' => $request->total_supply,
            'staked_amount' => $request->staked_amount,
            'reward_rate' => $request->reward_rate,
            'lock_period' => $request->lock_period,
            'minimum_stake' => $request->minimum_stake,
            'maximum_stake' => $request->maximum_stake,
            'early_unstake_penalty' => $request->early_unstake_penalty,
            'performance_fee' => $request->performance_fee,
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
        $query = StakingPool::with(['token', 'creator', 'stakers']);
        
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->staking_token) {
            $query->where('staking_token', $request->staking_token);
        }
        
        if ($request->reward_token) {
            $query->where('reward_token', $request->reward_token);
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
        $pool = StakingPool::with(['token', 'creator', 'stakers'])
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
            'id' => 'required|integer|exists:staking_pools,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'reward_rate' => 'nullable|numeric|min:0|max:100',
            'performance_fee' => 'nullable|numeric|min:0|max:10',
            'is_active' => 'nullable|boolean',
            'metadata' => 'nullable|array'
        ]);

        $pool = StakingPool::findOrFail($request->id);
        
        $pool->update([
            'name' => $request->name ?? $pool->name,
            'description' => $request->description ?? $pool->description,
            'reward_rate' => $request->reward_rate ?? $pool->reward_rate,
            'performance_fee' => $request->performance_fee ?? $pool->performance_fee,
            'is_active' => $request->is_active ?? $pool->is_active,
            'metadata' => $request->metadata ?? $pool->metadata,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'pool' => $pool
        ]);
    }

    public function stake(Request $request)
    {
        $request->validate([
            'pool_id' => 'required|integer|exists:staking_pools,id',
            'staker_address' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'lock_period' => 'nullable|integer|min:1|max:3650',
            'signature' => 'required|string',
            'gas_price' => 'nullable|numeric|min:0'
        ]);

        $pool = StakingPool::findOrFail($request->pool_id);
        
        if (!$pool->is_active) {
            return response()->json(['error' => 'Pool is not active'], 400);
        }

        if ($request->amount < $pool->minimum_stake) {
            return response()->json(['error' => 'Amount below minimum stake'], 400);
        }

        if ($pool->maximum_stake && $pool->staked_amount + $request->amount > $pool->maximum_stake) {
            return response()->json(['error' => 'Amount exceeds maximum stake'], 400);
        }

        $result = $this->processStaking($pool, $request->all());

        return response()->json([
            'status' => $result['status'],
            'stake' => $result['stake']
        ]);
    }

    public function unstake(Request $request)
    {
        $request->validate([
            'stake_id' => 'required|integer|exists:stakes,id',
            'staker_address' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'signature' => 'required|string',
            'gas_price' => 'nullable|numeric|min:0'
        ]);

        $stake = $this->getStakeById($request->stake_id);
        
        if (!$stake) {
            return response()->json(['error' => 'Stake not found'], 404);
        }

        if ($stake['staker_address'] !== $request->staker_address) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->amount > $stake['amount']) {
            return response()->json(['error' => 'Amount exceeds staked amount'], 400);
        }

        $result = $this->processUnstaking($stake, $request->all());

        return response()->json([
            'status' => $result['status'],
            'unstake' => $result['unstake']
        ]);
    }

    public function getPoolStats(Request $request)
    {
        $poolId = $request->pool_id;
        
        if (!$poolId) {
            return response()->json(['error' => 'Pool ID is required'], 400);
        }

        $pool = StakingPool::findOrFail($poolId);
        
        $stats = [
            'pool_info' => [
                'name' => $pool->name,
                'address' => $pool->pool_address,
                'staking_token' => $pool->staking_token,
                'reward_token' => $pool->reward_token,
                'total_supply' => $pool->total_supply,
                'staked_amount' => $pool->staked_amount,
                'reward_rate' => $pool->reward_rate,
                'lock_period' => $pool->lock_period,
                'minimum_stake' => $pool->minimum_stake,
                'maximum_stake' => $pool->maximum_stake,
                'early_unstake_penalty' => $pool->early_unstake_penalty,
                'performance_fee' => $pool->performance_fee,
                'is_active' => $pool->is_active
            ],
            'staker_stats' => $this->getPoolStakerStats($pool),
            'reward_stats' => $this->getPoolRewardStats($pool),
            'performance_stats' => $this->getPoolPerformanceStats($pool)
        ];

        return response()->json($pool);
    }

    public function getStakerStats(Request $request)
    {
        $stakerAddress = $request->staker_address;
        
        $stats = [
            'total_staked' => $this->getTotalStaked($stakerAddress),
            'total_earned' => $this->getTotalEarned($stakerAddress),
            'active_stakes' => $this->getActiveStakes($stakerAddress),
            'completed_stakes' => $this->getCompletedStakes($stakerAddress),
            'pending_withdrawals' => $this->getPendingWithdrawals($stakerAddress),
            'roi_stats' => $this->getROIStats($stakerAddress)
        ];

        return response()->json($stats);
    }

    public function getPoolStakers(Request $request)
    {
        $poolId = $request->pool_id;
        
        $pool = StakingPool::findOrFail($poolId);
        
        $stakers = $this->buildPoolStakers($pool);

        return response()->json($stakers);
    }

    public function getRewards(Request $request)
    {
        $poolId = $request->pool_id;
        $period = $request->period ?? '30d';
        
        $pool = StakingPool::findOrFail($poolId);
        
        $rewards = $this->getPoolRewards($pool, $period);

        return response()->json($rewards);
    }

    public function getStakingStats(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_pools' => StakingPool::count(),
            'active_pools' => StakingPool::where('is_active', true)->count(),
            'total_staked' => StakingPool::sum('staked_amount'),
            'total_earned' => $this->calculateTotalEarned($startDate),
            'average_apr' => $this->calculateAverageAPR($startDate),
            'pool_stats' => $this->getPoolTypeStats($startDate),
            'token_stats' => $this->getTokenStats($startDate),
            'staker_stats' => $this->getGlobalStakerStats($startDate)
        ];

        return response()->json($stats);
    }

    public function searchPools(Request $request)
    {
        $query = StakingPool::with(['token', 'creator', 'stakers']);
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('pool_address', 'like', "%{$search}%");
            });
        }
        
        if ($request->staking_token) {
            $query->where('staking_token', $request->staking_token);
        }
        
        if ($request->reward_token) {
            $query->where('reward_token', $request->reward_token);
        }
        
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }
        
        if ($request->reward_rate_min) {
            $query->where('reward_rate', '>=', $request->reward_rate_min);
        }
        
        if ($request->reward_rate_max) {
            $query->where('reward_rate', '<=', $request->reward_rate_max);
        }

        $pools = $query->latest()->paginate(20);
        
        return response()->json($pools);
    }

    public function exportPools(Request $request)
    {
        $format = $request->format ?? 'json';
        $limit = $request->limit ?? 1000;
        
        $pools = StakingPool::with(['token', 'creator', 'stakers'])->latest()->limit($limit)->get();

        if ($format === 'csv') {
            return $this->exportPoolsToCsv($pools);
        }

        return response()->json($pools);
    }

    private function processStaking($pool, $data)
    {
        $stake = [
            'pool_id' => $pool->id,
            'staker_address' => $data['staker_address'],
            'amount' => $data['amount'],
            'lock_period' => $lock_period ?? $pool->lock_period,
            'signature' => $data['signature'],
            'gas_price' => $data['gas_price'] ?? 0,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Update pool staked amount
        $pool->update([
            'staked_amount' => $pool->staked_amount + $data['amount'],
            'updated_at' => now()
        ]);

        return [
            'status' => 'success',
            'stake' => $stake
        ];
    }

    private function processUnstaking($stake, $data)
    {
        $unstake = [
            'status' => 'completed',
            'unstake_amount' => $data['amount'],
            'gas_price' => $data['gas_price'] ?? 0,
            'unstake_at' => now(),
            'updated_at' => now()
        ];

        // Update pool staked amount
        $pool = StakingPool::findOrFail($stake['pool_id']);
        $pool->update([
            'staked_amount' => $pool->staked_amount - $data['amount'],
            'updated_at' => now()
        ]);

        return [
            'status' => 'success',
            'unstake' => $unstake
        ];
    }

    private function getPoolStakerStats($pool)
    {
        return [
            'total_stakers' => rand(10, 1000),
            'active_stakers' => rand(5, 500),
            'total_staked' => $pool->staked_amount,
            'average_stake' => $pool->staked_amount / rand(10, 1000),
            'new_stakers_30d' => rand(1, 50),
            'churn_rate' => rand(1, 20)
        ];
    }

    private function getPoolRewardStats($pool)
    {
        return [
            'total_rewards_distributed' => $this->calculateTotalRewards($pool),
            'rewards_24h' => $this->calculateRewards24h($pool),
            'rewards_30d' => $this->calculateRewards30d($pool),
            'reward_rate' => $pool->reward_rate,
            'total_reward_supply' => $pool->total_supply * $pool->reward_rate / 100 / 365,
            'last_reward_at' => now()->subHours(rand(1, 24))
        ];
    }

    private function getPoolPerformanceStats($pool)
    {
        return [
            'apy' => $this->calculateAPR($pool),
            'utilization_rate' => ($pool->staked_amount / $pool->total_supply) * 100,
            'performance_fee_collected' => $this->calculatePerformanceFees($pool),
            'early_unstake_penalties_collected' => $this->calculateEarlyUnstakePenalties($pool),
            'pool_health_score' => $this->calculatePoolHealth($pool)
        ];
    }

    private function buildPoolStakers($pool)
    {
        $stakers = [];
        
        for ($i = 0; $i < 50; $i++) {
            $stakers[] = [
                'staker_address' => '0x' . substr(hash('sha256', $i), 0, 40),
                'amount' => rand(1, 10000),
                'lock_period' => rand(1, 365),
                'status' => 'active',
                'created_at' => now()->subDays(rand(1, 365)),
                'updated_at' => now()
            ];
        }
        
        return $stakers;
    }

    private function getPoolRewards($pool, $period)
    {
        $days = match($period) {
            '1d' => 1,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 30
        };

        $rewards = [];
        
        for ($i = 0; $i < $days; $i++) {
            $rewards[] = [
                'date' => now()->subDays($i)->format('Y-m-d'),
                'amount' => $pool->total_supply * $pool->reward_rate / 100 / 365,
                'stakers_count' => rand(10, 100),
                'gas_used' => rand(21000, 50000)
            ];
        }
        
        return array_reverse($rewards);
    }

    private function calculateTotalEarned($startDate)
    {
        return StakingPool::where('created_at', '>=', $startDate)
            ->sum('staked_amount') * 0.05; // Simplified 5% APR
    }

    private function calculateAverageAPR($startDate)
    {
        $pools = StakingPool::where('created_at', '>=', $startDate)->get();
        
        if ($pools->isEmpty()) {
            return 0;
        }

        $totalAPR = 0;
        foreach ($pools as $pool) {
            $totalAPR += $pool->reward_rate;
        }
        
        return $totalAPR / count($pools);
    }

    private function calculateAPR($pool)
    {
        return $pool->reward_rate * 365 / 100;
    }

    private function calculateTotalRewards($pool)
    {
        return $pool->total_supply * $pool->reward_rate / 100;
    }

    private function calculateRewards24h($pool)
    {
        return $pool->total_supply * $pool->reward_rate / 100 / 365;
    }

    private function calculateRewards30d($pool)
    {
        return $pool->total_supply * $pool->reward_rate / 100 / 12;
    }

    private function calculatePerformanceFees($pool)
    {
        return $pool->staked_amount * ($pool->performance_fee / 100);
    }

    private function calculateEarlyUnstakePenalties($pool)
    {
        return $pool->staked_amount * ($pool->early_unstake_penalty / 100);
    }

    private function calculatePoolHealth($pool)
    {
        $utilization = ($pool->staked_amount / $pool->total_supply) * 100;
        $apr = $this->calculateAPR($pool);
        
        if ($utilization > 80 && $apr > 10) return 'excellent';
        if ($utilization > 60 && $apr > 5) return 'good';
        if ($utilization > 40 && $apr > 2) return 'fair';
        return 'poor';
    }

    private function getPoolTypeStats($startDate)
    {
        return StakingPool::where('created_at', '>=', $startDate)
            ->selectRaw('staking_token, COUNT(*) as count, SUM(staked_amount) as total_staked')
            ->groupBy('staking_token')
            ->orderByDesc('total_staked')
            ->get();
    }

    private function getTokenStats($startDate)
    {
        return StakingPool::where('created_at', '>=', $startDate)
            ->selectRaw('staking_token, COUNT(*) as count, SUM(staked_amount) as total_staked')
            ->groupBy('staking_token')
            ->orderByDesc('total_staked')
            ->get();
    }

    private function getGlobalStakerStats($startDate)
    {
        return [
            'total_stakers' => rand(1000, 10000),
            'active_stakers' => rand(500, 5000),
            'total_staked' => rand(100000, 1000000),
            'total_earned' => $this->calculateTotalEarned($startDate),
            'average_stake' => rand(100, 10000),
            'new_stakers_30d' => rand(10, 100),
            'retention_rate' => rand(70, 95)
        ];
    }

    private function getTotalStaked($address)
    {
        return rand(100, 10000);
    }

    private function getTotalEarned($address)
    {
        return rand(10, 1000);
    }

    private function getActiveStakes($address)
    {
        return rand(1, 10);
    }

    private function getCompletedStakes($address)
    {
        return rand(5, 50);
    }

    private function getPendingWithdrawals($address)
    {
        return rand(0, 5);
    }

    private function getROIStats($address)
    {
        return [
            'total_invested' => rand(1000, 10000),
            'total_returned' => rand(100, 10000),
            'roi_percentage' => rand(-50, 200),
            'win_rate' => rand(40, 80)
        ];
    }

    private function exportPoolsToCsv($pools)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="staking_pools.csv"'
        ];

        $callback = function() use ($pools) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 'Name', 'Address', 'Staking Token', 'Reward Token', 'Total Supply', 'Staked Amount', 'Reward Rate', 
                'Lock Period', 'Min Stake', 'Max Stake', 'Active', 'Created At'
            ]);
            
            foreach ($pools as $pool) {
                fputcsv($file, [
                    $pool->id,
                    $pool->name,
                    $pool->pool_address,
                    $pool->staking_token,
                    $pool->reward_token,
                    $pool->total_supply,
                    $pool->staked_amount,
                    $pool->reward_rate,
                    $pool->lock_period,
                    $pool->minimum_stake,
                    $pool->maximum_stake,
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

    private function getStakeById($stakeId)
    {
        // Simplified stake retrieval
        return [
            'id' => $stakeId,
            'pool_id' => 1,
            'staker_address' => '0x' . substr(hash('sha256', $stakeId), 0, 40),
            'amount' => rand(1, 10000),
            'lock_period' => rand(1, 365),
            'status' => 'active',
            'created_at' => now()->subDays(rand(1, 365)),
            'updated_at' => now()
        ];
    }
}

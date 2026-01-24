<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SmartContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'abi',
        'bytecode',
        'source_code',
        'compiler_version',
        'optimization_used',
        'constructor_arguments',
        'deployment_tx_hash',
        'deployer_address',
        'deployment_block',
        'deployment_timestamp',
        'verified',
        'verification_status',
        'contract_type',
        'description',
        'tags',
        'metadata',
        'gas_limit',
        'gas_price',
        'deployment_cost',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'abi' => 'array',
        'source_code' => 'string',
        'constructor_arguments' => 'string',
        'deployment_timestamp' => 'datetime',
        'verified' => 'boolean',
        'tags' => 'array',
        'metadata' => 'array',
        'gas_limit' => 'integer',
        'gas_price' => 'decimal:18',
        'deployment_cost' => 'decimal:18',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('contract_type', $type);
    }

    public function scopeByDeployer($query, $address)
    {
        return $query->where('deployer_address', $address);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', Carbon::now()->subHours($hours));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getFormattedDeploymentCostAttribute()
    {
        return number_format($this->deployment_cost, 8);
    }

    public function getFormattedGasPriceAttribute()
    {
        return number_format($this->gas_price, 9);
    }

    public function getVerificationStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'verified' => 'تم التحقق',
            'failed' => 'فشل التحقق',
            'not_verified' => 'لم يتم التحقق'
        ];
        return $labels[$this->verification_status] ?? $this->verification_status;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'deprecated' => 'مهمل',
            'error' => 'خطأ'
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getContractTypeLabelAttribute()
    {
        $labels = [
            'erc20' => 'ERC-20 Token',
            'erc721' => 'ERC-721 NFT',
            'erc1155' => 'ERC-1155 Multi-Token',
            'dao' => 'DAO Contract',
            'defi' => 'DeFi Protocol',
            'staking' => 'Staking Contract',
            'liquidity' => 'Liquidity Pool',
            'custom' => 'Custom Contract'
        ];
        return $labels[$this->contract_type] ?? $this->contract_type;
    }

    public function getContractUrlAttribute()
    {
        return "https://etherscan.io/address/{$this->address}";
    }

    public function getDeploymentUrlAttribute()
    {
        return "https://etherscan.io/tx/{$this->deployment_tx_hash}";
    }

    public function getSourceCodeLinesAttribute()
    {
        return substr_count($this->source_code, "\n") + 1;
    }

    public function getAbiFunctionsAttribute()
    {
        return collect($this->abi)->filter(function ($item) {
            return $item['type'] === 'function';
        })->count();
    }

    public function getAbiEventsAttribute()
    {
        return collect($this->abi)->filter(function ($item) {
            return $item['type'] === 'event';
        })->count();
    }

    // Methods
    public function isVerified()
    {
        return $this->verified && $this->verification_status === 'verified';
    }

    public function isErc20()
    {
        return $this->contract_type === 'erc20';
    }

    public function isErc721()
    {
        return $this->contract_type === 'erc721';
    }

    public function isErc1155()
    {
        return $this->contract_type === 'erc1155';
    }

    public function isDao()
    {
        return $this->contract_type === 'dao';
    }

    public function isDefi()
    {
        return $this->contract_type === 'defi';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getDeploymentTime()
    {
        return $this->created_at->diffInSeconds($this->deployment_timestamp);
    }

    public function getFormattedDeploymentTime()
    {
        $seconds = $this->getDeploymentTime();
        if ($seconds < 60) {
            return "{$seconds} ثانية";
        } elseif ($seconds < 3600) {
            return floor($seconds / 60) . " دقيقة";
        } else {
            return floor($seconds / 3600) . " ساعة";
        }
    }

    public function hasFunction($functionName)
    {
        return collect($this->abi)->contains(function ($item) use ($functionName) {
            return $item['type'] === 'function' && $item['name'] === $functionName;
        });
    }

    public function getFunctionSignature($functionName)
    {
        $function = collect($this->abi)->first(function ($item) use ($functionName) {
            return $item['type'] === 'function' && $item['name'] === $functionName;
        });
        
        if (!$function) return null;
        
        $inputs = collect($function['inputs'])->map(function ($input) {
            return $input['type'];
        })->implode(',');
        
        return "{$functionName}({$inputs})";
    }

    public function getEventSignature($eventName)
    {
        $event = collect($this->abi)->first(function ($item) use ($eventName) {
            return $item['type'] === 'event' && $item['name'] === $eventName;
        });
        
        if (!$event) return null;
        
        $inputs = collect($event['inputs'])->map(function ($input) {
            return $input['type'];
        })->implode(',');
        
        return "{$eventName}({$inputs})";
    }

    // Relationships
    public function blockchainRecord(): BelongsTo
    {
        return $this->belongsTo(BlockchainRecord::class, 'deployment_tx_hash', 'transaction_hash');
    }

    public function nfts(): HasMany
    {
        return $this->hasMany(Nft::class, 'contract_address', 'address');
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class, 'contract_address', 'address');
    }

    public function daos(): HasMany
    {
        return $this->hasMany(Dao::class, 'contract_address', 'address');
    }

    public function defiLoans(): HasMany
    {
        return $this->hasMany(DefiLoan::class, 'contract_address', 'address');
    }

    public function stakingPools(): HasMany
    {
        return $this->hasMany(StakingPool::class, 'contract_address', 'address');
    }

    // Static Methods
    public static function getStats()
    {
        return [
            'total_contracts' => self::count(),
            'verified_contracts' => self::verified()->count(),
            'erc20_contracts' => self::byType('erc20')->count(),
            'erc721_contracts' => self::byType('erc721')->count(),
            'erc1155_contracts' => self::byType('erc1155')->count(),
            'dao_contracts' => self::byType('dao')->count(),
            'defi_contracts' => self::byType('defi')->count(),
            'active_contracts' => self::active()->count(),
            'total_deployment_cost' => self::sum('deployment_cost'),
            'avg_deployment_cost' => self::avg('deployment_cost'),
            'contracts_today' => self::whereDate('created_at', today())->count(),
            'contracts_this_week' => self::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'contracts_this_month' => self::whereMonth('created_at', now()->month)->count(),
        ];
    }

    public static function getRecentContracts($limit = 20)
    {
        return self::orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getVerifiedContracts($limit = 20)
    {
        return self::verified()
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getContractsByType($type, $limit = 20)
    {
        return self::byType($type)
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getContractsByDeployer($address, $limit = 20)
    {
        return self::byDeployer($address)
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getHighCostContracts($limit = 20)
    {
        return self::orderBy('deployment_cost', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function searchContracts($query, $limit = 50)
    {
        return self::where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('address', 'like', "%{$query}%")
                      ->orWhere('deployer_address', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
    }

    public static function getDailyDeploymentCount($days = 30)
    {
        return self::where('created_at', '>=', now()->subDays($days))
                   ->groupBy('date')
                   ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                   ->orderBy('date', 'desc')
                   ->get();
    }

    public static function getContractTypeDistribution()
    {
        return self::groupBy('contract_type')
                   ->selectRaw('contract_type, COUNT(*) as count')
                   ->orderBy('count', 'desc')
                   ->get();
    }

    public static function getVerificationStats()
    {
        return [
            'total' => self::count(),
            'verified' => self::verified()->count(),
            'not_verified' => self::where('verified', false)->count(),
            'verification_rate' => self::verified()->count() / max(self::count(), 1) * 100
        ];
    }

    public static function getDeployerStats($address)
    {
        $contracts = self::byDeployer($address);
        
        return [
            'total_contracts' => $contracts->count(),
            'verified_contracts' => $contracts->verified()->count(),
            'total_deployment_cost' => $contracts->sum('deployment_cost'),
            'contract_types' => $contracts->groupBy('contract_type')->map->count(),
            'first_deployment' => $contracts->min('created_at'),
            'last_deployment' => $contracts->max('created_at'),
        ];
    }

    // Export Methods
    public static function exportToCsv($contracts)
    {
        $headers = [
            'Name', 'Address', 'Contract Type', 'Verified', 'Deployer Address', 
            'Deployment Block', 'Deployment Cost', 'Status', 'Created At'
        ];

        $rows = $contracts->map(function ($contract) {
            return [
                $contract->name,
                $contract->address,
                $contract->contract_type_label,
                $contract->verified ? 'Yes' : 'No',
                $contract->deployer_address,
                $contract->deployment_block,
                $contract->formatted_deployment_cost,
                $contract->status_label,
                $contract->created_at
            ];
        });

        return collect([$headers])->concat($rows);
    }

    // Validation Methods
    public function validateContract()
    {
        $errors = [];
        
        if (empty($this->name)) {
            $errors[] = 'Contract name is required';
        }
        
        if (empty($this->address)) {
            $errors[] = 'Contract address is required';
        }
        
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $this->address)) {
            $errors[] = 'Invalid contract address format';
        }
        
        if (empty($this->deployer_address)) {
            $errors[] = 'Deployer address is required';
        }
        
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $this->deployer_address)) {
            $errors[] = 'Invalid deployer address format';
        }
        
        if (empty($this->deployment_tx_hash)) {
            $errors[] = 'Deployment transaction hash is required';
        }
        
        if (!is_array($this->abi)) {
            $errors[] = 'ABI must be a valid JSON array';
        }
        
        return $errors;
    }

    // Contract Interaction Methods
    public function callFunction($functionName, $params = [])
    {
        // This would integrate with actual blockchain API
        // For now, return simulated call result
        return [
            'function' => $functionName,
            'params' => $params,
            'result' => 'simulated_result',
            'success' => true,
            'gas_used' => 21000,
            'timestamp' => now()
        ];
    }

    public function getContractStorage()
    {
        // This would get actual contract storage from blockchain
        return [
            'storage_slots' => [],
            'total_slots' => 0,
            'last_updated' => now()
        ];
    }

    public function getContractEvents($fromBlock = 0, $toBlock = 'latest')
    {
        // This would get actual events from blockchain
        return [
            'events' => [],
            'from_block' => $fromBlock,
            'to_block' => $toBlock,
            'total_events' => 0
        ];
    }

    public function getContractBalance()
    {
        // This would get actual contract balance from blockchain
        return [
            'balance' => '0',
            'formatted_balance' => '0 ETH',
            'last_updated' => now()
        ];
    }

    public function getTransactionHistory($limit = 50)
    {
        // This would get actual transaction history from blockchain
        return [
            'transactions' => [],
            'total_transactions' => 0,
            'limit' => $limit
        ];
    }

    public function verifyContract()
    {
        // This would integrate with Etherscan API for verification
        $this->verified = true;
        $this->verification_status = 'verified';
        $this->save();
        
        return [
            'verified' => true,
            'message' => 'Contract verified successfully',
            'verification_time' => now()
        ];
    }
}

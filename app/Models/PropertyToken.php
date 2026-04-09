<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class PropertyToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'property_id',
        'token_address',
        'contract_address',
        'total_supply',
        'price_per_token',
        'total_value',
        'fractional_ownership',
        'minimum_investment',
        'maximum_investment',
        'rental_yield',
        'appreciation_rate',
        'management_fee',
        'platform_fee',
        'legal_fee',
        'maintenance_fee',
        'insurance_fee',
        'token_standard',
        'jurisdiction',
        'regulatory_compliance',
        'valuation_method',
        'last_valuation_date',
        'next_valuation_date',
        'tokenization_date',
        'expected_roi',
        'risk_level',
        'investment_term',
        'exit_strategy',
        'distribution_frequency',
        'total_investors',
        'active_investors',
        'total_invested',
        'total_distributed',
        'pending_distributions',
        'token_status',
        'is_verified',
        'verification_status',
        'documents',
        'legal_documents',
        'financial_reports',
        'property_photos',
        'tags',
        'metadata',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'total_supply' => 'decimal:18',
        'price_per_token' => 'decimal:18',
        'total_value' => 'decimal:18',
        'fractional_ownership' => 'decimal:5',
        'minimum_investment' => 'decimal:18',
        'maximum_investment' => 'decimal:18',
        'rental_yield' => 'decimal:5',
        'appreciation_rate' => 'decimal:5',
        'management_fee' => 'decimal:5',
        'platform_fee' => 'decimal:5',
        'legal_fee' => 'decimal:5',
        'maintenance_fee' => 'decimal:5',
        'insurance_fee' => 'decimal:5',
        'last_valuation_date' => 'datetime',
        'next_valuation_date' => 'datetime',
        'tokenization_date' => 'datetime',
        'expected_roi' => 'decimal:5',
        'total_investors' => 'integer',
        'active_investors' => 'integer',
        'total_invested' => 'decimal:18',
        'total_distributed' => 'decimal:18',
        'pending_distributions' => 'decimal:18',
        'is_verified' => 'boolean',
        'documents' => 'array',
        'legal_documents' => 'array',
        'financial_reports' => 'array',
        'property_photos' => 'array',
        'tags' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('token_status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
    }

    public function scopeByYield($query, $min = 0, $max = null)
    {
        $query->where('rental_yield', '>=', $min);
        if ($max !== null) {
            $query->where('rental_yield', '<=', $max);
        }
        return $query;
    }

    public function scopeByInvestment($query, $min = 0, $max = null)
    {
        $query->where('minimum_investment', '>=', $min);
        if ($max !== null) {
            $query->where('minimum_investment', '<=', $max);
        }
        return $query;
    }

    // Accessors
    public function getFormattedTotalSupplyAttribute()
    {
        return number_format($this->total_supply, 8);
    }

    public function getFormattedPricePerTokenAttribute()
    {
        return number_format($this->price_per_token, 2);
    }

    public function getFormattedTotalValueAttribute()
    {
        return number_format($this->total_value, 2);
    }

    public function getFormattedFractionalOwnershipAttribute()
    {
        return number_format($this->fractional_ownership, 2) . '%';
    }

    public function getFormattedMinimumInvestmentAttribute()
    {
        return number_format($this->minimum_investment, 2);
    }

    public function getFormattedMaximumInvestmentAttribute()
    {
        return number_format($this->maximum_investment, 2);
    }

    public function getFormattedRentalYieldAttribute()
    {
        return number_format($this->rental_yield, 2) . '%';
    }

    public function getFormattedAppreciationRateAttribute()
    {
        return number_format($this->appreciation_rate, 2) . '%';
    }

    public function getFormattedExpectedRoiAttribute()
    {
        return number_format($this->expected_roi, 2) . '%';
    }

    public function getTokenStatusLabelAttribute()
    {
        $labels = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'pending' => 'قيد الانتظار',
            'suspended' => 'معلق',
            'closed' => 'مغلق',
            'liquidated' => 'تم التصفية'
        ];
        return $labels[$this->token_status] ?? $this->token_status;
    }

    public function getVerificationStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'verified' => 'تم التحقق',
            'rejected' => 'مرفوض',
            'not_verified' => 'لم يتم التحقق'
        ];
        return $labels[$this->verification_status] ?? $this->verification_status;
    }

    public function getRiskLevelLabelAttribute()
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'very_high' => 'مرتفع جداً'
        ];
        return $labels[$this->risk_level] ?? $this->risk_level;
    }

    public function getTokenStandardLabelAttribute()
    {
        $labels = [
            'erc20' => 'ERC-20',
            'erc721' => 'ERC-721',
            'erc1155' => 'ERC-1155',
            'security_token' => 'Security Token'
        ];
        return $labels[$this->token_standard] ?? $this->token_standard;
    }

    public function getTokenUrlAttribute()
    {
        return "https://etherscan.io/token/{$this->token_address}";
    }

    public function getContractUrlAttribute()
    {
        return "https://etherscan.io/address/{$this->contract_address}";
    }

    public function getActiveInvestorRateAttribute()
    {
        if ($this->total_investors == 0) return 0;
        return ($this->active_investors / $this->total_investors) * 100;
    }

    public function getFormattedActiveInvestorRateAttribute()
    {
        return number_format($this->active_investor_rate, 2) . '%';
    }

    public function getAverageInvestmentPerInvestorAttribute()
    {
        if ($this->active_investors == 0) return 0;
        return $this->total_invested / $this->active_investors;
    }

    public function getFormattedAverageInvestmentPerInvestorAttribute()
    {
        return number_format($this->average_investment_per_investor, 2);
    }

    public function getTokensSoldAttribute()
    {
        return $this->total_supply - $this->getAvailableTokens();
    }

    public function getFormattedTokensSoldAttribute()
    {
        return number_format($this->tokens_sold, 8);
    }

    public function getAvailableTokensAttribute()
    {
        // Calculate remaining tokens
        return $this->total_supply * (1 - $this->fractional_ownership / 100);
    }

    public function getFormattedAvailableTokensAttribute()
    {
        return number_format($this->available_tokens, 8);
    }

    public function getDaysSinceTokenizationAttribute()
    {
        return $this->tokenization_date ? 
               $this->tokenization_date->diffInDays(now()) : 
               0;
    }

    public function getFormattedInvestmentTermAttribute()
    {
        $terms = [
            'short_term' => 'قصير الأجل (1-3 سنوات)',
            'medium_term' => 'متوسط الأجل (3-7 سنوات)',
            'long_term' => 'طويل الأجل (7+ سنوات)'
        ];
        
        return $terms[$this->investment_term] ?? $this->investment_term;
    }

    public function getFormattedExitStrategyAttribute()
    {
        $strategies = [
            'sale' => 'بيع العقار',
            'refinance' => 'إعادة التمويل',
            'buyback' => 'إعادة الشراء',
            'ipo' => 'طرح عام'
        ];
        
        return $strategies[$this->exit_strategy] ?? $this->exit_strategy;
    }

    public function getFormattedDistributionFrequencyAttribute()
    {
        $frequencies = [
            'monthly' => 'شهري',
            'quarterly' => 'ربع سنوي',
            'semi_annually' => 'نصف سنوي',
            'annually' => 'سنوي'
        ];
        
        return $frequencies[$this->distribution_frequency] ?? $this->distribution_frequency;
    }

    // Methods
    public function isVerified()
    {
        return $this->is_verified;
    }

    public function isActive()
    {
        return $this->token_status === 'active';
    }

    public function canInvest($amount)
    {
        return $amount >= $this->minimum_investment && 
               ($this->maximum_investment == 0 || $amount <= $this->maximum_investment);
    }

    public function calculateTokensForInvestment($amount)
    {
        return $amount / $this->price_per_token;
    }

    public function calculateInvestmentForTokens($tokens)
    {
        return $tokens * $this->price_per_token;
    }

    public function calculateExpectedReturns($investment, $years = 1)
    {
        $rentalReturns = $investment * ($this->rental_yield / 100) * $years;
        $appreciationReturns = $investment * ($this->appreciation_rate / 100) * $years;
        $totalFees = $investment * (($this->management_fee + $this->platform_fee) / 100) * $years;
        
        return [
            'rental_returns' => $rentalReturns,
            'appreciation_returns' => $appreciationReturns,
            'total_returns' => $rentalReturns + $appreciationReturns,
            'total_fees' => $totalFees,
            'net_returns' => $rentalReturns + $appreciationReturns - $totalFees,
            'roi_percentage' => (($rentalReturns + $appreciationReturns - $totalFees) / $investment) * 100
        ];
    }

    public function calculateMonthlyDistribution($tokens)
    {
        $investmentValue = $tokens * $this->price_per_token;
        $monthlyYield = $this->rental_yield / 12 / 100;
        $monthlyReturn = $investmentValue * $monthlyYield;
        
        return $monthlyReturn;
    }

    public function updateValuation($newValue, $valuationDate = null)
    {
        $oldValue = $this->total_value;
        $this->total_value = $newValue;
        $this->price_per_token = $newValue / $this->total_supply;
        $this->last_valuation_date = $valuationDate ?: now();
        
        // Update appreciation rate
        if ($oldValue > 0 && $this->tokenization_date) {
            $years = $this->tokenization_date->diffInDays(now()) / 365;
            if ($years > 0) {
                $this->appreciation_rate = (($newValue - $oldValue) / $oldValue / $years) * 100;
            }
        }
        
        $this->save();
    }

    public function getInvestmentMetrics()
    {
        return [
            'total_value' => $this->total_value,
            'price_per_token' => $this->price_per_token,
            'total_supply' => $this->total_supply,
            'tokens_sold' => $this->tokens_sold,
            'available_tokens' => $this->available_tokens,
            'total_investors' => $this->total_investors,
            'active_investors' => $this->active_investors,
            'total_invested' => $this->total_invested,
            'average_investment' => $this->average_investment_per_investor,
            'rental_yield' => $this->rental_yield,
            'appreciation_rate' => $this->appreciation_rate,
            'expected_roi' => $this->expected_roi,
            'risk_level' => $this->risk_level
        ];
    }

    public function getFeeStructure()
    {
        return [
            'management_fee' => $this->management_fee,
            'platform_fee' => $this->platform_fee,
            'legal_fee' => $this->legal_fee,
            'maintenance_fee' => $this->maintenance_fee,
            'insurance_fee' => $this->insurance_fee,
            'total_fees' => $this->management_fee + $this->platform_fee + $this->legal_fee + $this->maintenance_fee + $this->insurance_fee
        ];
    }

    public function getComplianceInfo()
    {
        return [
            'jurisdiction' => $this->jurisdiction,
            'regulatory_compliance' => $this->regulatory_compliance,
            'token_standard' => $this->token_standard,
            'verification_status' => $this->verification_status,
            'legal_documents' => $this->legal_documents,
            'is_verified' => $this->is_verified
        ];
    }

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Property::class, 'property_id');
    }

    public function smartContract(): BelongsTo
    {
        return $this->belongsTo(SmartContract::class, 'contract_address', 'address');
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'token_address', 'address');
    }

    public function investments(): HasMany
    {
        return $this->hasMany(PropertyInvestment::class, 'token_id');
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(PropertyDistribution::class, 'token_id');
    }

    public function valuations(): HasMany
    {
        return $this->hasMany(PropertyValuation::class, 'token_id');
    }

    // Static Methods
    public static function getStats()
    {
        return [
            'total_tokens' => self::count(),
            'active_tokens' => self::active()->count(),
            'verified_tokens' => self::verified()->count(),
            'total_value' => self::sum('total_value'),
            'total_investors' => self::sum('total_investors'),
            'active_investors' => self::sum('active_investors'),
            'total_invested' => self::sum('total_invested'),
            'total_distributed' => self::sum('total_distributed'),
            'average_rental_yield' => self::avg('rental_yield'),
            'average_appreciation_rate' => self::avg('appreciation_rate'),
            'average_expected_roi' => self::avg('expected_roi'),
            'tokens_today' => self::whereDate('created_at', today())->count(),
            'tokens_this_week' => self::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'tokens_this_month' => self::whereMonth('created_at', now()->month)->count(),
        ];
    }

    public static function getTopTokens($limit = 20)
    {
        return self::orderBy('total_value', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getHighestYieldTokens($limit = 20)
    {
        return self::orderBy('rental_yield', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getNewTokens($limit = 20)
    {
        return self::orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getVerifiedTokens($limit = 50)
    {
        return self::verified()
                   ->orderBy('total_value', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getTokensByRisk($level, $limit = 50)
    {
        return self::byRiskLevel($level)
                   ->orderBy('total_value', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getTokensByYield($min = 0, $max = null, $limit = 50)
    {
        return self::byYield($min, $max)
                   ->orderBy('rental_yield', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getTokensByInvestment($min = 0, $max = null, $limit = 50)
    {
        return self::byInvestment($min, $max)
                   ->orderBy('minimum_investment', 'asc')
                   ->limit($limit)
                   ->get();
    }

    public static function searchTokens($query, $limit = 50)
    {
        return self::where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('token_address', 'like', "%{$query}%");
                })
                ->orderBy('total_value', 'desc')
                ->limit($limit)
                ->get();
    }

    public static function getDailyTokenCount($days = 30)
    {
        return self::where('created_at', '>=', now()->subDays($days))
                   ->groupBy('date')
                   ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                   ->orderBy('date', 'desc')
                   ->get();
    }

    public static function getRiskDistribution()
    {
        return self::groupBy('risk_level')
                   ->selectRaw('risk_level, COUNT(*) as count')
                   ->orderBy('count', 'desc')
                   ->get();
    }

    public static function getYieldDistribution()
    {
        return self::selectRaw('
                CASE 
                    WHEN rental_yield < 3 THEN "< 3%"
                    WHEN rental_yield < 5 THEN "3-5%"
                    WHEN rental_yield < 8 THEN "5-8%"
                    WHEN rental_yield < 12 THEN "8-12%"
                    ELSE "> 12%"
                END as yield_range,
                COUNT(*) as count
            ')
            ->groupBy('yield_range')
            ->orderBy('count', 'desc')
            ->get();
    }

    public static function getInvestmentStats()
    {
        return [
            'total_value_tokenized' => self::sum('total_value'),
            'total_investors' => self::sum('total_investors'),
            'total_invested' => self::sum('total_invested'),
            'total_distributed' => self::sum('total_distributed'),
            'average_rental_yield' => self::avg('rental_yield'),
            'average_appreciation_rate' => self::avg('appreciation_rate'),
            'average_expected_roi' => self::avg('expected_roi'),
            'total_tokens' => self::count(),
            'active_tokens' => self::active()->count(),
        ];
    }

    // Export Methods
    public static function exportToCsv($tokens)
    {
        $headers = [
            'Name', 'Property ID', 'Token Address', 'Total Value', 'Price Per Token', 
            'Total Supply', 'Rental Yield', 'Expected ROI', 'Risk Level', 'Total Investors', 
            'Status', 'Verified', 'Created At'
        ];

        $rows = $tokens->map(function ($token) {
            return [
                $token->name,
                $token->property_id,
                $token->token_address,
                $token->formatted_total_value,
                $token->formatted_price_per_token,
                $token->formatted_total_supply,
                $token->formatted_rental_yield,
                $token->formatted_expected_roi,
                $token->risk_level_label,
                $token->total_investors,
                $token->status_label,
                $token->is_verified ? 'Yes' : 'No',
                $token->created_at
            ];
        });

        return collect([$headers])->concat($rows);
    }

    // Validation Methods
    public function validateToken()
    {
        $errors = [];
        
        if (empty($this->name)) {
            $errors[] = 'Token name is required';
        }
        
        if (empty($this->property_id)) {
            $errors[] = 'Property ID is required';
        }
        
        if (empty($this->token_address)) {
            $errors[] = 'Token address is required';
        }
        
        if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $this->token_address)) {
            $errors[] = 'Invalid token address format';
        }
        
        if ($this->total_supply <= 0) {
            $errors[] = 'Total supply must be positive';
        }
        
        if ($this->price_per_token <= 0) {
            $errors[] = 'Price per token must be positive';
        }
        
        if ($this->total_value <= 0) {
            $errors[] = 'Total value must be positive';
        }
        
        if ($this->fractional_ownership < 0 || $this->fractional_ownership > 100) {
            $errors[] = 'Fractional ownership must be between 0 and 100';
        }
        
        if ($this->minimum_investment <= 0) {
            $errors[] = 'Minimum investment must be positive';
        }
        
        if ($this->maximum_investment > 0 && $this->minimum_investment > $this->maximum_investment) {
            $errors[] = 'Minimum investment cannot exceed maximum investment';
        }
        
        if ($this->rental_yield < 0) {
            $errors[] = 'Rental yield must be positive';
        }
        
        if ($this->expected_roi < 0) {
            $errors[] = 'Expected ROI must be positive';
        }
        
        return $errors;
    }

    // Investment Operations
    public function invest($user, $amount)
    {
        if (!$this->canInvest($amount)) {
            throw new \Exception('Invalid investment amount');
        }
        
        $tokens = $this->calculateTokensForInvestment($amount);
        
        // Create investment record
        $investment = PropertyInvestment::create([
            'token_id' => $this->id,
            'user_address' => $user,
            'amount' => $amount,
            'tokens' => $tokens,
            'price_per_token' => $this->price_per_token,
            'invested_at' => now(),
            'status' => 'active'
        ]);
        
        // Update token stats
        $this->total_investors++;
        $this->active_investors++;
        $this->total_invested += $amount;
        $this->save();
        
        return $investment;
    }

    public function distributeReturns($amount, $type = 'rental')
    {
        // Create distribution record
        $distribution = PropertyDistribution::create([
            'token_id' => $this->id,
            'amount' => $amount,
            'type' => $type,
            'distributed_at' => now(),
            'status' => 'pending'
        ]);
        
        // Update token stats
        $this->total_distributed += $amount;
        $this->pending_distributions += $amount;
        $this->save();
        
        return $distribution;
    }

    public function getUserInvestment($userAddress)
    {
        return PropertyInvestment::where('token_id', $this->id)
                                ->where('user_address', $userAddress)
                                ->where('status', 'active')
                                ->first();
    }

    public function getUserStats($userAddress)
    {
        $investment = $this->getUserInvestment($userAddress);
        
        if (!$investment) {
            return [
                'investment' => null,
                'tokens' => 0,
                'investment_value' => 0,
                'ownership_percentage' => 0,
                'monthly_distribution' => 0,
                'total_distributions' => 0
            ];
        }

        $ownershipPercentage = ($investment->tokens / $this->total_supply) * 100;
        $monthlyDistribution = $this->calculateMonthlyDistribution($investment->tokens);
        $totalDistributions = PropertyDistribution::where('token_id', $this->id)
                                                 ->whereHas('investments', function($q) use ($userAddress) {
                                                     $q->where('user_address', $userAddress);
                                                 })
                                                 ->sum('amount');

        return [
            'investment' => $investment,
            'tokens' => $investment->tokens,
            'investment_value' => $investment->amount,
            'ownership_percentage' => $ownershipPercentage,
            'monthly_distribution' => $monthlyDistribution,
            'total_distributions' => $totalDistributions
        ];
    }
}

<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\AiFraudAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FraudDetectionService
{
    private const RISK_LEVELS = [
        'low' => 0.1,
        'medium' => 0.4,
        'high' => 0.7,
        'critical' => 0.9
    ];

    private const FRAUD_PATTERNS = [
        'rapid_transactions' => [
            'threshold' => 10, // transactions per hour
            'time_window' => 3600, // 1 hour
            'risk_score' => 0.6
        ],
        'unusual_amount' => [
            'threshold_multiplier' => 5, // 5x average
            'risk_score' => 0.7
        ],
        'suspicious_location' => [
            'risk_score' => 0.4
        ],
        'new_account_activity' => [
            'threshold_days' => 7,
            'risk_score' => 0.3
        ],
        'multiple_payment_methods' => [
            'threshold' => 3, // payment methods in 24h
            'risk_score' => 0.5
        ],
        'velocity_check' => [
            'max_attempts' => 5, // failed attempts
            'time_window' => 300, // 5 minutes
            'risk_score' => 0.8
        ],
        'device_fingerprint' => [
            'new_device_risk' => 0.2,
            'suspicious_device_risk' => 0.6
        ],
        'behavioral_analysis' => [
            'deviation_threshold' => 0.3, // 30% deviation
            'risk_score' => 0.4
        ]
    ];

    public function analyzeTransaction(array $transactionData): array
    {
        $riskFactors = [];
        $totalRiskScore = 0;

        // 1. Velocity Check
        $velocityRisk = $this->performVelocityCheck($transactionData);
        if ($velocityRisk['detected']) {
            $riskFactors[] = $velocityRisk;
            $totalRiskScore += $velocityRisk['risk_score'];
        }

        // 2. Amount Analysis
        $amountRisk = $this->analyzeAmount($transactionData);
        if ($amountRisk['detected']) {
            $riskFactors[] = $amountRisk;
            $totalRiskScore += $amountRisk['risk_score'];
        }

        // 3. Location Analysis
        $locationRisk = $this->analyzeLocation($transactionData);
        if ($locationRisk['detected']) {
            $riskFactors[] = $locationRisk;
            $totalRiskScore += $locationRisk['risk_score'];
        }

        // 4. Account Age Analysis
        $accountRisk = $this->analyzeAccountAge($transactionData);
        if ($accountRisk['detected']) {
            $riskFactors[] = $accountRisk;
            $totalRiskScore += $accountRisk['risk_score'];
        }

        // 5. Payment Method Analysis
        $paymentRisk = $this->analyzePaymentMethods($transactionData);
        if ($paymentRisk['detected']) {
            $riskFactors[] = $paymentRisk;
            $totalRiskScore += $paymentRisk['risk_score'];
        }

        // 6. Device Fingerprint Analysis
        $deviceRisk = $this->analyzeDeviceFingerprint($transactionData);
        if ($deviceRisk['detected']) {
            $riskFactors[] = $deviceRisk;
            $totalRiskScore += $deviceRisk['risk_score'];
        }

        // 7. Behavioral Analysis
        $behavioralRisk = $this->analyzeBehavior($transactionData);
        if ($behavioralRisk['detected']) {
            $riskFactors[] = $behavioralRisk;
            $totalRiskScore += $behavioralRisk['risk_score'];
        }

        // 8. Network Analysis
        $networkRisk = $this->analyzeNetwork($transactionData);
        if ($networkRisk['detected']) {
            $riskFactors[] = $networkRisk;
            $totalRiskScore += $networkRisk['risk_score'];
        }

        // Calculate final risk score
        $finalRiskScore = min($totalRiskScore, 1.0);
        $riskLevel = $this->determineRiskLevel($finalRiskScore);

        // Create fraud alert if needed
        if ($finalRiskScore > 0.3) {
            $this->createFraudAlert($transactionData, $riskFactors, $finalRiskScore, $riskLevel);
        }

        return [
            'risk_score' => $finalRiskScore,
            'risk_level' => $riskLevel,
            'risk_factors' => $riskFactors,
            'recommendation' => $this->getRecommendation($riskLevel),
            'requires_review' => $finalRiskScore > 0.5,
            'should_block' => $finalRiskScore > 0.8,
            'analysis_timestamp' => now()->toISOString(),
        ];
    }

    private function performVelocityCheck(array $transactionData): array
    {
        $userId = $transactionData['user_id'] ?? null;
        if (!$userId) return ['detected' => false];

        $recentTransactions = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours(1))
            ->count();

        $threshold = self::FRAUD_PATTERNS['rapid_transactions']['threshold'];
        
        if ($recentTransactions >= $threshold) {
            return [
                'detected' => true,
                'type' => 'velocity_check',
                'description' => "User has made {$recentTransactions} transactions in the last hour",
                'risk_score' => self::FRAUD_PATTERNS['rapid_transactions']['risk_score'],
                'severity' => 'high'
            ];
        }

        return ['detected' => false];
    }

    private function analyzeAmount(array $transactionData): array
    {
        $amount = $transactionData['amount'] ?? 0;
        $userId = $transactionData['user_id'] ?? null;

        if (!$userId || $amount <= 0) return ['detected' => false];

        // Get user's average transaction amount
        $avgAmount = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->avg('amount') ?? 0;

        $thresholdMultiplier = self::FRAUD_PATTERNS['unusual_amount']['threshold_multiplier'];
        
        if ($avgAmount > 0 && $amount > ($avgAmount * $thresholdMultiplier)) {
            return [
                'detected' => true,
                'type' => 'unusual_amount',
                'description' => "Transaction amount ({$amount}) is {$thresholdMultiplier}x user's average ({$avgAmount})",
                'risk_score' => self::FRAUD_PATTERNS['unusual_amount']['risk_score'],
                'severity' => 'high',
                'data' => [
                    'transaction_amount' => $amount,
                    'average_amount' => $avgAmount,
                    'multiplier' => $amount / $avgAmount
                ]
            ];
        }

        return ['detected' => false];
    }

    private function analyzeLocation(array $transactionData): array
    {
        $ipAddress = $transactionData['ip_address'] ?? null;
        $userId = $transactionData['user_id'] ?? null;

        if (!$ipAddress || !$userId) return ['detected' => false];

        // Get user's recent locations
        $recentLocations = DB::table('user_activity_logs')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(7))
            ->where('ip_address', '!=', $ipAddress)
            ->distinct('ip_address')
            ->pluck('ip_address');

        // Check if this IP is new for the user
        $isNewLocation = !$recentLocations->contains($ipAddress);

        // Check for suspicious IP ranges
        $isSuspiciousIP = $this->isSuspiciousIP($ipAddress);

        if ($isNewLocation || $isSuspiciousIP) {
            return [
                'detected' => true,
                'type' => 'suspicious_location',
                'description' => $isNewLocation ? 
                    "Transaction from new IP address: {$ipAddress}" :
                    "Transaction from suspicious IP address: {$ipAddress}",
                'risk_score' => self::FRAUD_PATTERNS['suspicious_location']['risk_score'],
                'severity' => $isSuspiciousIP ? 'high' : 'medium',
                'data' => [
                    'ip_address' => $ipAddress,
                    'is_new_location' => $isNewLocation,
                    'is_suspicious' => $isSuspiciousIP
                ]
            ];
        }

        return ['detected' => false];
    }

    private function analyzeAccountAge(array $transactionData): array
    {
        $userId = $transactionData['user_id'] ?? null;

        if (!$userId) return ['detected' => false];

        $user = User::find($userId);
        if (!$user) return ['detected' => false];

        $accountAge = now()->diffInDays($user->created_at);
        $thresholdDays = self::FRAUD_PATTERNS['new_account_activity']['threshold_days'];

        if ($accountAge <= $thresholdDays) {
            return [
                'detected' => true,
                'type' => 'new_account_activity',
                'description' => "Account is only {$accountAge} days old",
                'risk_score' => self::FRAUD_PATTERNS['new_account_activity']['risk_score'],
                'severity' => 'medium',
                'data' => [
                    'account_age_days' => $accountAge,
                    'threshold_days' => $thresholdDays
                ]
            ];
        }

        return ['detected' => false];
    }

    private function analyzePaymentMethods(array $transactionData): array
    {
        $userId = $transactionData['user_id'] ?? null;

        if (!$userId) return ['detected' => false];

        $recentPaymentMethods = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours(24))
            ->distinct('payment_method')
            ->count();

        $threshold = self::FRAUD_PATTERNS['multiple_payment_methods']['threshold'];

        if ($recentPaymentMethods >= $threshold) {
            return [
                'detected' => true,
                'type' => 'multiple_payment_methods',
                'description' => "User has used {$recentPaymentMethods} different payment methods in 24 hours",
                'risk_score' => self::FRAUD_PATTERNS['multiple_payment_methods']['risk_score'],
                'severity' => 'medium',
                'data' => [
                    'payment_methods_count' => $recentPaymentMethods,
                    'threshold' => $threshold
                ]
            ];
        }

        return ['detected' => false];
    }

    private function analyzeDeviceFingerprint(array $transactionData): array
    {
        $deviceFingerprint = $transactionData['device_fingerprint'] ?? null;
        $userId = $transactionData['user_id'] ?? null;

        if (!$deviceFingerprint || !$userId) return ['detected' => false];

        // Check if this is a known device for the user
        $knownDevice = DB::table('user_devices')
            ->where('user_id', $userId)
            ->where('device_fingerprint', $deviceFingerprint)
            ->exists();

        if (!$knownDevice) {
            return [
                'detected' => true,
                'type' => 'new_device',
                'description' => "Transaction from unrecognized device",
                'risk_score' => self::FRAUD_PATTERNS['device_fingerprint']['new_device_risk'],
                'severity' => 'low',
                'data' => [
                    'device_fingerprint' => $deviceFingerprint,
                    'is_known_device' => false
                ]
            ];
        }

        return ['detected' => false];
    }

    private function analyzeBehavior(array $transactionData): array
    {
        $userId = $transactionData['user_id'] ?? null;

        if (!$userId) return ['detected' => false];

        // Analyze transaction timing patterns
        $currentHour = now()->hour;
        $userTransactionHours = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $avgTransactionsAtThisHour = $userTransactionHours->get($currentHour, 0);

        // Check if current activity deviates from normal pattern
        $totalTransactions = $userTransactionHours->sum();
        $expectedTransactionsAtThisHour = $totalTransactions / 24; // Average per hour

        $deviationThreshold = self::FRAUD_PATTERNS['behavioral_analysis']['deviation_threshold'];
        
        if ($expectedTransactionsAtThisHour > 0) {
            $deviation = abs($avgTransactionsAtThisHour - $expectedTransactionsAtThisHour) / $expectedTransactionsAtThisHour;
            
            if ($deviation > $deviationThreshold) {
                return [
                    'detected' => true,
                    'type' => 'behavioral_anomaly',
                    'description' => "Transaction timing deviates from user's normal pattern",
                    'risk_score' => self::FRAUD_PATTERNS['behavioral_analysis']['risk_score'],
                    'severity' => 'medium',
                    'data' => [
                        'current_hour' => $currentHour,
                        'actual_transactions' => $avgTransactionsAtThisHour,
                        'expected_transactions' => $expectedTransactionsAtThisHour,
                        'deviation' => $deviation
                    ]
                ];
            }
        }

        return ['detected' => false];
    }

    private function analyzeNetwork(array $transactionData): array
    {
        $ipAddress = $transactionData['ip_address'] ?? null;

        if (!$ipAddress) return ['detected' => false];

        // Check for multiple accounts from same IP
        $accountsFromIP = DB::table('transactions')
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subHours(1))
            ->distinct('user_id')
            ->count();

        if ($accountsFromIP > 3) {
            return [
                'detected' => true,
                'type' => 'network_anomaly',
                'description' => "Multiple accounts ({$accountsFromIP}) from same IP in last hour",
                'risk_score' => 0.6,
                'severity' => 'high',
                'data' => [
                    'ip_address' => $ipAddress,
                    'accounts_count' => $accountsFromIP
                ]
            ];
        }

        return ['detected' => false];
    }

    private function isSuspiciousIP(string $ipAddress): bool
    {
        // Check against known suspicious IP ranges
        $suspiciousRanges = [
            '10.0.0.0/8',     // Private networks
            '172.16.0.0/12',  // Private networks
            '192.168.0.0/16', // Private networks
            // Add known proxy/VPN ranges
        ];

        foreach ($suspiciousRanges as $range) {
            if ($this->ipInRange($ipAddress, $range)) {
                return true;
            }
        }

        // Check against known malicious IPs (cache for performance)
        $maliciousIPs = Cache::remember('malicious_ips', 3600, function() {
            return $this->getMaliciousIPs();
        });

        return in_array($ipAddress, $maliciousIPs);
    }

    private function ipInRange(string $ip, string $range): bool
    {
        // Simple IP range check implementation
        [$subnet, $mask] = explode('/', $range);
        $subnetLong = ip2long($subnet);
        $ipLong = ip2long($ip);
        $maskLong = -1 << (32 - $mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    private function getMaliciousIPs(): array
    {
        // In production, this would integrate with threat intelligence APIs
        return [
            '192.168.1.100',
            '10.0.0.50',
            // Add known malicious IPs
        ];
    }

    private function determineRiskLevel(float $score): string
    {
        if ($score >= self::RISK_LEVELS['critical']) return 'critical';
        if ($score >= self::RISK_LEVELS['high']) return 'high';
        if ($score >= self::RISK_LEVELS['medium']) return 'medium';
        return 'low';
    }

    private function getRecommendation(string $riskLevel): string
    {
        return match($riskLevel) {
            'critical' => 'Block transaction and require manual review',
            'high' => 'Require additional verification',
            'medium' => 'Flag for review',
            'low' => 'Allow transaction',
            default => 'Allow transaction'
        };
    }

    private function createFraudAlert(array $transactionData, array $riskFactors, float $riskScore, string $riskLevel): void
    {
        try {
            AiFraudAlert::create([
                'property_id' => $transactionData['property_id'] ?? null, // Can be null for general transactions
                'user_id' => $transactionData['user_id'],
                'alert_type' => 'payment_fraud',
                'risk_level' => $riskLevel,
                'description' => "High risk transaction detected. Score: {$riskScore}",
                'evidence' => $riskFactors,
                'confidence_score' => $riskScore * 100,
                'status' => 'pending',
                'created_by' => 1, // System
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create fraud alert', ['error' => $e->getMessage()]);
        }
    }

    public function getFraudStatistics(): array
    {
        $last24Hours = now()->subHours(24);
        $last7Days = now()->subDays(7);

        return [
            'last_24_hours' => [
                'total_alerts' => AiFraudAlert::where('created_at', '>=', $last24Hours)->count(),
                'critical_alerts' => AiFraudAlert::where('created_at', '>=', $last24Hours)->where('risk_level', 'critical')->count(),
                'high_risk_alerts' => AiFraudAlert::where('created_at', '>=', $last24Hours)->where('risk_level', 'high')->count(),
                'auto_blocked' => AiFraudAlert::where('created_at', '>=', $last24Hours)->where('auto_blocked', true)->count(),
            ],
            'last_7_days' => [
                'total_alerts' => AiFraudAlert::where('created_at', '>=', $last7Days)->count(),
                'resolved_alerts' => AiFraudAlert::where('created_at', '>=', $last7Days)->where('status', 'resolved')->count(),
                'pending_review' => AiFraudAlert::where('created_at', '>=', $last7Days)->where('status', 'pending')->count(),
            ],
            'top_risk_factors' => $this->getTopRiskFactors(),
            'trending_patterns' => $this->getTrendingFraudPatterns(),
        ];
    }

    private function getTopRiskFactors(): array
    {
        return AiFraudAlert::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('JSON_UNQUOTE(JSON_EXTRACT(risk_factors, "$[0].type")) as factor_type, COUNT(*) as count')
            ->groupBy('factor_type')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getTrendingFraudPatterns(): array
    {
        // Analyze trending fraud patterns
        return [
            'rapid_transactions' => $this->getPatternTrend('velocity_check'),
            'unusual_amounts' => $this->getPatternTrend('unusual_amount'),
            'suspicious_locations' => $this->getPatternTrend('suspicious_location'),
            'new_devices' => $this->getPatternTrend('new_device'),
        ];
    }

    private function getPatternTrend(string $patternType): array
    {
        $last7Days = AiFraudAlert::where('created_at', '>=', now()->subDays(7))
            ->whereJsonContains('risk_factors->type', $patternType)
            ->count();

        $previous7Days = AiFraudAlert::where('created_at', '>=', now()->subDays(14))
            ->where('created_at', '<', now()->subDays(7))
            ->whereJsonContains('risk_factors->type', $patternType)
            ->count();

        $trend = $previous7Days > 0 ? (($last7Days - $previous7Days) / $previous7Days) * 100 : 0;

        return [
            'current_period' => $last7Days,
            'previous_period' => $previous7Days,
            'trend_percent' => $trend,
            'trend_direction' => $trend > 0 ? 'increasing' : ($trend < 0 ? 'decreasing' : 'stable')
        ];
    }
}

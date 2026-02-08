<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CurrencyService
{
    private const SUPPORTED_CURRENCIES = [
        'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'code' => 'USD', 'precision' => 2],
        'EUR' => ['name' => 'Euro', 'symbol' => '€', 'code' => 'EUR', 'precision' => 2],
        'GBP' => ['name' => 'British Pound', 'symbol' => '£', 'code' => 'GBP', 'precision' => 2],
        'JPY' => ['name' => 'Japanese Yen', 'symbol' => '¥', 'code' => 'JPY', 'precision' => 0],
        'CNY' => ['name' => 'Chinese Yuan', 'symbol' => '¥', 'code' => 'CNY', 'precision' => 2],
        'AUD' => ['name' => 'Australian Dollar', 'symbol' => 'A$', 'code' => 'AUD', 'precision' => 2],
        'CAD' => ['name' => 'Canadian Dollar', 'symbol' => 'C$', 'code' => 'CAD', 'precision' => 2],
        'CHF' => ['name' => 'Swiss Franc', 'symbol' => 'Fr', 'code' => 'CHF', 'precision' => 2],
        'HKD' => ['name' => 'Hong Kong Dollar', 'symbol' => 'HK$', 'code' => 'HKD', 'precision' => 2],
        'SGD' => ['name' => 'Singapore Dollar', 'symbol' => 'S$', 'code' => 'SGD', 'precision' => 2],
        'SAR' => ['name' => 'Saudi Riyal', 'symbol' => '﷼', 'code' => 'SAR', 'precision' => 2],
        'AED' => ['name' => 'UAE Dirham', 'symbol' => 'د.إ', 'code' => 'AED', 'precision' => 2],
        'INR' => ['name' => 'Indian Rupee', 'symbol' => '₹', 'code' => 'INR', 'precision' => 2],
        'BRL' => ['name' => 'Brazilian Real', 'symbol' => 'R$', 'code' => 'BRL', 'precision' => 2],
        'ZAR' => ['name' => 'South African Rand', 'symbol' => 'R', 'code' => 'ZAR', 'precision' => 2],
        'RUB' => ['name' => 'Russian Ruble', 'symbol' => '₽', 'code' => 'RUB', 'precision' => 2],
        'MXN' => ['name' => 'Mexican Peso', 'symbol' => '$', 'code' => 'MXN', 'precision' => 2],
        'KRW' => ['name' => 'South Korean Won', 'symbol' => '₩', 'code' => 'KRW', 'precision' => 0],
        'TRY' => ['name' => 'Turkish Lira', 'symbol' => '₺', 'code' => 'TRY', 'precision' => 2],
        'EGP' => ['name' => 'Egyptian Pound', 'symbol' => 'E£', 'code' => 'EGP', 'precision' => 2],
        'THB' => ['name' => 'Thai Baht', 'symbol' => '฿', 'code' => 'THB', 'precision' => 2],
        'IDR' => ['name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'code' => 'IDR', 'precision' => 0],
        'MYR' => ['name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'code' => 'MYR', 'precision' => 2],
        'PHP' => ['name' => 'Philippine Peso', 'symbol' => '₱', 'code' => 'PHP', 'precision' => 2],
        'PLN' => ['name' => 'Polish Zloty', 'symbol' => 'zł', 'code' => 'PLN', 'precision' => 2],
        'NOK' => ['name' => 'Norwegian Krone', 'symbol' => 'kr', 'code' => 'NOK', 'precision' => 2],
        'SEK' => ['name' => 'Swedish Krona', 'symbol' => 'kr', 'code' => 'SEK', 'precision' => 2],
        'DKK' => ['name' => 'Danish Krone', 'symbol' => 'kr', 'code' => 'DKK', 'precision' => 2],
        'CZK' => ['name' => 'Czech Koruna', 'symbol' => 'Kč', 'code' => 'CZK', 'precision' => 2],
        'HUF' => ['name' => 'Hungarian Forint', 'symbol' => 'Ft', 'code' => 'HUF', 'precision' => 0],
        'ILS' => ['name' => 'Israeli Shekel', 'symbol' => '₪', 'code' => 'ILS', 'precision' => 2],
        'NGN' => ['name' => 'Nigerian Naira', 'symbol' => '₦', 'code' => 'NGN', 'precision' => 2],
        'KES' => ['name' => 'Kenyan Shilling', 'symbol' => 'KSh', 'code' => 'KES', 'precision' => 2],
        'GHS' => ['name' => 'Ghanaian Cedi', 'symbol' => 'GH₵', 'code' => 'GHS', 'precision' => 2],
        'UGX' => ['name' => 'Ugandan Shilling', 'symbol' => 'USh', 'code' => 'UGX', 'precision' => 0],
        'TZS' => ['name' => 'Tanzanian Shilling', 'symbol' => 'TSh', 'code' => 'TZS', 'precision' => 0],
        'ZMW' => ['name' => 'Zambian Kwacha', 'symbol' => 'ZK', 'code' => 'ZMW', 'precision' => 2],
        'BWP' => ['name' => 'Botswana Pula', 'symbol' => 'P', 'code' => 'BWP', 'precision' => 2],
        'NAD' => ['name' => 'Namibian Dollar', 'symbol' => 'N$', 'code' => 'NAD', 'precision' => 2],
        'MUR' => ['name' => 'Mauritian Rupee', 'symbol' => '₨', 'code' => 'MUR', 'precision' => 2],
    ];

    private const EXCHANGE_RATE_PROVIDERS = [
        'fixer' => [
            'endpoint' => 'https://api.fixer.io/latest',
            'base_currency' => 'EUR',
            'free_limit' => 1000,
            'premium_limit' => 10000
        ],
        'exchangerate-api' => [
            'endpoint' => 'https://v6.exchangerate-api.com/v6/latest',
            'base_currency' => 'USD',
            'free_limit' => 2000,
            'premium_limit' => 10000
        ],
        'currencylayer' => [
            'endpoint' => 'https://api.currencylayer.com/live',
            'base_currency' => 'USD',
            'free_limit' => 1000,
            'premium_limit' => 10000
        ],
        'openexchangerates' => [
            'endpoint' => 'https://openexchangerates.org/api/latest.json',
            'base_currency' => 'USD',
            'free_limit' => 1000,
            'premium_limit' => 10000
        ]
    ];

    private const CACHE_DURATION = 3600; // 1 hour
    private const FALLBACK_CACHE_DURATION = 86400; // 24 hours

    public function convertCurrency(float $amount, string $fromCurrency, string $toCurrency): array
    {
        try {
            if ($fromCurrency === $toCurrency) {
                return [
                    'success' => true,
                    'original_amount' => $amount,
                    'converted_amount' => $amount,
                    'from_currency' => $fromCurrency,
                    'to_currency' => $toCurrency,
                    'exchange_rate' => 1.0,
                    'converted_at' => now()->toISOString()
                ];
            }

            // Validate currencies
            if (!$this->isCurrencySupported($fromCurrency) || !$this->isCurrencySupported($toCurrency)) {
                return [
                    'success' => false,
                    'message' => 'One or both currencies are not supported'
                ];
            }

            // Get exchange rate
            $rate = $this->getExchangeRate($fromCurrency, $toCurrency);
            
            if (!$rate) {
                return [
                    'success' => false,
                    'message' => 'Exchange rate not available'
                ];
            }

            // Convert amount
            $convertedAmount = $amount * $rate;
            
            // Format based on target currency precision
            $precision = self::SUPPORTED_CURRENCIES[$toCurrency]['precision'];
            $formattedAmount = number_format($convertedAmount, $precision, '.', ',');

            return [
                'success' => true,
                'original_amount' => $amount,
                'converted_amount' => $convertedAmount,
                'formatted_amount' => $formattedAmount,
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'exchange_rate' => $rate,
                'rate_source' => $this->getCurrentRateSource(),
                'converted_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Currency conversion failed', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'from' => $fromCurrency,
                'to' => $toCurrency
            ]);

            return [
                'success' => false,
                'message' => 'Currency conversion failed',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getExchangeRate(string $fromCurrency, string $toCurrency): ?float
    {
        try {
            // Check cache first
            $cacheKey = "exchange_rate_{$fromCurrency}_{$toCurrency}";
            $cachedRate = Cache::get($cacheKey);
            
            if ($cachedRate !== null) {
                return $cachedRate;
            }

            // Get rate from database or API
            $rate = $this->fetchExchangeRate($fromCurrency, $toCurrency);
            
            if ($rate) {
                // Cache the rate
                Cache::put($cacheKey, $rate, self::CACHE_DURATION);
                
                // Store in database
                $this->storeExchangeRate($fromCurrency, $toCurrency, $rate);
            }

            return $rate;
        } catch (\Exception $e) {
            Log::error('Failed to get exchange rate', [
                'error' => $e->getMessage(),
                'from' => $fromCurrency,
                'to' => $toCurrency
            ]);

            return null;
        }
    }

    public function updateExchangeRates(): array
    {
        try {
            $results = [];
            $baseCurrency = config('currency.base_currency', 'USD');
            $provider = $this->getActiveProvider();
            
            // Fetch rates from API
            $apiRates = $this->fetchRatesFromAPI($provider, $baseCurrency);
            
            if (!$apiRates) {
                return [
                    'success' => false,
                    'message' => 'Failed to fetch rates from API'
                ];
            }

            $updatedCount = 0;
            $errorCount = 0;

            // Update each currency rate
            foreach (self::SUPPORTED_CURRENCIES as $currencyCode => $currencyInfo) {
                if ($currencyCode === $baseCurrency) {
                    continue;
                }

                try {
                    $rate = $apiRates[$currencyCode] ?? null;
                    
                    if ($rate) {
                        $this->storeExchangeRate($baseCurrency, $currencyCode, $rate);
                        
                        // Cache the rate
                        $cacheKey = "exchange_rate_{$baseCurrency}_{$currencyCode}";
                        Cache::put($cacheKey, $rate, self::CACHE_DURATION);
                        
                        $updatedCount++;
                    } else {
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error("Failed to update rate for {$currencyCode}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Update last update timestamp
            Cache::put('exchange_rates_last_update', now(), self::CACHE_DURATION);

            return [
                'success' => true,
                'updated_count' => $updatedCount,
                'error_count' => $errorCount,
                'total_currencies' => count(self::SUPPORTED_CURRENCIES) - 1,
                'provider' => $provider,
                'updated_at' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to update exchange rates', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update exchange rates',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getCurrencyInfo(string $currencyCode): ?array
    {
        return self::SUPPORTED_CURRENCIES[$currencyCode] ?? null;
    }

    public function getSupportedCurrencies(): array
    {
        return self::SUPPORTED_CURRENCIES;
    }

    public function formatCurrency(float $amount, string $currencyCode, bool $includeSymbol = true): string
    {
        $currencyInfo = $this->getCurrencyInfo($currencyCode);
        
        if (!$currencyInfo) {
            return number_format($amount, 2);
        }

        $precision = $currencyInfo['precision'];
        $formattedAmount = number_format($amount, $precision, '.', ',');
        
        if ($includeSymbol) {
            $symbol = $currencyInfo['symbol'];
            
            // Determine symbol position based on currency
            $symbolBefore = in_array($currencyCode, ['USD', 'EUR', 'GBP', 'AUD', 'CAD', 'HKD', 'SGD', 'NZD', 'USD']);
            
            return $symbolBefore ? $symbol . $formattedAmount : $formattedAmount . ' ' . $symbol;
        }

        return $formattedAmount;
    }

    public function getUserPreferredCurrency(int $userId): string
    {
        $user = User::find($userId);
        
        if ($user && $user->preferred_currency) {
            return $user->preferred_currency;
        }

        // Try to detect from user's location
        $detectedCurrency = $this->detectCurrencyFromLocation($user->country ?? null);
        
        if ($detectedCurrency) {
            return $detectedCurrency;
        }

        // Fall back to default
        return config('currency.default', 'USD');
    }

    public function setUserPreferredCurrency(int $userId, string $currencyCode): array
    {
        try {
            if (!$this->isCurrencySupported($currencyCode)) {
                return [
                    'success' => false,
                    'message' => 'Currency not supported'
                ];
            }

            $user = User::findOrFail($userId);
            $user->update(['preferred_currency' => $currencyCode]);

            return [
                'success' => true,
                'currency' => $currencyCode,
                'message' => 'Preferred currency updated'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to set user preferred currency', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'currency' => $currencyCode
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update preferred currency',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getExchangeRateHistory(string $fromCurrency, string $toCurrency, int $days = 30): array
    {
        try {
            $endDate = now();
            $startDate = $endDate->copy()->subDays($days);

            $rates = CurrencyRate::where('from_currency', $fromCurrency)
                ->where('to_currency', $toCurrency)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'asc')
                ->get();

            return [
                'success' => true,
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'period_days' => $days,
                'rates' => $rates->map(function($rate) {
                    return [
                        'date' => $rate->date->toDateString(),
                        'rate' => $rate->rate,
                        'source' => $rate->source
                    ];
                }),
                'statistics' => $this->calculateRateStatistics($rates)
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get exchange rate history', [
                'error' => $e->getMessage(),
                'from' => $fromCurrency,
                'to' => $toCurrency,
                'days' => $days
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get rate history',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getCurrencyStatistics(): array
    {
        try {
            $baseCurrency = config('currency.base_currency', 'USD');
            $lastUpdate = Cache::get('exchange_rates_last_update');
            
            $stats = [
                'base_currency' => $baseCurrency,
                'supported_currencies_count' => count(self::SUPPORTED_CURRENCIES),
                'last_update' => $lastUpdate ? $lastUpdate->toISOString() : null,
                'update_status' => $this->getUpdateStatus(),
                'active_provider' => $this->getActiveProvider(),
                'cache_status' => $this->getCacheStatus(),
                'most_popular_currencies' => $this->getMostPopularCurrencies(),
                'rate_volatility' => $this->getRateVolatility(),
                'conversion_volume' => $this->getConversionVolume()
            ];

            return [
                'success' => true,
                'statistics' => $stats
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get currency statistics', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ];
        }
    }

    public function processTransactionInCurrency(array $transactionData): array
    {
        try {
            $amount = $transactionData['amount'];
            $transactionCurrency = $transactionData['currency'];
            $userCurrency = $transactionData['user_currency'] ?? $this->getUserPreferredCurrency($transactionData['user_id']);
            
            // Convert to user's preferred currency
            if ($transactionCurrency !== $userCurrency) {
                $conversion = $this->convertCurrency($amount, $transactionCurrency, $userCurrency);
                
                if (!$conversion['success']) {
                    return [
                        'success' => false,
                        'message' => 'Currency conversion failed',
                        'error' => $conversion['message']
                    ];
                }

                $convertedAmount = $conversion['converted_amount'];
                $exchangeRate = $conversion['exchange_rate'];
            } else {
                $convertedAmount = $amount;
                $exchangeRate = 1.0;
            }

            // Store transaction with currency info
            $transaction = Transaction::create([
                'user_id' => $transactionData['user_id'],
                'amount' => $amount,
                'currency' => $transactionCurrency,
                'converted_amount' => $convertedAmount,
                'user_currency' => $userCurrency,
                'exchange_rate' => $exchangeRate,
                'type' => $transactionData['type'],
                'status' => 'completed',
                'metadata' => array_merge($transactionData['metadata'] ?? [], [
                    'conversion_applied' => $transactionCurrency !== $userCurrency,
                    'original_currency' => $transactionCurrency,
                    'exchange_rate_source' => $this->getCurrentRateSource()
                ]),
                'created_at' => now()
            ]);

            return [
                'success' => true,
                'transaction' => $transaction,
                'original_amount' => $amount,
                'converted_amount' => $convertedAmount,
                'exchange_rate' => $exchangeRate,
                'currency_conversion' => $transactionCurrency !== $userCurrency
            ];
        } catch (\Exception $e) {
            Log::error('Failed to process transaction in currency', [
                'error' => $e->getMessage(),
                'transaction_data' => $transactionData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process transaction',
                'error' => $e->getMessage()
            ];
        }
    }

    // Private helper methods
    private function isCurrencySupported(string $currencyCode): bool
    {
        return isset(self::SUPPORTED_CURRENCIES[$currencyCode]);
    }

    private function fetchExchangeRate(string $fromCurrency, string $toCurrency): ?float
    {
        // Try database first
        $rate = CurrencyRate::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('date', now()->toDateString())
            ->value('rate');

        if ($rate) {
            return $rate;
        }

        // Try API
        $provider = $this->getActiveProvider();
        $apiRates = $this->fetchRatesFromAPI($provider, $fromCurrency);
        
        return $apiRates[$toCurrency] ?? null;
    }

    private function fetchRatesFromAPI(string $provider, string $baseCurrency): ?array
    {
        try {
            $providerConfig = self::EXCHANGE_RATE_PROVIDERS[$provider];
            $apiKey = config("currency.providers.{$provider}.api_key");
            
            if (!$apiKey) {
                Log::warning("No API key configured for provider: {$provider}");
                return null;
            }

            $endpoint = $providerConfig['endpoint'];
            $url = "{$endpoint}?access_key={$apiKey}&base={$baseCurrency}";

            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                Log::error("API request failed for provider: {$provider}", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();
            
            // Parse response based on provider
            return $this->parseAPIResponse($provider, $data);
        } catch (\Exception $e) {
            Log::error("Failed to fetch rates from API: {$provider}", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function parseAPIResponse(string $provider, array $data): ?array
    {
        switch ($provider) {
            case 'fixer':
                return $data['rates'] ?? null;
                
            case 'exchangerate-api':
                return $data['conversion_rates'] ?? null;
                
            case 'currencylayer':
                return $data['quotes'] ?? null;
                
            case 'openexchangerates':
                return $data['rates'] ?? null;
                
            default:
                return null;
        }
    }

    private function storeExchangeRate(string $fromCurrency, string $toCurrency, float $rate): void
    {
        CurrencyRate::updateOrCreate(
            [
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'date' => now()->toDateString()
            ],
            [
                'rate' => $rate,
                'source' => $this->getCurrentRateSource(),
                'updated_at' => now()
            ]
        );
    }

    private function getActiveProvider(): string
    {
        return config('currency.active_provider', 'exchangerate-api');
    }

    private function getCurrentRateSource(): string
    {
        return $this->getActiveProvider();
    }

    private function detectCurrencyFromLocation(?string $country): ?string
    {
        if (!$country) {
            return null;
        }

        $countryCurrencyMap = [
            'US' => 'USD',
            'GB' => 'GBP',
            'JP' => 'JPY',
            'CN' => 'CNY',
            'AU' => 'AUD',
            'CA' => 'CAD',
            'CH' => 'CHF',
            'HK' => 'HKD',
            'SG' => 'SGD',
            'SA' => 'SAR',
            'AE' => 'AED',
            'IN' => 'INR',
            'BR' => 'BRL',
            'ZA' => 'ZAR',
            'RU' => 'RUB',
            'MX' => 'MXN',
            'KR' => 'KRW',
            'TR' => 'TRY',
            'EG' => 'EGP',
            'TH' => 'THB',
            'ID' => 'IDR',
            'MY' => 'MYR',
            'PH' => 'PHP',
            'PL' => 'PLN',
            'NO' => 'NOK',
            'SE' => 'SEK',
            'DK' => 'DKK',
            'CZ' => 'CZK',
            'HU' => 'HUF',
            'IL' => 'ILS',
            'NG' => 'NGN',
            'KE' => 'KES',
            'GH' => 'GHS',
            'UG' => 'UGX',
            'TZ' => 'TZS',
            'ZM' => 'ZMW',
            'BW' => 'BWP',
            'NA' => 'NAD',
            'MU' => 'MUR'
        ];

        return $countryCurrencyMap[$country] ?? null;
    }

    private function calculateRateStatistics($rates): array
    {
        if ($rates->isEmpty()) {
            return [];
        }

        $rateValues = $rates->pluck('rate')->toArray();
        
        return [
            'min_rate' => min($rateValues),
            'max_rate' => max($rateValues),
            'avg_rate' => array_sum($rateValues) / count($rateValues),
            'volatility' => $this->calculateVolatility($rateValues),
            'trend' => $this->calculateTrend($rateValues)
        ];
    }

    private function calculateVolatility(array $rates): float
    {
        if (count($rates) < 2) {
            return 0;
        }

        $mean = array_sum($rates) / count($rates);
        $squaredDiffs = array_map(fn($rate) => pow($rate - $mean, 2), $rates);
        $variance = array_sum($squaredDiffs) / count($rates);
        
        return sqrt($variance);
    }

    private function calculateTrend(array $rates): string
    {
        if (count($rates) < 2) {
            return 'stable';
        }

        $firstHalf = array_slice($rates, 0, floor(count($rates) / 2));
        $secondHalf = array_slice($rates, floor(count($rates) / 2));

        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);

        if ($secondAvg > $firstAvg * 1.02) return 'increasing';
        if ($secondAvg < $firstAvg * 0.98) return 'decreasing';
        return 'stable';
    }

    private function getUpdateStatus(): string
    {
        $lastUpdate = Cache::get('exchange_rates_last_update');
        
        if (!$lastUpdate) {
            return 'never_updated';
        }

        $hoursSinceUpdate = $lastUpdate->diffInHours(now());
        
        if ($hoursSinceUpdate < 1) return 'recent';
        if ($hoursSinceUpdate < 6) return 'good';
        if ($hoursSinceUpdate < 24) return 'stale';
        return 'very_stale';
    }

    private function getCacheStatus(): array
    {
        $cacheKeys = [];
        
        foreach (self::SUPPORTED_CURRENCIES as $fromCurrency => $fromInfo) {
            foreach (self::SUPPORTED_CURRENCIES as $toCurrency => $toInfo) {
                if ($fromCurrency !== $toCurrency) {
                    $cacheKeys[] = "exchange_rate_{$fromCurrency}_{$toCurrency}";
                }
            }
        }

        $cachedCount = 0;
        $totalPairs = count($cacheKeys);

        foreach ($cacheKeys as $key) {
            if (Cache::has($key)) {
                $cachedCount++;
            }
        }

        return [
            'cached_pairs' => $cachedCount,
            'total_pairs' => $totalPairs,
            'cache_hit_rate' => $totalPairs > 0 ? ($cachedCount / $totalPairs) * 100 : 0
        ];
    }

    private function getMostPopularCurrencies(): array
    {
        // This would typically be based on actual usage data
        return [
            'USD' => ['usage_count' => 1000, 'percentage' => 35.5],
            'EUR' => ['usage_count' => 800, 'percentage' => 28.4],
            'GBP' => ['usage_count' => 600, 'percentage' => 21.3],
            'JPY' => ['usage_count' => 400, 'percentage' => 14.2],
            'CNY' => ['usage_count' => 200, 'percentage' => 7.1]
        ];
    }

    private function getRateVolatility(): array
    {
        // Calculate volatility for major currency pairs
        $volatilePairs = [
            'USD_EUR' => $this->getPairVolatility('USD', 'EUR'),
            'USD_GBP' => $this->getPairVolatility('USD', 'GBP'),
            'USD_JPY' => $this->getPairVolatility('USD', 'JPY'),
            'EUR_GBP' => $this->getPairVolatility('EUR', 'GBP'),
            'EUR_JPY' => $this->getPairVolatility('EUR', 'JPY')
        ];

        return $volatilePairs;
    }

    private function getPairVolatility(string $from, string $to): array
    {
        $rates = CurrencyRate::where('from_currency', $from)
            ->where('to_currency', $to)
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'asc')
            ->get();

        if ($rates->isEmpty()) {
            return ['volatility' => 0, 'trend' => 'stable'];
        }

        $rateValues = $rates->pluck('rate')->toArray();
        
        return [
            'volatility' => $this->calculateVolatility($rateValues),
            'trend' => $this->calculateTrend($rateValues)
        ];
    }

    private function getConversionVolume(): array
    {
        // This would typically query actual transaction data
        return [
            'daily_volume' => 1500000,
            'weekly_volume' => 10500000,
            'monthly_volume' => 45000000,
            'most_converted_pair' => 'USD_EUR',
            'growth_rate' => 12.5
        ];
    }
}

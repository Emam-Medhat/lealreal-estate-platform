<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'symbol',
        'precision',
        'is_default',
        'is_active',
        'exchange_rate_provider',
        'last_rate_update',
        'metadata'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'last_rate_update' => 'datetime',
        'metadata' => 'json'
    ];

    public function rates()
    {
        return $this->hasMany(CurrencyRate::class, 'from_currency', 'code');
    }

    public function incomingRates()
    {
        return $this->hasMany(CurrencyRate::class, 'to_currency', 'code');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'preferred_currency', 'code');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'currency', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function getCurrentRate(string $toCurrency): ?float
    {
        return $this->rates()
            ->where('to_currency', $toCurrency)
            ->where('date', now()->toDateString())
            ->value('rate');
    }

    public function updateRate(string $toCurrency, float $rate): void
    {
        CurrencyRate::updateOrCreate(
            [
                'from_currency' => $this->code,
                'to_currency' => $toCurrency,
                'date' => now()->toDateString()
            ],
            [
                'rate' => $rate,
                'source' => config('currency.active_provider', 'default'),
                'updated_at' => now()
            ]
        );

        $this->update(['last_rate_update' => now()]);
    }

    public function formatAmount(float $amount, bool $includeSymbol = true): string
    {
        $formatted = number_format($amount, $this->precision, '.', ',');
        
        if ($includeSymbol) {
            $symbolBefore = in_array($this->code, ['USD', 'EUR', 'GBP', 'AUD', 'CAD', 'HKD', 'SGD']);
            return $symbolBefore ? $this->symbol . $formatted : $formatted . ' ' . $this->symbol;
        }

        return $formatted;
    }

    public function getRateHistory(string $toCurrency, int $days = 30): array
    {
        return $this->rates()
            ->where('to_currency', $toCurrency)
            ->where('date', '>=', now()->subDays($days))
            ->orderBy('date')
            ->get()
            ->map(function($rate) {
                return [
                    'date' => $rate->date->toDateString(),
                    'rate' => $rate->rate,
                    'source' => $rate->source
                ];
            })
            ->toArray();
    }
}

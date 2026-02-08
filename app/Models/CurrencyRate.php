<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CurrencyRate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'date',
        'source',
        'metadata'
    ];

    protected $casts = [
        'date' => 'date',
        'rate' => 'decimal:8',
        'metadata' => 'json'
    ];

    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency', 'code');
    }

    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency', 'code');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('date', 'desc');
    }

    public function scopeForPair($query, string $from, string $to)
    {
        return $query->where('from_currency', $from)->where('to_currency', $to);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function getInverseRate(): float
    {
        return $this->rate > 0 ? 1 / $this->rate : 0;
    }

    public function getChangeFromPrevious(): ?float
    {
        $previousRate = static::where('from_currency', $this->from_currency)
            ->where('to_currency', $this->to_currency)
            ->where('date', '<', $this->date)
            ->latest()
            ->first();

        if (!$previousRate) {
            return null;
        }

        return (($this->rate - $previousRate->rate) / $previousRate->rate) * 100;
    }

    public function getVolatility(int $periodDays = 30): float
    {
        $rates = static::where('from_currency', $this->from_currency)
            ->where('to_currency', $this->to_currency)
            ->where('date', '>=', $this->date->copy()->subDays($periodDays))
            ->orderBy('date')
            ->pluck('rate')
            ->toArray();

        if (count($rates) < 2) {
            return 0;
        }

        $mean = array_sum($rates) / count($rates);
        $squaredDiffs = array_map(fn($rate) => pow($rate - $mean, 2), $rates);
        $variance = array_sum($squaredDiffs) / count($rates);
        
        return sqrt($variance);
    }
}

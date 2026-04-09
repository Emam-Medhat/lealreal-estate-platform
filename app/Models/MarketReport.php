<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'market_area',
        'average_property_price',
        'median_property_price',
        'total_listings',
        'total_sales',
        'price_per_square_foot',
        'average_days_on_market',
        'inventory_level',
        'price_trends',
        'market_segments',
        'neighborhood_data',
        'market_indicators',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'average_property_price' => 'decimal:2',
        'median_property_price' => 'decimal:2',
        'price_per_square_foot' => 'decimal:2',
        'average_days_on_market' => 'decimal:2',
        'inventory_level' => 'decimal:2',
        'price_trends' => 'array',
        'market_segments' => 'array',
        'neighborhood_data' => 'array',
        'market_indicators' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function getFormattedAveragePriceAttribute()
    {
        return number_format($this->average_property_price, 2);
    }

    public function getFormattedMedianPriceAttribute()
    {
        return number_format($this->median_property_price, 2);
    }

    public function getFormattedPricePerSquareFootAttribute()
    {
        return number_format($this->price_per_square_foot, 2);
    }

    public function getMarketCondition()
    {
        $inventory = $this->inventory_level;

        if ($inventory <= 2) {
            return 'Seller\'s Market';
        } elseif ($inventory <= 4) {
            return 'Balanced Market';
        } else {
            return 'Buyer\'s Market';
        }
    }

    public function getPriceTrend()
    {
        $trends = $this->price_trends ?? [];
        
        if (empty($trends) || count($trends) < 2) {
            return 'Stable';
        }

        $latest = end($trends);
        $previous = prev($trends);
        
        if ($latest['price'] > $previous['price'] * 1.05) {
            return 'Rising';
        } elseif ($latest['price'] < $previous['price'] * 0.95) {
            return 'Falling';
        } else {
            return 'Stable';
        }
    }

    public function getTopNeighborhoods($limit = 5)
    {
        $neighborhoods = $this->neighborhood_data ?? [];
        
        return collect($neighborhoods)
            ->sortByDesc('average_price')
            ->take($limit)
            ->toArray();
    }

    public function getMarketSegmentData()
    {
        return $this->market_segments ?? [];
    }

    public function getMarketIndicators()
    {
        return $this->market_indicators ?? [];
    }
}

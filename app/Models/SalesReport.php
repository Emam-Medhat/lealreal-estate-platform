<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'total_sales',
        'total_commission',
        'properties_sold',
        'average_sale_price',
        'average_days_on_market',
        'sales_by_agent',
        'sales_by_property_type',
        'sales_by_location',
        'monthly_sales',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'total_sales' => 'decimal:2',
        'total_commission' => 'decimal:2',
        'average_sale_price' => 'decimal:2',
        'average_days_on_market' => 'decimal:2',
        'sales_by_agent' => 'array',
        'sales_by_property_type' => 'array',
        'sales_by_location' => 'array',
        'monthly_sales' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function getFormattedTotalSalesAttribute()
    {
        return number_format($this->total_sales, 2);
    }

    public function getFormattedTotalCommissionAttribute()
    {
        return number_format($this->total_commission, 2);
    }

    public function getFormattedAverageSalePriceAttribute()
    {
        return number_format($this->average_sale_price, 2);
    }

    public function getTopPerformingAgent()
    {
        $salesByAgent = $this->sales_by_agent ?? [];
        
        if (empty($salesByAgent)) {
            return null;
        }

        return collect($salesByAgent)->sortByDesc('total_sales')->first();
    }

    public function getBestSellingPropertyType()
    {
        $salesByType = $this->sales_by_property_type ?? [];
        
        if (empty($salesByType)) {
            return null;
        }

        return collect($salesByType)->sortByDesc('count')->first();
    }

    public function getBestSellingLocation()
    {
        $salesByLocation = $this->sales_by_location ?? [];
        
        if (empty($salesByLocation)) {
            return null;
        }

        return collect($salesByLocation)->sortByDesc('count')->first();
    }
}

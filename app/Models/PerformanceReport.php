<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'agent_id',
        'total_sales',
        'total_commission',
        'properties_listed',
        'properties_sold',
        'conversion_rate',
        'average_sale_price',
        'customer_satisfaction',
        'leads_generated',
        'appointments_scheduled',
        'monthly_performance',
        'performance_metrics',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'total_sales' => 'decimal:2',
        'total_commission' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'average_sale_price' => 'decimal:2',
        'customer_satisfaction' => 'decimal:2',
        'monthly_performance' => 'array',
        'performance_metrics' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
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

    public function getPerformanceScore()
    {
        $score = 0;
        $factors = 0;

        // Sales performance (40% weight)
        if ($this->total_sales > 0) {
            $score += min($this->total_sales / 100000, 1) * 40;
            $factors++;
        }

        // Conversion rate (30% weight)
        if ($this->conversion_rate > 0) {
            $score += min($this->conversion_rate / 100, 1) * 30;
            $factors++;
        }

        // Customer satisfaction (30% weight)
        if ($this->customer_satisfaction > 0) {
            $score += ($this->customer_satisfaction / 5) * 30;
            $factors++;
        }

        return $factors > 0 ? round($score, 1) : 0;
    }

    public function getPerformanceGrade()
    {
        $score = $this->getPerformanceScore();

        if ($score >= 90) return 'A+';
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'B+';
        if ($score >= 75) return 'B';
        if ($score >= 70) return 'C+';
        if ($score >= 65) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    public function getTrendData($metric)
    {
        $monthlyData = $this->monthly_performance ?? [];
        
        return collect($monthlyData)->map(function ($month) use ($metric) {
            return $month[$metric] ?? 0;
        })->toArray();
    }
}

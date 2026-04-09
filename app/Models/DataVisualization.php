<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataVisualization extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'title',
        'type',
        'chart_type',
        'data_source',
        'chart_config',
        'data_series',
        'description',
        'position_order',
        'is_visible',
    ];

    protected $casts = [
        'chart_config' => 'array',
        'data_source' => 'array',
        'data_series' => 'array',
        'position_order' => 'integer',
        'is_visible' => 'boolean',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position_order');
    }

    public function getChartTypeLabel()
    {
        return match($this->chart_type) {
            'bar' => 'Bar Chart',
            'line' => 'Line Chart',
            'pie' => 'Pie Chart',
            'area' => 'Area Chart',
            'scatter' => 'Scatter Plot',
            'bubble' => 'Bubble Chart',
            'gauge' => 'Gauge',
            'table' => 'Data Table',
            'map' => 'Map',
            default => ucfirst($this->chart_type),
        };
    }

    public function getDataForChart()
    {
        $source = $this->data_source ?? [];
        $series = $this->data_series ?? [];

        if ($this->type === 'chart') {
            return [
                'labels' => $source['labels'] ?? [],
                'datasets' => $series ?? []
            ];
        }

        return $source;
    }

    public function getChartConfig()
    {
        $defaultConfig = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top'
                ]
            ]
        ];

        return array_merge($defaultConfig, $this->chart_config ?? []);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Heatmap extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_url',
        'heatmap_type',
        'time_range',
        'data',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeByPage($query, $pageUrl)
    {
        return $query->where('page_url', $pageUrl);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('heatmap_type', $type);
    }

    public function scopeByTimeRange($query, $timeRange)
    {
        return $query->where('time_range', $timeRange);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>', now()->subDays($days));
    }

    public function isClickHeatmap()
    {
        return $this->heatmap_type === 'click';
    }

    public function isMovementHeatmap()
    {
        return $this->heatmap_type === 'movement';
    }

    public function isScrollHeatmap()
    {
        return $this->heatmap_type === 'scroll';
    }

    public function isAttentionHeatmap()
    {
        return $this->heatmap_type === 'attention';
    }

    public function getIntensityDataAttribute()
    {
        return $this->data['intensity'] ?? [];
    }

    public function getCoordinatesAttribute()
    {
        return $this->data['coordinates'] ?? [];
    }

    public function getMetadataAttribute()
    {
        return $this->data['metadata'] ?? [];
    }

    public function getTotalInteractionsAttribute()
    {
        return array_sum($this->data['intensity'] ?? []);
    }

    public function getHotspotsAttribute()
    {
        $intensityData = $this->data['intensity'] ?? [];
        arsort($intensityData);
        
        return array_slice($intensityData, 0, 10, true);
    }

    public function getColdZonesAttribute()
    {
        $intensityData = $this->data['intensity'] ?? [];
        asort($intensityData);
        
        return array_slice($intensityData, 0, 10, true);
    }

    public function getAverageIntensityAttribute()
    {
        $intensityData = $this->data['intensity'] ?? [];
        
        return count($intensityData) > 0 ? array_sum($intensityData) / count($intensityData) : 0;
    }

    public function getMaxIntensityAttribute()
    {
        $intensityData = $this->data['intensity'] ?? [];
        
        return count($intensityData) > 0 ? max($intensityData) : 0;
    }

    public function getGridSizeAttribute()
    {
        return $this->data['grid_size'] ?? 50;
    }

    public function getPageTitleAttribute()
    {
        $url = parse_url($this->page_url);
        return $url['path'] ?? $this->page_url;
    }

    public function getDimensionsAttribute()
    {
        return $this->data['dimensions'] ?? [
            'width' => 1920,
            'height' => 1080
        ];
    }

    public function getResolutionAttribute()
    {
        return $this->data['resolution'] ?? [
            'width' => 1920,
            'height' => 1080
        ];
    }

    public function getDeviceTypeAttribute()
    {
        return $this->data['device_type'] ?? 'desktop';
    }

    public function getViewportSizeAttribute()
    {
        return $this->data['viewport_size'] ?? [
            'width' => 1920,
            'height' => 1080
        ];
    }

    public function calculateEngagementScore()
    {
        $totalInteractions = $this->getTotalInteractionsAttribute();
        $maxPossibleInteractions = $this->getMaxIntensityAttribute() * 100;
        
        if ($maxPossibleInteractions > 0) {
            return ($totalInteractions / $maxPossibleInteractions) * 100;
        }
        
        return 0;
    }

    public function getClickDensity()
    {
        if ($this->heatmap_type !== 'click') {
            return 0;
        }

        $totalClicks = $this->getTotalInteractionsAttribute();
        $area = ($this->getDimensionsAttribute()['width'] * $this->getDimensionsAttribute()['height']) / 10000; // Convert to cm²
        
        return $area > 0 ? $totalClicks / $area : 0;
    }

    public function getScrollDepthAttribute()
    {
        if ($this->heatmap_type !== 'scroll') {
            return 0;
        }

        $scrollData = $this->data['scroll_depth'] ?? [];
        
        return count($scrollData) > 0 ? array_sum($scrollData) / count($scrollData) : 0;
    }

    public function getAttentionZones()
    {
        $intensityData = $this->data['intensity'] ?? [];
        $zones = [];
        
        foreach ($intensityData as $key => $intensity) {
            if ($intensity > 70) {
                $zones[] = ['zone' => 'high', 'key' => $key, 'intensity' => $intensity];
            } elseif ($intensity > 40) {
                $zones[] = ['zone' => 'medium', 'key' => $key, 'intensity' => $intensity];
            } else {
                $zones[] = ['zone' => 'low', 'key' => $key, 'intensity' => $intensity];
            }
        }
        
        return $zones;
    }

    public function addInteraction($x, $y, $intensity = 1)
    {
        $gridSize = $this->getGridSizeAttribute();
        $gridX = floor($x / $gridSize);
        $gridY = floor($y / $gridSize);
        $key = "{$gridX}_{$gridY}";
        
        $data = $this->data;
        if (!isset($data['intensity'])) {
            $data['intensity'] = [];
        }
        
        if (!isset($data['intensity'][$key])) {
            $data['intensity'][$key] = 0;
        }
        
        $data['intensity'][$key] += $intensity;
        $this->data = $data;
        $this->save();
    }

    public function generateReport()
    {
        return [
            'page_url' => $this->page_url,
            'heatmap_type' => $this->heatmap_type,
            'time_range' => $this->time_range,
            'total_interactions' => $this->getTotalInteractionsAttribute(),
            'engagement_score' => $this->calculateEngagementScore(),
            'hotspots' => $this->getHotspotsAttribute(),
            'cold_zones' => $this->getColdZonesAttribute(),
            'average_intensity' => $this->getAverageIntensityAttribute(),
            'max_intensity' => $this->getMaxIntensityAttribute(),
            'click_density' => $this->getClickDensity(),
            'attention_zones' => $this->getAttentionZones(),
            'created_at' => $this->created_at->toDateString()
        ];
    }

    public function exportToJson()
    {
        return [
            'page_url' => $this->page_url,
            'heatmap_type' => $this->heatmap_type,
            'time_range' => $this->time_range,
            'data' => $this->data,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function exportToCsv()
    {
        $data = $this->data;
        $csv = "position,intensity\n";
        
        if (isset($data['intensity'])) {
            foreach ($data['intensity'] as $position => $intensity) {
                $csv .= "{$position},{$intensity}\n";
            }
        }
        
        return $csv;
    }
}

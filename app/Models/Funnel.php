<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Funnel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(FunnelStep::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getConversionRate()
    {
        $steps = $this->steps()->orderBy('order')->get();
        
        if ($steps->count() < 2) {
            return 0;
        }

        $firstStep = $steps->first();
        $lastStep = $steps->last();

        $firstStepUsers = $this->getStepUsers($firstStep);
        $lastStepUsers = $this->getStepUsers($lastStep);

        return $firstStepUsers > 0 ? ($lastStepUsers / $firstStepUsers) * 100 : 0;
    }

    public function getDropOffRate()
    {
        return 100 - $this->getConversionRate();
    }

    public function getStepUsers($step)
    {
        // This would typically query the analytics events
        // For now, return a placeholder implementation
        return rand(100, 1000);
    }

    public function getBiggestDropOff()
    {
        $steps = $this->steps()->orderBy('order')->get();
        $biggestDropOff = null;
        $maxDropRate = 0;

        for ($i = 0; $i < $steps->count() - 1; $i++) {
            $currentStep = $steps[$i];
            $nextStep = $steps[$i + 1];

            $currentUsers = $this->getStepUsers($currentStep);
            $nextUsers = $this->getStepUsers($nextStep);

            $dropRate = $currentUsers > 0 ? (($currentUsers - $nextUsers) / $currentUsers) * 100 : 0;

            if ($dropRate > $maxDropRate) {
                $maxDropRate = $dropRate;
                $biggestDropOff = $nextStep->name;
            }
        }

        return [
            'step' => $biggestDropOff,
            'drop_rate' => $maxDropRate
        ];
    }

    public function getFunnelAnalysis($startDate = null, $endDate = null)
    {
        $steps = $this->steps()->orderBy('order')->get();
        $analysis = [];

        foreach ($steps as $step) {
            $users = $this->getStepUsers($step);
            $previousUsers = $step->order > 1 ? $this->getStepUsers($steps[$step->order - 2]) : $users;
            
            $conversionRate = $previousUsers > 0 ? ($users / $previousUsers) * 100 : 100;
            $dropOffRate = 100 - $conversionRate;

            $analysis[] = [
                'step_name' => $step->name,
                'event_name' => $step->event_name,
                'order' => $step->order,
                'users' => $users,
                'conversion_rate' => $conversionRate,
                'drop_off_rate' => $dropOffRate
            ];
        }

        return $analysis;
    }

    public function generateReport($period = '30d')
    {
        $analysis = $this->getFunnelAnalysis();
        
        return [
            'funnel_name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'period' => $period,
            'analysis' => $analysis,
            'overall_conversion_rate' => $this->getConversionRate(),
            'biggest_drop_off' => $this->getBiggestDropOff(),
            'total_steps' => $this->steps()->count(),
            'created_at' => $this->created_at->toDateString()
        ];
    }

    public function duplicate()
    {
        $newFunnel = $this->replicate();
        $newFunnel->name = $this->name . ' (Copy)';
        $newFunnel->status = 'draft';
        $newFunnel->save();

        foreach ($this->steps as $step) {
            $newStep = $step->replicate();
            $newStep->funnel_id = $newFunnel->id;
            $newStep->save();
        }

        return $newFunnel;
    }

    public function archive()
    {
        $this->status = 'archived';
        $this->save();
    }

    public function activate()
    {
        $this->status = 'active';
        $this->save();
    }

    public function deactivate()
    {
        $this->status = 'inactive';
        $this->save();
    }

    public function getPerformanceMetrics($period = '30d')
    {
        $analysis = $this->getFunnelAnalysis();
        
        $metrics = [
            'conversion_rate' => $this->getConversionRate(),
            'drop_off_rate' => $this->getDropOffRate(),
            'total_users' => $analysis[0]['users'] ?? 0,
            'converting_users' => end($analysis)['users'] ?? 0,
            'avg_step_time' => $this->getAverageStepTime(),
            'bottleneck_step' => $this->getBiggestDropOff()['step'],
            'optimization_suggestions' => $this->getOptimizationSuggestions($analysis)
        ];

        return $metrics;
    }

    private function getAverageStepTime()
    {
        // Placeholder implementation - would calculate actual time between steps
        return rand(30, 300); // seconds
    }

    private function getOptimizationSuggestions($analysis)
    {
        $suggestions = [];
        
        foreach ($analysis as $step) {
            if ($step['drop_off_rate'] > 50) {
                $suggestions[] = "Focus on improving {$step['step_name']} - high drop-off rate of {$step['drop_off_rate']}%";
            } elseif ($step['drop_off_rate'] > 30) {
                $suggestions[] = "Review {$step['step_name']} for potential improvements";
            }
        }

        return $suggestions;
    }

    public function compareWith($otherFunnel, $period = '30d')
    {
        $thisAnalysis = $this->getFunnelAnalysis();
        $otherAnalysis = $otherFunnel->getFunnelAnalysis();

        $comparison = [
            'this_funnel' => [
                'name' => $this->name,
                'conversion_rate' => $this->getConversionRate(),
                'analysis' => $thisAnalysis
            ],
            'other_funnel' => [
                'name' => $otherFunnel->name,
                'conversion_rate' => $otherFunnel->getConversionRate(),
                'analysis' => $otherAnalysis
            ],
            'comparison' => [
                'conversion_difference' => $this->getConversionRate() - $otherFunnel->getConversionRate(),
                'better_performer' => $this->getConversionRate() > $otherFunnel->getConversionRate() ? $this->name : $otherFunnel->name,
                'recommendations' => $this->getComparisonRecommendations($thisAnalysis, $otherAnalysis)
            ]
        ];

        return $comparison;
    }

    private function getComparisonRecommendations($thisAnalysis, $otherAnalysis)
    {
        $recommendations = [];
        
        for ($i = 0; $i < min(count($thisAnalysis), count($otherAnalysis)); $i++) {
            $thisStep = $thisAnalysis[$i];
            $otherStep = $otherAnalysis[$i];
            
            if ($thisStep['conversion_rate'] < $otherStep['conversion_rate']) {
                $difference = $otherStep['conversion_rate'] - $thisStep['conversion_rate'];
                $recommendations[] = "Improve {$thisStep['step_name']} - underperforming by {$difference}% compared to alternative funnel";
            }
        }

        return $recommendations;
    }
}

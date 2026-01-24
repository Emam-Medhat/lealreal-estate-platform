<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cohort extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'start_date',
        'end_date',
        'description',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cohortGroups(): HasMany
    {
        return $this->hasMany(CohortGroup::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isDaily()
    {
        return $this->type === 'daily';
    }

    public function isWeekly()
    {
        return $this->type === 'weekly';
    }

    public function isMonthly()
    {
        return $this->type === 'monthly';
    }

    public function getDuration()
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    public function getPeriodCount()
    {
        $duration = $this->getDuration();
        
        return match($this->type) {
            'daily' => $duration,
            'weekly' => ceil($duration / 7),
            'monthly' => ceil($duration / 30),
            default => $duration
        };
    }

    public function getCohortGroups()
    {
        $groups = [];
        $currentDate = $this->start_date->copy();
        $endDate = $this->end_date->copy();

        while ($currentDate <= $endDate) {
            $periodStart = match($this->type) {
                'daily' => $currentDate->copy(),
                'weekly' => $currentDate->copy()->startOfWeek(),
                'monthly' => $currentDate->copy()->startOfMonth(),
                default => $currentDate->copy()
            };

            $periodEnd = match($this->type) {
                'daily' => $periodStart->copy()->endOfDay(),
                'weekly' => $periodStart->copy()->endOfWeek(),
                'monthly' => $periodStart->copy()->endOfMonth(),
                default => $periodStart->copy()->endOfDay()
            };

            $groups[] = [
                'period' => $periodStart->format('Y-m-d'),
                'period_label' => $this->getPeriodLabel($periodStart),
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'users' => $this->getPeriodUsers($periodStart, $periodEnd)
            ];

            $currentDate = match($this->type) {
                'daily' => $currentDate->addDay(),
                'weekly' => $currentDate->addWeek(),
                'monthly' => $currentDate->addMonth(),
                default => $currentDate->addDay()
            };
        }

        return $groups;
    }

    private function getPeriodUsers($startDate, $endDate)
    {
        // This would typically query the user sessions
        // For now, return a placeholder implementation
        return rand(50, 500);
    }

    private function getPeriodLabel($date)
    {
        return match($this->type) {
            'daily' => $date->format('M j'),
            'weekly' => $date->format('M j') . ' - ' . $date->copy()->endOfWeek()->format('M j'),
            'monthly' => $date->format('M Y'),
            default => $date->format('Y-m-d')
        };
    }

    public function getRetentionAnalysis()
    {
        $groups = $this->getCohortGroups();
        $retentionMatrix = [];
        $maxPeriods = count($groups);

        foreach ($groups as $cohortIndex => $cohort) {
            $retentionMatrix[$cohortIndex] = [
                'period_label' => $cohort['period_label'],
                'initial_users' => $cohort['users'],
                'retention_rates' => []
            ];

            for ($period = 0; $period < $maxPeriods; $period++) {
                if ($cohortIndex + $period < count($groups)) {
                    $retentionRate = $this->calculateRetentionRate(
                        $cohort['users'],
                        $groups[$cohortIndex + $period]['users'],
                        $period
                    );
                    $retentionMatrix[$cohortIndex]['retention_rates'][] = $retentionRate;
                } else {
                    $retentionMatrix[$cohortIndex]['retention_rates'][] = null;
                }
            }
        }

        return $retentionMatrix;
    }

    private function calculateRetentionRate($initialUsers, $currentUsers, $period)
    {
        if (empty($initialUsers) || $period === 0) return 100;

        // Simplified retention calculation
        $retainedUsers = $currentUsers * (1 - ($period * 0.1)); // Assume 10% drop-off per period
        return ($retainedUsers / $initialUsers) * 100;
    }

    public function getRevenueAnalysis()
    {
        $groups = $this->getCohortGroups();
        $revenueMatrix = [];

        foreach ($groups as $cohortIndex => $cohort) {
            $revenueMatrix[$cohortIndex] = [
                'period_label' => $cohort['period_label'],
                'initial_users' => $cohort['users'],
                'revenue_per_period' => []
            ];

            for ($period = 0; $period < 10; $period++) {
                if ($cohortIndex + $period < count($groups)) {
                    $revenue = $this->calculateCohortRevenue($cohort['users'], $period);
                    $revenueMatrix[$cohortIndex]['revenue_per_period'][] = $revenue;
                } else {
                    $revenueMatrix[$cohortIndex]['revenue_per_period'][] = 0;
                }
            }
        }

        return $revenueMatrix;
    }

    private function calculateCohortRevenue($users, $period)
    {
        // Simplified revenue calculation
        $avgRevenuePerUser = 100;
        $retentionRate = max(0, 1 - ($period * 0.1));
        
        return $users * $avgRevenuePerUser * $retentionRate;
    }

    public function getBehaviorAnalysis()
    {
        $groups = $this->getCohortGroups();
        $behaviorMatrix = [];

        foreach ($groups as $cohortIndex => $cohort) {
            $behaviorMatrix[$cohortIndex] = [
                'period_label' => $cohort['period_label'],
                'initial_users' => $cohort['users'],
                'avg_sessions' => [],
                'avg_duration' => []
            ];

            for ($period = 0; $period < 10; $period++) {
                if ($cohortIndex + $period < count($groups)) {
                    $behavior = $this->calculateCohortBehavior($cohort['users'], $period);
                    $behaviorMatrix[$cohortIndex]['avg_sessions'][] = $behavior['sessions'];
                    $behaviorMatrix[$cohortIndex]['avg_duration'][] = $behavior['duration'];
                } else {
                    $behaviorMatrix[$cohortIndex]['avg_sessions'][] = 0;
                    $behaviorMatrix[$cohortIndex]['avg_duration'][] = 0;
                }
            }
        }

        return $behaviorMatrix;
    }

    private function calculateCohortBehavior($users, $period)
    {
        // Simplified behavior calculation
        $retentionRate = max(0, 1 - ($period * 0.1));
        
        return [
            'sessions' => rand(1, 10) * $retentionRate,
            'duration' => rand(60, 600) * $retentionRate
        ];
    }

    public function generateReport($analysisType = 'retention')
    {
        $analysis = match($analysisType) {
            'retention' => $this->getRetentionAnalysis(),
            'revenue' => $this->getRevenueAnalysis(),
            'behavior' => $this->getBehaviorAnalysis(),
            default => $this->getRetentionAnalysis()
        };

        return [
            'cohort_info' => [
                'name' => $this->name,
                'type' => $this->type,
                'start_date' => $this->start_date->format('Y-m-d'),
                'end_date' => $this->end_date->format('Y-m-d'),
                'duration_days' => $this->getDuration(),
                'period_count' => $this->getPeriodCount()
            ],
            'analysis_type' => $analysisType,
            'analysis' => $analysis,
            'generated_at' => now()->toDateString()
        ];
    }

    public function duplicate()
    {
        $newCohort = $this->replicate();
        $newCohort->name = $this->name . ' (Copy)';
        $newCohort->status = 'draft';
        $newCohort->save();

        return $newCohort;
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

    public function getPerformanceMetrics()
    {
        $retentionAnalysis = $this->getRetentionAnalysis();
        
        return [
            'total_periods' => $this->getPeriodCount(),
            'total_users' => array_sum(array_column($retentionAnalysis, 'initial_users')),
            'avg_retention_rate' => $this->calculateAverageRetentionRate($retentionAnalysis),
            'best_performing_period' => $this->getBestPerformingPeriod($retentionAnalysis),
            'worst_performing_period' => $this->getWorstPerformingPeriod($retentionAnalysis),
            'retention_trend' => $this->getRetentionTrend($retentionAnalysis)
        ];
    }

    private function calculateAverageRetentionRate($retentionAnalysis)
    {
        $allRates = [];
        
        foreach ($retentionAnalysis as $cohort) {
            $allRates = array_merge($allRates, array_filter($cohort['retention_rates']));
        }
        
        return count($allRates) > 0 ? array_sum($allRates) / count($allRates) : 0;
    }

    private function getBestPerformingPeriod($retentionAnalysis)
    {
        $periodAverages = [];
        
        for ($period = 0; $period < 10; $period++) {
            $periodRates = [];
            
            foreach ($retentionAnalysis as $cohort) {
                if (isset($cohort['retention_rates'][$period]) && $cohort['retention_rates'][$period] !== null) {
                    $periodRates[] = $cohort['retention_rates'][$period];
                }
            }
            
            $periodAverages[$period] = count($periodRates) > 0 ? array_sum($periodRates) / count($periodRates) : 0;
        }
        
        $bestPeriod = array_keys($periodAverages, max($periodAverages))[0];
        
        return [
            'period' => $bestPeriod,
            'rate' => $periodAverages[$bestPeriod]
        ];
    }

    private function getWorstPerformingPeriod($retentionAnalysis)
    {
        $periodAverages = [];
        
        for ($period = 0; $period < 10; $period++) {
            $periodRates = [];
            
            foreach ($retentionAnalysis as $cohort) {
                if (isset($cohort['retention_rates'][$period]) && $cohort['retention_rates'][$period] !== null) {
                    $periodRates[] = $cohort['retention_rates'][$period];
                }
            }
            
            $periodAverages[$period] = count($periodRates) > 0 ? array_sum($periodRates) / count($periodRates) : 0;
        }
        
        $worstPeriod = array_keys($periodAverages, min($periodAverages))[0];
        
        return [
            'period' => $worstPeriod,
            'rate' => $periodAverages[$worstPeriod]
        ];
    }

    private function getRetentionTrend($retentionAnalysis)
    {
        $firstPeriodRates = [];
        $lastPeriodRates = [];
        
        foreach ($retentionAnalysis as $cohort) {
            if (isset($cohort['retention_rates'][0]) && $cohort['retention_rates'][0] !== null) {
                $firstPeriodRates[] = $cohort['retention_rates'][0];
            }
            
            $lastNonNullIndex = null;
            for ($i = count($cohort['retention_rates']) - 1; $i >= 0; $i--) {
                if ($cohort['retention_rates'][$i] !== null) {
                    $lastNonNullIndex = $i;
                    break;
                }
            }
            
            if ($lastNonNullIndex !== null) {
                $lastPeriodRates[] = $cohort['retention_rates'][$lastNonNullIndex];
            }
        }
        
        $firstAvg = count($firstPeriodRates) > 0 ? array_sum($firstPeriodRates) / count($firstPeriodRates) : 0;
        $lastAvg = count($lastPeriodRates) > 0 ? array_sum($lastPeriodRates) / count($lastPeriodRates) : 0;
        
        if ($lastAvg > $firstAvg * 1.05) return 'improving';
        if ($lastAvg < $firstAvg * 0.95) return 'declining';
        return 'stable';
    }
}

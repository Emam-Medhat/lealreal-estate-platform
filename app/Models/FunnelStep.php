<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FunnelStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'funnel_id',
        'name',
        'event_name',
        'description',
        'order',
        'is_required',
        'expected_conversion_rate',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'expected_conversion_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    public function isRequired()
    {
        return $this->is_required;
    }

    public function isOptional()
    {
        return !$this->is_required;
    }

    public function getActualConversionRate($period = '30d')
    {
        // This would typically query the analytics events
        // For now, return a placeholder implementation
        return rand(20, 95);
    }

    public function getConversionPerformance($period = '30d')
    {
        $actualRate = $this->getActualConversionRate($period);
        $expectedRate = $this->expected_conversion_rate;
        
        if ($expectedRate > 0) {
            $performance = ($actualRate / $expectedRate) * 100;
        } else {
            $performance = 100;
        }

        return [
            'actual_rate' => $actualRate,
            'expected_rate' => $expectedRate,
            'performance_percentage' => $performance,
            'status' => $performance >= 100 ? 'meeting' : ($performance >= 80 ? 'below' : 'critical')
        ];
    }

    public function getStepMetrics($period = '30d')
    {
        $performance = $this->getConversionPerformance($period);
        
        return [
            'step_name' => $this->name,
            'event_name' => $this->event_name,
            'order' => $this->order,
            'is_required' => $this->is_required,
            'users_entered' => rand(100, 1000),
            'users_completed' => rand(50, 800),
            'conversion_rate' => $performance['actual_rate'],
            'expected_rate' => $performance['expected_rate'],
            'performance_status' => $performance['status'],
            'avg_time_in_step' => rand(30, 300), // seconds
            'drop_off_reasons' => $this->getDropOffReasons(),
            'optimization_suggestions' => $this->getOptimizationSuggestions($performance)
        ];
    }

    private function getDropOffReasons()
    {
        return [
            'Page load too slow',
            'Confusing interface',
            'Missing information',
            'Technical issues',
            'User changed mind'
        ];
    }

    private function getOptimizationSuggestions($performance)
    {
        $suggestions = [];
        
        if ($performance['status'] === 'critical') {
            $suggestions[] = 'Immediate attention required - conversion rate significantly below expectations';
            $suggestions[] = 'Consider A/B testing different approaches';
            $suggestions[] = 'Review user feedback and identify pain points';
        } elseif ($performance['status'] === 'below') {
            $suggestions[] = 'Monitor performance and consider improvements';
            $suggestions[] = 'Analyze user behavior patterns';
            $suggestions[] = 'Test small optimizations';
        } else {
            $suggestions[] = 'Performance is meeting expectations';
            $suggestions[] = 'Continue monitoring for optimization opportunities';
        }

        return $suggestions;
    }

    public function duplicate()
    {
        $newStep = $this->replicate();
        $newStep->name = $this->name . ' (Copy)';
        $newStep->save();

        return $newStep;
    }

    public function moveUp()
    {
        if ($this->order > 1) {
            $previousStep = $this->funnel->steps()->where('order', $this->order - 1)->first();
            
            if ($previousStep) {
                $previousStep->order = $this->order;
                $previousStep->save();
                
                $this->order = $this->order - 1;
                $this->save();
            }
        }
    }

    public function moveDown()
    {
        $maxOrder = $this->funnel->steps()->max('order');
        
        if ($this->order < $maxOrder) {
            $nextStep = $this->funnel->steps()->where('order', $this->order + 1)->first();
            
            if ($nextStep) {
                $nextStep->order = $this->order;
                $nextStep->save();
                
                $this->order = $this->order + 1;
                $this->save();
            }
        }
    }

    public function getStepDescription()
    {
        return $this->description ?: "Step {$this->order}: {$this->name}";
    }

    public function getStepIcon()
    {
        return match($this->event_name) {
            'page_view' => 'eye',
            'property_detail' => 'home',
            'contact_form' => 'envelope',
            'appointment' => 'calendar',
            'purchase' => 'shopping-cart',
            'signup' => 'user-plus',
            'login' => 'sign-in-alt',
            default => 'circle'
        };
    }

    public function getStepColor()
    {
        return match($this->event_name) {
            'page_view' => 'blue',
            'property_detail' => 'green',
            'contact_form' => 'yellow',
            'appointment' => 'purple',
            'purchase' => 'red',
            'signup' => 'indigo',
            'login' => 'gray',
            default => 'gray'
        };
    }

    public function getStepType()
    {
        if (in_array($this->event_name, ['page_view', 'property_detail'])) {
            return 'view';
        } elseif (in_array($this->event_name, ['contact_form', 'appointment'])) {
            return 'action';
        } elseif (in_array($this->event_name, ['purchase', 'signup'])) {
            return 'conversion';
        } else {
            return 'other';
        }
    }

    public function getStepWeight()
    {
        return match($this->getStepType()) {
            'conversion' => 3,
            'action' => 2,
            'view' => 1,
            'other' => 1,
            default => 1
        };
    }

    public function getStepPriority()
    {
        $performance = $this->getConversionPerformance();
        
        if ($performance['status'] === 'critical') {
            return 'high';
        } elseif ($performance['status'] === 'below') {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function generateReport($period = '30d')
    {
        $metrics = $this->getStepMetrics($period);
        
        return [
            'step_info' => [
                'name' => $this->name,
                'event_name' => $this->event_name,
                'description' => $this->description,
                'order' => $this->order,
                'is_required' => $this->is_required,
                'expected_rate' => $this->expected_conversion_rate
            ],
            'metrics' => $metrics,
            'period' => $period,
            'generated_at' => now()->toDateString()
        ];
    }

    public function exportToJson()
    {
        return [
            'id' => $this->id,
            'funnel_id' => $this->funnel_id,
            'name' => $this->name,
            'event_name' => $this->event_name,
            'description' => $this->description,
            'order' => $this->order,
            'is_required' => $this->is_required,
            'expected_conversion_rate' => $this->expected_conversion_rate,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}

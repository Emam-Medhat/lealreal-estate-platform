<?php

namespace App\Models\Marketing;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'name',
        'subject',
        'preheader',
        'from_name',
        'from_email',
        'reply_to_email',
        'campaign_type',
        'status',
        'template_id',
        'content',
        'html_content',
        'text_content',
        'target_audience',
        'segment_criteria',
        'personalization_settings',
        'schedule_settings',
        'sending_settings',
        'tracking_settings',
        'automation_settings',
        'test_settings',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'opened_count',
        'clicked_count',
        'unsubscribed_count',
        'bounced_count',
        'complained_count',
        'open_rate',
        'click_rate',
        'bounce_rate',
        'unsubscribe_rate',
        'complaint_rate',
        'conversion_rate',
        'revenue_generated',
        'cost_per_send',
        'return_on_investment',
        'scheduled_at',
        'started_at',
        'completed_at',
        'paused_at',
        'resumed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'target_audience' => 'array',
        'segment_criteria' => 'array',
        'personalization_settings' => 'array',
        'schedule_settings' => 'array',
        'sending_settings' => 'array',
        'tracking_settings' => 'array',
        'automation_settings' => 'array',
        'test_settings' => 'array',
        'content' => 'array',
        'open_rate' => 'decimal:2',
        'click_rate' => 'decimal:2',
        'bounce_rate' => 'decimal:2',
        'unsubscribe_rate' => 'decimal:2',
        'complaint_rate' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'revenue_generated' => 'decimal:2',
        'cost_per_send' => 'decimal:2',
        'return_on_investment' => 'decimal:2',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('campaign_type', $type);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'active')
                    ->where('started_at', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('completed_at')
                          ->orWhere('completed_at', '>', now());
                    });
    }

    // Methods
    public function send()
    {
        $this->update([
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    public function pause()
    {
        $this->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);
    }

    public function resume()
    {
        $this->update([
            'status' => 'active',
            'resumed_at' => now(),
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function schedule($dateTime)
    {
        $this->update([
            'status' => 'scheduled',
            'scheduled_at' => $dateTime,
        ]);
    }

    public function calculatePerformance()
    {
        if ($this->delivered_count > 0) {
            $this->open_rate = ($this->opened_count / $this->delivered_count) * 100;
            $this->click_rate = ($this->clicked_count / $this->delivered_count) * 100;
            $this->unsubscribe_rate = ($this->unsubscribed_count / $this->delivered_count) * 100;
            $this->complaint_rate = ($this->complained_count / $this->delivered_count) * 100;
        }

        if ($this->sent_count > 0) {
            $this->bounce_rate = ($this->bounced_count / $this->sent_count) * 100;
        }

        if ($this->clicked_count > 0) {
            // Mock conversion calculation - in real implementation this would track actual conversions
            $this->conversion_rate = ($this->clicked_count * 0.1) / $this->delivered_count * 100;
        }

        if ($this->sent_count > 0) {
            $this->cost_per_send = $this->calculateCostPerSend();
        }

        if ($this->revenue_generated > 0 && $this->calculateTotalCost() > 0) {
            $this->return_on_investment = (($this->revenue_generated - $this->calculateTotalCost()) / $this->calculateTotalCost()) * 100;
        }

        $this->save();
    }

    public function calculateCostPerSend()
    {
        // Mock cost calculation - in real implementation this would be based on ESP pricing
        return 0.01; // $0.01 per email
    }

    public function calculateTotalCost()
    {
        return $this->sent_count * $this->cost_per_send;
    }

    public function getDeliveryRateAttribute()
    {
        return $this->sent_count > 0 
            ? (($this->delivered_count / $this->sent_count) * 100) 
            : 0;
    }

    public function getListGrowthRateAttribute()
    {
        return $this->delivered_count > 0 
            ? (($this->unsubscribed_count - $this->bounced_count) / $this->delivered_count) * 100 
            : 0;
    }

    public function getEngagementScoreAttribute()
    {
        // Calculate engagement score based on open rate, click rate, and low bounce/unsubscribe rates
        $engagementScore = 0;
        
        // Open rate contribution (40% weight)
        $engagementScore += min($this->open_rate / 25 * 40, 40); // 25% open rate = full points
        
        // Click rate contribution (40% weight)
        $engagementScore += min($this->click_rate / 5 * 40, 40); // 5% click rate = full points
        
        // Negative metrics deduction (20% weight)
        $negativeScore = ($this->bounce_rate + $this->unsubscribe_rate + $this->complaint_rate) / 3;
        $engagementScore -= min($negativeScore / 5 * 20, 20); // 5% combined negative = full deduction
        
        return max(0, round($engagementScore));
    }

    public function getPerformanceStatusAttribute()
    {
        $score = $this->engagement_score;
        
        if ($score >= 80) {
            return 'excellent';
        } elseif ($score >= 60) {
            return 'good';
        } elseif ($score >= 40) {
            return 'average';
        } else {
            return 'poor';
        }
    }

    public function canBeSent()
    {
        return in_array($this->status, ['draft', 'scheduled']) && 
               !empty($this->subject) && 
               !empty($this->html_content) &&
               $this->total_recipients > 0;
    }

    public function canBeScheduled()
    {
        return in_array($this->status, ['draft']) && 
               !empty($this->subject) && 
               !empty($this->html_content) &&
               $this->total_recipients > 0;
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled' && $this->scheduled_at && $this->scheduled_at > now();
    }

    public function isOverdue()
    {
        return $this->status === 'scheduled' && 
               $this->scheduled_at && 
               $this->scheduled_at < now();
    }

    public function getEstimatedRevenueAttribute()
    {
        // Mock revenue estimation based on industry averages
        $averageRevenuePerEmail = 0.50; // $0.50 per email
        return $this->delivered_count * $averageRevenuePerEmail;
    }

    public function getABTestResultsAttribute()
    {
        // Mock A/B test results - in real implementation this would track actual test data
        return [
            'version_a' => [
                'subject' => $this->subject,
                'open_rate' => $this->open_rate * 0.95,
                'click_rate' => $this->click_rate * 0.90,
                'conversions' => $this->clicked_count * 0.08,
            ],
            'version_b' => [
                'subject' => $this->subject . ' (Alternative)',
                'open_rate' => $this->open_rate * 1.05,
                'click_rate' => $this->click_rate * 1.10,
                'conversions' => $this->clicked_count * 0.12,
            ],
            'winner' => 'version_b',
            'confidence' => '95%',
        ];
    }

    public function getBestSendingTimeAttribute()
    {
        // Mock best sending time analysis - in real implementation this would analyze historical data
        return [
            'day_of_week' => 'Tuesday',
            'time_of_day' => '10:00 AM',
            'timezone' => 'UTC+3',
            'reason' => 'Highest open rates historically observed',
        ];
    }

    public function getSubjectLineAnalysisAttribute()
    {
        return [
            'length' => strlen($this->subject),
            'word_count' => str_word_count($this->subject),
            'has_emoji' => preg_match('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]/u', $this->subject),
            'has_personalization' => strpos($this->subject, '{') !== false,
            'has_numbers' => preg_match('/\d/', $this->subject),
            'has_urgency' => preg_match('/\b(urgent|limited|now|today|don\'t miss)\b/i', $this->subject),
            'readability_score' => $this->calculateReadabilityScore(),
        ];
    }

    private function calculateReadabilityScore()
    {
        // Simple readability calculation based on subject length and complexity
        $words = str_word_count($this->subject);
        $avgWordLength = strlen(str_replace(' ', '', $this->subject)) / max($words, 1);
        
        // Shorter words and appropriate length get better scores
        $lengthScore = $words <= 10 ? 100 : max(0, 100 - ($words - 10) * 5);
        $complexityScore = $avgWordLength <= 5 ? 100 : max(0, 100 - ($avgWordLength - 5) * 10);
        
        return round(($lengthScore + $complexityScore) / 2);
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($campaign) {
            if (auth()->check()) {
                $campaign->created_by = auth()->id();
            }
        });

        static::updating(function ($campaign) {
            if (auth()->check()) {
                $campaign->updated_by = auth()->id();
            }
        });

        static::saving(function ($campaign) {
            // Auto-complete campaigns that have been running for more than 24 hours without activity
            if ($campaign->status === 'active' && 
                $campaign->started_at && 
                $campaign->started_at->diffInHours(now()) > 24 &&
                $campaign->sent_count >= $campaign->total_recipients) {
                $campaign->status = 'completed';
                $campaign->completed_at = now();
            }
        });
    }
}

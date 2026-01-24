<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'agent_id', 'first_name', 'last_name', 'full_name', 'email',
        'phone', 'whatsapp', 'company', 'job_title', 'lead_source', 'lead_status',
        'lead_type', 'priority', 'budget_min', 'budget_max', 'currency',
        'preferred_property_types', 'preferred_locations', 'preferred_bedrooms',
        'preferred_bathrooms', 'preferred_area_min', 'preferred_area_max',
        'preferred_price_min', 'preferred_price_max', 'preferred_amenities',
        'timeline', 'financing_status', 'pre_approved', 'property_purpose',
        'notes', 'tags', 'assigned_to', 'created_by', 'last_contacted_at',
        'next_follow_up_at', 'follow_up_count', 'conversion_probability',
        'estimated_value', 'lost_reason', 'won_reason', 'converted_at',
        'archived_at', 'score', 'temperature', 'stage', 'pipeline_position',
        'contact_frequency', 'response_rate', 'engagement_score', 'activity_score',
        'last_activity_at', 'first_contact_at', 'days_in_pipeline', 'contact_attempts',
        'email_opens', 'email_clicks', 'website_visits', 'form_submissions',
        'call_count', 'meeting_count', 'property_viewings', 'offer_count',
        'referral_source', 'campaign_source', 'medium', 'content', 'term',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
        'ip_address', 'user_agent', 'device_type', 'browser', 'location',
        'language', 'timezone', 'gdpr_consent', 'marketing_consent',
        'data_privacy_accepted', 'communication_preferences', 'opt_out_date',
        'social_profiles', 'company_info', 'decision_maker', 'influencers',
        'competitors', 'pain_points', 'objections', 'motivations', 'goals',
        'requirements', 'constraints', 'decision_criteria', 'evaluation_process',
        'timeline_details', 'budget_details', 'stakeholder_analysis', 'risk_factors',
        'opportunity_strength', 'relationship_strength', 'trust_level', 'credibility_score',
        'custom_fields', 'metadata', 'internal_notes', 'private_notes',
    ];

    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'preferred_price_min' => 'decimal:2',
        'preferred_price_max' => 'decimal:2',
        'preferred_area_min' => 'decimal:2',
        'preferred_area_max' => 'decimal:2',
        'estimated_value' => 'decimal:2',
        'conversion_probability' => 'decimal:2',
        'score' => 'integer',
        'temperature' => 'integer',
        'pipeline_position' => 'integer',
        'follow_up_count' => 'integer',
        'contact_attempts' => 'integer',
        'email_opens' => 'integer',
        'email_clicks' => 'integer',
        'website_visits' => 'integer',
        'form_submissions' => 'integer',
        'call_count' => 'integer',
        'meeting_count' => 'integer',
        'property_viewings' => 'integer',
        'offer_count' => 'integer',
        'days_in_pipeline' => 'integer',
        'contact_frequency' => 'integer',
        'response_rate' => 'decimal:2',
        'engagement_score' => 'integer',
        'activity_score' => 'integer',
        'pre_approved' => 'boolean',
        'gdpr_consent' => 'boolean',
        'marketing_consent' => 'boolean',
        'data_privacy_accepted' => 'boolean',
        'decision_maker' => 'boolean',
        'preferred_property_types' => 'array',
        'preferred_locations' => 'array',
        'preferred_amenities' => 'array',
        'tags' => 'array',
        'social_profiles' => 'array',
        'company_info' => 'array',
        'competitors' => 'array',
        'pain_points' => 'array',
        'objections' => 'array',
        'motivations' => 'array',
        'goals' => 'array',
        'requirements' => 'array',
        'constraints' => 'array',
        'decision_criteria' => 'array',
        'evaluation_process' => 'array',
        'stakeholder_analysis' => 'array',
        'risk_factors' => 'array',
        'custom_fields' => 'array',
        'metadata' => 'array',
        'last_contacted_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'converted_at' => 'datetime',
        'archived_at' => 'datetime',
        'first_contact_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'opt_out_date' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(PropertyInquiry::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(LeadTask::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LeadDocument::class);
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'lead_properties')
            ->withPivot(['interest_level', 'viewing_date', 'feedback', 'status'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('lead_status', '!=', 'archived');
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('lead_status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('lead_type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeFilter($query, array $filters)
    {
        // Search by name, email, or phone
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('first_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('last_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('lead_status', $filters['status']);
        }

        // Filter by source
        if (!empty($filters['source'])) {
            $query->where('source_id', $filters['source']);
        }

        // Filter by assigned user
        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        // Filter by date range
        if (!empty($filters['date_range'])) {
            switch ($filters['date_range']) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'quarter':
                    $quarter = ceil(now()->month / 3);
                    $query->whereQuarter('created_at', $quarter)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        return $query;
    }

    public function scopeHot($query)
    {
        return $query->where('temperature', '>=', 80);
    }

    public function scopeWarm($query)
    {
        return $query->whereBetween('temperature', [40, 79]);
    }

    public function scopeCold($query)
    {
        return $query->where('temperature', '<', 40);
    }

    public function scopeNew($query)
    {
        return $query->where('lead_status', 'new');
    }

    public function scopeQualified($query)
    {
        return $query->where('lead_status', 'qualified');
    }

    public function scopeConverted($query)
    {
        return $query->where('lead_status', 'converted');
    }

    public function scopeLost($query)
    {
        return $query->where('lead_status', 'lost');
    }

    public function scopeFollowUpDue($query)
    {
        return $query->where('next_follow_up_at', '<=', now());
    }

    public function scopeOverdueFollowUp($query)
    {
        return $query->where('next_follow_up_at', '<', now());
    }

    // Helper Methods
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFormattedBudgetAttribute(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return number_format($this->budget_min, 2) . ' - ' . number_format($this->budget_max, 2) . ' ' . $this->currency;
        } elseif ($this->budget_min) {
            return 'From ' . number_format($this->budget_min, 2) . ' ' . $this->currency;
        } elseif ($this->budget_max) {
            return 'Up to ' . number_format($this->budget_max, 2) . ' ' . $this->currency;
        }
        
        return 'Not specified';
    }

    public function getTemperatureLabelAttribute(): string
    {
        if ($this->temperature >= 80) return 'Hot';
        if ($this->temperature >= 40) return 'Warm';
        return 'Cold';
    }

    public function getTemperatureColorAttribute(): string
    {
        if ($this->temperature >= 80) return 'red';
        if ($this->temperature >= 40) return 'yellow';
        return 'blue';
    }

    public function getPriorityLabelAttribute(): string
    {
        return ucfirst($this->priority);
    }

    public function getPriorityColorAttribute(): string
    {
        switch ($this->priority) {
            case 'high': return 'red';
            case 'medium': return 'yellow';
            case 'low': return 'green';
            default: return 'gray';
        }
    }

    public function getDaysSinceLastContactAttribute(): int
    {
        if (!$this->last_contacted_at) {
            return 999;
        }
        
        return $this->last_contacted_at->diffInDays(now());
    }

    public function getDaysSinceFirstContactAttribute(): int
    {
        if (!$this->first_contact_at) {
            return 0;
        }
        
        return $this->first_contact_at->diffInDays(now());
    }

    public function isHot(): bool
    {
        return $this->temperature >= 80;
    }

    public function isWarm(): bool
    {
        return $this->temperature >= 40 && $this->temperature < 80;
    }

    public function isCold(): bool
    {
        return $this->temperature < 40;
    }

    public function isConverted(): bool
    {
        return $this->lead_status === 'converted';
    }

    public function isLost(): bool
    {
        return $this->lead_status === 'lost';
    }

    public function isArchived(): bool
    {
        return $this->lead_status === 'archived';
    }

    public function isFollowUpDue(): bool
    {
        return $this->next_follow_up_at && $this->next_follow_up_at <= now();
    }

    public function isOverdue(): bool
    {
        return $this->next_follow_up_at && $this->next_follow_up_at < now();
    }

    public function convert($reason = null): void
    {
        $this->update([
            'lead_status' => 'converted',
            'converted_at' => now(),
            'won_reason' => $reason,
        ]);
    }

    public function lose($reason): void
    {
        $this->update([
            'lead_status' => 'lost',
            'lost_reason' => $reason,
        ]);
    }

    public function archive(): void
    {
        $this->update([
            'lead_status' => 'archived',
            'archived_at' => now(),
        ]);
    }

    public function updateTemperature($score): void
    {
        $this->update(['temperature' => $score]);
    }

    public function updateScore($score): void
    {
        $this->update(['score' => $score]);
    }

    public function scheduleFollowUp($datetime): void
    {
        $this->update([
            'next_follow_up_at' => $datetime,
            'follow_up_count' => $this->follow_up_count + 1,
        ]);
    }

    public function recordContact(): void
    {
        $this->update([
            'last_contacted_at' => now(),
            'contact_attempts' => $this->contact_attempts + 1,
        ]);
    }

    public function addActivity($type, $description, $metadata = []): void
    {
        $this->activities()->create([
            'type' => $type,
            'description' => $description,
            'metadata' => $metadata,
            'created_by' => Auth::id(),
        ]);
        
        $this->update(['last_activity_at' => now()]);
    }

    public function calculateEngagementScore(): void
    {
        $score = 0;
        
        // Email engagement
        $score += $this->email_opens * 2;
        $score += $this->email_clicks * 5;
        
        // Website engagement
        $score += $this->website_visits * 3;
        $score += $this->form_submissions * 10;
        
        // Direct engagement
        $score += $this->call_count * 8;
        $score += $this->meeting_count * 15;
        $score += $this->property_viewings * 12;
        
        // Response rate bonus
        if ($this->response_rate > 0) {
            $score += $this->response_rate * 10;
        }
        
        $this->update(['engagement_score' => $score]);
    }

    public function calculateActivityScore(): void
    {
        $score = 0;
        
        // Recent activity weight
        $daysSinceLastActivity = $this->last_activity_at ? $this->last_activity_at->diffInDays(now()) : 999;
        $score += max(0, 100 - $daysSinceLastActivity);
        
        // Contact frequency
        $score += min($this->contact_frequency * 5, 50);
        
        // Follow-up compliance
        if ($this->next_follow_up_at && $this->next_follow_up_at >= now()) {
            $score += 20;
        }
        
        $this->update(['activity_score' => $score]);
    }
}

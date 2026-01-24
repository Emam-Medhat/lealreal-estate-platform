<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentLead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agent_id',
        'client_name',
        'client_email',
        'client_phone',
        'property_id',
        'property_type',
        'budget_min',
        'budget_max',
        'location_preference',
        'property_requirements',
        'lead_source',
        'status',
        'priority',
        'notes',
        'follow_up_date',
        'last_contacted_at',
        'assigned_at',
        'converted_at',
        'lost_at',
        'lost_reason',
        'contact_attempts',
        'response_count',
        'appointment_count',
        'viewing_count',
        'offer_count',
        'tags',
        'custom_fields',
    ];

    protected $casts = [
        'budget_min' => 'decimal:15,2',
        'budget_max' => 'decimal:15,2',
        'property_requirements' => 'json',
        'tags' => 'json',
        'custom_fields' => 'json',
        'follow_up_date' => 'datetime',
        'last_contacted_at' => 'datetime',
        'assigned_at' => 'datetime',
        'converted_at' => 'datetime',
        'lost_at' => 'datetime',
        'contact_attempts' => 'integer',
        'response_count' => 'integer',
        'appointment_count' => 'integer',
        'viewing_count' => 'integer',
        'offer_count' => 'integer',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(AgentAppointment::class);
    }

    // Scopes
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('lead_source', $source);
    }

    public function scopeByPropertyType($query, $propertyType)
    {
        return $query->where('property_type', $propertyType);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location_preference', 'like', '%' . $location . '%');
    }

    public function scopeByBudget($query, $minBudget, $maxBudget = null)
    {
        if ($maxBudget) {
            return $query->whereBetween('budget_min', [$minBudget, $maxBudget])
                        ->orWhereBetween('budget_max', [$minBudget, $maxBudget]);
        }
        
        return $query->where('budget_min', '>=', $minBudget);
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeContacted($query)
    {
        return $query->where('status', 'contacted');
    }

    public function scopeQualified($query)
    {
        return $query->where('status', 'qualified');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }

    public function scopeLost($query)
    {
        return $query->where('status', 'lost');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeMediumPriority($query)
    {
        return $query->where('priority', 'medium');
    }

    public function scopeLowPriority($query)
    {
        return $query->where('priority', 'low');
    }

    public function scopeFollowUpDue($query)
    {
        return $query->where('follow_up_date', '<=', now())
                    ->whereNotIn('status', ['converted', 'lost']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('follow_up_date', '<', now())
                    ->whereNotIn('status', ['converted', 'lost']);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeWithTags($query, array $tags)
    {
        return $query->where(function($q) use ($tags) {
            foreach ($tags as $tag) {
                $q->orWhereJsonContains('tags', $tag);
            }
        });
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('client_name', 'like', '%' . $term . '%')
              ->orWhere('client_email', 'like', '%' . $term . '%')
              ->orWhere('client_phone', 'like', '%' . $term . '%')
              ->orWhere('location_preference', 'like', '%' . $term . '%')
              ->orWhere('notes', 'like', '%' . $term . '%');
        });
    }

    // Helper Methods
    public function getFormattedBudgetRangeAttribute(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return number_format($this->budget_min, 0) . ' - ' . number_format($this->budget_max, 0) . ' SAR';
        } elseif ($this->budget_min) {
            return 'From ' . number_format($this->budget_min, 0) . ' SAR';
        } elseif ($this->budget_max) {
            return 'Up to ' . number_format($this->budget_max, 0) . ' SAR';
        }

        return 'Not specified';
    }

    public function getDaysSinceCreationAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getDaysSinceLastContactAttribute(): ?int
    {
        return $this->last_contacted_at ? $this->last_contacted_at->diffInDays(now()) : null;
    }

    public function getDaysUntilFollowUpAttribute(): ?int
    {
        if (!$this->follow_up_date) {
            return null;
        }

        $diff = $this->follow_up_date->diffInDays(now(), false);
        
        return $diff >= 0 ? $diff : -$diff;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->follow_up_date && $this->follow_up_date->isPast() && !in_array($this->status, ['converted', 'lost']);
    }

    public function getIsDueTodayAttribute(): bool
    {
        return $this->follow_up_date && $this->follow_up_date->isToday() && !in_array($this->status, ['converted', 'lost']);
    }

    public function getContactRateAttribute(): float
    {
        return $this->contact_attempts > 0 ? ($this->response_count / $this->contact_attempts) * 100 : 0;
    }

    public function getConversionRateAttribute(): float
    {
        return $this->appointment_count > 0 ? ($this->offer_count / $this->appointment_count) * 100 : 0;
    }

    public function getEngagementScoreAttribute(): int
    {
        $score = 0;
        
        // Response score
        $score += min($this->response_count * 10, 30);
        
        // Appointment score
        $score += min($this->appointment_count * 20, 40);
        
        // Viewing score
        $score += min($this->viewing_count * 15, 20);
        
        // Offer score
        $score += min($this->offer_count * 10, 10);

        return min($score, 100);
    }

    public function getPriorityScoreAttribute(): int
    {
        $score = 0;
        
        // Budget consideration
        if ($this->budget_max >= 1000000) $score += 20;
        elseif ($this->budget_max >= 500000) $score += 15;
        elseif ($this->budget_max >= 250000) $score += 10;
        
        // Engagement score
        $score += $this->engagement_score * 0.3;
        
        // Response rate
        $score += $this->contact_rate * 0.2;
        
        // Priority level
        switch ($this->priority) {
            case 'high':
                $score += 25;
                break;
            case 'medium':
                $score += 15;
                break;
            case 'low':
                $score += 5;
                break;
        }

        return min(ceil($score), 100);
    }

    public function getTagsListAttribute(): array
    {
        return $this->tags ?? [];
    }

    public function getPropertyRequirementsListAttribute(): array
    {
        return $this->property_requirements ?? [];
    }

    public function getCustomFieldsListAttribute(): array
    {
        return $this->custom_fields ?? [];
    }

    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function isContacted(): bool
    {
        return $this->status === 'contacted';
    }

    public function isQualified(): bool
    {
        return $this->status === 'qualified';
    }

    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }

    public function isLost(): bool
    {
        return $this->status === 'lost';
    }

    public function isActive(): bool
    {
        return !in_array($this->status, ['converted', 'lost']);
    }

    public function canFollowUp(): bool
    {
        return $this->isActive() && $this->follow_up_date && $this->follow_up_date->isPast();
    }

    public function hasProperty(): bool
    {
        return !empty($this->property_id);
    }

    public function hasBudget(): bool
    {
        return !empty($this->budget_min) || !empty($this->budget_max);
    }

    public function hasLocationPreference(): bool
    {
        return !empty($this->location_preference);
    }

    public function hasRequirements(): bool
    {
        return !empty($this->property_requirements);
    }

    public function hasTags(): bool
    {
        return !empty($this->tags);
    }

    public function hasCustomFields(): bool
    {
        return !empty($this->custom_fields);
    }

    public function addTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        
        if (($key = array_search($tag, $tags)) !== false) {
            unset($tags[$key]);
            $this->update(['tags' => array_values($tags)]);
        }
    }

    public function setCustomField(string $key, $value): void
    {
        $customFields = $this->custom_fields ?? [];
        $customFields[$key] = $value;
        $this->update(['custom_fields' => $customFields]);
    }

    public function getCustomField(string $key, $default = null)
    {
        $customFields = $this->custom_fields ?? [];
        return $customFields[$key] ?? $default;
    }

    public function incrementContactAttempts(): void
    {
        $this->increment('contact_attempts');
        $this->update(['last_contacted_at' => now()]);
    }

    public function incrementResponseCount(): void
    {
        $this->increment('response_count');
    }

    public function incrementAppointmentCount(): void
    {
        $this->increment('appointment_count');
    }

    public function incrementViewingCount(): void
    {
        $this->increment('viewing_count');
    }

    public function incrementOfferCount(): void
    {
        $this->increment('offer_count');
    }

    public function markAsContacted(): void
    {
        $this->update(['status' => 'contacted']);
    }

    public function markAsQualified(): void
    {
        $this->update(['status' => 'qualified']);
    }

    public function markAsConverted(): void
    {
        $this->update([
            'status' => 'converted',
            'converted_at' => now(),
        ]);
    }

    public function markAsLost(string $reason = null): void
    {
        $this->update([
            'status' => 'lost',
            'lost_at' => now(),
            'lost_reason' => $reason,
        ]);
    }

    public function setFollowUpDate($date): void
    {
        $this->update(['follow_up_date' => $date]);
    }

    public function clearFollowUpDate(): void
    {
        $this->update(['follow_up_date' => null]);
    }

    public function assignToAgent($agentId): void
    {
        $this->update([
            'agent_id' => $agentId,
            'assigned_at' => now(),
        ]);
    }

    public function reassignToAgent($agentId): void
    {
        $this->update([
            'agent_id' => $agentId,
            'assigned_at' => now(),
        ]);
    }

    public function getLeadAgeAttribute(): string
    {
        $days = $this->days_since_creation;
        
        if ($days === 0) {
            return 'Today';
        } elseif ($days === 1) {
            return 'Yesterday';
        } elseif ($days < 7) {
            return $days . ' days ago';
        } elseif ($days < 30) {
            return floor($days / 7) . ' weeks ago';
        } elseif ($days < 365) {
            return floor($days / 30) . ' months ago';
        } else {
            return floor($days / 365) . ' years ago';
        }
    }

    public function getLeadUrgencyAttribute(): string
    {
        if ($this->is_lost) {
            return 'lost';
        } elseif ($this->is_converted) {
            return 'converted';
        } elseif ($this->is_overdue) {
            return 'urgent';
        } elseif ($this->days_since_last_contact > 7) {
            return 'stale';
        } elseif ($this->days_since_last_contact > 3) {
            return 'cold';
        } elseif ($this->engagement_score > 70) {
            return 'hot';
        } elseif ($this->engagement_score > 40) {
            return 'warm';
        } else {
            return 'cool';
        }
    }

    public function getUrgencyColorAttribute(): string
    {
        switch ($this->lead_urgency) {
            case 'urgent':
                return 'red';
            case 'hot':
                return 'orange';
            case 'warm':
                return 'yellow';
            case 'cool':
                return 'blue';
            case 'cold':
                return 'gray';
            case 'stale':
                return 'purple';
            case 'lost':
                return 'black';
            case 'converted':
                return 'green';
            default:
                return 'gray';
        }
    }
}

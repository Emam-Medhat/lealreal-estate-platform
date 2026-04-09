<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AgentAppointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agent_id',
        'client_id',
        'lead_id',
        'property_id',
        'title',
        'description',
        'appointment_date',
        'appointment_time',
        'duration_minutes',
        'location',
        'meeting_type',
        'status',
        'priority',
        'notes',
        'client_name',
        'client_email',
        'client_phone',
        'property_address',
        'meeting_link',
        'reminder_sent',
        'reminder_sent_at',
        'confirmation_sent',
        'confirmation_sent_at',
        'feedback',
        'rating',
        'outcome',
        'next_action',
        'follow_up_date',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'rescheduled_from',
        'rescheduled_to',
        'created_by',
        'attendees',
        'custom_fields',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime',
        'duration_minutes' => 'integer',
        'reminder_sent' => 'boolean',
        'confirmation_sent' => 'boolean',
        'reminder_sent_at' => 'datetime',
        'confirmation_sent_at' => 'datetime',
        'rating' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'rescheduled_from' => 'datetime',
        'rescheduled_to' => 'datetime',
        'follow_up_date' => 'datetime',
        'attendees' => 'json',
        'custom_fields' => 'json',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(AgentLead::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }

    // Scopes
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByLead($query, $leadId)
    {
        return $query->where('lead_id', $leadId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByMeetingType($query, $meetingType)
    {
        return $query->where('meeting_type', $meetingType);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('appointment_date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('appointment_date', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }

    public function scopeTomorrow($query)
    {
        return $query->whereDate('appointment_date', tomorrow());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('appointment_date', now()->month)
                    ->whereYear('appointment_date', now()->year);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', today())
                    ->where('status', 'scheduled');
    }

    public function scopePast($query)
    {
        return $query->where('appointment_date', '<', today())
                    ->orWhereIn('status', ['completed', 'cancelled', 'no_show']);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeNoShow($query)
    {
        return $query->where('status', 'no_show');
    }

    public function scopeRescheduled($query)
    {
        return $query->whereNotNull('rescheduled_from');
    }

    public function scopePendingReminder($query)
    {
        return $query->where('reminder_sent', false)
                    ->where('appointment_date', '>=', today())
                    ->where('appointment_date', '<=', today()->addDays(2));
    }

    public function scopePendingConfirmation($query)
    {
        return $query->where('confirmation_sent', false)
                    ->where('status', 'scheduled');
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

    public function scopeInPerson($query)
    {
        return $query->where('meeting_type', 'in_person');
    }

    public function scopeVirtual($query)
    {
        return $query->where('meeting_type', 'virtual');
    }

    public function scopePhone($query)
    {
        return $query->where('meeting_type', 'phone');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('title', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $term . '%')
              ->orWhere('location', 'like', '%' . $term . '%')
              ->orWhere('client_name', 'like', '%' . $term . '%')
              ->orWhere('client_email', 'like', '%' . $term . '%')
              ->orWhere('client_phone', 'like', '%' . $term . '%')
              ->orWhere('property_address', 'like', '%' . $term . '%')
              ->orWhere('notes', 'like', '%' . $term . '%');
        });
    }

    // Helper Methods
    public function getFormattedDateAttribute(): string
    {
        return $this->appointment_date->format('M d, Y');
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->appointment_time->format('h:i A');
    }

    public function getFormattedDateTimeAttribute(): string
    {
        return $this->appointment_date->format('M d, Y') . ' at ' . $this->appointment_time->format('h:i A');
    }

    public function getEndTimeAttribute(): \DateTime
    {
        $endTime = clone $this->appointment_time;
        $endTime->addMinutes($this->duration_minutes);
        return $endTime;
    }

    public function getFormattedEndTimeAttribute(): string
    {
        return $this->getEndTimeAttribute()->format('h:i A');
    }

    public function getTimeRangeAttribute(): string
    {
        return $this->formatted_time . ' - ' . $this->formatted_end_time;
    }

    public function getFullDateTimeRangeAttribute(): string
    {
        return $this->formatted_date . ' (' . $this->time_range . ')';
    }

    public function getDurationHoursAttribute(): float
    {
        return $this->duration_minutes / 60;
    }

    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration_minutes < 60) {
            return $this->duration_minutes . ' minutes';
        } elseif ($this->duration_minutes === 60) {
            return '1 hour';
        } else {
            $hours = floor($this->duration_minutes / 60);
            $minutes = $this->duration_minutes % 60;
            
            if ($minutes === 0) {
                return $hours . ' hours';
            } else {
                return $hours . 'h ' . $minutes . 'm';
            }
        }
    }

    public function getDaysUntilAppointmentAttribute(): ?int
    {
        if ($this->appointment_date->isPast()) {
            return null;
        }

        return $this->appointment_date->diffInDays(today());
    }

    public function getIsTodayAttribute(): bool
    {
        return $this->appointment_date->isToday();
    }

    public function getIsTomorrowAttribute(): bool
    {
        return $this->appointment_date->isTomorrow();
    }

    public function getIsPastAttribute(): bool
    {
        return $this->appointment_date->isPast() || in_array($this->status, ['completed', 'cancelled', 'no_show']);
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->appointment_date->isFuture() && $this->status === 'scheduled';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->appointment_date->isPast() && $this->status === 'scheduled';
    }

    public function getIsVirtualAttribute(): bool
    {
        return $this->meeting_type === 'virtual';
    }

    public function getIsInPersonAttribute(): bool
    {
        return $this->meeting_type === 'in_person';
    }

    public function getIsPhoneAttribute(): bool
    {
        return $this->meeting_type === 'phone';
    }

    public function getHasMeetingLinkAttribute(): bool
    {
        return !empty($this->meeting_link);
    }

    public function getHasPropertyAttribute(): bool
    {
        return !empty($this->property_id);
    }

    public function getHasLeadAttribute(): bool
    {
        return !empty($this->lead_id);
    }

    public function getHasClientAttribute(): bool
    {
        return !empty($this->client_id);
    }

    public function getClientDisplayNameAttribute(): string
    {
        if ($this->client) {
            return $this->client->full_name;
        } elseif ($this->lead) {
            return $this->lead->client_name;
        } else {
            return $this->client_name;
        }
    }

    public function getClientDisplayEmailAttribute(): ?string
    {
        if ($this->client) {
            return $this->client->email;
        } elseif ($this->lead) {
            return $this->lead->client_email;
        } else {
            return $this->client_email;
        }
    }

    public function getClientDisplayPhoneAttribute(): ?string
    {
        if ($this->client) {
            return $this->client->phone;
        } elseif ($this->lead) {
            return $this->lead->client_phone;
        } else {
            return $this->client_phone;
        }
    }

    public function getPropertyDisplayAddressAttribute(): string
    {
        if ($this->property) {
            return $this->property->full_address;
        } else {
            return $this->property_address;
        }
    }

    public function getAttendeesListAttribute(): array
    {
        return $this->attendees ?? [];
    }

    public function getCustomFieldsListAttribute(): array
    {
        return $this->custom_fields ?? [];
    }

    public function getStatusColorAttribute(): string
    {
        switch ($this->status) {
            case 'scheduled':
                return 'blue';
            case 'completed':
                return 'green';
            case 'cancelled':
                return 'red';
            case 'no_show':
                return 'orange';
            case 'rescheduled':
                return 'yellow';
            default:
                return 'gray';
        }
    }

    public function getPriorityColorAttribute(): string
    {
        switch ($this->priority) {
            case 'high':
                return 'red';
            case 'medium':
                return 'yellow';
            case 'low':
                return 'gray';
            default:
                return 'gray';
        }
    }

    public function getMeetingTypeIconAttribute(): string
    {
        switch ($this->meeting_type) {
            case 'in_person':
                return 'map-pin';
            case 'virtual':
                return 'video';
            case 'phone':
                return 'phone';
            default:
                return 'calendar';
        }
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isNoShow(): bool
    {
        return $this->status === 'no_show';
    }

    public function isRescheduled(): bool
    {
        return !empty($this->rescheduled_from);
    }

    public function canBeRescheduled(): bool
    {
        return in_array($this->status, ['scheduled']) && $this->appointment_date->isFuture();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['scheduled']) && $this->appointment_date->isFuture();
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'scheduled' && $this->appointment_date->isPast();
    }

    public function needsReminder(): bool
    {
        return !$this->reminder_sent && 
               $this->appointment_date->isFuture() && 
               $this->appointment_date->diffInDays(today()) <= 2;
    }

    public function needsConfirmation(): bool
    {
        return !$this->confirmation_sent && $this->status === 'scheduled';
    }

    public function markAsScheduled(): void
    {
        $this->update(['status' => 'scheduled']);
    }

    public function markAsCompleted(string $outcome = null, string $feedback = null, float $rating = null): void
    {
        $this->update([
            'status' => 'completed',
            'outcome' => $outcome,
            'feedback' => $feedback,
            'rating' => $rating,
        ]);
    }

    public function markAsCancelled(string $reason = null, string $cancelledBy = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_by' => $cancelledBy,
            'cancelled_at' => now(),
        ]);
    }

    public function markAsNoShow(string $reason = null): void
    {
        $this->update([
            'status' => 'no_show',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    public function rescheduleTo($newDateTime, string $reason = null): void
    {
        $this->update([
            'rescheduled_from' => $this->appointment_time,
            'rescheduled_to' => $newDateTime,
            'appointment_time' => $newDateTime,
            'appointment_date' => $newDateTime->format('Y-m-d'),
            'status' => 'rescheduled',
            'cancellation_reason' => $reason,
        ]);
    }

    public function sendReminder(): void
    {
        $this->update([
            'reminder_sent' => true,
            'reminder_sent_at' => now(),
        ]);
    }

    public function sendConfirmation(): void
    {
        $this->update([
            'confirmation_sent' => true,
            'confirmation_sent_at' => now(),
        ]);
    }

    public function addAttendee(array $attendee): void
    {
        $attendees = $this->attendees ?? [];
        $attendees[] = $attendee;
        $this->update(['attendees' => $attendees]);
    }

    public function removeAttendee($index): void
    {
        $attendees = $this->attendees ?? [];
        
        if (isset($attendees[$index])) {
            unset($attendees[$index]);
            $this->update(['attendees' => array_values($attendees)]);
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

    public function getAppointmentUrgencyAttribute(): string
    {
        if ($this->is_completed) {
            return 'completed';
        } elseif ($this->is_cancelled) {
            return 'cancelled';
        } elseif ($this->is_no_show) {
            return 'no_show';
        } elseif ($this->is_today) {
            return 'today';
        } elseif ($this->is_tomorrow) {
            return 'tomorrow';
        } elseif ($this->days_until_appointment <= 3) {
            return 'soon';
        } elseif ($this->is_overdue) {
            return 'overdue';
        } else {
            return 'future';
        }
    }

    public function getUrgencyColorAttribute(): string
    {
        switch ($this->appointment_urgency) {
            case 'overdue':
                return 'red';
            case 'today':
                return 'orange';
            case 'tomorrow':
                return 'yellow';
            case 'soon':
                return 'blue';
            case 'completed':
                return 'green';
            case 'cancelled':
                return 'gray';
            case 'no_show':
                return 'purple';
            default:
                return 'gray';
        }
    }

    public function createFollowUpAppointment($intervalDays = 7): self
    {
        $followUpDate = $this->appointment_date->addDays($intervalDays);
        
        return self::create([
            'agent_id' => $this->agent_id,
            'client_id' => $this->client_id,
            'lead_id' => $this->lead_id,
            'property_id' => $this->property_id,
            'title' => 'Follow-up: ' . $this->title,
            'description' => 'Follow-up appointment for: ' . $this->description,
            'appointment_date' => $followUpDate->format('Y-m-d'),
            'appointment_time' => $followUpDate,
            'duration_minutes' => $this->duration_minutes,
            'location' => $this->location,
            'meeting_type' => $this->meeting_type,
            'status' => 'scheduled',
            'priority' => $this->priority,
            'client_name' => $this->client_name,
            'client_email' => $this->client_email,
            'client_phone' => $this->client_phone,
            'property_address' => $this->property_address,
            'meeting_link' => $this->meeting_link,
            'created_by' => $this->agent_id,
        ]);
    }
}

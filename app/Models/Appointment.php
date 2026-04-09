<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'user_id', 'participant_id', 'agent_id', 'lead_id', 'property_id', 'title', 'description',
        'appointment_type', 'status', 'priority', 'start_datetime', 'end_datetime',
        'duration', 'timezone', 'location', 'location_type', 'address',
        'meeting_link', 'meeting_password', 'meeting_platform', 'phone_number',
        'notes', 'agenda', 'preparation_notes', 'follow_up_notes', 'outcome',
        'next_steps', 'attendees', 'required_attendees', 'optional_attendees',
        'reminders', 'confirmation_status', 'confirmed_at', 'confirmed_by',
        'rescheduled_count', 'original_start_datetime', 'cancellation_reason',
        'canceled_at', 'canceled_by', 'no_show_reason', 'no_show_at',
        'rating', 'feedback', 'client_feedback', 'agent_feedback', 'tags',
        'custom_fields', 'metadata', 'created_by', 'updated_by',
        'calendar_event_id', 'video_conference_link', 'documents_required',
        'pre_meeting_checklist', 'post_meeting_checklist', 'action_items',
        'decisions_made', 'commitments', 'next_appointment_id', 'previous_appointment_id',
        'property_viewing_notes', 'client_interest_level', 'budget_discussion',
        'timeline_discussion', 'objections_handled', 'concerns_raised',
        'next_follow_up_date', 'probability_of_sale', 'estimated_close_date',
        'competition_mentioned', 'decision_makers_present', 'buying_signals',
        'red_flags', 'opportunities', 'strengths', 'weaknesses', 'threats',
        'internal_notes', 'private_notes', 'client_notes', 'agent_notes',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'original_start_datetime' => 'datetime',
        'confirmed_at' => 'datetime',
        'canceled_at' => 'datetime',
        'no_show_at' => 'datetime',
        'duration' => 'integer',
        'rescheduled_count' => 'integer',
        'rating' => 'integer',
        'probability_of_sale' => 'decimal:2',
        'estimated_close_date' => 'date',
        'next_follow_up_date' => 'date',
        'attendees' => 'array',
        'required_attendees' => 'array',
        'optional_attendees' => 'array',
        'reminders' => 'array',
        'agenda' => 'array',
        'preparation_notes' => 'array',
        'follow_up_notes' => 'array',
        'next_steps' => 'array',
        'tags' => 'array',
        'custom_fields' => 'array',
        'metadata' => 'array',
        'documents_required' => 'array',
        'pre_meeting_checklist' => 'array',
        'post_meeting_checklist' => 'array',
        'action_items' => 'array',
        'decisions_made' => 'array',
        'commitments' => 'array',
        'property_viewing_notes' => 'array',
        'objections_handled' => 'array',
        'concerns_raised' => 'array',
        'buying_signals' => 'array',
        'red_flags' => 'array',
        'opportunities' => 'array',
        'strengths' => 'array',
        'weaknesses' => 'array',
        'threats' => 'array',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }
    
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function canceledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'canceled_by');
    }

    public function nextAppointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'next_appointment_id');
    }

    public function previousAppointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'previous_appointment_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(AppointmentNote::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AppointmentDocument::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(AppointmentReminder::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    // Scopes
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
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

    public function scopeByType($query, $type)
    {
        return $query->where('appointment_type', $type);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_datetime', today());
    }

    public function scopeTomorrow($query)
    {
        return $query->whereDate('start_datetime', today()->addDay());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('start_datetime', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('start_datetime', now()->month)
                    ->whereYear('start_datetime', now()->year);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>', now())
                    ->where('status', '!=', 'canceled');
    }

    public function scopePast($query)
    {
        return $query->where('start_datetime', '<', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    public function scopeNoShow($query)
    {
        return $query->where('status', 'no_show');
    }

    public function scopePropertyViewing($query)
    {
        return $query->where('appointment_type', 'property_viewing');
    }

    public function scopeConsultation($query)
    {
        return $query->where('appointment_type', 'consultation');
    }

    public function scopeFollowUp($query)
    {
        return $query->where('appointment_type', 'follow_up');
    }

    public function scopeClosing($query)
    {
        return $query->where('appointment_type', 'closing');
    }

    // Helper Methods
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        
        return $minutes . 'm';
    }

    public function getFormattedDateTimeAttribute(): string
    {
        return $this->start_datetime->format('M j, Y g:i A');
    }

    public function getTimeAttribute(): string
    {
        return $this->start_datetime->format('g:i A');
    }

    public function getDateAttribute(): string
    {
        return $this->start_datetime->format('M j, Y');
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        switch ($this->status) {
            case 'pending': return 'yellow';
            case 'confirmed': return 'green';
            case 'completed': return 'blue';
            case 'canceled': return 'red';
            case 'no_show': return 'gray';
            default: return 'gray';
        }
    }

    public function getTypeLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->appointment_type));
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

    public function isUpcoming(): bool
    {
        return $this->start_datetime > now() && $this->status !== 'canceled';
    }

    public function isPast(): bool
    {
        return $this->start_datetime < now();
    }

    public function isToday(): bool
    {
        return $this->start_datetime->isToday();
    }

    public function isTomorrow(): bool
    {
        return $this->start_datetime->isTomorrow();
    }

    public function isThisWeek(): bool
    {
        return $this->start_datetime->between(now()->startOfWeek(), now()->endOfWeek());
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function isNoShow(): bool
    {
        return $this->status === 'no_show';
    }

    public function isPropertyViewing(): bool
    {
        return $this->appointment_type === 'property_viewing';
    }

    public function canBeConfirmed(): bool
    {
        return in_array($this->status, ['pending']) && $this->start_datetime > now();
    }

    public function canBeRescheduled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) && $this->start_datetime > now();
    }

    public function canBeCanceled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) && $this->start_datetime > now();
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, ['confirmed']) && $this->start_datetime < now();
    }

    public function confirm($confirmedBy = null): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by' => $confirmedBy ?? Auth::id(),
        ]);
    }

    public function cancel($reason, $canceledBy = null): void
    {
        $this->update([
            'status' => 'canceled',
            'cancellation_reason' => $reason,
            'canceled_at' => now(),
            'canceled_by' => $canceledBy ?? Auth::id(),
        ]);
    }

    public function markAsNoShow($reason): void
    {
        $this->update([
            'status' => 'no_show',
            'no_show_reason' => $reason,
            'no_show_at' => now(),
        ]);
    }

    public function complete($outcome = null, $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'outcome' => $outcome,
            'notes' => $notes,
        ]);
    }

    public function reschedule($newStartDateTime, $newEndDateTime, $reason = null): void
    {
        $this->update([
            'start_datetime' => $newStartDateTime,
            'end_datetime' => $newEndDateTime,
            'duration' => $newStartDateTime->diffInMinutes($newEndDateTime),
            'original_start_datetime' => $this->original_start_datetime ?? $this->start_datetime,
            'rescheduled_count' => $this->rescheduled_count + 1,
            'status' => 'pending',
            'confirmed_at' => null,
            'confirmed_by' => null,
        ]);
        
        if ($reason) {
            $this->notes = ($this->notes ?? '') . "\n\nRescheduled: " . $reason;
            $this->save();
        }
    }

    public function addReminder($datetime, $type = 'email', $message = null): void
    {
        $reminders = $this->reminders ?? [];
        $reminders[] = [
            'datetime' => $datetime->toISOString(),
            'type' => $type,
            'message' => $message,
            'sent' => false,
            'created_at' => now()->toISOString(),
        ];
        
        $this->update(['reminders' => $reminders]);
    }

    public function addAttendee($email, $name = null, $required = false): void
    {
        $attendees = $this->attendees ?? [];
        $attendees[] = [
            'email' => $email,
            'name' => $name,
            'required' => $required,
            'status' => 'pending',
            'added_at' => now()->toISOString(),
        ];
        
        $this->update(['attendees' => $attendees]);
    }

    public function addNote($content, $type = 'general', $isPrivate = false): void
    {
        $this->notes()->create([
            'content' => $content,
            'type' => $type,
            'is_private' => $isPrivate,
            'created_by' => Auth::id(),
        ]);
    }

    public function addDocument($title, $filePath, $type = 'general'): void
    {
        $this->documents()->create([
            'title' => $title,
            'file_path' => $filePath,
            'type' => $type,
            'uploaded_by' => Auth::id(),
        ]);
    }

    public function generateCalendarLink(): string
    {
        $startDate = urlencode($this->start_datetime->format('Ymd\THis\Z'));
        $endDate = urlencode($this->end_datetime->format('Ymd\THis\Z'));
        $title = urlencode($this->title);
        $description = urlencode($this->description ?? '');
        
        return "https://calendar.google.com/calendar/render?action=TEMPLATE&dates={$startDate}/{$endDate}&text={$title}&details={$description}";
    }

    public function generateVideoConferenceLink(): string
    {
        if ($this->meeting_platform === 'zoom') {
            return "https://zoom.us/j/" . $this->meeting_link;
        } elseif ($this->meeting_platform === 'teams') {
            return $this->meeting_link;
        } elseif ($this->meeting_platform === 'google_meet') {
            return $this->meeting_link;
        }
        
        return $this->meeting_link ?? '';
    }

    public function getRemainingTimeAttribute(): string
    {
        if ($this->start_datetime <= now()) {
            return 'Started';
        }
        
        $diff = $this->start_datetime->diffForHumans(now(), true);
        
        if ($this->start_datetime->isToday()) {
            return 'Today at ' . $this->start_datetime->format('g:i A');
        } elseif ($this->start_datetime->isTomorrow()) {
            return 'Tomorrow at ' . $this->start_datetime->format('g:i A');
        }
        
        return $diff;
    }

    public function updateProbability($probability): void
    {
        $this->update(['probability_of_sale' => $probability]);
    }

    public function updateClientInterestLevel($level): void
    {
        $this->update(['client_interest_level' => $level]);
    }

    public function recordDecision($decision, $details = null): void
    {
        $decisions = $this->decisions_made ?? [];
        $decisions[] = [
            'decision' => $decision,
            'details' => $details,
            'recorded_at' => now()->toISOString(),
            'recorded_by' => Auth::id(),
        ];
        
        $this->update(['decisions_made' => $decisions]);
    }

    public function addActionItem($action, $assignee = null, $dueDate = null): void
    {
        $actionItems = $this->action_items ?? [];
        $actionItems[] = [
            'action' => $action,
            'assignee' => $assignee,
            'due_date' => $dueDate?->toISOString(),
            'status' => 'pending',
            'created_at' => now()->toISOString(),
            'created_by' => Auth::id(),
        ];
        
        $this->update(['action_items' => $actionItems]);
    }
}

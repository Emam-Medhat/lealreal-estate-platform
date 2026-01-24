<?php

namespace App\Models\Marketing;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualOpenHouseMarketing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'title',
        'description',
        'platform',
        'event_type',
        'status',
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration',
        'max_attendees',
        'registration_required',
        'registration_deadline',
        'meeting_link',
        'meeting_id',
        'password',
        'host_info',
        'promotion_channels',
        'email_template',
        'social_media_posts',
        'reminder_settings',
        'recording_settings',
        'follow_up_settings',
        'custom_banner',
        'featured_images',
        'virtual_tour_link',
        'property_video_url',
        'floor_plans',
        'total_attendees',
        'total_views',
        'total_registrations',
        'total_interactions',
        'average_attendance_time',
        'peak_attendance_time',
        'questions_asked',
        'chat_messages',
        'poll_participation',
        'conversion_rate',
        'lead_generation',
        'property_inquiries',
        'tour_requests',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'registration_deadline' => 'datetime',
        'host_info' => 'array',
        'promotion_channels' => 'array',
        'email_template' => 'array',
        'social_media_posts' => 'array',
        'reminder_settings' => 'array',
        'recording_settings' => 'array',
        'follow_up_settings' => 'array',
        'featured_images' => 'array',
        'floor_plans' => 'array',
        'registration_required' => 'boolean',
        'recording_settings.record_session' => 'boolean',
        'recording_settings.auto_share' => 'boolean',
        'reminder_settings.enabled' => 'boolean',
        'follow_up_settings.enabled' => 'boolean',
        'follow_up_settings.send_recording' => 'boolean',
        'follow_up_settings.send_survey' => 'boolean',
        'follow_up_settings.schedule_next_steps' => 'boolean',
        'average_attendance_time' => 'integer',
        'peak_attendance_time' => 'integer',
        'conversion_rate' => 'decimal:2',
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

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('scheduled_at', '<', now());
    }

    public function scopeWithRegistration($query)
    {
        return $query->where('registration_required', true);
    }

    // Methods
    public function start()
    {
        $this->update([
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    public function end()
    {
        $this->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'ended_at' => now(),
        ]);
    }

    public function schedule($dateTime)
    {
        $this->update([
            'status' => 'scheduled',
            'scheduled_at' => $dateTime,
        ]);
    }

    public function calculateMetrics()
    {
        if ($this->total_registrations > 0) {
            $this->conversion_rate = ($this->total_attendees / $this->total_registrations) * 100;
        }

        if ($this->total_attendees > 0) {
            $this->average_attendance_time = $this->calculateAverageAttendanceTime();
            $this->peak_attendance_time = $this->calculatePeakAttendanceTime();
        }

        $this->save();
    }

    private function calculateAverageAttendanceTime()
    {
        // Mock calculation - in real implementation this would track actual attendance data
        return rand($this->duration * 0.3, $this->duration * 0.8);
    }

    private function calculatePeakAttendanceTime()
    {
        // Mock calculation - in real implementation this would analyze attendance patterns
        return rand(5, min(30, $this->duration));
    }

    public function getAttendanceRateAttribute()
    {
        return $this->total_registrations > 0 
            ? (($this->total_attendees / $this->total_registrations) * 100) 
            : 0;
    }

    public function getEngagementRateAttribute()
    {
        if ($this->total_attendees === 0) {
            return 0;
        }

        $totalInteractions = $this->questions_asked + $this->chat_messages + $this->poll_participation;
        return ($totalInteractions / $this->total_attendees) * 100;
    }

    public function getPlatformDisplayNameAttribute()
    {
        return match($this->platform) {
            'zoom' => 'Zoom',
            'teams' => 'Microsoft Teams',
            'google_meet' => 'Google Meet',
            'skype' => 'Skype',
            'custom' => 'مخصص',
            default => $this->platform,
        };
    }

    public function getEventTypeDisplayNameAttribute()
    {
        return match($this->event_type) {
            'live_tour' => 'جولة مباشرة',
            'recorded_tour' => 'جولة مسجلة',
            'qna_session' => 'جلسة أسئلة',
            'webinar' => 'ندوة عبر الويب',
            'presentation' => 'عرض تقديمي',
            default => $this->event_type,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'scheduled' => 'blue',
            'active' => 'green',
            'completed' => 'gray',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getDurationFormattedAttribute()
    {
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        
        if ($hours > 0) {
            return "{$hours} ساعة {$minutes} دقيقة";
        }
        
        return "{$minutes} دقيقة";
    }

    public function isLive()
    {
        return $this->status === 'active' && 
               $this->started_at && 
               (!$this->ended_at || $this->ended_at > now());
    }

    public function isUpcoming()
    {
        return $this->status === 'scheduled' && 
               $this->scheduled_at && 
               $this->scheduled_at > now();
    }

    public function isPast()
    {
        return $this->status === 'completed' || 
               ($this->scheduled_at && $this->scheduled_at < now());
    }

    public function canStart()
    {
        return in_array($this->status, ['scheduled']) && 
               $this->scheduled_at && 
               $this->scheduled_at <= now();
    }

    public function canRegister()
    {
        return $this->registration_required && 
               $this->status === 'scheduled' && 
               (!$this->registration_deadline || $this->registration_deadline > now()) &&
               (!$this->max_attendees || $this->total_registrations < $this->max_attendees);
    }

    public function isFull()
    {
        return $this->max_attendees && 
               $this->total_registrations >= $this->max_attendees;
    }

    public function getRemainingSlotsAttribute()
    {
        if (!$this->max_attendees) {
            return null;
        }
        
        return max(0, $this->max_attendees - $this->total_registrations);
    }

    public function getTimeUntilStartAttribute()
    {
        if (!$this->scheduled_at) {
            return null;
        }
        
        return $this->scheduled_at->diffForHumans();
    }

    public function getPerformanceMetricsAttribute()
    {
        return [
            'attendance_rate' => $this->attendance_rate,
            'engagement_rate' => $this->engagement_rate,
            'conversion_rate' => $this->conversion_rate,
            'lead_generation' => $this->lead_generation,
            'property_inquiries' => $this->property_inquiries,
            'tour_requests' => $this->tour_requests,
            'average_attendance_time' => $this->average_attendance_time,
            'peak_attendance_time' => $this->peak_attendance_time,
            'total_interactions' => $this->total_interactions,
        ];
    }

    public function getAudienceDemographicsAttribute()
    {
        // Mock demographics - in real implementation this would track actual attendee data
        return [
            'age_groups' => [
                '18-24' => rand(5, 15),
                '25-34' => rand(25, 40),
                '35-44' => rand(25, 35),
                '45-54' => rand(15, 25),
                '55+' => rand(5, 15),
            ],
            'genders' => [
                'male' => rand(45, 55),
                'female' => rand(45, 55),
            ],
            'locations' => [
                'الرياض' => rand(20, 35),
                'جدة' => rand(15, 25),
                'الدمام' => rand(10, 20),
                'مكة' => rand(8, 15),
                'أخرى' => rand(15, 25),
            ],
            'device_types' => [
                'desktop' => rand(40, 60),
                'mobile' => rand(30, 50),
                'tablet' => rand(5, 15),
            ],
        ];
    }

    public function getEngagementAnalyticsAttribute()
    {
        return [
            'questions_by_time' => $this->getQuestionsByTime(),
            'chat_activity' => $this->getChatActivity(),
            'poll_results' => $this->getPollResults(),
            'interaction_types' => [
                'questions' => $this->questions_asked,
                'chat_messages' => $this->chat_messages,
                'poll_participation' => $this->poll_participation,
                'reactions' => rand(10, 100),
                'screen_shares' => rand(1, 5),
            ],
            'most_active_attendees' => $this->getMostActiveAttendees(),
        ];
    }

    private function getQuestionsByTime()
    {
        // Mock data - in real implementation this would track actual timing
        $data = [];
        for ($i = 0; $i < $this->duration; $i += 10) {
            $data[$i . 'min'] = rand(0, 5);
        }
        return $data;
    }

    private function getChatActivity()
    {
        return [
            'total_messages' => $this->chat_messages,
            'unique_participants' => rand($this->total_attendees * 0.3, $this->total_attendees * 0.7),
            'average_response_time' => rand(30, 180) . ' seconds',
            'peak_activity_time' => $this->peak_attendance_time . ' minutes',
        ];
    }

    private function getPollResults()
    {
        // Mock poll results - in real implementation this would track actual poll data
        return [
            'total_polls' => rand(2, 8),
            'participation_rate' => rand(40, 80) . '%',
            'average_completion_time' => rand(30, 120) . ' seconds',
            'most_popular_poll' => 'Property Features Preference',
        ];
    }

    private function getMostActiveAttendees()
    {
        // Mock data - in real implementation this would track actual attendee activity
        $attendees = [];
        for ($i = 0; $i < min(5, $this->total_attendees); $i++) {
            $attendees[] = [
                'name' => 'Attendee ' . ($i + 1),
                'questions' => rand(0, 5),
                'chat_messages' => rand(0, 20),
                'poll_participation' => rand(0, 5),
                'total_score' => rand(1, 25),
            ];
        }
        return $attendees;
    }

    public function getFollowUpActionsAttribute()
    {
        $actions = [];

        if ($this->recording_settings['record_session'] ?? false) {
            $actions[] = [
                'type' => 'send_recording',
                'status' => $this->recording_settings['auto_share'] ?? false ? 'automatic' : 'manual',
                'recipients' => $this->total_attendees,
            ];
        }

        if ($this->follow_up_settings['send_survey'] ?? false) {
            $actions[] = [
                'type' => 'send_survey',
                'status' => 'pending',
                'recipients' => $this->total_attendees,
            ];
        }

        if ($this->lead_generation > 0) {
            $actions[] = [
                'type' => 'lead_follow_up',
                'status' => 'pending',
                'recipients' => $this->lead_generation,
            ];
        }

        return $actions;
    }

    public function getTechnicalMetricsAttribute()
    {
        return [
            'average_connection_quality' => rand(3, 5) . '/5',
            'technical_issues' => rand(0, 5),
            'device_breakdown' => $this->getDeviceBreakdown(),
            'bandwidth_usage' => rand(1, 10) . ' Mbps',
            'connection_drops' => rand(0, 3),
            'audio_quality_score' => rand(3, 5) . '/5',
            'video_quality_score' => rand(3, 5) . '/5',
        ];
    }

    private function getDeviceBreakdown()
    {
        return [
            'desktop' => rand(40, 70),
            'mobile' => rand(20, 40),
            'tablet' => rand(10, 20),
        ];
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($event) {
            if (auth()->check()) {
                $event->created_by = auth()->id();
            }
        });

        static::updating(function ($event) {
            if (auth()->check()) {
                $event->updated_by = auth()->id();
            }
        });

        static::saving(function ($event) {
            // Auto-complete events that have ended
            if ($event->status === 'active' && 
                $event->ended_at && 
                $event->ended_at < now()) {
                $event->status = 'completed';
            }
        });
    }
}

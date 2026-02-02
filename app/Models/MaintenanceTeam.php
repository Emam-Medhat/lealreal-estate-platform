<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceTeam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'team_code',
        'name',
        'description',
        'leader_name',
        'leader_email',
        'leader_phone',
        'team_leader_id',
        'specialization',
        'contact_phone',
        'contact_email',
        'max_concurrent_jobs',
        'working_hours',
        'working_hours_start',
        'working_hours_end',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'max_concurrent_jobs' => 'integer',
        'is_active' => 'boolean',
        'working_hours_start' => 'datetime',
        'working_hours_end' => 'datetime',
    ];

    public function teamLeader()
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'maintenance_team_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'assigned_team_id');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function emergencyRepairs()
    {
        return $this->hasMany(EmergencyRepair::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('specialization', $specialization);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
                    ->whereHas('schedules', function ($q) {
                        $q->where('status', 'in_progress')
                          ->havingRaw('COUNT(*) < max_concurrent_jobs');
                    });
    }

    public function getSpecializationLabelAttribute()
    {
        $labels = [
            'plumbing' => 'سباكة',
            'electrical' => 'كهرباء',
            'hvac' => 'تكييف',
            'structural' => 'إنشائي',
            'general' => 'عام',
            'multi' => 'متعدد',
        ];

        return $labels[$this->specialization] ?? $this->specialization;
    }

    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'نشط' : 'غير نشط';
    }

    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'green' : 'red';
    }

    public function getCurrentJobs()
    {
        return $this->schedules()->where('status', 'in_progress')->count();
    }

    public function getAvailableSlots()
    {
        return $this->max_concurrent_jobs - $this->getCurrentJobs();
    }

    public function isAvailable()
    {
        return $this->is_active && $this->getAvailableSlots() > 0;
    }

    public function isWorkingHours()
    {
        $now = now();
        $startTime = $this->working_hours_start->copy()->setDate($now->year, $now->month, $now->day);
        $endTime = $this->working_hours_end->copy()->setDate($now->year, $now->month, $now->day);

        return $now->between($startTime, $endTime);
    }

    public function canAcceptNewJob()
    {
        return $this->is_active && 
               $this->getAvailableSlots() > 0 && 
               $this->isWorkingHours();
    }

    public function getMemberCount()
    {
        return $this->members()->count();
    }

    public function getActiveMembersCount()
    {
        return $this->members()->where('is_active', true)->count();
    }

    public function addMember($userId, $role = 'member')
    {
        if (!$this->members()->where('user_id', $userId)->exists()) {
            $this->members()->attach($userId, [
                'role' => $role,
                'joined_at' => now(),
            ]);
        }
    }

    public function removeMember($userId)
    {
        $this->members()->detach($userId);
    }

    public function updateMemberRole($userId, $role)
    {
        $this->members()->updateExistingPivot($userId, ['role' => $role]);
    }

    public function isMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    public function getMemberRole($userId)
    {
        $member = $this->members()->where('user_id', $userId)->first();
        return $member ? $member->pivot->role : null;
    }

    public function getCompletedJobsCount()
    {
        return $this->schedules()->where('status', 'completed')->count();
    }

    public function getCompletionRate()
    {
        $totalJobs = $this->schedules()->count();
        $completedJobs = $this->getCompletedJobsCount();

        if ($totalJobs === 0) {
            return 0;
        }

        return ($completedJobs / $totalJobs) * 100;
    }

    public function getAverageCompletionTime()
    {
        $completedSchedules = $this->schedules()
            ->where('status', 'completed')
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at');

        if ($completedSchedules->count() === 0) {
            return null;
        }

        $totalTime = $completedSchedules
            ->get()
            ->sum(function ($schedule) {
                return $schedule->started_at->diffInMinutes($schedule->completed_at);
            });

        return $totalTime / $completedSchedules->count();
    }

    public function getPerformanceMetrics()
    {
        return [
            'total_members' => $this->getMemberCount(),
            'active_members' => $this->getActiveMembersCount(),
            'current_jobs' => $this->getCurrentJobs(),
            'available_slots' => $this->getAvailableSlots(),
            'completed_jobs' => $this->getCompletedJobsCount(),
            'completion_rate' => $this->getCompletionRate(),
            'average_completion_time' => $this->getAverageCompletionTime(),
            'is_available' => $this->isAvailable(),
            'is_working_hours' => $this->isWorkingHours(),
        ];
    }

    public function getUpcomingSchedules($days = 7)
    {
        return $this->schedules()
            ->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays($days))
            ->where('status', 'scheduled')
            ->with('property')
            ->orderBy('scheduled_date')
            ->get();
    }

    public function getOverdueSchedules()
    {
        return $this->schedules()
            ->where('scheduled_date', '<', now())
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->with('property')
            ->get();
    }

    public function getTodaySchedules()
    {
        return $this->schedules()
            ->whereDate('scheduled_date', today())
            ->with('property')
            ->orderBy('scheduled_date')
            ->get();
    }

    public function getTotalRevenue()
    {
        return $this->maintenanceRequests()
            ->where('status', 'completed')
            ->whereNotNull('actual_cost')
            ->sum('actual_cost');
    }

    public function getMonthlyRevenue($year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        return $this->maintenanceRequests()
            ->where('status', 'completed')
            ->whereNotNull('actual_cost')
            ->whereYear('completed_at', $year)
            ->whereMonth('completed_at', $month)
            ->sum('actual_cost');
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public function updateTeamLeader($userId)
    {
        // Remove old leader from members if they were a member
        if ($this->team_leader_id) {
            $this->removeMember($this->team_leader_id);
        }

        // Add new leader as member with leader role
        $this->addMember($userId, 'leader');
        
        // Update team leader
        $this->update(['team_leader_id' => $userId]);
    }

    public function getWorkingHoursRange()
    {
        return [
            'start' => $this->working_hours_start->format('H:i'),
            'end' => $this->working_hours_end->format('H:i'),
            'duration' => $this->working_hours_start->diffInMinutes($this->working_hours_end),
        ];
    }

    public function isWithinWorkingHours($dateTime)
    {
        $date = $dateTime->copy()->setDate(
            $this->working_hours_start->year,
            $this->working_hours_start->month,
            $this->working_hours_start->day
        );

        return $dateTime->between(
            $this->working_hours_start->copy()->setDate($date->year, $date->month, $date->day),
            $this->working_hours_end->copy()->setDate($date->year, $date->month, $date->day)
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'report_type',
        'parameters',
        'filters',
        'frequency',
        'schedule_config',
        'format',
        'recipients',
        'is_active',
        'next_run_at',
        'last_run_at',
        'run_count',
        'created_by',
    ];

    protected $casts = [
        'parameters' => 'array',
        'filters' => 'array',
        'schedule_config' => 'array',
        'recipients' => 'array',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDueNow($query)
    {
        return $query->where('next_run_at', '<=', now());
    }

    public function scopeByFrequency($query, $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isDue(): bool
    {
        return $this->next_run_at && $this->next_run_at->isPast();
    }

    public function getFrequencyLabel(): string
    {
        return match($this->frequency) {
            'daily' => 'يومياً',
            'weekly' => 'أسبوعياً',
            'monthly' => 'شهرياً',
            'quarterly' => 'ربع سنوياً',
            'yearly' => 'سنوياً',
            'custom' => 'مخصص',
            default => $this->frequency
        };
    }

    public function getFormattedScheduleTime(): string
    {
        return $this->schedule_time ? $this->schedule_time->format('Y-m-d H:i:s') : 'N/A';
    }

    public function getFormattedLastRunAt(): string
    {
        return $this->last_run_at ? $this->last_run_at->format('Y-m-d H:i:s') : 'لم يتم التشغيل بعد';
    }

    public function getFormattedNextRunAt(): string
    {
        return $this->next_run_at ? $this->next_run_at->format('Y-m-d H:i:s') : 'غير محدد';
    }

    public function getSuccessRate(): float
    {
        $totalRuns = $this->run_count;
        $errorRuns = $this->error_count;
        
        if ($totalRuns === 0) {
            return 0;
        }
        
        return (($totalRuns - $errorRuns) / $totalRuns) * 100;
    }

    public function getRunUrl(): string
    {
        return route('report-schedules.run', $this->id);
    }

    public function getEditUrl(): string
    {
        return route('report-schedules.edit', $this->id);
    }

    public function getDeleteUrl(): string
    {
        return route('report-schedules.destroy', $this->id);
    }

    public function getToggleUrl(): string
    {
        return route('report-schedules.toggle', $this->id);
    }

    public function toggle()
    {
        $this->is_active = !$this->is_active;
        $this->save();
        
        if ($this->is_active) {
            $this->calculateNextRun();
        } else {
            $this->next_run_at = null;
            $this->save();
        }
    }

    public function calculateNextRun()
    {
        if (!$this->is_active) {
            return;
        }

        $now = now();
        $scheduleTime = $this->next_run_at ?? $now;

        switch ($this->frequency) {
            case 'daily':
                $this->next_run_at = $now->copy()->setTime(
                    $scheduleTime->hour,
                    $scheduleTime->minute,
                    $scheduleTime->second
                )->addDay();
                if ($this->next_run_at->isPast()) {
                    $this->next_run_at->addDay();
                }
                break;

            case 'weekly':
                $this->next_run_at = $now->copy()->setTime(
                    $scheduleTime->hour,
                    $scheduleTime->minute,
                    $scheduleTime->second
                )->next($scheduleTime->dayOfWeek ?? 0);
                if ($this->next_run_at->isPast()) {
                    $this->next_run_at->addWeek();
                }
                break;

            case 'monthly':
                $this->next_run_at = $now->copy()->setTime(
                    $scheduleTime->hour,
                    $scheduleTime->minute,
                    $scheduleTime->second
                )->addMonth()->setDay($scheduleTime->day ?? 1);
                break;

            case 'quarterly':
                $this->next_run_at = $now->copy()->setTime(
                    $scheduleTime->hour,
                    $scheduleTime->minute,
                    $scheduleTime->second
                )->addQuarter();
                break;

            case 'yearly':
                $this->next_run_at = $now->copy()->setTime(
                    $scheduleTime->hour,
                    $scheduleTime->minute,
                    $scheduleTime->second
                )->addYear();
                break;

            case 'custom':
                // Custom frequency logic would be implemented here
                $this->next_run_at = $now->copy()->addDay();
                break;

            default:
                $this->next_run_at = $now->copy()->addDay();
        }

        $this->save();
    }

    public function run()
    {
        try {
            $this->run_count++;
            $this->last_run_at = now();
            $this->save();

            // Create and generate report
            $report = Report::create([
                'title' => $this->name . ' - ' . now()->format('Y-m-d H:i'),
                'user_id' => $this->user_id,
                'generated_by' => $this->user_id,
                'type' => $this->report_type,
                'parameters' => $this->parameters,
                'filters' => $this->filters,
                'format' => $this->format,
                'status' => 'pending'
            ]);

            // Queue report generation
            dispatch(function () use ($report) {
                // Report generation logic would be here
                $report->update(['status' => 'completed', 'generated_at' => now()]);
            });

            // Send to recipients
            if (!empty($this->recipients)) {
                $this->sendReportToRecipients($report);
            }

            $this->calculateNextRun();

            return true;
        } catch (\Exception $e) {
            $this->error_count++;
            $this->save();
            
            throw $e;
        }
    }

    private function sendReportToRecipients(Report $report)
    {
        foreach ($this->recipients as $recipient) {
            // Email sending logic would be here
            // Mail::to($recipient)->send(new ReportGenerated($report));
        }
    }

    public function getRecipientsList(): string
    {
        return implode(', ', $this->recipients ?? []);
    }

    public function hasRecipients(): bool
    {
        return !empty($this->recipients);
    }

    public function canBeRun(): bool
    {
        return $this->is_active && $this->report_type && $this->user_id;
    }

    public function getEstimatedNextRun(): string
    {
        if (!$this->is_active) {
            return 'غير نشط';
        }

        if (!$this->next_run_at) {
            return 'غير محدد';
        }

        $diff = $this->next_run_at->diffForHumans(now(), true);
        
        return "خلال {$diff}";
    }

    public function getRunHistory()
    {
        return $this->reports()
            ->latest()
            ->take(10)
            ->get(['id', 'title', 'status', 'generated_at', 'created_at']);
    }

    protected static function booted()
    {
        static::creating(function ($schedule) {
            if ($schedule->is_active && !$schedule->next_run_at) {
                $schedule->calculateNextRun();
            }
        });

        static::updating(function ($schedule) {
            if ($schedule->isDirty('is_active') && $schedule->is_active) {
                $schedule->calculateNextRun();
            }
        });
    }
}

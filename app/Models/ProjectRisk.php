<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ProjectRisk extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'category',
        'probability',
        'impact',
        'risk_level',
        'status',
        'mitigation_plan',
        'contingency_plan',
        'owner_id',
        'identified_date',
        'review_date',
        'closed_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'identified_date' => 'date',
        'review_date' => 'date',
        'closed_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(ProjectRiskAction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
    }

    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function getRiskScore()
    {
        $probabilityMap = [
            'very_low' => 1,
            'low' => 2,
            'medium' => 3,
            'high' => 4,
            'very_high' => 5,
        ];

        $impactMap = [
            'very_low' => 1,
            'low' => 2,
            'medium' => 3,
            'high' => 4,
            'very_high' => 5,
        ];

        $probabilityScore = $probabilityMap[$this->probability] ?? 1;
        $impactScore = $impactMap[$this->impact] ?? 1;

        return $probabilityScore * $impactScore;
    }

    public function calculateRiskLevel()
    {
        $score = $this->getRiskScore();

        if ($score <= 4) {
            return 'low';
        } elseif ($score <= 9) {
            return 'medium';
        } elseif ($score <= 16) {
            return 'high';
        } else {
            return 'critical';
        }
    }

    public function getDaysSinceIdentification()
    {
        return Carbon::parse($this->identified_date)->diffInDays(now());
    }

    public function getDaysUntilReview()
    {
        if (!$this->review_date) return null;
        return Carbon::now()->diffInDays($this->review_date, false);
    }

    public function isOverdueForReview()
    {
        return $this->review_date && $this->review_date < now() && $this->status === 'active';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function close($closedBy = null)
    {
        $this->update([
            'status' => 'closed',
            'closed_date' => now(),
            'updated_by' => $closedBy,
        ]);
    }

    public function reopen()
    {
        $this->update([
            'status' => 'active',
            'closed_date' => null,
        ]);
    }

    public function updateRiskLevel()
    {
        $this->update(['risk_level' => $this->calculateRiskLevel()]);
    }

    // Static methods for risk categories
    public static function getCategories()
    {
        return [
            'technical' => 'فني',
            'financial' => 'مالي',
            'schedule' => 'جدول زمني',
            'resource' => 'موارد',
            'external' => 'خارجي',
            'legal' => 'قانوني',
            'environmental' => 'بيئي',
            'safety' => 'سلامة',
        ];
    }

    public static function getProbabilityLevels()
    {
        return [
            'very_low' => 'منخفض جداً',
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'very_high' => 'مرتفع جداً',
        ];
    }

    public static function getImpactLevels()
    {
        return [
            'very_low' => 'منخفض جداً',
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'very_high' => 'مرتفع جداً',
        ];
    }
}

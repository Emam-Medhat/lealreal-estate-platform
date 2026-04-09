<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantScreening extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'screening_number',
        'status',
        'screening_date',
        'completed_at',
        'credit_check',
        'criminal_check',
        'employment_verification',
        'rental_history',
        'background_check',
        'credit_score',
        'criminal_status',
        'employment_status',
        'rental_status',
        'background_status',
        'overall_score',
        'risk_level',
        'recommendation',
        'screening_notes',
        'documents_verified',
        'references_checked',
        'income_verified',
        'identity_verified',
        'screening_agency',
        'report_reference',
        'user_id',
    ];

    protected $casts = [
        'screening_date' => 'datetime',
        'completed_at' => 'datetime',
        'credit_score' => 'integer',
        'overall_score' => 'integer',
        'documents_verified' => 'boolean',
        'references_checked' => 'boolean',
        'income_verified' => 'boolean',
        'identity_verified' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePassed($query)
    {
        return $query->where('recommendation', 'approved');
    }

    public function scopeFailed($query)
    {
        return $query->where('recommendation', 'rejected');
    }

    // Attributes
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsProcessingAttribute(): bool
    {
        return $this->status === 'processing';
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsPassedAttribute(): bool
    {
        return $this->recommendation === 'approved';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->recommendation === 'rejected';
    }

    public function getScreeningDurationAttribute(): int
    {
        if (!$this->completed_at) return 0;
        return $this->screening_date->diffInHours($this->completed_at);
    }

    public function getCreditGradeAttribute(): string
    {
        if (!$this->credit_score) return 'N/A';
        
        return match(true) {
            $this->credit_score >= 750 => 'Excellent',
            $this->credit_score >= 700 => 'Good',
            $this->credit_score >= 650 => 'Fair',
            $this->credit_score >= 600 => 'Poor',
            default => 'Very Poor',
        };
    }

    public function getRiskLevelColorAttribute(): string
    {
        return match($this->risk_level) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'secondary',
        };
    }

    // Methods
    public function startScreening(): void
    {
        $this->update([
            'status' => 'processing',
            'screening_date' => now(),
        ]);
    }

    public function completeScreening(array $results): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'credit_check' => $results['credit_check'] ?? 'pending',
            'criminal_check' => $results['criminal_check'] ?? 'pending',
            'employment_verification' => $results['employment_verification'] ?? 'pending',
            'rental_history' => $results['rental_history'] ?? 'pending',
            'background_check' => $results['background_check'] ?? 'pending',
            'credit_score' => $results['credit_score'] ?? null,
            'overall_score' => $results['overall_score'] ?? 0,
            'risk_level' => $results['risk_level'] ?? 'medium',
            'recommendation' => $results['recommendation'] ?? 'pending',
            'screening_notes' => $results['screening_notes'] ?? null,
            'documents_verified' => $results['documents_verified'] ?? false,
            'references_checked' => $results['references_checked'] ?? false,
            'income_verified' => $results['income_verified'] ?? false,
            'identity_verified' => $results['identity_verified'] ?? false,
        ]);

        // Update tenant screening status
        $this->tenant->update([
            'screening_status' => $this->recommendation === 'approved' ? 'passed' : 'failed',
            'screening_completed_at' => now(),
        ]);
    }

    public function approve(): void
    {
        $this->update([
            'recommendation' => 'approved',
            'risk_level' => 'low',
        ]);

        $this->tenant->update([
            'screening_status' => 'passed',
            'screening_completed_at' => now(),
        ]);
    }

    public function reject(string $reason): void
    {
        $this->update([
            'recommendation' => 'rejected',
            'risk_level' => 'high',
            'screening_notes' => ($this->screening_notes ?? '') . "\n\nRejection: " . $reason,
        ]);

        $this->tenant->update([
            'screening_status' => 'failed',
            'screening_completed_at' => now(),
        ]);
    }

    public function calculateOverallScore(): int
    {
        $score = 0;
        $maxScore = 100;

        // Credit score (40%)
        if ($this->credit_score) {
            $creditScore = min(40, ($this->credit_score / 850) * 40);
            $score += $creditScore;
        }

        // Employment verification (20%)
        if ($this->employment_verification === 'verified') {
            $score += 20;
        } elseif ($this->employment_verification === 'partially_verified') {
            $score += 10;
        }

        // Rental history (20%)
        if ($this->rental_history === 'positive') {
            $score += 20;
        } elseif ($this->rental_history === 'neutral') {
            $score += 10;
        }

        // Criminal check (10%)
        if ($this->criminal_check === 'clear') {
            $score += 10;
        }

        // Documents verification (10%)
        if ($this->documents_verified) {
            $score += 10;
        }

        return min($maxScore, $score);
    }

    public function determineRiskLevel(): string
    {
        $score = $this->calculateOverallScore();

        return match(true) {
            $score >= 80 => 'low',
            $score >= 60 => 'medium',
            default => 'high',
        };
    }

    public function determineRecommendation(): string
    {
        $score = $this->calculateOverallScore();

        if ($this->criminal_check === 'flagged' || $this->criminal_check === 'convicted') {
            return 'rejected';
        }

        return match(true) {
            $score >= 70 => 'approved',
            $score >= 50 => 'conditional',
            default => 'rejected',
        };
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge badge-warning">معلق</span>',
            'processing' => '<span class="badge badge-info">قيد المعالجة</span>',
            'completed' => '<span class="badge badge-success">مكتمل</span>',
            default => '<span class="badge badge-secondary">' . $this->status . '</span>',
        };
    }

    public function getRecommendationBadge(): string
    {
        return match($this->recommendation) {
            'approved' => '<span class="badge badge-success">موافق</span>',
            'conditional' => '<span class="badge badge-warning">مشروط</span>',
            'rejected' => '<span class="badge badge-danger">مرفوض</span>',
            default => '<span class="badge badge-secondary">' . $this->recommendation . '</span>',
        };
    }

    public function generateScreeningNumber(): string
    {
        return 'SCR-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}

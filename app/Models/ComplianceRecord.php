<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ComplianceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'compliance_type',
        'compliance_level',
        'status',
        'score',
        'details',
        'requirements',
        'evidence',
        'assessment_date',
        'next_assessment_date',
        'expiry_date',
        'certification_number',
        'certifying_authority',
        'compliance_standards',
        'risk_factors',
        'mitigation_measures',
        'audit_trail',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'details' => 'array',
        'requirements' => 'array',
        'evidence' => 'array',
        'compliance_standards' => 'array',
        'risk_factors' => 'array',
        'mitigation_measures' => 'array',
        'audit_trail' => 'array',
        'assessment_date' => 'datetime',
        'next_assessment_date' => 'datetime',
        'expiry_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'assessment_date' => 'datetime',
        'next_assessment_date' => 'datetime',
        'expiry_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function fraudAlerts(): MorphMany
    {
        return $this->morphMany(FraudAlert::class, 'auditable');
    }

    public function securityIncidents(): MorphMany
    {
        return $this->morphMany(SecurityIncident::class, 'auditable');
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('compliance_type', $type);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('compliance_level', $level);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompliant($query)
    {
        return $query->where('status', 'compliant');
    }

    public function scopeNonCompliant($query)
    {
        return $query->where('status', 'non_compliant');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
            ->where('expiry_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeHighRisk($query)
    {
        return $query->where('score', '<=', 40);
    }

    public function scopeMediumRisk($query)
    {
        return $query->where('score', '>', 40)->where('score', '<=', 70);
    }

    public function scopeLowRisk($query)
    {
        return $query->where('score', '>', 70);
    }

    public function scopeDueForAssessment($query, $days = 7)
    {
        return $query->where('next_assessment_date', '<=', now()->addDays($days));
    }

    // Methods
    public function calculateComplianceScore(): int
    {
        $score = 100;
        
        // Check compliance requirements
        if (!empty($this->requirements)) {
            $totalRequirements = count($this->requirements);
            $metRequirements = 0;
            
            foreach ($this->requirements as $requirement) {
                if ($requirement['met'] ?? false) {
                    $metRequirements++;
                }
            }
            
            $score = ($metRequirements / $totalRequirements) * 100;
        }
        
        // Apply risk factors
        if (!empty($this->risk_factors)) {
            foreach ($this->risk_factors as $risk) {
                $score -= ($risk['impact'] ?? 0);
            }
        }
        
        return max(0, min(100, $score));
    }

    public function assessCompliance(): array
    {
        $score = $this->calculateComplianceScore();
        $status = 'compliant';
        
        if ($score < 40) {
            $status = 'non_compliant';
        } elseif ($score < 70) {
            $status = 'partially_compliant';
        }
        
        return [
            'score' => $score,
            'status' => $status,
            'level' => $this->determineComplianceLevel($score),
            'recommendations' => $this->generateRecommendations($score),
            'next_assessment_date' => $this->calculateNextAssessmentDate($score),
        ];
    }

    private function determineComplianceLevel($score): string
    {
        if ($score >= 90) return 'excellent';
        if ($score >= 80) return 'good';
        if ($score >= 70) return 'satisfactory';
        if ($score >= 60) return 'needs_improvement';
        if ($score >= 40) return 'poor';
        return 'critical';
    }

    private function generateRecommendations($score): array
    {
        $recommendations = [];
        
        if ($score < 40) {
            $recommendations[] = 'Immediate action required to address critical compliance issues';
            $recommendations[] = 'Implement comprehensive compliance training';
            $recommendations[] = 'Schedule weekly compliance reviews';
        } elseif ($score < 70) {
            $recommendations[] = 'Address identified compliance gaps';
            $recommendations[] = 'Enhance monitoring and reporting procedures';
            $recommendations[] = 'Consider additional compliance training';
        } elseif ($score < 90) {
            $recommendations[] = 'Continue monitoring compliance metrics';
            $recommendations[] = 'Optimize compliance processes';
        }
        
        return $recommendations;
    }

    private function calculateNextAssessmentDate($score): \Carbon\Carbon
    {
        $interval = '1 year';
        
        if ($score < 40) {
            $interval = '1 month';
        } elseif ($score < 70) {
            $interval = '3 months';
        } elseif ($score < 90) {
            $interval = '6 months';
        }
        
        return now()->add($interval);
    }

    public function updateComplianceStatus($status, $notes = null): void
    {
        $this->status = $status;
        if ($notes) {
            $this->notes = $notes;
        }
        
        $this->audit_trail[] = [
            'action' => 'status_updated',
            'old_status' => $this->getOriginal('status'),
            'new_status' => $status,
            'timestamp' => now(),
            'user_id' => auth()->id(),
            'notes' => $notes,
        ];
        
        $this->save();
    }

    public function addEvidence($evidenceType, $evidenceData, $description = null): void
    {
        $evidence = [
            'type' => $evidenceType,
            'data' => $evidenceData,
            'description' => $description,
            'added_at' => now(),
            'added_by' => auth()->id(),
        ];
        
        $this->evidence[] = $evidence;
        
        $this->audit_trail[] = [
            'action' => 'evidence_added',
            'evidence_type' => $evidenceType,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];
        
        $this->save();
    }

    public function addRiskFactor($riskType, $impact, $mitigation = null): void
    {
        $riskFactor = [
            'type' => $riskType,
            'impact' => $impact,
            'mitigation' => $mitigation,
            'identified_at' => now(),
            'identified_by' => auth()->id(),
        ];
        
        $this->risk_factors[] = $riskFactor;
        
        $this->audit_trail[] = [
            'action' => 'risk_factor_added',
            'risk_type' => $riskType,
            'impact' => $impact,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];
        
        $this->save();
    }

    public function addMitigationMeasure($measure, $effectiveness = null): void
    {
        $mitigation = [
            'measure' => $measure,
            'effectiveness' => $effectiveness,
            'implemented_at' => now(),
            'implemented_by' => auth()->id(),
        ];
        
        $this->mitigation_measures[] = $mitigation;
        
        $this->audit_trail[] = [
            'action' => 'mitigation_added',
            'measure' => $measure,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];
        
        $this->save();
    }

    public function generateComplianceReport(): array
    {
        return [
            'compliance_id' => $this->id,
            'compliance_type' => $this->compliance_type,
            'current_score' => $this->calculateComplianceScore(),
            'status' => $this->status,
            'level' => $this->determineComplianceLevel($this->calculateComplianceScore()),
            'assessment_date' => $this->assessment_date,
            'next_assessment_date' => $this->next_assessment_date,
            'expiry_date' => $this->expiry_date,
            'requirements_met' => $this->getRequirementsMetCount(),
            'total_requirements' => count($this->requirements ?? []),
            'risk_factors_count' => count($this->risk_factors ?? []),
            'mitigation_measures_count' => count($this->mitigation_measures ?? []),
            'evidence_count' => count($this->evidence ?? []),
            'recommendations' => $this->generateRecommendations($this->calculateComplianceScore()),
            'compliance_trend' => $this->getComplianceTrend(),
            'risk_assessment' => $this->getRiskAssessment(),
            'generated_at' => now(),
        ];
    }

    private function getRequirementsMetCount(): int
    {
        if (empty($this->requirements)) {
            return 0;
        }
        
        return count(array_filter($this->requirements, function ($req) {
            return $req['met'] ?? false;
        }));
    }

    private function getComplianceTrend(): array
    {
        // This would typically query historical compliance data
        return [
            'trend' => 'improving', // improving, declining, stable
            'change_percentage' => 15.5,
            'period' => 'last_6_months',
        ];
    }

    private function getRiskAssessment(): array
    {
        $totalRisk = 0;
        $riskCategories = [];
        
        if (!empty($this->risk_factors)) {
            foreach ($this->risk_factors as $risk) {
                $totalRisk += $risk['impact'] ?? 0;
                $category = $risk['type'] ?? 'other';
                $riskCategories[$category] = ($riskCategories[$category] ?? 0) + ($risk['impact'] ?? 0);
            }
        }
        
        return [
            'total_risk_score' => $totalRisk,
            'risk_level' => $this->determineRiskLevel($totalRisk),
            'risk_categories' => $riskCategories,
            'mitigation_coverage' => $this->calculateMitigationCoverage(),
        ];
    }

    private function determineRiskLevel($totalRisk): string
    {
        if ($totalRisk >= 80) return 'critical';
        if ($totalRisk >= 60) return 'high';
        if ($totalRisk >= 40) return 'medium';
        if ($totalRisk >= 20) return 'low';
        return 'minimal';
    }

    private function calculateMitigationCoverage(): float
    {
        if (empty($this->risk_factors)) {
            return 100.0;
        }
        
        $mitigatedRisks = 0;
        foreach ($this->risk_factors as $risk) {
            if (!empty($risk['mitigation'])) {
                $mitigatedRisks++;
            }
        }
        
        return ($mitigatedRisks / count($this->risk_factors)) * 100;
    }

    public function isExpiringSoon($days = 30): bool
    {
        return $this->expiry_date && $this->expiry_date->lessThanOrEqualTo(now()->addDays($days));
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isDueForAssessment($days = 7): bool
    {
        return $this->next_assessment_date && $this->next_assessment_date->lessThanOrEqualTo(now()->addDays($days));
    }

    public function extendExpiryDate($days): void
    {
        $this->expiry_date = $this->expiry_date->addDays($days);
        
        $this->audit_trail[] = [
            'action' => 'expiry_extended',
            'old_expiry_date' => $this->getOriginal('expiry_date'),
            'new_expiry_date' => $this->expiry_date,
            'days_extended' => $days,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];
        
        $this->save();
    }

    public function scheduleNextAssessment($date): void
    {
        $this->next_assessment_date = $date;
        
        $this->audit_trail[] = [
            'action' => 'assessment_scheduled',
            'old_date' => $this->getOriginal('next_assessment_date'),
            'new_date' => $date,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];
        
        $this->save();
    }

    public function getComplianceMetrics(): array
    {
        return [
            'score' => $this->calculateComplianceScore(),
            'status' => $this->status,
            'level' => $this->determineComplianceLevel($this->calculateComplianceScore()),
            'requirements_met' => $this->getRequirementsMetCount(),
            'total_requirements' => count($this->requirements ?? []),
            'compliance_percentage' => $this->getCompliancePercentage(),
            'risk_score' => $this->calculateTotalRiskScore(),
            'mitigation_coverage' => $this->calculateMitigationCoverage(),
            'days_until_expiry' => $this->getDaysUntilExpiry(),
            'days_until_assessment' => $this->getDaysUntilAssessment(),
        ];
    }

    private function getCompliancePercentage(): float
    {
        if (empty($this->requirements)) {
            return 100.0;
        }
        
        return ($this->getRequirementsMetCount() / count($this->requirements)) * 100;
    }

    private function calculateTotalRiskScore(): int
    {
        $totalRisk = 0;
        
        if (!empty($this->risk_factors)) {
            foreach ($this->risk_factors as $risk) {
                $totalRisk += $risk['impact'] ?? 0;
            }
        }
        
        return $totalRisk;
    }

    private function getDaysUntilExpiry(): int
    {
        if (!$this->expiry_date) {
            return -1;
        }
        
        return now()->diffInDays($this->expiry_date, false);
    }

    private function getDaysUntilAssessment(): int
    {
        if (!$this->next_assessment_date) {
            return -1;
        }
        
        return now()->diffInDays($this->next_assessment_date, false);
    }

    public static function getComplianceOverview($filters = []): array
    {
        $query = self::query();
        
        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        if (isset($filters['property_id'])) {
            $query->where('property_id', $filters['property_id']);
        }
        
        if (isset($filters['compliance_type'])) {
            $query->where('compliance_type', $filters['compliance_type']);
        }
        
        $records = $query->get();
        
        return [
            'total_records' => $records->count(),
            'compliant_records' => $records->where('status', 'compliant')->count(),
            'non_compliant_records' => $records->where('status', 'non_compliant')->count(),
            'pending_records' => $records->where('status', 'pending')->count(),
            'average_score' => $records->avg('score'),
            'high_risk_records' => $records->where('score', '<=', 40)->count(),
            'expiring_soon' => $records->filter(function ($record) {
                return $record->isExpiringSoon();
            })->count(),
            'expired' => $records->filter(function ($record) {
                return $record->isExpired();
            })->count(),
            'due_for_assessment' => $records->filter(function ($record) {
                return $record->isDueForAssessment();
            })->count(),
        ];
    }
}

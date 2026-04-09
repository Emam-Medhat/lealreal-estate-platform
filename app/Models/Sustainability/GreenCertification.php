<?php

namespace App\Models\Sustainability;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GreenCertification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'property_sustainability_id',
        'certification_type',
        'certification_level',
        'issuer_id',
        'application_date',
        'issued_date',
        'expiry_date',
        'status',
        'score',
        'requirements_met',
        'documents',
        'certificate_number',
        'verification_url',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'renewal_notes',
        'renewed_at',
        'renewed_by',
        'notes',
    ];

    protected $casts = [
        'application_date' => 'date',
        'issued_date' => 'date',
        'expiry_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'renewed_at' => 'datetime',
        'requirements_met' => 'array',
        'documents' => 'array',
        'score' => 'decimal:2',
    ];

    // Relationships
    public function propertySustainability(): BelongsTo
    {
        return $this->belongsTo(PropertySustainability::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issuer_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function renewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renewed_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expiry_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('certification_type', $type);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('certification_level', $level);
    }

    public function scopeExpiringSoon($query, $days = 90)
    {
        return $query->where('status', 'active')
                    ->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now());
    }

    // Attributes
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد المعالجة',
            'active' => 'نشط',
            'expired' => 'منتهي الصلاحية',
            'rejected' => 'مرفوض',
            'suspended' => 'معلق',
            default => 'غير معروف',
        };
    }

    public function getCertificationTypeTextAttribute(): string
    {
        return match($this->certification_type) {
            'leed' => 'LEED',
            'breeam' => 'BREEAM',
            'estidama' => 'Estidama',
            'green_globes' => 'Green Globes',
            'energy_star' => 'ENERGY STAR',
            'passive_house' => 'Passive House',
            'living_building' => 'Living Building Challenge',
            'well' => 'WELL Building Standard',
            'local_green' => 'شهادة خضراء محلية',
            default => 'غير معروف',
        };
    }

    public function getCertificationLevelTextAttribute(): string
    {
        return match($this->certification_level) {
            'platinum' => 'Platinum - البلاتيني',
            'gold' => 'Gold - الذهبي',
            'silver' => 'Silver - الفضي',
            'bronze' => 'Bronze - البرونزي',
            'certified' => 'Certified - معتمد',
            default => 'غير معروف',
        };
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) return null;
        return now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->days_until_expiry !== null && 
               $this->days_until_expiry > 0 && 
               $this->days_until_expiry <= 90;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsValidAttribute(): bool
    {
        return $this->status === 'active' && !$this->is_expired;
    }

    public function getProgressPercentageAttribute(): float
    {
        if (!$this->score) return 0;
        
        $maxScores = [
            'platinum' => 100,
            'gold' => 80,
            'silver' => 60,
            'bronze' => 40,
            'certified' => 30,
        ];

        $maxScore = $maxScores[$this->certification_level] ?? 100;
        return min(100, ($this->score / $maxScore) * 100);
    }

    public function getApplicationDurationAttribute(): ?string
    {
        if (!$this->application_date || !$this->issued_date) return null;
        
        $duration = $this->application_date->diffInDays($this->issued_date);
        return $duration . ' يوم';
    }

    // Methods
    public function approve(User $approver): void
    {
        $this->update([
            'status' => 'active',
            'issued_date' => now(),
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        // Update property sustainability certification status
        $this->propertySustainability->update([
            'certification_status' => 'certified',
        ]);
    }

    public function reject(User $rejecter, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejected_by' => $rejecter->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // Update property sustainability certification status
        $this->propertySustainability->update([
            'certification_status' => 'not_certified',
        ]);
    }

    public function renew(User $renewer, \DateTime $newExpiryDate, ?string $notes = null): void
    {
        $this->update([
            'expiry_date' => $newExpiryDate,
            'renewal_notes' => $notes,
            'renewed_at' => now(),
            'renewed_by' => $renewer->id,
        ]);
    }

    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    public function reactivate(): void
    {
        $this->update(['status' => 'active']);
    }

    public function getRequirementsProgress(): array
    {
        $requirements = $this->getCertificationRequirements();
        $met = $this->requirements_met ?? [];
        
        $progress = [];
        foreach ($requirements as $category => $categoryRequirements) {
            $totalInCategory = count($categoryRequirements);
            $metInCategory = 0;
            
            foreach ($categoryRequirements as $requirement) {
                if (in_array($requirement, $met)) {
                    $metInCategory++;
                }
            }
            
            $progress[$category] = [
                'total' => $totalInCategory,
                'met' => $metInCategory,
                'percentage' => $totalInCategory > 0 ? round(($metInCategory / $totalInCategory) * 100, 1) : 0,
                'status' => $metInCategory === $totalInCategory ? 'مكتمل' : 'قيد التنفيذ',
            ];
        }
        
        return $progress;
    }

    public function getCertificationRequirements(): array
    {
        $requirements = [
            'leed' => [
                'sustainable_sites' => [
                    'site_selection',
                    'development_density',
                    'brownfield_redevelopment',
                    'alternative_transportation',
                    'site_development',
                ],
                'water_efficiency' => [
                    'water_efficient_landscaping',
                    'innovative_wastewater_technologies',
                    'water_use_reduction',
                ],
                'energy_atmosphere' => [
                    'fundamental_commissioning',
                    'minimum_energy_performance',
                    'fundamental_refrigerant_management',
                    'optimize_energy_performance',
                    'on_site_renewable_energy',
                ],
            ],
            'breeam' => [
                'management' => [
                    'commissioning',
                    'construction_site_impacts',
                    'operational_management',
                ],
                'health_and_wellbeing' => [
                    'visual_comfort',
                    'indoor_air_quality',
                    'thermal_comfort',
                ],
                'energy' => [
                    'energy_performance',
                    'energy_reduction',
                    'low_carbon_design',
                ],
            ],
            'estidama' => [
                'integrated_development' => [
                    'site_analysis',
                    'integrated_design',
                    'stakeholder_participation',
                ],
                'natural_resources' => [
                    'water_conservation',
                    'energy_efficiency',
                    'materials',
                ],
                'livable_communities' => [
                    'connectivity',
                    'open_spaces',
                    'cultural_identity',
                ],
            ],
        ];

        return $requirements[$this->certification_type] ?? [];
    }

    public function getRemainingRequirements(): array
    {
        $allRequirements = $this->getCertificationRequirements();
        $metRequirements = $this->requirements_met ?? [];
        $remaining = [];
        
        foreach ($allRequirements as $category => $categoryRequirements) {
            foreach ($categoryRequirements as $requirement) {
                if (!in_array($requirement, $metRequirements)) {
                    $remaining[$category][] = $requirement;
                }
            }
        }
        
        return $remaining;
    }

    public function calculateEstimatedCost(): array
    {
        $baseCosts = [
            'leed' => [
                'platinum' => 50000,
                'gold' => 35000,
                'silver' => 25000,
                'certified' => 15000,
            ],
            'breeam' => [
                'outstanding' => 45000,
                'excellent' => 30000,
                'very_good' => 20000,
                'good' => 15000,
                'pass' => 10000,
            ],
            'estidama' => [
                '5_pearls' => 40000,
                '4_pearls' => 30000,
                '3_pearls' => 20000,
                '2_pearls' => 15000,
                '1_pearl' => 10000,
            ],
        ];

        $certificationCosts = $baseCosts[$this->certification_type] ?? [];
        $baseCost = $certificationCosts[$this->certification_level] ?? 20000;
        
        // Add additional costs
        $consultingFees = $baseCost * 0.3;
        $documentationFees = $baseCost * 0.2;
        $testingFees = $baseCost * 0.15;
        
        $totalCost = $baseCost + $consultingFees + $documentationFees + $testingFees;
        
        return [
            'base_certification' => $baseCost,
            'consulting_fees' => $consultingFees,
            'documentation_fees' => $documentationFees,
            'testing_fees' => $testingFees,
            'total_estimated' => round($totalCost, 2),
            'currency' => 'USD',
        ];
    }

    public function getTimeline(): array
    {
        $timelines = [
            'leed' => [
                'platinum' => 24,
                'gold' => 18,
                'silver' => 12,
                'certified' => 8,
            ],
            'breeam' => [
                'outstanding' => 18,
                'excellent' => 15,
                'very_good' => 12,
                'good' => 9,
                'pass' => 6,
            ],
            'estidama' => [
                '5_pearls' => 20,
                '4_pearls' => 16,
                '3_pearls' => 12,
                '2_pearls' => 8,
                '1_pearl' => 6,
            ],
        ];

        $certificationTimelines = $timelines[$this->certification_type] ?? [];
        $estimatedMonths = $certificationTimelines[$this->certification_level] ?? 12;
        
        return [
            'estimated_months' => $estimatedMonths,
            'phases' => [
                'pre_assessment' => '1-2 أشهر',
                'documentation' => '2-4 أشهر',
                'assessment' => '1-2 أشهر',
                'verification' => '1 شهر',
                'certification' => '1 شهر',
            ],
        ];
    }

    public function getMaintenanceRequirements(): array
    {
        return [
            'annual_reporting' => true,
            'performance_monitoring' => true,
            'periodic_audits' => true,
            'documentation_updates' => true,
            'stakeholder_training' => $this->certification_level === 'platinum',
            'continuous_improvement' => true,
        ];
    }

    public function getBenefits(): array
    {
        $benefits = [
            'environmental' => [
                'reduced_carbon_footprint',
                'energy_efficiency',
                'water_conservation',
                'waste_reduction',
            ],
            'economic' => [
                'increased_property_value',
                'lower_operating_costs',
                'marketing_advantage',
                'tax_incentives',
            ],
            'social' => [
                'enhanced_reputation',
                'occupant_health',
                'community_benefits',
                'sustainability_leadership',
            ],
        ];

        // Add level-specific benefits
        if ($this->certification_level === 'platinum') {
            $benefits['premium'] = [
                'industry_recognition',
                'awards_eligibility',
                'media_coverage',
                'premium_pricing',
            ];
        }

        return $benefits;
    }

    public function getVerificationData(): array
    {
        return [
            'certificate_number' => $this->certificate_number,
            'certification_type' => $this->certification_type_text,
            'certification_level' => $this->certification_level_text,
            'status' => $this->status_text,
            'issued_date' => $this->issued_date,
            'expiry_date' => $this->expiry_date,
            'score' => $this->score,
            'verification_url' => $this->verification_url,
            'is_valid' => $this->is_valid,
        ];
    }

    // Events
    protected static function booted()
    {
        static::updated(function ($certification) {
            // Update property sustainability certification status
            if ($certification->wasChanged('status')) {
                $status = match($certification->status) {
                    'active' => 'certified',
                    'expired', 'rejected', 'suspended' => 'not_certified',
                    'pending' => 'in_progress',
                    default => 'not_certified',
                };
                
                $certification->propertySustainability->update([
                    'certification_status' => $status,
                ]);
            }
        });
    }
}

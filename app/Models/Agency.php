<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'website',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'logo',
        'license_number',
        'established_year',
        'number_of_agents',
        'specializations',
        'coverage_areas',
        'commission_rates',
        'status',
        'is_verified',
        'is_active',
        'rating',
        'total_sales',
        'total_properties',
        'social_media',
        'contact_person',
        'contact_person_title',
        'contact_person_phone',
        'contact_person_email',
        'office_hours',
        'languages_spoken',
        'awards_certifications',
        'company_description',
        'mission_statement',
        'vision_statement',
        'core_values',
        'services_offered',
        'market_focus',
        'target_audience',
        'competitive_advantages',
        'technology_stack',
        'marketing_strategy',
        'business_model',
        'revenue_streams',
        'operational_structure',
        'growth_plans',
        'partnerships',
        'client_testimonials',
        'case_studies',
        'media_mentions',
        'industry_recognition',
        'regulatory_compliance',
        'insurance_coverage',
        'financial_performance',
        'key_metrics',
        'swot_analysis',
        'market_position',
        'brand_guidelines',
        'quality_standards',
        'training_programs',
        'agent_support',
        'commission_structure',
        'benefits_packages',
        'career_development',
        'company_culture',
        'employee_policies',
        'performance_metrics',
        'client_relationship_management',
        'lead_generation_systems',
        'crm_integration',
        'document_management',
        'transaction_management',
        'listing_management',
        'marketing_automation',
        'analytics_reporting',
        'compliance_monitoring',
        'risk_management',
        'data_security',
        'privacy_protection',
        'business_continuity',
        'disaster_recovery',
        'scalability_planning',
        'innovation_initiatives',
        'sustainability_practices',
        'community_involvement',
        'corporate_social_responsibility',
        'environmental_impact',
        'diversity_inclusion',
        'ethical_standards',
        'professional_development',
        'industry_associations',
        'network_affiliations',
        'thought_leadership',
        'research_contributions',
        'best_practices',
        'industry_trends',
        'market_analysis',
        'competitive_landscape',
        'strategic_planning',
        'operational_excellence',
        'customer_experience',
        'service_quality',
        'brand_reputation',
        'market_share',
        'growth_rate',
        'profitability',
        'efficiency_metrics',
        'satisfaction_scores',
        'retention_rates',
        'referral_rates',
        'conversion_rates',
        'lead_quality',
        'deal_velocity',
        'market_penetration',
        'geographic_expansion',
        'service_expansion',
        'product_development',
        'technology_adoption',
        'process_optimization',
        'cost_management',
        'resource_allocation',
        'capacity_planning',
        'talent_acquisition',
        'team_development',
        'leadership_development',
        'succession_planning',
        'governance_structure',
        'compliance_framework',
        'audit_processes',
        'internal_controls',
        'risk_assessment',
        'performance_monitoring',
        'continuous_improvement',
        'change_management',
        'stakeholder_engagement',
        'communication_strategy',
        'crisis_management',
        'reputation_management',
        'public_relations',
        'brand_management',
        'digital_presence',
        'online_marketing',
        'social_media_strategy',
        'content_strategy',
        'seo_optimization',
        'paid_advertising',
        'email_marketing',
        'event_marketing',
        'partnership_marketing',
        'referral_marketing',
        'direct_marketing',
        'offline_marketing',
        'traditional_advertising',
        'guerrilla_marketing',
        'viral_marketing',
        'influencer_marketing',
        'affiliate_marketing',
        'co_marketing',
        'joint_ventures',
        'strategic_alliances',
        'distribution_channels',
        'sales_channels',
        'partnership_channels',
        'direct_sales',
        'indirect_sales',
        'online_sales',
        'offline_sales',
        'hybrid_sales',
        'multi_channel_sales',
        'cross_channel_sales',
        'omnichannel_sales',
        'integrated_sales',
        'seamless_sales',
        'frictionless_sales',
        'automated_sales',
        'ai_powered_sales',
        'data_driven_sales',
        'predictive_sales',
        'personalized_sales',
        'contextual_sales',
        'real_time_sales',
        'dynamic_sales',
        'adaptive_sales',
        'intelligent_sales',
        'smart_sales',
        'automated_marketing',
        'ai_marketing',
        'data_marketing',
        'predictive_marketing',
        'personalized_marketing',
        'contextual_marketing',
        'real_time_marketing',
        'dynamic_marketing',
        'adaptive_marketing',
        'intelligent_marketing',
        'smart_marketing',
        'automated_operations',
        'ai_operations',
        'data_operations',
        'predictive_operations',
        'personalized_operations',
        'contextual_operations',
        'real_time_operations',
        'dynamic_operations',
        'adaptive_operations',
        'intelligent_operations',
        'smart_operations',
        'automated_management',
        'ai_management',
        'data_management',
        'predictive_management',
        'personalized_management',
        'contextual_management',
        'real_time_management',
        'dynamic_management',
        'adaptive_management',
        'intelligent_management',
        'smart_management',
    ];

    protected $casts = [
        'established_year' => 'integer',
        'number_of_agents' => 'integer',
        'specializations' => 'array',
        'coverage_areas' => 'array',
        'commission_rates' => 'json',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
        'total_sales' => 'decimal:15,2',
        'total_properties' => 'integer',
        'social_media' => 'json',
        'office_hours' => 'json',
        'languages_spoken' => 'array',
        'awards_certifications' => 'array',
        'services_offered' => 'array',
        'market_focus' => 'array',
        'target_audience' => 'array',
        'competitive_advantages' => 'array',
        'technology_stack' => 'array',
        'marketing_strategy' => 'json',
        'business_model' => 'json',
        'revenue_streams' => 'array',
        'operational_structure' => 'json',
        'growth_plans' => 'json',
        'partnerships' => 'array',
        'client_testimonials' => 'array',
        'case_studies' => 'array',
        'media_mentions' => 'array',
        'industry_recognition' => 'array',
        'regulatory_compliance' => 'json',
        'insurance_coverage' => 'json',
        'financial_performance' => 'json',
        'key_metrics' => 'json',
        'swot_analysis' => 'json',
        'market_position' => 'json',
        'brand_guidelines' => 'json',
        'quality_standards' => 'json',
        'training_programs' => 'array',
        'agent_support' => 'json',
        'commission_structure' => 'json',
        'benefits_packages' => 'json',
        'career_development' => 'json',
        'company_culture' => 'json',
        'employee_policies' => 'json',
        'performance_metrics' => 'json',
        'client_relationship_management' => 'json',
        'lead_generation_systems' => 'json',
        'crm_integration' => 'json',
        'document_management' => 'json',
        'transaction_management' => 'json',
        'listing_management' => 'json',
        'marketing_automation' => 'json',
        'analytics_reporting' => 'json',
        'compliance_monitoring' => 'json',
        'risk_management' => 'json',
        'data_security' => 'json',
        'privacy_protection' => 'json',
        'business_continuity' => 'json',
        'disaster_recovery' => 'json',
        'scalability_planning' => 'json',
        'innovation_initiatives' => 'json',
        'sustainability_practices' => 'json',
        'community_involvement' => 'json',
        'corporate_social_responsibility' => 'json',
        'environmental_impact' => 'json',
        'diversity_inclusion' => 'json',
        'ethical_standards' => 'json',
        'professional_development' => 'json',
        'industry_associations' => 'array',
        'network_affiliations' => 'array',
        'thought_leadership' => 'json',
        'research_contributions' => 'json',
        'best_practices' => 'json',
        'industry_trends' => 'json',
        'market_analysis' => 'json',
        'competitive_landscape' => 'json',
        'strategic_planning' => 'json',
        'operational_excellence' => 'json',
        'customer_experience' => 'json',
        'service_quality' => 'json',
        'brand_reputation' => 'json',
        'market_share' => 'json',
        'growth_rate' => 'json',
        'profitability' => 'json',
        'efficiency_metrics' => 'json',
        'satisfaction_scores' => 'json',
        'retention_rates' => 'json',
        'referral_rates' => 'json',
        'conversion_rates' => 'json',
        'lead_quality' => 'json',
        'deal_velocity' => 'json',
        'market_penetration' => 'json',
        'geographic_expansion' => 'json',
        'service_expansion' => 'json',
        'product_development' => 'json',
        'technology_adoption' => 'json',
        'process_optimization' => 'json',
        'cost_management' => 'json',
        'resource_allocation' => 'json',
        'capacity_planning' => 'json',
        'talent_acquisition' => 'json',
        'team_development' => 'json',
        'leadership_development' => 'json',
        'succession_planning' => 'json',
        'governance_structure' => 'json',
        'compliance_framework' => 'json',
        'audit_processes' => 'json',
        'internal_controls' => 'json',
        'risk_assessment' => 'json',
        'performance_monitoring' => 'json',
        'continuous_improvement' => 'json',
        'change_management' => 'json',
        'stakeholder_engagement' => 'json',
        'communication_strategy' => 'json',
        'crisis_management' => 'json',
        'reputation_management' => 'json',
        'public_relations' => 'json',
        'brand_management' => 'json',
        'digital_presence' => 'json',
        'online_marketing' => 'json',
        'social_media_strategy' => 'json',
        'content_strategy' => 'json',
        'seo_optimization' => 'json',
        'paid_advertising' => 'json',
        'email_marketing' => 'json',
        'event_marketing' => 'json',
        'partnership_marketing' => 'json',
        'referral_marketing' => 'json',
        'direct_marketing' => 'json',
        'offline_marketing' => 'json',
        'traditional_advertising' => 'json',
        'guerrilla_marketing' => 'json',
        'viral_marketing' => 'json',
        'influencer_marketing' => 'json',
        'affiliate_marketing' => 'json',
        'co_marketing' => 'json',
        'joint_ventures' => 'json',
        'strategic_alliances' => 'json',
        'distribution_channels' => 'json',
        'sales_channels' => 'json',
        'partnership_channels' => 'json',
        'direct_sales' => 'json',
        'indirect_sales' => 'json',
        'online_sales' => 'json',
        'offline_sales' => 'json',
        'hybrid_sales' => 'json',
        'multi_channel_sales' => 'json',
        'cross_channel_sales' => 'json',
        'omnichannel_sales' => 'json',
        'integrated_sales' => 'json',
        'seamless_sales' => 'json',
        'frictionless_sales' => 'json',
        'automated_sales' => 'json',
        'ai_powered_sales' => 'json',
        'data_driven_sales' => 'json',
        'predictive_sales' => 'json',
        'personalized_sales' => 'json',
        'contextual_sales' => 'json',
        'real_time_sales' => 'json',
        'dynamic_sales' => 'json',
        'adaptive_sales' => 'json',
        'intelligent_sales' => 'json',
        'smart_sales' => 'json',
        'automated_marketing' => 'json',
        'ai_marketing' => 'json',
        'data_marketing' => 'json',
        'predictive_marketing' => 'json',
        'personalized_marketing' => 'json',
        'contextual_marketing' => 'json',
        'real_time_marketing' => 'json',
        'dynamic_marketing' => 'json',
        'adaptive_marketing' => 'json',
        'intelligent_marketing' => 'json',
        'smart_marketing' => 'json',
        'automated_operations' => 'json',
        'ai_operations' => 'json',
        'data_operations' => 'json',
        'predictive_operations' => 'json',
        'personalized_operations' => 'json',
        'contextual_operations' => 'json',
        'real_time_operations' => 'json',
        'dynamic_operations' => 'json',
        'adaptive_operations' => 'json',
        'intelligent_operations' => 'json',
        'smart_operations' => 'json',
        'automated_management' => 'json',
        'ai_management' => 'json',
        'data_management' => 'json',
        'predictive_management' => 'json',
        'personalized_management' => 'json',
        'contextual_management' => 'json',
        'real_time_management' => 'json',
        'dynamic_management' => 'json',
        'adaptive_management' => 'json',
        'intelligent_management' => 'json',
        'smart_management' => 'json',
        'deleted_at' => 'datetime',
    ];

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByRating($query, $minRating = 0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->agents()->avg('rating') ?? 0;
    }

    public function getTotalAgentsAttribute(): int
    {
        return $this->agents()->count();
    }

    public function getActiveAgentsAttribute(): int
    {
        return $this->agents()->where('is_active', true)->count();
    }

    public function getVerifiedAgentsAttribute(): int
    {
        return $this->agents()->where('is_verified', true)->count();
    }

    public function getTotalPropertiesAttribute(): int
    {
        return $this->agents()->withCount('properties')->get()->sum('properties_count');
    }

    public function getActivePropertiesAttribute(): int
    {
        return $this->agents()->withCount(['properties' => function($query) {
            $query->where('status', 'active');
        }])->get()->sum('properties_count');
    }

    public function getTotalSalesAttribute(): float
    {
        return $this->agents()->sum('total_sales');
    }

    public function getAverageCommissionRateAttribute(): float
    {
        return $this->agents()->avg('commission_rate') ?? 0;
    }

    public function getTopPerformingAgentsAttribute(): collection
    {
        return $this->agents()->orderBy('total_sales', 'desc')->take(5)->get();
    }

    public function getRecentAgentsAttribute(): collection
    {
        return $this->agents()->orderBy('created_at', 'desc')->take(5)->get();
    }

    public function getSpecializationAreasAttribute(): array
    {
        $specializations = $this->agents()->pluck('specializations')->flatten()->unique()->filter();
        return $specializations->toArray();
    }

    public function getCoverageAreasAttribute(): array
    {
        $areas = $this->agents()->pluck('coverage_areas')->flatten()->unique()->filter();
        return $areas->toArray();
    }

    public function getMarketPresenceAttribute(): array
    {
        return [
            'total_agents' => $this->total_agents,
            'active_agents' => $this->active_agents,
            'verified_agents' => $this->verified_agents,
            'total_properties' => $this->total_properties,
            'active_properties' => $this->active_properties,
            'total_sales' => $this->total_sales,
            'average_rating' => $this->average_rating,
            'specialization_areas' => $this->specialization_areas,
            'coverage_areas' => $this->coverage_areas,
        ];
    }

    public function getPerformanceMetricsAttribute(): array
    {
        return [
            'agent_productivity' => $this->total_properties / max($this->total_agents, 1),
            'sales_per_agent' => $this->total_sales / max($this->total_agents, 1),
            'average_agent_rating' => $this->average_rating,
            'verification_rate' => ($this->verified_agents / max($this->total_agents, 1)) * 100,
            'activity_rate' => ($this->active_agents / max($this->total_agents, 1)) * 100,
        ];
    }

    public function calculateMarketShare(): float
    {
        // This would typically compare against total market data
        // For now, return a calculated metric based on agency performance
        return ($this->total_sales / 1000000) * 100; // Assuming 1M market size
    }

    public function getGrowthRate(): float
    {
        // Calculate growth rate based on recent performance
        $sixMonthsAgo = now()->subMonths(6);
        $recentSales = $this->agents()
            ->whereHas('properties', function($query) use ($sixMonthsAgo) {
                $query->where('sold_at', '>=', $sixMonthsAgo);
            })
            ->withCount(['properties' => function($query) use ($sixMonthsAgo) {
                $query->where('sold_at', '>=', $sixMonthsAgo);
            }])
            ->get()
            ->sum('properties_count');

        return $recentSales / max($this->total_sales, 1) * 100;
    }

    public function getCompetitivePosition(): string
    {
        $marketShare = $this->calculateMarketShare();
        
        if ($marketShare >= 20) return 'Market Leader';
        if ($marketShare >= 10) return 'Major Player';
        if ($marketShare >= 5) return 'Competitive';
        if ($marketShare >= 1) return 'Emerging';
        return 'Niche';
    }

    public function getStrategicRecommendations(): array
    {
        $recommendations = [];
        $metrics = $this->performance_metrics;

        if ($metrics['verification_rate'] < 80) {
            $recommendations[] = 'Focus on agent verification and certification';
        }

        if ($metrics['activity_rate'] < 70) {
            $recommendations[] = 'Implement agent engagement and retention programs';
        }

        if ($metrics['agent_productivity'] < 5) {
            $recommendations[] = 'Provide additional training and resources';
        }

        if ($this->average_rating < 4.0) {
            $recommendations[] = 'Improve service quality and customer satisfaction';
        }

        if ($this->growth_rate < 10) {
            $recommendations[] = 'Develop growth strategies and market expansion';
        }

        return $recommendations;
    }
}

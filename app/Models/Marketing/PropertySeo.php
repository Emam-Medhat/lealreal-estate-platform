<?php

namespace App\Models\Marketing;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertySeo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'page_title',
        'meta_description',
        'meta_keywords',
        'focus_keywords',
        'canonical_url',
        'robots_meta',
        'og_tags',
        'twitter_cards',
        'structured_data',
        'content_optimization',
        'technical_seo',
        'tracking_analytics',
        'local_seo',
        'keyword_research',
        'competitor_analysis',
        'performance_tracking',
        'seo_score',
        'total_keywords',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'meta_keywords' => 'array',
        'focus_keywords' => 'array',
        'robots_meta' => 'array',
        'og_tags' => 'array',
        'twitter_cards' => 'array',
        'structured_data' => 'array',
        'content_optimization' => 'array',
        'technical_seo' => 'array',
        'tracking_analytics' => 'array',
        'local_seo' => 'array',
        'keyword_research' => 'array',
        'competitor_analysis' => 'array',
        'performance_tracking' => 'array',
        'seo_score' => 'integer',
        'content_optimization.heading_structure' => 'boolean',
        'content_optimization.keyword_density' => 'boolean',
        'content_optimization.readability_score' => 'boolean',
        'technical_seo.mobile_friendly' => 'boolean',
        'technical_seo.https_enabled' => 'boolean',
        'technical_seo.xml_sitemap' => 'boolean',
        'technical_seo.robots_txt' => 'boolean',
        'technical_seo.breadcrumb_navigation' => 'boolean',
        'technical_seo.schema_markup' => 'boolean',
        'tracking_analytics.google_analytics' => 'boolean',
        'tracking_analytics.google_search_console' => 'boolean',
        'tracking_analytics.bing_webmaster_tools' => 'boolean',
        'tracking_analytics.google_tag_manager' => 'boolean',
        'tracking_analytics.facebook_pixel' => 'boolean',
        'local_seo.google_business_profile' => 'boolean',
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
    public function scopeOptimized($query)
    {
        return $query->where('seo_score', '>=', 80);
    }

    public function scopeNeedsImprovement($query)
    {
        return $query->whereBetween('seo_score', [50, 79]);
    }

    public function scopePoorSeo($query)
    {
        return $query->where('seo_score', '<', 50);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeWithAnalytics($query)
    {
        return $query->where('tracking_analytics.google_analytics', true);
    }

    public function scopeWithSchema($query)
    {
        return $query->where('technical_seo.schema_markup', true);
    }

    public function scopeMobileFriendly($query)
    {
        return $query->where('technical_seo.mobile_friendly', true);
    }

    public function scopeWithLocalSeo($query)
    {
        return $query->where('local_seo.google_business_profile', true);
    }

    // Methods
    public function calculateSeoScore()
    {
        $score = 0;
        $totalChecks = 0;

        // Basic SEO elements (30 points)
        if (!empty($this->page_title) && strlen($this->page_title) <= 60) {
            $score += 10;
        }
        $totalChecks++;

        if (!empty($this->meta_description) && strlen($this->meta_description) <= 160) {
            $score += 10;
        }
        $totalChecks++;

        if (!empty($this->focus_keywords) && count($this->focus_keywords) >= 1) {
            $score += 10;
        }
        $totalChecks++;

        // Technical SEO (25 points)
        if ($this->technical_seo['mobile_friendly'] ?? false) {
            $score += 5;
        }
        $totalChecks++;

        if ($this->technical_seo['https_enabled'] ?? false) {
            $score += 5;
        }
        $totalChecks++;

        if ($this->technical_seo['xml_sitemap'] ?? false) {
            $score += 5;
        }
        $totalChecks++;

        if ($this->technical_seo['robots_txt'] ?? false) {
            $score += 5;
        }
        $totalChecks++;

        if ($this->technical_seo['schema_markup'] ?? false) {
            $score += 5;
        }
        $totalChecks++;

        // Social Media (20 points)
        if (!empty($this->og_tags['og_title'])) {
            $score += 5;
        }
        $totalChecks++;

        if (!empty($this->og_tags['og_description'])) {
            $score += 5;
        }
        $totalChecks++;

        if (!empty($this->twitter_cards['title'])) {
            $score += 5;
        }
        $totalChecks++;

        if (!empty($this->twitter_cards['description'])) {
            $score += 5;
        }
        $totalChecks++;

        // Content Optimization (25 points)
        if ($this->content_optimization['heading_structure'] ?? false) {
            $score += 8;
        }
        $totalChecks++;

        if ($this->content_optimization['keyword_density'] ?? false) {
            $score += 8;
        }
        $totalChecks++;

        if ($this->content_optimization['readability_score'] ?? false) {
            $score += 9;
        }
        $totalChecks++;

        $this->seo_score = round(($score / $totalChecks) * 100);
        $this->save();

        return $this->seo_score;
    }

    public function getSeoGradeAttribute()
    {
        return match(true) {
            $this->seo_score >= 90 => 'A+',
            $this->seo_score >= 80 => 'A',
            $this->seo_score >= 70 => 'B',
            $this->seo_score >= 60 => 'C',
            $this->seo_score >= 50 => 'D',
            default => 'F',
        };
    }

    public function getSeoStatusAttribute()
    {
        return match(true) {
            $this->seo_score >= 80 => 'ممتاز',
            $this->seo_score >= 60 => 'جيد',
            $this->seo_score >= 40 => 'متوسط',
            default => 'يحتاج تحسين',
        };
    }

    public function getStatusColorAttribute()
    {
        return match(true) {
            $this->seo_score >= 80 => 'green',
            $this->seo_score >= 60 => 'yellow',
            $this->seo_score >= 40 => 'orange',
            default => 'red',
        };
    }

    public function getTitleLengthStatusAttribute()
    {
        $length = strlen($this->page_title ?? '');
        
        return match(true) {
            $length === 0 => 'فارغ',
            $length <= 60 => 'مثالي',
            $length <= 70 => 'جيد',
            default => 'طويل جداً',
        };
    }

    public function getDescriptionLengthStatusAttribute()
    {
        $length = strlen($this->meta_description ?? '');
        
        return match(true) {
            $length === 0 => 'فارغ',
            $length <= 160 => 'مثالي',
            $length <= 170 => 'جيد',
            default => 'طويل جداً',
        };
    }

    public function getKeywordDensityAttribute()
    {
        // Mock calculation - in real implementation this would analyze actual content
        return rand(1, 5) . '%';
    }

    public function getReadabilityScoreAttribute()
    {
        // Mock calculation - in real implementation this would analyze actual content
        return rand(60, 95);
    }

    public function getPageSpeedAttribute()
    {
        // Mock calculation - in real implementation this would use actual page speed data
        return rand(60, 95);
    }

    public function getMobileUsabilityScoreAttribute()
    {
        return $this->technical_seo['mobile_friendly'] ?? false ? 100 : 0;
    }

    public function getCoreWebVitalsAttribute()
    {
        return [
            'lcp' => [
                'score' => rand(60, 95),
                'status' => rand(60, 95) > 70 ? 'Good' : 'Needs Improvement',
                'value' => rand(1, 4) . 's',
            ],
            'fid' => [
                'score' => rand(70, 100),
                'status' => 'Good',
                'value' => rand(50, 200) . 'ms',
            ],
            'cls' => [
                'score' => rand(70, 100),
                'status' => 'Good',
                'value' => rand(0, 0.3),
            ],
        ];
    }

    public function getKeywordRankingsAttribute()
    {
        $rankings = [];
        
        foreach ($this->focus_keywords ?? [] as $keyword) {
            $rankings[] = [
                'keyword' => $keyword,
                'position' => rand(1, 50),
                'search_volume' => rand(100, 10000),
                'difficulty' => ['easy', 'medium', 'hard'][rand(0, 2)],
                'trend' => rand(-20, 50),
                'url' => $this->canonical_url,
            ];
        }
        
        return $rankings;
    }

    public function getCompetitorAnalysisAttribute()
    {
        return [
            'competitors' => $this->getCompetitorList(),
            'keyword_overlap' => rand(20, 80) . '%',
            'backlink_gap' => rand(100, 10000),
            'content_gap' => $this->getContentGap(),
            'technical_gap' => $this->getTechnicalGap(),
        ];
    }

    private function getCompetitorList()
    {
        // Mock competitor data - in real implementation this would track actual competitors
        return [
            [
                'name' => 'Competitor A',
                'url' => 'https://example-a.com',
                'seo_score' => rand(60, 95),
                'keywords' => rand(50, 500),
                'traffic' => rand(5000, 50000),
            ],
            [
                'name' => 'Competitor B',
                'url' => 'https://example-b.com',
                'seo_score' => rand(60, 95),
                'keywords' => rand(50, 500),
                'traffic' => rand(5000, 50000),
            ],
            [
                'name' => 'Competitor C',
                'url' => 'https://example-c.com',
                'seo_score' => rand(60, 95),
                'keywords' => rand(50, 500),
                'traffic' => rand(5000, 50000),
            ],
        ];
    }

    private function getContentGap()
    {
        return [
            'missing_topics' => [
                'دليل الحي',
                'مرافق قريبة',
                'مواصلات',
                'مدارس قريبة',
            ],
            'content_opportunities' => rand(5, 15),
        ];
    }

    private function getTechnicalGap()
    {
        return [
            'missing_structured_data' => !$this->technical_seo['schema_markup'],
            'missing_breadcrumbs' => !$this->technical_seo['breadcrumb_navigation'],
            'missing_sitemap' => !$this->technical_seo['xml_sitemap'],
            'missing_robots_txt' => !$this->technical_seo['robots_txt'],
        ];
    }

    public function getLocalSeoMetricsAttribute()
    {
        return [
            'google_business_profile' => $this->local_seo['google_business_profile'] ?? false,
            'local_citations' => count($this->local_seo['local_citations'] ?? []),
            'local_reviews' => [
                'platform' => $this->local_seo['local_reviews']['platform'] ?? null,
                'rating' => $this->local_seo['local_reviews']['rating'] ?? 0,
                'review_count' => $this->local_seo['local_reviews']['review_count'] ?? 0,
            ],
            'local_rankings' => rand(1, 20),
            'map_visibility' => rand(60, 95) . '%',
        ];
    }

    public function getBacklinkProfileAttribute()
    {
        // Mock backlink data - in real implementation this would track actual backlinks
        return [
            'total_backlinks' => rand(10, 500),
            'referring_domains' => rand(5, 100),
            'domain_authority' => rand(10, 80),
            'page_authority' => rand(10, 80),
            'spam_score' => rand(0, 30) . '%',
            'new_backlinks' => rand(0, 20),
            'lost_backlinks' => rand(0, 10),
        ];
    }

    public function getContentPerformanceAttribute()
    {
        return [
            'organic_traffic' => rand(100, 10000),
            'organic_traffic_growth' => rand(-20, 50) . '%',
            'average_position' => rand(1, 50),
            'click_through_rate' => rand(1, 15) . '%',
            'impressions' => rand(1000, 100000),
            'pages_indexed' => rand(5, 50),
            'crawl_errors' => rand(0, 10),
            'index_coverage' => rand(80, 100) . '%',
        ];
    }

    public function getTechnicalHealthAttribute()
    {
        $issues = [];
        
        if (!$this->technical_seo['mobile_friendly']) {
            $issues[] = 'Mobile usability issues';
        }
        
        if (!$this->technical_seo['https_enabled']) {
            $issues[] = 'HTTPS not enabled';
        }
        
        if (!$this->technical_seo['xml_sitemap']) {
            $issues[] = 'Missing XML sitemap';
        }
        
        if (!$this->technical_seo['robots_txt']) {
            $issues[] = 'Missing robots.txt';
        }
        
        if (!$this->technical_seo['schema_markup']) {
            $issues[] = 'Missing structured data';
        }
        
        return [
            'health_score' => max(0, 100 - count($issues) * 15),
            'critical_issues' => array_filter($issues, fn($issue) => 
                in_array($issue, ['Mobile usability issues', 'HTTPS not enabled'])
            ),
            'warnings' => array_filter($issues, fn($issue) => 
                !in_array($issue, ['Mobile usability issues', 'HTTPS not enabled'])
            ),
            'recommendations' => $this->getTechnicalRecommendations(),
        ];
    }

    private function getTechnicalRecommendations()
    {
        $recommendations = [];
        
        if (!$this->technical_seo['mobile_friendly']) {
            $recommendations[] = 'Optimize for mobile devices';
        }
        
        if (!$this->technical_seo['https_enabled']) {
            $recommendations[] = 'Enable HTTPS';
        }
        
        if (!$this->technical_seo['xml_sitemap']) {
            $recommendations[] = 'Create XML sitemap';
        }
        
        if (!$this->technical_seo['schema_markup']) {
            $recommendations[] = 'Add structured data markup';
        }
        
        if (!$this->content_optimization['heading_structure']) {
            $recommendations[] = 'Improve heading structure';
        }
        
        return $recommendations;
    }

    public function getOptimizationOpportunitiesAttribute()
    {
        $opportunities = [];
        
        if ($this->seo_score < 80) {
            $opportunities[] = [
                'type' => 'general',
                'priority' => 'high',
                'description' => 'Improve overall SEO score',
                'impact' => 'High',
                'effort' => 'Medium',
            ];
        }
        
        if (empty($this->structured_data)) {
            $opportunities[] = [
                'type' => 'structured_data',
                'priority' => 'medium',
                'description' => 'Add structured data for better search visibility',
                'impact' => 'Medium',
                'effort' => 'Low',
            ];
        }
        
        if (!$this->local_seo['google_business_profile']) {
            $opportunities[] = [
                'type' => 'local_seo',
                'priority' => 'medium',
                'description' => 'Set up Google Business Profile',
                'impact' => 'High',
                'effort' => 'Low',
            ];
        }
        
        return $opportunities;
    }

    public function getMonthlyReportAttribute()
    {
        return [
            'seo_score_trend' => $this->getSeoScoreTrend(),
            'keyword_rankings_trend' => $this->getKeywordRankingsTrend(),
            'organic_traffic_trend' => $this->getOrganicTrafficTrend(),
            'technical_issues_trend' => $this->getTechnicalIssuesTrend(),
            'recommendations_completed' => rand(0, 5),
            'new_opportunities' => rand(1, 3),
        ];
    }

    private function getSeoScoreTrend()
    {
        $trend = [];
        $currentScore = $this->seo_score;
        
        for ($i = 6; $i >= 0; $i--) {
            $trend[] = [
                'date' => now()->subMonths($i)->format('Y-m'),
                'score' => max(0, $currentScore - rand(-5, 5)),
            ];
        }
        
        return $trend;
    }

    private function getKeywordRankingsTrend()
    {
        $trend = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $trend[] = [
                'date' => now()->subMonths($i)->format('Y-m'),
                'average_position' => rand(1, 50),
                'top_10_keywords' => rand(0, count($this->focus_keywords ?? [])),
            ];
        }
        
        return $trend;
    }

    private function getOrganicTrafficTrend()
    {
        $trend = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $trend[] = [
                'date' => now()->subMonths($i)->format('Y-m'),
                'sessions' => rand(100, 1000),
                'users' => rand(80, 800),
                'page_views' => rand(200, 2000),
            ];
        }
        
        return $trend;
    }

    private function getTechnicalIssuesTrend()
    {
        $trend = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $trend[] = [
                'date' => now()->subMonths($i)->format('Y-m'),
                'critical_issues' => rand(0, 3),
                'warnings' => rand(0, 10),
                'notices' => rand(0, 20),
            ];
        }
        
        return $trend;
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($seo) {
            if (auth()->check()) {
                $seo->created_by = auth()->id();
            }
        });

        static::updating(function ($seo) {
            if (auth()->check()) {
                $seo->updated_by = auth()->id();
            }
        });

        static::saving(function ($seo) {
            // Auto-calculate SEO score if not set
            if (is_null($seo->seo_score)) {
                $seo->calculateSeoScore();
            }
        });
    }
}

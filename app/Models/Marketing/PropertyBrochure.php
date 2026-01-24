<?php

namespace App\Models\Marketing;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyBrochure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'title',
        'description',
        'template',
        'format',
        'orientation',
        'status',
        'cover_image',
        'logo',
        'gallery_images',
        'features',
        'amenities',
        'contact_info',
        'pricing_info',
        'custom_colors',
        'font_family',
        'include_floor_plans',
        'include_location_map',
        'include_qr_code',
        'pdf_file',
        'download_count',
        'view_count',
        'generated_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'features' => 'array',
        'amenities' => 'array',
        'contact_info' => 'array',
        'pricing_info' => 'array',
        'custom_colors' => 'array',
        'include_floor_plans' => 'boolean',
        'include_location_map' => 'boolean',
        'include_qr_code' => 'boolean',
        'generated_at' => 'datetime',
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
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByTemplate($query, $template)
    {
        return $query->where('template', $template);
    }

    public function scopeByFormat($query, $format)
    {
        return $query->where('format', $format);
    }

    public function scopeWithQRCode($query)
    {
        return $query->where('include_qr_code', true);
    }

    public function scopeWithFloorPlans($query)
    {
        return $query->where('include_floor_plans', true);
    }

    // Methods
    public function publish()
    {
        $this->update([
            'status' => 'published',
            'generated_at' => now(),
        ]);
    }

    public function generate()
    {
        $this->update([
            'status' => 'processing',
        ]);
        
        // In a real implementation, this would trigger a background job to generate the PDF
        // For now, we'll simulate the generation
        $this->publish();
    }

    public function incrementDownload()
    {
        $this->increment('download_count');
    }

    public function incrementView()
    {
        $this->increment('view_count');
    }

    public function getEngagementRateAttribute()
    {
        return $this->view_count > 0 
            ? (($this->download_count / $this->view_count) * 100) 
            : 0;
    }

    public function getTemplateDisplayNameAttribute()
    {
        return match($this->template) {
            'modern' => 'حديث',
            'classic' => 'كلاسيكي',
            'luxury' => 'فاخر',
            'minimal' => 'بسيط',
            'corporate' => 'شركات',
            default => $this->template,
        };
    }

    public function getFormatDisplayNameAttribute()
    {
        return match($this->format) {
            'a4' => 'A4',
            'a5' => 'A5',
            'letter' => 'Letter',
            'legal' => 'Legal',
            'square' => 'مربع',
            default => $this->format,
        };
    }

    public function getOrientationDisplayNameAttribute()
    {
        return match($this->orientation) {
            'portrait' => 'عمودي',
            'landscape' => 'أفقي',
            default => $this->orientation,
        };
    }

    public function getFeatureListAttribute()
    {
        return implode(', ', $this->features ?? []);
    }

    public function getAmenityListAttribute()
    {
        return implode(', ', $this->amenities ?? []);
    }

    public function getImageCountAttribute()
    {
        return count($this->gallery_images ?? []);
    }

    public function hasCoverImage()
    {
        return !empty($this->cover_image);
    }

    public function hasLogo()
    {
        return !empty($this->logo);
    }

    public function hasPDF()
    {
        return !empty($this->pdf_file);
    }

    public function isGenerated()
    {
        return $this->status === 'published' && $this->hasPDF();
    }

    public function canBeGenerated()
    {
        return in_array($this->status, ['draft']) && 
               !empty($this->title) &&
               !empty($this->property_id);
    }

    public function getDownloadUrlAttribute()
    {
        return $this->hasPDF() ? storage_path('app/public/' . $this->pdf_file) : null;
    }

    public function getCoverImageUrlAttribute()
    {
        return $this->hasCoverImage() ? storage_path('app/public/' . $this->cover_image) : null;
    }

    public function getLogoUrlAttribute()
    {
        return $this->hasLogo() ? storage_path('app/public/' . $this->logo) : null;
    }

    public function getGalleryUrlsAttribute()
    {
        $urls = [];
        foreach ($this->gallery_images ?? [] as $image) {
            $urls[] = storage_path('app/public/' . $image);
        }
        return $urls;
    }

    public function getPerformanceMetricsAttribute()
    {
        return [
            'total_views' => $this->view_count,
            'total_downloads' => $this->download_count,
            'engagement_rate' => $this->engagement_rate,
            'average_session_duration' => $this->getAverageSessionDuration(),
            'bounce_rate' => $this->getBounceRate(),
            'conversion_rate' => $this->getConversionRate(),
        ];
    }

    private function getAverageSessionDuration()
    {
        // Mock calculation - in real implementation this would track actual user behavior
        return rand(30, 180) . ' seconds';
    }

    private function getBounceRate()
    {
        // Mock calculation - in real implementation this would track actual user behavior
        return rand(20, 60) . '%';
    }

    private function getConversionRate()
    {
        return $this->view_count > 0 
            ? (($this->download_count / $this->view_count) * 100) 
            : 0;
    }

    public function getDesignAnalysisAttribute()
    {
        return [
            'template_complexity' => $this->getTemplateComplexity(),
            'color_scheme' => $this->getColorScheme(),
            'layout_balance' => $this->getLayoutBalance(),
            'content_density' => $this->getContentDensity(),
            'visual_hierarchy' => $this->getVisualHierarchy(),
            'brand_consistency' => $this->getBrandConsistency(),
        ];
    }

    private function getTemplateComplexity()
    {
        return match($this->template) {
            'minimal' => 'بسيط',
            'modern' => 'متوسط',
            'classic' => 'متوسط',
            'luxury' => 'معقد',
            'corporate' => 'متوسط',
            default => 'غير معروف',
        };
    }

    private function getColorScheme()
    {
        if (empty($this->custom_colors)) {
            return 'افتراضي';
        }

        $colors = count($this->custom_colors);
        return match($colors) {
            1 => 'أحادي اللون',
            2 => 'ثنائي اللون',
            3 => 'ثلاثي الألوان',
            default => 'متعدد الألوان',
        };
    }

    private function getLayoutBalance()
    {
        // Mock analysis - in real implementation this would analyze the actual layout
        return match($this->orientation) {
            'portrait' => 'متوازن عمودياً',
            'landscape' => 'متوازن أفقياً',
            default => 'غير متوازن',
        };
    }

    private function getContentDensity()
    {
        $contentItems = count($this->features) + count($this->amenities) + $this->image_count;
        
        return match(true) {
            $contentItems <= 5 => 'منخفض',
            $contentItems <= 10 => 'متوسط',
            default => 'مرتفع',
        };
    }

    private function getVisualHierarchy()
    {
        // Mock analysis - in real implementation this would analyze the actual design
        return $this->hasCoverImage() ? 'جيد' : 'يحتاج تحسين';
    }

    private function getBrandConsistency()
    {
        return $this->hasLogo() ? 'عالي' : 'متوسط';
    }

    public function getOptimizationSuggestionsAttribute()
    {
        $suggestions = [];

        if (!$this->hasCoverImage()) {
            $suggestions[] = 'إضافة صورة غلاف لجذب الانتباه';
        }

        if (!$this->hasLogo()) {
            $suggestions[] = 'إضافة شعار للهوية البصرية';
        }

        if ($this->image_count < 3) {
            $suggestions[] = 'إضافة المزيد من الصور لعرض العقار بشكل أفضل';
        }

        if (!$this->include_floor_plans) {
            $suggestions[] = 'تضمين مخططات الطوابق للمساعدة في التصور';
        }

        if (!$this->include_location_map) {
            $suggestions[] = 'إضافة خريطة الموقع لتسهيل الوصول';
        }

        if (!$this->include_qr_code) {
            $suggestions[] = 'إضافة رمز QR للوصول السريع للمعلومات';
        }

        if (empty($this->pricing_info)) {
            $suggestions[] = 'إضافة معلومات الأسعار للشفافية';
        }

        if ($this->engagement_rate < 10) {
            $suggestions[] = 'تحسين المحتوى لزيادة معدل التحميل';
        }

        return $suggestions;
    }

    public function getABTestResultsAttribute()
    {
        // Mock A/B test results - in real implementation this would track actual test data
        return [
            'template_a' => [
                'template' => $this->template,
                'views' => $this->view_count * 0.9,
                'downloads' => $this->download_count * 0.85,
                'engagement_rate' => $this->engagement_rate * 0.95,
            ],
            'template_b' => [
                'template' => $this->getAlternativeTemplate(),
                'views' => $this->view_count * 1.1,
                'downloads' => $this->download_count * 1.15,
                'engagement_rate' => $this->engagement_rate * 1.05,
            ],
            'winner' => 'template_b',
            'confidence' => '92%',
        ];
    }

    private function getAlternativeTemplate()
    {
        $templates = ['modern', 'classic', 'luxury', 'minimal', 'corporate'];
        $alternatives = array_diff($templates, [$this->template]);
        return $alternatives[array_rand($alternatives)];
    }

    public function getPopularSectionsAttribute()
    {
        // Mock analytics - in real implementation this would track actual user behavior
        return [
            'cover_page' => rand(80, 100) . '%',
            'property_overview' => rand(60, 90) . '%',
            'features_section' => rand(70, 95) . '%',
            'amenities_section' => rand(50, 80) . '%',
            'pricing_section' => rand(40, 70) . '%',
            'contact_section' => rand(30, 60) . '%',
            'floor_plans' => $this->include_floor_plans ? rand(60, 85) . '%' : 'N/A',
            'location_map' => $this->include_location_map ? rand(55, 80) . '%' : 'N/A',
        ];
    }

    // Events
    protected static function booted()
    {
        static::creating(function ($brochure) {
            if (auth()->check()) {
                $brochure->created_by = auth()->id();
            }
        });

        static::updating(function ($brochure) {
            if (auth()->check()) {
                $brochure->updated_by = auth()->id();
            }
        });
    }
}

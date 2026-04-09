<?php

namespace App\Models\Neighborhood;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class LocalBusiness extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'neighborhood_id',
        'name',
        'description',
        'category',
        'status',
        'address',
        'phone',
        'email',
        'website',
        'latitude',
        'longitude',
        'opening_hours',
        'services',
        'products',
        'specialties',
        'price_range',
        'payment_methods',
        'delivery_options',
        'contact_person',
        'social_media',
        'images',
        'logo',
        'cover_image',
        'gallery',
        'verified',
        'featured',
        'tags',
        'metadata',
        'rating',
        'review_count',
        'view_count',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'neighborhood_id' => 'integer',
        'latitude' => 'decimal:8,6',
        'longitude' => 'decimal:9,6',
        'opening_hours' => 'array',
        'services' => 'array',
        'products' => 'array',
        'specialties' => 'array',
        'price_range' => 'array',
        'payment_methods' => 'array',
        'delivery_options' => 'array',
        'social_media' => 'array',
        'images' => 'array',
        'verified' => 'boolean',
        'featured' => 'boolean',
        'tags' => 'array',
        'metadata' => 'array',
        'rating' => 'decimal:2,1',
        'review_count' => 'integer',
        'view_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the neighborhood that owns the business.
     */
    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id');
    }

    /**
     * Scope a query to only include active businesses.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by neighborhood.
     */
    public function scopeByNeighborhood(Builder $query, int $neighborhoodId): Builder
    {
        return $query->where('neighborhood_id', $neighborhoodId);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to get verified businesses.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verified', true);
    }

    /**
     * Scope a query to get featured businesses.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to get businesses by rating range.
     */
    public function scopeByRatingRange(Builder $query, $min, $max = null): Builder
    {
        if ($min !== null) {
            $query->where('rating', '>=', $min);
        }
        if ($max !== null) {
            $query->where('rating', '<=', $max);
        }
        return $query;
    }

    /**
     * Scope a query to get businesses by price range.
     */
    public function scopeByPriceRange(Builder $query, $min, $max = null): Builder
    {
        if ($min !== null) {
            $query->whereJsonContains('price_range', ['min' => $min]);
        }
        if ($max !== null) {
            $query->whereJsonContains('price_range', ['max' => $max]);
        }
        return $query;
    }

    /**
     * Scope a query to get businesses with specific payment methods.
     */
    public function scopeByPaymentMethod(Builder $query, string $method): Builder
    {
        return $query->whereJsonContains('payment_methods', $method);
    }

    /**
     * Scope a query to get businesses with delivery options.
     */
    public function scopeWithDelivery(Builder $query): Builder
    {
        return $query->whereJsonLength('delivery_options', '>', 0);
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'closed' => 'مغلق',
            'pending' => 'قيد المراجعة',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get the category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        $categories = [
            'restaurant' => 'مطعم',
            'cafe' => 'مقهى',
            'grocery' => 'بقالة',
            'pharmacy' => 'صيدلية',
            'clinic' => 'عيادة',
            'school' => 'مدرسة',
            'bank' => 'بنك',
            'gas_station' => 'محطة وقود',
            'beauty_salon' => 'صالون تجميل',
            'fitness' => 'لياقة بدنية',
            'retail' => 'تجزئة',
            'service' => 'خدمات',
            'other' => 'أخرى',
        ];

        return $categories[$this->category] ?? 'غير معروف';
    }

    /**
     * Get the rating label.
     */
    public function getRatingLabelAttribute(): string
    {
        return $this->rating . ' / 5';
    }

    /**
     * Get the view count label.
     */
    public function getViewCountLabelAttribute(): string
    {
        return number_format($this->view_count) . ' مشاهدة';
    }

    /**
     * Get the review count label.
     */
    public function getReviewCountLabelAttribute(): string
    {
        return number_format($this->review_count) . ' تقييم';
    }

    /**
     * Get the formatted price range.
     */
    public function getFormattedPriceRangeAttribute(): string
    {
        if (!$this->price_range) {
            return 'غير محدد';
        }

        $min = $this->price_range['min'] ?? 0;
        $max = $this->price_range['max'] ?? 0;

        if ($min === 0 && $max === 0) {
            return 'غير محدد';
        }

        return number_format($min, 2) . ' - ' . number_format($max, 2) . ' ريال';
    }

    /**
     * Get the opening hours for today.
     */
    public function getTodayHoursAttribute(): array
    {
        $today = now()->format('l');
        return $this->opening_hours[strtolower($today)] ?? [];
    }

    /**
     * Get the opening hours for today as formatted string.
     */
    public function getTodayHoursFormattedAttribute(): string
    {
        $hours = $this->today_hours;
        
        if (empty($hours)) {
            return 'غير محدد';
        }

        $open = $hours['open'] ?? '';
        $close = $hours['close'] ?? '';

        if (empty($open) && empty($close)) {
            return 'مغلق';
        }

        if ($open === '24/7') {
            return 'مفتوح على مدار 24 ساعة';
        }

        return $open . ' - ' . $close;
    }

    /**
     * Get the services list.
     */
    public function getServicesListAttribute(): string
    {
        return implode(', ', $this->services ?? []);
    }

    /**
     * Get the products list.
     */
    public function getProductsListAttribute(): string
    {
        return implode(', ', $this->products ?? []);
    }

    /**
     * Get the specialties list.
     */
    public function getSpecialtiesListAttribute(): string
    {
        return implode(', ', $this->specialties ?? []);
    }

    /**
     * Get the payment methods list.
     */
    public function getPaymentMethodsListAttribute(): string
    {
        return implode(', ', $this->payment_methods ?? []);
    }

    /**
     * Get the delivery options list.
     */
    public function getDeliveryOptionsListAttribute(): string
    {
        return implode(', ', $this->delivery_options ?? []);
    }

    /**
     * Get the tags list.
     */
    public function getTagsListAttribute(): string
    {
        return implode(', ', $this->tags ?? []);
    }

    /**
     * Get the social media links.
     */
    public function getSocialMediaLinksAttribute(): array
    {
        return $this->social_media ?? [];
    }

    /**
     * Get the images list.
     */
    public function getImagesListAttribute(): array
    {
        return $this->images ?? [];
    }

    /**
     * Get the gallery list.
     */
    public function getGalleryListAttribute(): array
    {
        return $this->gallery ?? [];
    }

    /**
     * Get the metadata as JSON.
     */
    public function getMetadataAttribute(): string
    {
        return json_encode($this->metadata ?? []);
    }

    /**
     * Get the formatted coordinates.
     */
    public function getCoordinatesAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return $this->latitude . ', ' . $this->longitude;
        }
        return 'غير محدد';
    }

    /**
     * Get the contact info array.
     */
    public function getContactInfoAttribute(): array
    {
        return [
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'contact_person' => $this->contact_person,
            'address' => $this->address,
        ];
    }

    /**
     * Check if the business is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the business is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * Check if the business is featured.
     */
    public function isFeatured(): bool
    {
        return $this->featured;
    }

    /**
     * Check if the business is highly rated.
     */
    public function isHighlyRated(): bool
    {
        return $this->rating >= 4.0;
    }

    /**
     * Check if the business has coordinates.
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Check if the business has opening hours.
     */
    public function hasOpeningHours(): bool
    {
        return !empty($this->opening_hours);
    }

    /**
     * Check if the business is open now.
     */
    public function isOpenNow(): bool
    {
        if (!$this->hasOpeningHours()) {
            return false;
        }

        $todayHours = $this->today_hours;
        
        if (empty($todayHours)) {
            return false;
        }

        $open = $todayHours['open'] ?? null;
        $close = $todayHours['close'] ?? null;

        if ($open === '24/7') {
            return true;
        }

        if (empty($open) || empty($close)) {
            return false;
        }

        $now = now()->format('H:i');
        return $now >= $open && $now <= $close;
    }

    /**
     * Check if the business has services.
     */
    public function hasServices(): bool
    {
        return !empty($this->services);
    }

    /**
     * Check if the business has products.
     */
    public function hasProducts(): bool
    {
        return !empty($this->products);
    }

    /**
     * Check if the business has specialties.
     */
    public function hasSpecialties(): bool
    {
        return !empty($this->specialties);
    }

    /**
     * Check if the business has payment methods.
     */
    public function hasPaymentMethods(): bool
    {
        return !empty($this->payment_methods);
    }

    /**
     * Check if the business has delivery options.
     */
    public function hasDeliveryOptions(): bool
    {
        return !empty($this->delivery_options);
    }

    /**
     * Check if the business has social media.
     */
    public function hasSocialMedia(): bool
    {
        return !empty($this->social_media);
    }

    /**
     * Check if the business has images.
     */
    public function hasImages(): bool
    {
        return !empty($this->images);
    }

    /**
     * Check if the business has a logo.
     */
    public function hasLogo(): bool
    {
        return !empty($this->logo);
    }

    /**
     * Check if the business has a cover image.
     */
    public function hasCoverImage(): bool
    {
        return !empty($this->cover_image);
    }

    /**
     * Check if the business has a gallery.
     */
    public function hasGallery(): bool
    {
        return !empty($this->gallery);
    }

    /**
     * Check if the business accepts cash.
     */
    public function acceptsCash(): bool
    {
        return in_array('cash', $this->payment_methods ?? []);
    }

    /**
     * Check if the business accepts credit cards.
     */
    public function acceptsCreditCards(): bool
    {
        return in_array('credit_card', $this->payment_methods ?? []);
    }

    /**
     * Check if the business accepts digital payments.
     */
    public function acceptsDigitalPayments(): bool
    {
        return in_array('digital_payment', $this->payment_methods ?? []);
    }

    /**
     * Check if the business offers delivery.
     */
    public function offersDelivery(): bool
    {
        return !empty($this->delivery_options);
    }

    /**
     * Check if the business offers pickup.
     */
    public function offersPickup(): bool
    {
        return in_array('pickup', $this->delivery_options ?? []);
    }

    /**
     * Get the business status label.
     */
    public function getBusinessStatusAttribute(): string
    {
        if ($this->isOpenNow()) {
            return 'مفتوح الآن';
        } else {
            return 'مغلق الآن';
        }
    }

    /**
     * Get the completeness score.
     */
    public function getCompletenessScore(): float
    {
        $score = 0;
        $maxScore = 12;

        if ($this->hasOpeningHours()) $score += 1;
        if ($this->hasServices()) $score += 1;
        if ($this->hasPaymentMethods()) $score += 1;
        if ($this->hasSocialMedia()) $score += 1;
        if ($this->hasImages()) $score += 1;
        if ($this->hasLogo()) $score += 1;
        if ($this->hasCoverImage()) $score += 1;
        if ($this->hasGallery()) $score += 1;
        if ($this->hasCoordinates()) $score += 1;
        if ($this->hasSpecialties()) $score += 1;
        if ($this->acceptsCash()) $score += 1;
        if ($this->offersDelivery()) $score += 1;

        return $score / $maxScore;
    }

    /**
     * Get the completeness label.
     */
    public function getCompletenessLabelAttribute(): string
    {
        $score = $this->completeness_score;

        if ($score >= 0.8) {
            return 'مكتمل';
        } elseif ($score >= 0.6) {
            return 'جيد جداً';
        } elseif ($score >= 0.4) {
            return 'جيد';
        } elseif ($score >= 0.2) {
            return 'ضعيف';
        } else {
            return 'ضعيف جداً';
        }
    }

    /**
     * Get the popularity score.
     */
    public function getPopularityScore(): float
    {
        // Calculate popularity based on views, rating, and reviews
        $viewScore = min($this->view_count / 1000, 1) * 0.4;
        $ratingScore = ($this->rating / 5) * 0.3;
        $reviewScore = min($this->review_count / 50, 1) * 0.3;

        return $viewScore + $ratingScore + $reviewScore;
    }

    /**
     * Get the popularity label.
     */
    public function getPopularityLabelAttribute(): string
    {
        $score = $this->popularity_score;

        if ($score >= 0.8) {
            return 'شعبي جداً';
        } elseif ($score >= 0.6) {
            return 'شعبي';
        } elseif ($score >= 0.4) {
            return 'متوسط';
        } elseif ($score >= 0.2) {
            return 'قليل';
        } else {
            return 'قليل جداً';
        }
    }

    /**
     * Get the full address.
     */
    public function getFullAddressAttribute(): string
    {
        if ($this->neighborhood) {
            return $this->neighborhood->city . ', ' . $this->neighborhood->district . ', ' . $this->neighborhood->name . ', ' . $this->address;
        }
        return $this->address;
    }

    /**
     * Get the search index.
     */
    public function getSearchIndex(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'status' => $this->status,
            'rating' => $this->rating,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'services' => $this->services,
            'products' => $this->products,
            'specialties' => $this->specialties,
            'tags' => $this->tags,
            'neighborhood' => $this->neighborhood?->name ?? '',
            'city' => $this->neighborhood?->city ?? '',
            'district' => $this->neighborhood?->district ?? '',
        ];
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Update rating.
     */
    public function updateRating(float $newRating): void
    {
        $totalRating = $this->rating * $this->review_count;
        $this->review_count++;
        $this->rating = ($totalRating + $newRating) / $this->review_count;
        $this->save();
    }

    /**
     * Bootstrap the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($builder) {
            $builder->whereNull('deleted_at');
        });
    }
}

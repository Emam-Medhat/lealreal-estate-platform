<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProvider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'website',
        'license_number',
        'insurance_number',
        'specializations',
        'services_offered',
        'service_areas',
        'hourly_rate',
        'rating',
        'total_reviews',
        'is_active',
        'is_verified',
        'verified_at',
        'verified_by',
        'notes',
        'attachments',
        'created_by',
        'status',
    ];

    protected $casts = [
        'specializations' => 'array',
        'services_offered' => 'array',
        'service_areas' => 'array',
        'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'attachments' => 'array',
    ];

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function schedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function warranties()
    {
        return $this->hasMany(Warranty::class);
    }

    public function invoices()
    {
        return $this->hasMany(MaintenanceInvoice::class);
    }

    public function emergencyRepairs()
    {
        return $this->hasMany(EmergencyRepair::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviews()
    {
        return $this->hasMany(ServiceProviderReview::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeBySpecialization($query, $specialization)
    {
        return $query->whereJsonContains('specializations', $specialization);
    }

    public function scopeByServiceArea($query, $area)
    {
        return $query->whereJsonContains('service_areas', $area);
    }

    public function scope和李($query, $minRating)
    {
        return $query->wherewhere('rating', '>=', $minRating);
    }

    public function getSpecializationLabelsAttribute()
    {
        $labels = [
            'plumbing' => 'سباكة',
            'electrical' => 'كهرباء',
            'hvac' => 'تكييف',
            'structural' => 'إنشائي',
            'general' => 'عام',
            'cosmetic' => 'تجميلي',
            'landscaping' => 'تنسيق حدائق',
            'pest_control' => 'مكافحة حشرات',
            'cleaning' => 'تنظيف',
            'security' => 'أمن',
        ];

        $specializations = [];
        foreach ($this->specializations ?? [] as $spec) {
            $specializations[] = $labels[$spec] ?? $spec;
        }

        return implode(', ', $specializations);
    }

    public function getStatusLabelAttribute()
    {
        if ($this->is_active && $this->is_verified) {
            return 'نشط وموثق';
        } elseif ($this->is_active) {
            return 'نشط';
        } else {
            return 'غير نشط';
        }
    }

    public function getStatusColorAttribute()
    {
        if ($this->is_active && $this->is_verified) {
            return 'green';
        } elseif ($this->is_active) {
            return 'yellow';
        } else {
            return 'red';
        }
    }

    public function getRatingStarsAttribute()
    {
        if (!$this->rating) {
            return 'غير مصنف';
        }

        $fullStars = floor($this->rating);
        $halfStar = ($this->rating - $fullStars) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;

        return str_repeat('★', $fullStars) . 
               ($halfStar ? '☆' : '') . 
               str_repeat('☆', $emptyStars) . 
               " ({$this->rating})";
    }

    public function getAverageResponseTime()
    {
        $completedRequests = $this->maintenanceRequests()
            ->where('status', 'completed')
            ->whereNotNull('assigned_at')
            ->whereNotNull('started_at');

        if ($completedRequests->count() === 0) {
            return null;
        }

        $totalResponseTime = $completedRequests
            ->get()
            ->sum(function ($request) {
                return $request->assigned_at->diffInMinutes($request->started_at);
            });

        return $totalResponseTime / $completedRequests->count();
    }

    public function getCompletionRate()
    {
        $totalRequests = $this->maintenanceRequests()->count();
        $completedRequests = $this->maintenanceRequests()->where('status', 'completed')->count();

        if ($totalRequests === 0) {
            return 0;
        }

        return ($completedRequests / $totalRequests) * 100;
    }

    public function getTotalRevenue()
    {
        return $this->maintenanceRequests()
            ->where('status', 'completed')
            ->whereNotNull('actual_cost')
            ->sum('actual_cost');
    }

    public function getActiveRequestsCount()
    {
        return $this->maintenanceRequests()
            ->whereIn('status', ['assigned', 'in_progress'])
            ->count();
    }

    public function canAcceptNewRequest()
    {
        // Define capacity limits (can be customized based on provider capacity)
        $maxActiveRequests = 10;
        $activeRequests = $this->getActiveRequestsCount();

        return $this->is_active && $activeRequests < $maxActiveRequests;
    }

    public function isAvailableInArea($area)
    {
        return in_array($area, $this->service_areas ?? []);
    }

    public function hasSpecialization($specialization)
    {
        return in_array($specialization, $this->specializations ?? []);
    }

    public function offersService($service)
    {
        return in_array($service, $this->services_offered ?? []);
    }

    public function updateRating()
    {
        $reviews = $this->reviews()->where('is_approved', true);
        $totalReviews = $reviews->count();

        if ($totalReviews > 0) {
            $averageRating = $reviews->avg('rating');
            $this->update([
                'rating' => round($averageRating, 2),
                'total_reviews' => $totalReviews,
            ]);
        } else {
            $this->update([
                'rating' => null,
                'total_reviews' => 0,
            ]);
        }
    }

    public function verify($verifiedBy)
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
        ]);
    }

    public function unverify()
    {
        $this->update([
            'is_verified' => false,
            'verified_at' => null,
            'verified_by' => null,
        ]);
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public function getPerformanceMetrics()
    {
        return [
            'completion_rate' => $this->getCompletionRate(),
            'average_response_time' => $this->getAverageResponseTime(),
            'total_revenue' => $this->getTotalRevenue(),
            'active_requests' => $this->getActiveRequestsCount(),
            'total_requests' => $this->maintenanceRequests()->count(),
            'rating' => $this->rating,
            'total_reviews' => $this->total_reviews,
        ];
    }

    public function addSpecialization($specialization)
    {
        $specializations = $this->specializations ?? [];
        
        if (!in_array($specialization, $specializations)) {
            $specializations[] = $specialization;
            $this->update(['specializations' => $specializations]);
        }
    }

    public function removeSpecialization($specialization)
    {
        $specializations = $this->specializations ?? [];
        
        if (($key = array_search($specialization, $specializations)) !== false) {
            unset($specializations[$key]);
            $this->update(['specializations' => array_values($specializations)]);
        }
    }

    public function addServiceArea($area)
    {
        $serviceAreas = $this->service_areas ?? [];
        
        if (!in_array($area, $serviceAreas)) {
            $serviceAreas[] = $area;
            $this->update(['service_areas' => $serviceAreas]);
        }
    }

    public function removeServiceArea($area)
    {
        $serviceAreas = $this->service_areas ?? [];
        
        if (($key = array_search($area, $serviceAreas)) !== false) {
            unset($serviceAreas[$key]);
            $this->update(['service_areas' => array_values($serviceAreas)]);
        }
    }
}

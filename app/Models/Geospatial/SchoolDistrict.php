<?php

namespace App\Models\Geospatial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Metaverse\MetaverseProperty;

class SchoolDistrict extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'analytics_id',
        'district_name',
        'district_type',
        'elementary_rating',
        'middle_rating',
        'high_rating',
        'overall_rating',
        'school_count',
        'student_teacher_ratio',
        'academic_performance',
        'extracurricular_activities',
        'school_types',
        'transportation_options',
        'improvement_suggestions',
        'analysis_parameters',
        'metadata',
        'status',
    ];

    protected $casts = [
        'academic_performance' => 'array',
        'extracurricular_activities' => 'array',
        'school_types' => 'array',
        'transportation_options' => 'array',
        'improvement_suggestions' => 'array',
        'analysis_parameters' => 'array',
        'metadata' => 'array',
        'elementary_rating' => 'decimal:2',
        'middle_rating' => 'decimal:2',
        'high_rating' => 'decimal:2',
        'overall_rating' => 'decimal:2',
        'school_count' => 'integer',
        'student_teacher_ratio' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(MetaverseProperty::class);
    }

    public function analytics(): BelongsTo
    {
        return $this->belongsTo(GeospatialAnalytics::class, 'analytics_id');
    }

    // Scopes
    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDistrictType($query, $type)
    {
        return $query->where('district_type', $type);
    }

    public function scopeExcellentSchools($query)
    {
        return $query->where('overall_rating', '>=', 8.5);
    }

    public function scopeGoodSchools($query)
    {
        return $query->where('overall_rating', '>=', 7.0);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VirtualTour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'agent_id',
        'title',
        'description',
        'tour_type',
        'tour_url',
        'thumbnail_url',
        'duration',
        'views_count',
        'status',
        'is_featured',
        'is_public',
        'access_code',
        'password_protected',
        'password',
        'start_date',
        'end_date',
        'max_viewers',
        'allow_comments',
        'allow_download',
        'tour_data',
        'hotspots',
        'navigation_points',
        'room_labels',
        'floor_plan_url',
        'measurement_data',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'duration' => 'integer',
        'views_count' => 'integer',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'password_protected' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'max_viewers' => 'integer',
        'allow_comments' => 'boolean',
        'allow_download' => 'boolean',
        'tour_data' => 'json',
        'hotspots' => 'json',
        'navigation_points' => 'json',
        'room_labels' => 'json',
        'measurement_data' => 'json',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

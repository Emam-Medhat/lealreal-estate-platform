<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'type',
        'content',
        'variables',
        'is_active',
        'created_by',
        'default_duration',
        'language',
        'direction',
        'requires_signature',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'requires_signature' => 'boolean',
    ];

    public function terms()
    {
        return $this->hasMany(ContractTerm::class);
    }

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        })->when($filters['category'] ?? null, function ($query, $category) {
            $query->where('category_id', $category);
        })->when($filters['type'] ?? null, function ($query, $type) {
            $query->where('type', $type);
        })->when($filters['status'] ?? null, function ($query, $status) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'type',
        'category',
        'configuration',
        'default_parameters',
        'required_parameters',
        'available_formats',
        'is_system',
        'is_public',
        'active',
        'sort_order'
    ];

    protected $casts = [
        'configuration' => 'array',
        'default_parameters' => 'array',
        'required_parameters' => 'array',
        'available_formats' => 'array',
        'is_system' => 'boolean',
        'is_public' => 'boolean',
        'active' => 'boolean'
    ];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function isSystemTemplate(): bool
    {
        return $this->is_system;
    }

    public function isPublicTemplate(): bool
    {
        return $this->is_public;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'sales' => 'تقارير المبيعات',
            'performance' => 'تقارير الأداء',
            'financial' => 'تقارير مالية',
            'market' => 'تقارير السوق',
            'custom' => 'تقارير مخصصة',
            'compliance' => 'تقارير الامتثال',
            default => $this->type
        };
    }

    public function getCategoryLabel(): string
    {
        return match($this->category) {
            'general' => 'عام',
            'sales' => 'مبيعات',
            'marketing' => 'تسويق',
            'finance' => 'مالية',
            'operations' => 'عمليات',
            'analytics' => 'تحليلات',
            default => $this->category
        };
    }

    public function getConfigurationValue($key, $default = null)
    {
        return data_get($this->configuration, $key, $default);
    }

    public function setConfigurationValue($key, $value)
    {
        $configuration = $this->configuration ?? [];
        data_set($configuration, $key, $value);
        $this->configuration = $configuration;
    }

    public function hasParameter($parameter): bool
    {
        return in_array($parameter, $this->required_parameters ?? []);
    }

    public function requiresParameter($parameter): bool
    {
        return in_array($parameter, $this->required_parameters ?? []);
    }

    public function getDefaultParameterValue($parameter)
    {
        return data_get($this->default_parameters, $parameter);
    }

    public function supportsFormat($format): bool
    {
        return in_array($format, $this->available_formats ?? []);
    }

    public function getAvailableFormats(): array
    {
        return $this->available_formats ?? ['pdf', 'excel', 'csv'];
    }

    public function getRequiredParameters(): array
    {
        return $this->required_parameters ?? [];
    }

    public function getDefaultParameters(): array
    {
        return $this->default_parameters ?? [];
    }

    public function getConfiguration(): array
    {
        return $this->configuration ?? [];
    }

    public function canBeUsedBy($user): bool
    {
        if ($this->is_system) {
            return true;
        }

        if ($this->is_public) {
            return true;
        }

        return $this->user_id === $user->id;
    }

    public function getPreviewUrl(): string
    {
        return route('report-templates.preview', $this->id);
    }

    public function getEditUrl(): string
    {
        return route('report-templates.edit', $this->id);
    }

    public function getDeleteUrl(): string
    {
        return route('report-templates.destroy', $this->id);
    }

    public function getDuplicateUrl(): string
    {
        return route('report-templates.duplicate', $this->id);
    }

    public function duplicate()
    {
        $newTemplate = $this->replicate();
        $newTemplate->name = $this->name . ' (نسخة)';
        $newTemplate->slug = $this->slug . '-copy-' . time();
        $newTemplate->is_system = false;
        $newTemplate->is_public = false;
        $newTemplate->active = false;
        $newTemplate->save();

        return $newTemplate;
    }

    public function validateParameters(array $parameters): array
    {
        $errors = [];
        $required = $this->getRequiredParameters();

        foreach ($required as $parameter) {
            if (!isset($parameters[$parameter]) || empty($parameters[$parameter])) {
                $errors[$parameter] = "المعلمة '{$parameter}' مطلوبة";
            }
        }

        return $errors;
    }

    public function mergeWithDefaults(array $parameters): array
    {
        $defaults = $this->getDefaultParameters();
        
        return array_merge($defaults, $parameters);
    }

    public function getFormattedConfiguration(): array
    {
        $config = $this->getConfiguration();
        
        return [
            'sections' => $config['sections'] ?? [],
            'fields' => $config['fields'] ?? [],
            'charts' => $config['charts'] ?? [],
            'tables' => $config['tables'] ?? [],
            'filters' => $config['filters'] ?? []
        ];
    }

    protected static function booted()
    {
        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = str()->slug($template->name);
            }
        });

        static::updating(function ($template) {
            if ($template->isDirty('name') && empty($template->slug)) {
                $template->slug = str()->slug($template->name);
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'name',
        'description',
        'data_sources',
        'query_config',
        'visualization_config',
        'custom_fields',
        'report_data',
        'is_public',
        'is_template',
        'created_by',
        'category',
        'tags',
        'active',
        'usage_count'
    ];

    protected $casts = [
        'data_sources' => 'array',
        'query_config' => 'array',
        'visualization_config' => 'array',
        'custom_fields' => 'array',
        'report_data' => 'array',
        'is_public' => 'boolean',
        'is_template' => 'boolean',
        'tags' => 'array',
        'active' => 'boolean',
        'usage_count' => 'integer'
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopePopular($query)
    {
        return $query->orderByDesc('usage_count');
    }

    public function isPublic(): bool
    {
        return $this->is_public;
    }

    public function isTemplate(): bool
    {
        return $this->is_template;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getCategoryLabel(): string
    {
        return match($this->category) {
            'sales' => 'مبيعات',
            'marketing' => 'تسويق',
            'finance' => 'مالية',
            'operations' => 'عمليات',
            'analytics' => 'تحليلات',
            'general' => 'عام',
            default => $this->category
        };
    }

    public function getDataSourceLabels(): string
    {
        $labels = [];
        foreach ($this->data_sources as $source) {
            $labels[] = match($source) {
                'properties' => 'العقارات',
                'transactions' => 'المعاملات',
                'users' => 'المستخدمون',
                'reviews' => 'التقييمات',
                'agents' => 'الوكلاء',
                'companies' => 'الشركات',
                default => $source
            };
        }
        
        return implode(', ', $labels);
    }

    public function getColumnList(): string
    {
        return implode(', ', $this->columns ?? []);
    }

    public function getTagList(): string
    {
        return implode(', ', $this->tags ?? []);
    }

    public function hasDataSource($source): bool
    {
        return in_array($source, $this->data_sources ?? []);
    }

    public function hasColumn($column): bool
    {
        return in_array($column, $this->columns ?? []);
    }

    public function hasTag($tag): bool
    {
        return in_array($tag, $this->tags ?? []);
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

    public function addDataSource($source)
    {
        $dataSources = $this->data_sources ?? [];
        if (!in_array($source, $dataSources)) {
            $dataSources[] = $source;
            $this->data_sources = $dataSources;
        }
    }

    public function removeDataSource($source)
    {
        $dataSources = $this->data_sources ?? [];
        $key = array_search($source, $dataSources);
        if ($key !== false) {
            unset($dataSources[$key]);
            $this->data_sources = array_values($dataSources);
        }
    }

    public function addColumn($column)
    {
        $columns = $this->columns ?? [];
        if (!in_array($column, $columns)) {
            $columns[] = $column;
            $this->columns = $columns;
        }
    }

    public function removeColumn($column)
    {
        $columns = $this->columns ?? [];
        $key = array_search($column, $columns);
        if ($key !== false) {
            unset($columns[$key]);
            $this->columns = array_values($columns);
        }
    }

    public function addTag($tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->tags = $tags;
        }
    }

    public function removeTag($tag)
    {
        $tags = $this->tags ?? [];
        $key = array_search($tag, $tags);
        if ($key !== false) {
            unset($tags[$key]);
            $this->tags = array_values($tags);
        }
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    public function getUsageCount(): int
    {
        return $this->usage_count ?? 0;
    }

    public function getFormattedUsageCount(): string
    {
        $count = $this->getUsageCount();
        
        if ($count >= 1000) {
            return number_format($count / 1000, 1) . 'K';
        }
        
        return number_format($count);
    }

    public function canBeUsedBy($user): bool
    {
        if ($this->user_id === $user->id) {
            return true;
        }

        if ($this->is_public && $this->active) {
            return true;
        }

        return false;
    }

    public function canBeEditedBy($user): bool
    {
        return $this->user_id === $user->id;
    }

    public function canBeDeletedBy($user): bool
    {
        return $this->user_id === $user->id;
    }

    public function canBeDuplicatedBy($user): bool
    {
        return $this->canBeUsedBy($user);
    }

    public function duplicate($userId = null)
    {
        $newReport = $this->replicate();
        $newReport->user_id = $userId ?? $this->user_id;
        $newReport->name = $this->name . ' (نسخة)';
        $newReport->is_public = false;
        $newReport->is_template = false;
        $newReport->usage_count = 0;
        $newReport->save();

        return $newReport;
    }

    public function makePublic()
    {
        $this->update(['is_public' => true]);
    }

    public function makePrivate()
    {
        $this->update(['is_public' => false]);
    }

    public function activate()
    {
        $this->update(['active' => true]);
    }

    public function deactivate()
    {
        $this->update(['active' => false]);
    }

    public function makeTemplate()
    {
        $this->update([
            'is_template' => true,
            'template_name' => $this->name,
            'template_description' => $this->description
        ]);
    }

    public function removeTemplateStatus()
    {
        $this->update([
            'is_template' => false,
            'template_name' => null,
            'template_description' => null
        ]);
    }

    public function getEditUrl(): string
    {
        return route('custom-reports.edit', $this->id);
    }

    public function getDeleteUrl(): string
    {
        return route('custom-reports.destroy', $this->id);
    }

    public function getDuplicateUrl(): string
    {
        return route('custom-reports.duplicate', $this->id);
    }

    public function getRunUrl(): string
    {
        return route('custom-reports.run', $this->id);
    }

    public function getShareUrl(): string
    {
        return route('custom-reports.share', $this->id);
    }

    public function validateConfiguration(): array
    {
        $errors = [];

        // Validate data sources
        if (empty($this->data_sources)) {
            $errors['data_sources'] = 'يجب اختيار مصدر بيانات واحد على الأقل';
        }

        // Validate columns
        if (empty($this->columns)) {
            $errors['columns'] = 'يجب اختيار عمود واحد على الأقل';
        }

        // Validate limit
        if ($this->limit && ($this->limit < 1 || $this->limit > 1000)) {
            $errors['limit'] = 'الحد الأقصى يجب أن يكون بين 1 و 1000';
        }

        return $errors;
    }

    public function getEstimatedExecutionTime(): string
    {
        $complexity = 0;

        // Calculate complexity based on configuration
        $complexity += count($this->data_sources ?? []) * 10;
        $complexity += count($this->columns ?? []) * 5;
        $complexity += count($this->filters ?? []) * 15;
        $complexity += count($this->group_by ?? []) * 20;
        $complexity += count($this->aggregations ?? []) * 25;
        $complexity += ($this->limit ?? 100) / 10;

        if ($complexity < 50) {
            return 'أقل من دقيقة';
        } elseif ($complexity < 100) {
            return '1-3 دقائق';
        } elseif ($complexity < 200) {
            return '3-5 دقائق';
        } else {
            return 'أكثر من 5 دقائق';
        }
    }

    public function getComplexityLevel(): string
    {
        $complexity = 0;

        $complexity += count($this->data_sources ?? []) * 10;
        $complexity += count($this->columns ?? []) * 5;
        $complexity += count($this->filters ?? []) * 15;
        $complexity += count($this->group_by ?? []) * 20;
        $complexity += count($this->aggregations ?? []) * 25;

        if ($complexity < 50) {
            return 'بسيط';
        } elseif ($complexity < 100) {
            return 'متوسط';
        } elseif ($complexity < 200) {
            return 'معقد';
        } else {
            return 'معقد جداً';
        }
    }

    public function getComplexityColor(): string
    {
        $level = $this->getComplexityLevel();

        return match($level) {
            'بسيط' => 'success',
            'متوسط' => 'warning',
            'معقد' => 'danger',
            'معقد جداً' => 'dark',
            default => 'secondary'
        };
    }

    protected static function booted()
    {
        static::creating(function ($report) {
            if ($report->is_template) {
                $report->template_name = $report->template_name ?? $report->name;
                $report->template_description = $report->template_description ?? $report->description;
            }
        });

        static::updating(function ($report) {
            if ($report->isDirty('is_template')) {
                if ($report->is_template) {
                    $report->template_name = $report->template_name ?? $report->name;
                    $report->template_description = $report->template_description ?? $report->description;
                } else {
                    $report->template_name = null;
                    $report->template_description = null;
                }
            }
        });
    }
}

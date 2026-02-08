<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'direction',
        'locale',
        'flag',
        'is_default',
        'is_active',
        'is_rtl',
        'sort_order',
        'metadata'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'is_rtl' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'json'
    ];

    public function translations()
    {
        return $this->hasMany(Translation::class, 'language', 'code');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'preferred_language', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeRTL($query)
    {
        return $query->where('is_rtl', true);
    }

    public function scopeLTR($query)
    {
        return $query->where('is_rtl', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getTranslation(string $key, $default = null): ?string
    {
        return $this->translations()
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    public function setTranslation(string $key, string $value): void
    {
        Translation::updateOrCreate(
            [
                'language' => $this->code,
                'key' => $key
            ],
            [
                'value' => $value,
                'updated_at' => now()
            ]
        );
    }

    public function getTranslationCount(): int
    {
        return $this->translations()->count();
    }

    public function getCompletionPercentage(): float
    {
        $totalKeys = Translation::where('language', 'en')->distinct('key')->count('key');
        $translatedKeys = $this->translations()->distinct('key')->count('key');
        
        return $totalKeys > 0 ? ($translatedKeys / $totalKeys) * 100 : 0;
    }

    public function exportTranslations(): array
    {
        return $this->translations()
            ->orderBy('key')
            ->pluck('value', 'key')
            ->toArray();
    }

    public function importTranslations(array $translations): array
    {
        $imported = 0;
        $errors = [];

        foreach ($translations as $key => $value) {
            try {
                $this->setTranslation($key, $value);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Failed to import key: {$key} - {$e->getMessage()}";
            }
        }

        return [
            'imported' => $imported,
            'total' => count($translations),
            'errors' => $errors
        ];
    }
}

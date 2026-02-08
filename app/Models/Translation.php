<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Translation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'key',
        'language',
        'value',
        'group',
        'is_verified',
        'is_published',
        'metadata'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_published' => 'boolean',
        'metadata' => 'json'
    ];

    public function languageModel()
    {
        return $this->belongsTo(Language::class, 'language', 'code');
    }

    public function scopeForLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    public function scopeForGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('key', 'LIKE', "%{$term}%")
              ->orWhere('value', 'LIKE', "%{$term}%");
        });
    }

    public function getTranslationKey(): string
    {
        return $this->group ? "{$this->group}.{$this->key}" : $this->key;
    }

    public function getFormattedValue(): string
    {
        return $this->value;
    }

    public function getWordCount(): int
    {
        return str_word_count(strip_tags($this->value));
    }

    public function getCharacterCount(): int
    {
        return strlen(strip_tags($this->value));
    }

    public function isMissing(): bool
    {
        return empty($this->value) || $this->value === $this->key;
    }

    public function markAsVerified(): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now()
        ]);
    }

    public function publish(): void
    {
        $this->update([
            'is_published' => true,
            'published_at' => now()
        ]);
    }

    public function unpublish(): void
    {
        $this->update([
            'is_published' => false,
            'published_at' => null
        ]);
    }

    public static function getMissingTranslations(string $language): array
    {
        $englishKeys = static::where('language', 'en')
            ->distinct('key')
            ->pluck('key')
            ->toArray();

        $translatedKeys = static::where('language', $language)
            ->distinct('key')
            ->pluck('key')
            ->toArray();

        return array_diff($englishKeys, $translatedKeys);
    }

    public static function getUnverifiedCount(string $language): int
    {
        return static::where('language', $language)
            ->where('is_verified', false)
            ->count();
    }

    public static function getTranslationStats(): array
    {
        $languages = Language::active()->get();
        $stats = [];

        foreach ($languages as $language) {
            $total = Translation::where('language', $language->code)->count();
            $verified = Translation::where('language', $language->code)->where('is_verified', true)->count();
            $published = Translation::where('language', $language->code)->where('is_published', true)->count();
            $missing = count(self::getMissingTranslations($language->code));

            $stats[$language->code] = [
                'language' => $language->name,
                'total' => $total,
                'verified' => $verified,
                'published' => $published,
                'missing' => $missing,
                'completion' => $total > 0 ? (($total - $missing) / $total) * 100 : 0
            ];
        }

        return $stats;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'avatar',
        'cover_image',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'website',
        'social_links',
        'education',
        'work_experience',
        'skills',
        'languages',
        'interests',
        'is_public',
        'is_verified',
        'verification_date',
        'metadata',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'social_links' => 'json',
        'education' => 'json',
        'work_experience' => 'json',
        'skills' => 'json',
        'languages' => 'json',
        'interests' => 'json',
        'is_public' => 'boolean',
        'is_verified' => 'boolean',
        'verification_date' => 'datetime',
        'metadata' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : 0;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }
        
        // Default avatar based on gender
        return $this->gender === 'female' 
            ? 'https://ui-avatars.com/api/?name=' . urlencode($this->user->first_name) . '&background=ec4899&color=fff'
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->user->first_name) . '&background=3b82f6&color=fff';
    }

    public function getCoverImageUrlAttribute(): string
    {
        return $this->cover_image 
            ? asset('storage/covers/' . $this->cover_image)
            : 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1920&h=400&fit=crop';
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function addSocialLink(string $platform, string $url): void
    {
        $socialLinks = $this->social_links ?? [];
        $socialLinks[$platform] = $url;
        $this->update(['social_links' => $socialLinks]);
    }

    public function removeSocialLink(string $platform): void
    {
        $socialLinks = $this->social_links ?? [];
        unset($socialLinks[$platform]);
        $this->update(['social_links' => $socialLinks]);
    }

    public function addSkill(string $skill): void
    {
        $skills = $this->skills ?? [];
        if (!in_array($skill, $skills)) {
            $skills[] = $skill;
            $this->update(['skills' => $skills]);
        }
    }

    public function removeSkill(string $skill): void
    {
        $skills = $this->skills ?? [];
        $skills = array_filter($skills, fn($s) => $s !== $skill);
        $this->update(['skills' => array_values($skills)]);
    }

    public function addLanguage(string $language, string $level = 'intermediate'): void
    {
        $languages = $this->languages ?? [];
        $languages[$language] = $level;
        $this->update(['languages' => $languages]);
    }

    public function removeLanguage(string $language): void
    {
        $languages = $this->languages ?? [];
        unset($languages[$language]);
        $this->update(['languages' => $languages]);
    }
}

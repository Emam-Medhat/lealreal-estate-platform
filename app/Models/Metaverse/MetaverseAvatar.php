<?php

namespace App\Models\Metaverse;

use App\Models\User;
use App\Models\VirtualWorld;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class MetaverseAvatar extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'display_name',
        'avatar_type',
        'gender',
        'appearance',
        'clothing',
        'accessories',
        'skills',
        'preferences',
        'bio',
        'personality_traits',
        'language',
        'timezone',
        'privacy_settings',
        'current_world_id',
        'current_location',
        'current_activity',
        'is_online',
        'is_active',
        'last_active_at',
        'avatar_image_path',
        'model_path',
        'model_file_type',
        'model_file_size',
        'inventory_slots',
        'experience_points',
        'level',
        'reputation_points',
        'achievement_points',
        'social_rank',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'appearance' => 'array',
        'clothing' => 'array',
        'accessories' => 'array',
        'skills' => 'array',
        'preferences' => 'array',
        'personality_traits' => 'array',
        'privacy_settings' => 'array',
        'is_online' => 'boolean',
        'is_active' => 'boolean',
        'last_active_at' => 'datetime',
        'inventory_slots' => 'integer',
        'experience_points' => 'integer',
        'level' => 'integer',
        'reputation_points' => 'integer',
        'achievement_points' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'last_active_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function currentWorld(): BelongsTo
    {
        return $this->belongsTo(VirtualWorld::class, 'current_world_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function appearances(): HasMany
    {
        return $this->hasMany(AvatarAppearance::class, 'avatar_id');
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(AvatarInteraction::class, 'avatar_id');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(AvatarInventory::class, 'avatar_id');
    }

    public function equippedItems(): HasMany
    {
        return $this->hasMany(AvatarInventory::class, 'avatar_id')
            ->where('equipped', true);
    }

    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(MetaverseAvatar::class, 'avatar_friends', 'avatar_id', 'friend_avatar_id')
            ->withPivot(['status', 'friend_since', 'initiated_by'])
            ->wherePivot('status', 'accepted');
    }

    public function friendRequests(): HasMany
    {
        return $this->hasMany(AvatarFriendRequest::class, 'sender_avatar_id');
    }

    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(AvatarFriendRequest::class, 'receiver_avatar_id');
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(AvatarAchievement::class, 'avatar_achievement_progress', 'avatar_id', 'achievement_id')
            ->withPivot(['progress', 'completed_at', 'reward_claimed']);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(AvatarSkill::class, 'avatar_skill_levels', 'avatar_id', 'skill_id')
            ->withPivot(['level', 'experience', 'unlocked_at']);
    }

    public function tags(): MorphMany
    {
        return $this->morphMany(MetaverseTag::class, 'taggable');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(AvatarVisit::class, 'avatar_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AvatarMessage::class, 'sender_avatar_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(AvatarMessage::class, 'receiver_avatar_id');
    }

    // Scopes
    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByAvatarType($query, $avatarType)
    {
        return $query->where('avatar_type', $avatarType);
    }

    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeByWorld($query, $worldId)
    {
        return $query->where('current_world_id', $worldId);
    }

    public function scopeByLevel($query, $minLevel, $maxLevel = null)
    {
        $query->where('level', '>=', $minLevel);
        if ($maxLevel) {
            $query->where('level', '<=', $maxLevel);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('display_name', 'like', "%{$search}%")
              ->orWhere('bio', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getAvatarTypeTextAttribute(): string
    {
        return match($this->avatar_type) {
            'human' => 'إنسان',
            'robot' => 'روبوت',
            'animal' => 'حيوان',
            'fantasy' => 'خيالي',
            'custom' => 'مخصص',
            default => $this->avatar_type,
        };
    }

    public function getGenderTextAttribute(): string
    {
        return match($this->gender) {
            'male' => 'ذكر',
            'female' => 'أنثى',
            'non_binary' => 'غير ثنائي',
            'other' => 'أخرى',
            default => $this->gender,
        };
    }

    public function getFormattedLevelAttribute(): string
    {
        return "Level {$this->level}";
    }

    public function getExperienceToNextLevelAttribute(): int
    {
        return ($this->level + 1) * 1000 - $this->experience_points;
    }

    public function getProgressToNextLevelAttribute(): float
    {
        $currentLevelExp = $this->level * 1000;
        $nextLevelExp = ($this->level + 1) * 1000;
        $expInCurrentLevel = $this->experience_points - $currentLevelExp;
        $expNeededForNextLevel = $nextLevelExp - $currentLevelExp;
        
        return $expNeededForNextLevel > 0 ? ($expInCurrentLevel / $expNeededForNextLevel) * 100 : 0;
    }

    public function getSocialRankTextAttribute(): string
    {
        return match($this->social_rank) {
            'newcomer' => 'وافد جديد',
            'resident' => 'ساكن',
            'citizen' => 'مواطن',
            'influencer' => 'مؤثر',
            'celebrity' => 'مشهور',
            'legend' => 'أسطورة',
            default => $this->social_rank,
        };
    }

    public function getIsNewAvatarAttribute(): bool
    {
        return $this->created_at->diffInDays(now()) <= 7;
    }

    public function getIsExperiencedAttribute(): bool
    {
        return $this->level >= 10;
    }

    public function getIsInfluencerAttribute(): bool
    {
        return in_array($this->social_rank, ['influencer', 'celebrity', 'legend']);
    }

    public function getAvatarImageUrlAttribute(): string
    {
        return $this->avatar_image_path ? asset('storage/' . $this->avatar_image_path) : asset('images/default-avatar.jpg');
    }

    public function getModelUrlAttribute(): string
    {
        return $this->model_path ? asset('storage/' . $this->model_path) : null;
    }

    public function getOnlineStatusAttribute(): string
    {
        if ($this->is_online) {
            return 'online';
        } elseif ($this->last_active_at && $this->last_active_at->diffInMinutes(now()) <= 5) {
            return 'away';
        } else {
            return 'offline';
        }
    }

    public function getOnlineStatusTextAttribute(): string
    {
        return match($this->getOnlineStatusAttribute()) {
            'online' => 'متصل',
            'away' => 'بعيد',
            'offline' => 'غير متصل',
            default => 'غير معروف',
        };
    }

    // Methods
    public function setOnline(): void
    {
        $this->update([
            'is_online' => true,
            'last_active_at' => now(),
        ]);
    }

    public function setOffline(): void
    {
        $this->update([
            'is_online' => false,
            'last_active_at' => now(),
        ]);
    }

    public function updateLocation(string $location, ?VirtualWorld $world = null): void
    {
        $this->update([
            'current_location' => $location,
            'current_world_id' => $world?->id,
            'last_active_at' => now(),
        ]);
    }

    public function updateActivity(string $activity): void
    {
        $this->update([
            'current_activity' => $activity,
            'last_active_at' => now(),
        ]);
    }

    public function addExperience(int $points): void
    {
        $this->increment('experience_points', $points);
        
        // Check for level up
        $newLevel = floor($this->experience_points / 1000);
        if ($newLevel > $this->level) {
            $this->levelUp($newLevel);
        }
    }

    public function addReputation(int $points): void
    {
        $this->increment('reputation_points', $points);
        $this->updateSocialRank();
    }

    public function addAchievementPoints(int $points): void
    {
        $this->increment('achievement_points', $points);
    }

    private function levelUp(int $newLevel): void
    {
        $this->update([
            'level' => $newLevel,
            'inventory_slots' => $this->inventory_slots + 5, // Add inventory slots
        ]);

        // Award level up achievement
        $this->awardAchievement('level_up', $newLevel);
    }

    private function updateSocialRank(): void
    {
        $rank = 'newcomer';
        
        if ($this->reputation_points >= 10000) {
            $rank = 'legend';
        } elseif ($this->reputation_points >= 5000) {
            $rank = 'celebrity';
        } elseif ($this->reputation_points >= 2000) {
            $rank = 'influencer';
        } elseif ($this->reputation_points >= 500) {
            $rank = 'citizen';
        } elseif ($this->reputation_points >= 100) {
            $rank = 'resident';
        }

        $this->update(['social_rank' => $rank]);
    }

    public function awardAchievement(string $achievementType, $value = null): void
    {
        $achievement = AvatarAchievement::where('type', $achievementType)
            ->where('required_value', '<=', $value ?? 1)
            ->first();

        if ($achievement && !$this->hasAchievement($achievement->id)) {
            $this->achievements()->attach($achievement->id, [
                'progress' => 100,
                'completed_at' => now(),
                'reward_claimed' => false,
            ]);

            $this->addAchievementPoints($achievement->points);
        }
    }

    public function hasAchievement(int $achievementId): bool
    {
        return $this->achievements()
            ->where('achievement_id', $achievementId)
            ->whereNotNull('completed_at')
            ->exists();
    }

    public function addFriend(MetaverseAvatar $friendAvatar, string $message = null): void
    {
        if ($this->isFriendsWith($friendAvatar)) {
            return;
        }

        $this->friendRequests()->create([
            'receiver_avatar_id' => $friendAvatar->id,
            'message' => $message ?? 'أود أن أكون صديقاً',
            'status' => 'pending',
        ]);
    }

    public function acceptFriendRequest(int $requestId): void
    {
        $friendRequest = $this->receivedFriendRequests()
            ->where('id', $requestId)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendRequest->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Create reciprocal friendship
        $this->friends()->attach($friendRequest->sender_avatar_id, [
            'status' => 'accepted',
            'friend_since' => now(),
            'initiated_by' => $friendRequest->sender_avatar_id,
        ]);

        $friendRequest->sender->friends()->attach($this->id, [
            'status' => 'accepted',
            'friend_since' => now(),
            'initiated_by' => $friendRequest->sender_avatar_id,
        ]);

        $this->addReputation(10); // Add reputation for making friends
    }

    public function rejectFriendRequest(int $requestId): void
    {
        $friendRequest = $this->receivedFriendRequests()
            ->where('id', $requestId)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendRequest->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
    }

    public function removeFriend(MetaverseAvatar $friendAvatar): void
    {
        $this->friends()->detach($friendAvatar->id);
        $friendAvatar->friends()->detach($this->id);
    }

    public function isFriendsWith(MetaverseAvatar $avatar): bool
    {
        return $this->friends()->where('friend_avatar_id', $avatar->id)->exists();
    }

    public function equipItem(int $inventoryItemId, string $slot): bool
    {
        $inventoryItem = $this->inventory()->findOrFail($inventoryItemId);

        if (!$this->canEquipInSlot($inventoryItem, $slot)) {
            return false;
        }

        // Unequip current item in slot if exists
        $currentEquipped = $this->equippedItems()->where('equipped_slot', $slot)->first();
        if ($currentEquipped) {
            $currentEquipped->update(['equipped' => false, 'equipped_slot' => null]);
        }

        // Equip new item
        $inventoryItem->update([
            'equipped' => true,
            'equipped_slot' => $slot,
        ]);

        return true;
    }

    public function unequipItem(int $inventoryItemId): bool
    {
        $inventoryItem = $this->inventory()->findOrFail($inventoryItemId);

        $inventoryItem->update([
            'equipped' => false,
            'equipped_slot' => null,
        ]);

        return true;
    }

    private function canEquipInSlot($inventoryItem, string $slot): bool
    {
        // Define slot compatibility
        $slotCompatibility = [
            'head' => ['hat', 'helmet', 'hair_accessory'],
            'body' => ['shirt', 'jacket', 'armor'],
            'legs' => ['pants', 'skirt', 'armor_legs'],
            'feet' => ['shoes', 'boots'],
            'hands' => ['gloves', 'weapon'],
            'accessory' => ['ring', 'necklace', 'bracelet'],
        ];

        return in_array($inventoryItem->item->category, $slotCompatibility[$slot] ?? []);
    }

    public function getEquippedItems(): array
    {
        return $this->equippedItems()
            ->with('item')
            ->get()
            ->groupBy('equipped_slot')
            ->toArray();
    }

    public function getInventoryStats(): array
    {
        return [
            'total_items' => $this->inventory()->count(),
            'equipped_items' => $this->equippedItems()->count(),
            'inventory_usage' => $this->inventory()->count() . '/' . $this->inventory_slots,
            'total_value' => $this->inventory()->sum('value'),
        ];
    }

    public function getSocialStats(): array
    {
        return [
            'friends_count' => $this->friends()->count(),
            'friend_requests_sent' => $this->friendRequests()->count(),
            'friend_requests_received' => $this->receivedFriendRequests()->count(),
            'messages_sent' => $this->messages()->count(),
            'messages_received' => $this->receivedMessages()->count(),
            'interactions_count' => $this->interactions()->count(),
        ];
    }

    public function getActivityStats(): array
    {
        return [
            'total_appearances' => $this->appearances()->count(),
            'total_visits' => $this->visits()->count(),
            'average_session_duration' => $this->getAverageSessionDuration(),
            'most_visited_worlds' => $this->getMostVisitedWorlds(),
            'peak_activity_hours' => $this->getPeakActivityHours(),
        ];
    }

    private function getAverageSessionDuration(): float
    {
        return $this->appearances()
            ->whereNotNull('exited_at')
            ->avg(\DB::raw('TIMESTAMPDIFF(SECOND, entered_at, exited_at)')) ?? 0;
    }

    private function getMostVisitedWorlds(): array
    {
        return $this->appearances()
            ->join('virtual_worlds', 'avatar_appearances.virtual_world_id', '=', 'virtual_worlds.id')
            ->selectRaw('virtual_worlds.name, COUNT(*) as visits')
            ->groupBy('virtual_worlds.id', 'virtual_worlds.name')
            ->orderBy('visits', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getPeakActivityHours(): array
    {
        return $this->appearances()
            ->selectRaw('HOUR(entered_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function generateProfileUrl(): string
    {
        return route('metaverse.avatars.show', $this->id);
    }

    public function generateAvatarImageUrl(): string
    {
        return $this->getAvatarImageUrlAttribute();
    }

    public function canInteractWith(MetaverseAvatar $avatar): bool
    {
        // Check if both avatars are online and in the same world
        return $this->is_online && 
               $avatar->is_online && 
               $this->current_world_id === $avatar->current_world_id &&
               $this->id !== $avatar->id;
    }

    public function sendAvatarMessage(MetaverseAvatar $receiver, string $content): void
    {
        $this->messages()->create([
            'receiver_avatar_id' => $receiver->id,
            'content' => $content,
            'type' => 'text',
            'status' => 'sent',
        ]);
    }

    public function getPrivacySetting(string $setting): mixed
    {
        return $this->privacy_settings[$setting] ?? null;
    }

    public function updatePrivacySetting(string $setting, mixed $value): void
    {
        $privacySettings = $this->privacy_settings;
        $privacySettings[$setting] = $value;
        
        $this->update(['privacy_settings' => $privacySettings]);
    }
}

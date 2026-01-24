<?php

namespace App\Http\Controllers\Metaverse;

use App\Http\Controllers\Controller;
use App\Models\Metaverse\MetaverseAvatar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MetaverseAvatarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of metaverse avatars.
     */
    public function index(Request $request)
    {
        $query = MetaverseAvatar::with(['user', 'virtualWorld'])
            ->withCount(['appearances', 'interactions']);

        // Filters
        if ($request->filled('world_id')) {
            $query->where('virtual_world_id', $request->world_id);
        }

        if ($request->filled('avatar_type')) {
            $query->where('avatar_type', $request->avatar_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $avatars = $query->paginate(12);

        return Inertia::render('Metaverse/Avatars/Index', [
            'avatars' => $avatars,
            'filters' => $request->only(['world_id', 'avatar_type', 'status', 'gender', 'search', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Show the form for creating a new metaverse avatar.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Check if user already has an avatar
        if ($user->metaverseAvatar) {
            return redirect()->route('metaverse.avatars.edit', $user->metaverseAvatar)
                ->with('info', 'لديك بالفعل أفاتار. يمكنك تعديله.');
        }

        return Inertia::render('Metaverse/Avatars/Create');
    }

    /**
     * Store a newly created metaverse avatar.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Check if user already has an avatar
        if ($user->metaverseAvatar) {
            return back()->with('error', 'لديك بالفعل أفاتار');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'display_name' => 'required|string|max:100',
            'avatar_type' => 'required|in:human,robot,animal,fantasy,custom',
            'gender' => 'required|in:male,female,non_binary,other',
            'appearance' => 'required|array',
            'appearance.skin_tone' => 'required|string|max:50',
            'appearance.hair_style' => 'required|string|max:50',
            'appearance.hair_color' => 'required|string|max:50',
            'appearance.eye_color' => 'required|string|max:50',
            'appearance.body_type' => 'required|string|max:50',
            'appearance.height' => 'required|numeric|min:50|max:300',
            'clothing' => 'nullable|array',
            'accessories' => 'nullable|array',
            'skills' => 'nullable|array',
            'preferences' => 'nullable|array',
            'bio' => 'nullable|string|max:500',
            'personality_traits' => 'nullable|array',
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
            'privacy_settings' => 'nullable|array',
        ]);

        $avatar = MetaverseAvatar::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'display_name' => $request->display_name,
            'avatar_type' => $request->avatar_type,
            'gender' => $request->gender,
            'appearance' => $request->appearance,
            'clothing' => $request->clothing ?? [],
            'accessories' => $request->accessories ?? [],
            'skills' => $request->skills ?? [],
            'preferences' => $request->preferences ?? [],
            'bio' => $request->bio,
            'personality_traits' => $request->personality_traits ?? [],
            'language' => $request->language ?? 'en',
            'timezone' => $request->timezone ?? 'UTC',
            'privacy_settings' => $request->privacy_settings ?? [
                'show_online_status' => true,
                'show_location' => true,
                'allow_friend_requests' => true,
                'show_activity' => false,
            ],
            'status' => 'active',
            'is_online' => false,
            'last_active_at' => now(),
            'created_by' => $user->id,
        ]);

        // Handle avatar images
        if ($request->hasFile('avatar_image')) {
            $path = $request->file('avatar_image')->store('metaverse/avatars/' . $avatar->id, 'public');
            $avatar->update(['avatar_image_path' => $path]);
        }

        // Handle 3D model
        if ($request->hasFile('avatar_model')) {
            $path = $request->file('avatar_model')->store('metaverse/avatar-models/' . $avatar->id, 'public');
            $avatar->update([
                'model_path' => $path,
                'model_file_type' => $request->file('avatar_model')->getClientOriginalExtension(),
                'model_file_size' => $request->file('avatar_model')->getSize(),
            ]);
        }

        return redirect()->route('metaverse.avatars.show', $avatar)
            ->with('success', 'تم إنشاء الأفاتار بنجاح');
    }

    /**
     * Display the specified metaverse avatar.
     */
    public function show(MetaverseAvatar $avatar)
    {
        $avatar->load([
            'user',
            'virtualWorld',
            'appearances' => function ($query) {
                $query->latest()->limit(10);
            },
            'friends' => function ($query) {
                $query->where('status', 'accepted')->with('friend.avatar');
            },
            'inventory',
            'achievements',
        ]);

        // Get avatar statistics
        $stats = [
            'total_appearances' => $avatar->appearances_count,
            'total_interactions' => $avatar->interactions_count,
            'friends_count' => $avatar->friends->count(),
            'inventory_value' => $avatar->inventory()->sum('value') ?? 0,
            'achievements_count' => $avatar->achievements->count(),
            'online_status' => $avatar->is_online,
            'last_active' => $avatar->last_active_at,
        ];

        return Inertia::render('Metaverse/Avatars/Show', [
            'avatar' => $avatar,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified metaverse avatar.
     */
    public function edit(MetaverseAvatar $avatar)
    {
        $this->authorize('update', $avatar);

        $avatar->load(['inventory', 'skills']);

        return Inertia::render('Metaverse/Avatars/Edit', [
            'avatar' => $avatar,
        ]);
    }

    /**
     * Update the specified metaverse avatar.
     */
    public function update(Request $request, MetaverseAvatar $avatar)
    {
        $this->authorize('update', $avatar);

        $request->validate([
            'name' => 'required|string|max:100',
            'display_name' => 'required|string|max:100',
            'avatar_type' => 'required|in:human,robot,animal,fantasy,custom',
            'gender' => 'required|in:male,female,non_binary,other',
            'appearance' => 'required|array',
            'appearance.skin_tone' => 'required|string|max:50',
            'appearance.hair_style' => 'required|string|max:50',
            'appearance.hair_color' => 'required|string|max:50',
            'appearance.eye_color' => 'required|string|max:50',
            'appearance.body_type' => 'required|string|max:50',
            'appearance.height' => 'required|numeric|min:50|max:300',
            'clothing' => 'nullable|array',
            'accessories' => 'nullable|array',
            'skills' => 'nullable|array',
            'preferences' => 'nullable|array',
            'bio' => 'nullable|string|max:500',
            'personality_traits' => 'nullable|array',
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
            'privacy_settings' => 'nullable|array',
        ]);

        $avatar->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'avatar_type' => $request->avatar_type,
            'gender' => $request->gender,
            'appearance' => $request->appearance,
            'clothing' => $request->clothing ?? [],
            'accessories' => $request->accessories ?? [],
            'skills' => $request->skills ?? [],
            'preferences' => $request->preferences ?? [],
            'bio' => $request->bio,
            'personality_traits' => $request->personality_traits ?? [],
            'language' => $request->language,
            'timezone' => $request->timezone,
            'privacy_settings' => $request->privacy_settings ?? $avatar->privacy_settings,
            'updated_by' => auth()->id(),
        ]);

        // Handle new avatar image
        if ($request->hasFile('new_avatar_image')) {
            // Delete old image
            if ($avatar->avatar_image_path) {
                Storage::disk('public')->delete($avatar->avatar_image_path);
            }
            
            $path = $request->file('new_avatar_image')->store('metaverse/avatars/' . $avatar->id, 'public');
            $avatar->update(['avatar_image_path' => $path]);
        }

        // Handle new 3D model
        if ($request->hasFile('new_avatar_model')) {
            // Delete old model
            if ($avatar->model_path) {
                Storage::disk('public')->delete($avatar->model_path);
            }
            
            $path = $request->file('new_avatar_model')->store('metaverse/avatar-models/' . $avatar->id, 'public');
            $avatar->update([
                'model_path' => $path,
                'model_file_type' => $request->file('new_avatar_model')->getClientOriginalExtension(),
                'model_file_size' => $request->file('new_avatar_model')->getSize(),
            ]);
        }

        return redirect()->route('metaverse.avatars.show', $avatar)
            ->with('success', 'تم تحديث الأفاتار بنجاح');
    }

    /**
     * Remove the specified metaverse avatar.
     */
    public function destroy(MetaverseAvatar $avatar)
    {
        $this->authorize('delete', $avatar);

        // Delete associated files
        if ($avatar->avatar_image_path) {
            Storage::disk('public')->delete($avatar->avatar_image_path);
        }

        if ($avatar->model_path) {
            Storage::disk('public')->delete($avatar->model_path);
        }

        $avatar->delete();

        return redirect()->route('metaverse.avatars.index')
            ->with('success', 'تم حذف الأفاتار بنجاح');
    }

    /**
     * Update avatar online status.
     */
    public function updateOnlineStatus(Request $request, MetaverseAvatar $avatar)
    {
        $this->authorize('update', $avatar);

        $request->validate([
            'is_online' => 'required|boolean',
            'location' => 'nullable|string|max:255',
            'activity' => 'nullable|string|max:100',
        ]);

        $avatar->update([
            'is_online' => $request->is_online,
            'current_location' => $request->location,
            'current_activity' => $request->activity,
            'last_active_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'is_online' => $avatar->is_online,
            'last_active_at' => $avatar->last_active_at,
        ]);
    }

    /**
     * Add friend to avatar.
     */
    public function addFriend(Request $request, MetaverseAvatar $avatar)
    {
        $this->authorize('addFriend', $avatar);

        $request->validate([
            'friend_avatar_id' => 'required|exists:metaverse_avatars,id',
        ]);

        $friendAvatar = MetaverseAvatar::findOrFail($request->friend_avatar_id);

        // Check if already friends
        if ($avatar->friends()->where('friend_avatar_id', $friendAvatar->id)->exists()) {
            return back()->with('error', 'أنت بالفعل أصدقاء');
        }

        // Check if already sent request
        if ($avatar->friendRequests()->where('receiver_avatar_id', $friendAvatar->id)->exists()) {
            return back()->with('error', 'تم إرسال طلب الصداقة بالفعل');
        }

        // Create friend request
        $avatar->friendRequests()->create([
            'receiver_avatar_id' => $friendAvatar->id,
            'status' => 'pending',
            'message' => $request->message ?? 'أود أن أكون صديقاً',
        ]);

        return back()->with('success', 'تم إرسال طلب الصداقة');
    }

    /**
     * Accept friend request.
     */
    public function acceptFriendRequest(MetaverseAvatar $avatar, $requestId)
    {
        $this->authorize('manageFriends', $avatar);

        $friendRequest = $avatar->receivedFriendRequests()
            ->where('id', $requestId)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendRequest->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Create reciprocal friendship
        $avatar->friends()->create([
            'friend_avatar_id' => $friendRequest->sender_avatar_id,
            'status' => 'accepted',
            'friend_since' => now(),
        ]);

        $friendRequest->sender->friends()->create([
            'friend_avatar_id' => $avatar->id,
            'status' => 'accepted',
            'friend_since' => now(),
        ]);

        return back()->with('success', 'تم قبول طلب الصداقة');
    }

    /**
     * Reject friend request.
     */
    public function rejectFriendRequest(MetaverseAvatar $avatar, $requestId)
    {
        $this->authorize('manageFriends', $avatar);

        $friendRequest = $avatar->receivedFriendRequests()
            ->where('id', $requestId)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendRequest->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'تم رفض طلب الصداقة');
    }

    /**
     * Remove friend.
     */
    public function removeFriend(MetaverseAvatar $avatar, $friendId)
    {
        $this->authorize('manageFriends', $avatar);

        $friendship = $avatar->friends()
            ->where('friend_avatar_id', $friendId)
            ->firstOrFail();

        $friendship->delete();

        // Remove reciprocal friendship
        $friendAvatar = MetaverseAvatar::findOrFail($friendId);
        $friendAvatar->friends()
            ->where('friend_avatar_id', $avatar->id)
            ->delete();

        return back()->with('success', 'تم إزالة الصديق');
    }

    /**
     * Get avatar inventory.
     */
    public function inventory(MetaverseAvatar $avatar)
    {
        $this->authorize('view', $avatar);

        $inventory = $avatar->inventory()
            ->with(['item'])
            ->get()
            ->groupBy('category');

        return response()->json([
            'inventory' => $inventory,
            'total_value' => $avatar->inventory()->sum('value'),
        ]);
    }

    /**
     * Equip item to avatar.
     */
    public function equipItem(Request $request, MetaverseAvatar $avatar)
    {
        $this->authorize('update', $avatar);

        $request->validate([
            'inventory_item_id' => 'required|exists:avatar_inventory,id',
            'slot' => 'required|string|max:50',
        ]);

        $inventoryItem = $avatar->inventory()->findOrFail($request->inventory_item_id);

        // Check if item can be equipped in this slot
        if (!$this->canEquipInSlot($inventoryItem, $request->slot)) {
            return back()->with('error', 'لا يمكن تجهيز هذا العنصر في هذه الفتحة');
        }

        // Unequip current item in slot if exists
        $currentEquipped = $avatar->equippedItems()
            ->where('slot', $request->slot)
            ->first();

        if ($currentEquipped) {
            $currentEquipped->update(['equipped' => false]);
        }

        // Equip new item
        $inventoryItem->update([
            'equipped' => true,
            'equipped_slot' => $request->slot,
        ]);

        return back()->with('success', 'تم تجهيز العنصر بنجاح');
    }

    /**
     * Unequip item from avatar.
     */
    public function unequipItem(Request $request, MetaverseAvatar $avatar)
    {
        $this->authorize('update', $avatar);

        $request->validate([
            'inventory_item_id' => 'required|exists:avatar_inventory,id',
        ]);

        $inventoryItem = $avatar->inventory()->findOrFail($request->inventory_item_id);

        $inventoryItem->update([
            'equipped' => false,
            'equipped_slot' => null,
        ]);

        return back()->with('success', 'تم إزالة تجهيز العنصر بنجاح');
    }

    /**
     * Get avatar analytics.
     */
    public function analytics(MetaverseAvatar $avatar)
    {
        $this->authorize('view', $avatar);

        $analytics = [
            'activity_timeline' => $avatar->appearances()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get(),
            
            'interaction_stats' => [
                'total_interactions' => $avatar->interactions_count,
                'by_type' => $avatar->interactions()
                    ->selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->get(),
            ],
            
            'social_metrics' => [
                'friends_count' => $avatar->friends()->count(),
                'friend_requests_sent' => $avatar->friendRequests()->count(),
                'friend_requests_received' => $avatar->receivedFriendRequests()->count(),
            ],
            
            'engagement_metrics' => [
                'avg_session_duration' => $this->calculateAvgSessionDuration($avatar),
                'most_visited_worlds' => $this->getMostVisitedWorlds($avatar),
                'peak_activity_hours' => $this->getPeakActivityHours($avatar),
            ],
        ];

        return response()->json($analytics);
    }

    /**
     * Check if item can be equipped in slot.
     */
    private function canEquipInSlot($inventoryItem, $slot): bool
    {
        $item = $inventoryItem->item;
        
        // Define slot compatibility
        $slotCompatibility = [
            'head' => ['hat', 'helmet', 'hair_accessory'],
            'body' => ['shirt', 'jacket', 'armor'],
            'legs' => ['pants', 'skirt', 'armor_legs'],
            'feet' => ['shoes', 'boots'],
            'hands' => ['gloves', 'weapon'],
            'accessory' => ['ring', 'necklace', 'bracelet'],
        ];

        return in_array($item->category, $slotCompatibility[$slot] ?? []);
    }

    /**
     * Calculate average session duration.
     */
    private function calculateAvgSessionDuration(MetaverseAvatar $avatar): float
    {
        return $avatar->appearances()
            ->whereNotNull('ended_at')
            ->avg(\DB::raw('TIMESTAMPDIFF(SECOND, started_at, ended_at)')) ?? 0;
    }

    /**
     * Get most visited worlds.
     */
    private function getMostVisitedWorlds(MetaverseAvatar $avatar): array
    {
        return $avatar->appearances()
            ->join('virtual_worlds', 'avatar_appearances.virtual_world_id', '=', 'virtual_worlds.id')
            ->selectRaw('virtual_worlds.name, COUNT(*) as visits')
            ->groupBy('virtual_worlds.id', 'virtual_worlds.name')
            ->orderBy('visits', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Get peak activity hours.
     */
    private function getPeakActivityHours(MetaverseAvatar $avatar): array
    {
        return $avatar->appearances()
            ->selectRaw('HOUR(started_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }
}

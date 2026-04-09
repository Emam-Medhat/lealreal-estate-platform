<?php

namespace App\Http\Controllers\Metaverse;

use App\Http\Controllers\Controller;
use App\Models\Metaverse\VirtualPropertyTour;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\Metaverse\MetaverseShowroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class VirtualPropertyTourController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of virtual property tours.
     */
    public function index(Request $request)
    {
        $query = VirtualPropertyTour::with(['guide', 'property', 'showroom'])
            ->withCount(['participants', 'sessions']);

        // Filters
        if ($request->filled('property_id')) {
            $query->where('metaverse_property_id', $request->property_id);
        }

        if ($request->filled('showroom_id')) {
            $query->where('metaverse_showroom_id', $request->showroom_id);
        }

        if ($request->filled('tour_type')) {
            $query->where('tour_type', $request->tour_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('guide_id')) {
            $query->where('guide_id', $request->guide_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tours = $query->paginate(12);

        return Inertia::render('Metaverse/Tours/Index', [
            'tours' => $tours,
            'filters' => $request->only(['property_id', 'showroom_id', 'tour_type', 'status', 'guide_id', 'search', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Show the form for creating a new virtual property tour.
     */
    public function create()
    {
        $properties = MetaverseProperty::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->with(['virtualWorld'])
            ->get();

        $showrooms = MetaverseShowroom::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->where('is_active', true)
            ->with(['virtualWorld'])
            ->get();

        $guides = User::where('role', 'tour_guide')->get();

        return Inertia::render('Metaverse/Tours/Create', [
            'properties' => $properties,
            'showrooms' => $showrooms,
            'guides' => $guides,
        ]);
    }

    /**
     * Store a newly created virtual property tour.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'tour_type' => 'required|in:guided,self_guided,group,private,virtual_reality,augmented_reality',
            'metaverse_property_id' => 'nullable|exists:metaverse_properties,id',
            'metaverse_showroom_id' => 'nullable|exists:metaverse_showrooms,id',
            'guide_id' => 'nullable|exists:users,id',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'max_participants' => 'required|integer|min:1|max:100',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'languages' => 'nullable|array',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'accessibility_features' => 'nullable|array',
            'equipment_required' => 'nullable|array',
            'tour_points' => 'required|array',
            'tour_points.*.title' => 'required|string|max:255',
            'tour_points.*.description' => 'required|string|max:1000',
            'tour_points.*.coordinates' => 'required|string|max:255',
            'tour_points.*.duration' => 'required|integer|min:1',
            'tour_points.*.media_type' => 'required|in:text,image,audio,video,3d_model',
            'tour_points.*.media_url' => 'nullable|url|max:500',
            'tour_points.*.interaction_type' => 'nullable|string|max:100',
            'tour_points.*.order' => 'required|integer|min:0',
            'schedule_settings' => 'nullable|array',
            'schedule_settings.available_times' => 'nullable|array',
            'schedule_settings.booking_window' => 'nullable|integer|min:1|max:365',
            'schedule_settings.cancellation_policy' => 'nullable|string|max:500',
            'interactive_elements' => 'nullable|array',
            'multimedia_content' => 'nullable|array',
            'navigation_settings' => 'nullable|array',
            'customization_options' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $tour = VirtualPropertyTour::create([
            'title' => $request->title,
            'description' => $request->description,
            'tour_type' => $request->tour_type,
            'metaverse_property_id' => $request->metaverse_property_id,
            'metaverse_showroom_id' => $request->metaverse_showroom_id,
            'guide_id' => $request->guide_id,
            'duration_minutes' => $request->duration_minutes,
            'max_participants' => $request->max_participants,
            'price' => $request->price,
            'currency' => $request->currency,
            'languages' => $request->languages ?? ['en'],
            'difficulty_level' => $request->difficulty_level,
            'accessibility_features' => $request->accessibility_features ?? [],
            'equipment_required' => $request->equipment_required ?? [],
            'tour_points' => $request->tour_points,
            'schedule_settings' => $request->schedule_settings ?? [
                'available_times' => [],
                'booking_window' => 30,
                'cancellation_policy' => '24 hours notice required',
            ],
            'interactive_elements' => $request->interactive_elements ?? [],
            'multimedia_content' => $request->multimedia_content ?? [],
            'navigation_settings' => $request->navigation_settings ?? [
                'allow_free_navigation' => true,
                'show_progress_bar' => true,
                'auto_advance' => false,
            ],
            'customization_options' => $request->customization_options ?? [],
            'status' => 'active',
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured', false),
            'created_by' => auth()->id(),
        ]);

        // Handle tour images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('metaverse/tours/' . $tour->id, 'public');
                $tour->images()->create(['path' => $path]);
            }
        }

        // Handle tour media files
        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $media) {
                $path = $media->store('metaverse/tour-media/' . $tour->id, 'public');
                $tour->mediaFiles()->create([
                    'path' => $path,
                    'file_type' => $media->getClientOriginalExtension(),
                    'file_size' => $media->getSize(),
                    'file_name' => $media->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('metaverse.tours.show', $tour)
            ->with('success', 'تم إنشاء جولة العقار الافتراضية بنجاح');
    }

    /**
     * Display the specified virtual property tour.
     */
    public function show(VirtualPropertyTour $tour)
    {
        $tour->load([
            'guide',
            'property' => function ($query) {
                $query->with(['virtualWorld', 'owner', 'images']);
            },
            'showroom' => function ($query) {
                $query->with(['virtualWorld', 'owner']);
            },
            'images',
            'mediaFiles',
            'participants' => function ($query) {
                $query->where('status', 'confirmed')->with('user');
            },
            'sessions' => function ($query) {
                $query->where('start_time', '>', now())->orderBy('start_time');
            },
            'reviews' => function ($query) {
                $query->with('reviewer')->latest();
            },
        ]);

        // Get tour statistics
        $stats = [
            'total_participants' => $tour->participants_count,
            'total_sessions' => $tour->sessions_count,
            'average_rating' => $tour->reviews()->avg('rating') ?? 0,
            'total_reviews' => $tour->reviews()->count(),
            'completion_rate' => $this->calculateCompletionRate($tour),
            'average_duration' => $this->calculateAverageDuration($tour),
        ];

        // Get available time slots
        $availableSlots = $this->getAvailableTimeSlots($tour);

        // Get similar tours
        $similarTours = VirtualPropertyTour::where('id', '!=', $tour->id)
            ->where('tour_type', $tour->tour_type)
            ->where('status', 'active')
            ->where('is_active', true)
            ->with(['guide', 'property', 'showroom'])
            ->limit(6)
            ->get();

        return Inertia::render('Metaverse/Tours/Show', [
            'tour' => $tour,
            'stats' => $stats,
            'availableSlots' => $availableSlots,
            'similarTours' => $similarTours,
        ]);
    }

    /**
     * Show the form for editing the specified virtual property tour.
     */
    public function edit(VirtualPropertyTour $tour)
    {
        $this->authorize('update', $tour);

        $tour->load(['images', 'mediaFiles']);
        $properties = MetaverseProperty::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->with(['virtualWorld'])
            ->get();

        $showrooms = MetaverseShowroom::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->where('is_active', true)
            ->with(['virtualWorld'])
            ->get();

        $guides = User::where('role', 'tour_guide')->get();

        return Inertia::render('Metaverse/Tours/Edit', [
            'tour' => $tour,
            'properties' => $properties,
            'showrooms' => $showrooms,
            'guides' => $guides,
        ]);
    }

    /**
     * Update the specified virtual property tour.
     */
    public function update(Request $request, VirtualPropertyTour $tour)
    {
        $this->authorize('update', $tour);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'tour_type' => 'required|in:guided,self_guided,group,private,virtual_reality,augmented_reality',
            'metaverse_property_id' => 'nullable|exists:metaverse_properties,id',
            'metaverse_showroom_id' => 'nullable|exists:metaverse_showrooms,id',
            'guide_id' => 'nullable|exists:users,id',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'max_participants' => 'required|integer|min:1|max:100',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'languages' => 'nullable|array',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'accessibility_features' => 'nullable|array',
            'equipment_required' => 'nullable|array',
            'tour_points' => 'required|array',
            'tour_points.*.title' => 'required|string|max:255',
            'tour_points.*.description' => 'required|string|max:1000',
            'tour_points.*.coordinates' => 'required|string|max:255',
            'tour_points.*.duration' => 'required|integer|min:1',
            'tour_points.*.media_type' => 'required|in:text,image,audio,video,3d_model',
            'tour_points.*.media_url' => 'nullable|url|max:500',
            'tour_points.*.interaction_type' => 'nullable|string|max:100',
            'tour_points.*.order' => 'required|integer|min:0',
            'schedule_settings' => 'nullable|array',
            'interactive_elements' => 'nullable|array',
            'multimedia_content' => 'nullable|array',
            'navigation_settings' => 'nullable|array',
            'customization_options' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $tour->update([
            'title' => $request->title,
            'description' => $request->description,
            'tour_type' => $request->tour_type,
            'metaverse_property_id' => $request->metaverse_property_id,
            'metaverse_showroom_id' => $request->metaverse_showroom_id,
            'guide_id' => $request->guide_id,
            'duration_minutes' => $request->duration_minutes,
            'max_participants' => $request->max_participants,
            'price' => $request->price,
            'currency' => $request->currency,
            'languages' => $request->languages ?? ['en'],
            'difficulty_level' => $request->difficulty_level,
            'accessibility_features' => $request->accessibility_features ?? [],
            'equipment_required' => $request->equipment_required ?? [],
            'tour_points' => $request->tour_points,
            'schedule_settings' => $request->schedule_settings ?? $tour->schedule_settings,
            'interactive_elements' => $request->interactive_elements ?? [],
            'multimedia_content' => $request->multimedia_content ?? [],
            'navigation_settings' => $request->navigation_settings ?? $tour->navigation_settings,
            'customization_options' => $request->customization_options ?? [],
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
            'updated_by' => auth()->id(),
        ]);

        // Handle new images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('metaverse/tours/' . $tour->id, 'public');
                $tour->images()->create(['path' => $path]);
            }
        }

        // Handle new media files
        if ($request->hasFile('new_media_files')) {
            foreach ($request->file('new_media_files') as $media) {
                $path = $media->store('metaverse/tour-media/' . $tour->id, 'public');
                $tour->mediaFiles()->create([
                    'path' => $path,
                    'file_type' => $media->getClientOriginalExtension(),
                    'file_size' => $media->getSize(),
                    'file_name' => $media->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('metaverse.tours.show', $tour)
            ->with('success', 'تم تحديث جولة العقار الافتراضية بنجاح');
    }

    /**
     * Remove the specified virtual property tour.
     */
    public function destroy(VirtualPropertyTour $tour)
    {
        $this->authorize('delete', $tour);

        // Delete associated files
        foreach ($tour->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        foreach ($tour->mediaFiles as $media) {
            Storage::disk('public')->delete($media->path);
        }

        $tour->delete();

        return redirect()->route('metaverse.tours.index')
            ->with('success', 'تم حذف جولة العقار الافتراضية بنجاح');
    }

    /**
     * Book a tour.
     */
    public function book(Request $request, VirtualPropertyTour $tour)
    {
        $this->authorize('book', $tour);

        $request->validate([
            'scheduled_time' => 'required|date|after:now',
            'participant_count' => 'required|integer|min:1|max:' . $tour->max_participants,
            'special_requirements' => 'nullable|string|max:500',
            'customization_choices' => 'nullable|array',
        ]);

        // Check availability
        if (!$this->isTimeSlotAvailable($tour, $request->scheduled_time)) {
            return back()->with('error', 'هذا الوقت غير متاح');
        }

        // Process payment if required
        $totalPrice = $tour->price * $request->participant_count;
        if ($totalPrice > 0) {
            $paymentResult = $this->processTourPayment($tour, $totalPrice);
            if (!$paymentResult['success']) {
                return back()->with('error', 'فشلت عملية الدفع: ' . $paymentResult['message']);
            }
        }

        // Create booking
        $booking = $tour->sessions()->create([
            'user_id' => auth()->id(),
            'scheduled_time' => $request->scheduled_time,
            'participant_count' => $request->participant_count,
            'total_price' => $totalPrice,
            'currency' => $tour->currency,
            'special_requirements' => $request->special_requirements,
            'customization_choices' => $request->customization_choices ?? [],
            'status' => 'confirmed',
            'payment_status' => $totalPrice > 0 ? 'paid' : 'free',
            'booked_at' => now(),
        ]);

        return back()->with('success', 'تم حجز الجولة بنجاح');
    }

    /**
     * Start tour session.
     */
    public function start(VirtualPropertyTour $tour, $sessionId)
    {
        $this->authorize('join', $tour);

        $session = $tour->sessions()->findOrFail($sessionId);

        // Check if user is participant
        if ($session->user_id !== auth()->id()) {
            return back()->with('error', 'لست مشاركاً في هذه الجولة');
        }

        // Check if session is ready to start
        if ($session->status !== 'confirmed') {
            return back()->with('error', 'الجولة غير جاهزة للبدء');
        }

        // Create participant record
        $participant = $tour->participants()->create([
            'user_id' => auth()->id(),
            'session_id' => $session->id,
            'joined_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'current_point' => 0,
            'progress' => 0,
        ]);

        return Inertia::render('Metaverse/Tours/VirtualSpace', [
            'tour' => $tour->load(['property', 'showroom', 'mediaFiles']),
            'session' => $session,
            'participant' => $participant,
            'userAvatar' => auth()->user()->metaverseAvatar,
        ]);
    }

    /**
     * Update tour progress.
     */
    public function updateProgress(Request $request, VirtualPropertyTour $tour)
    {
        $this->authorize('participate', $tour);

        $request->validate([
            'participant_id' => 'required|exists:tour_participants,id',
            'current_point' => 'required|integer|min:0',
            'progress' => 'required|integer|min:0|max:100',
            'interaction_data' => 'nullable|array',
        ]);

        $participant = $tour->participants()->findOrFail($request->participant_id);

        $participant->update([
            'current_point' => $request->current_point,
            'progress' => $request->progress,
            'interaction_data' => $request->interaction_data ?? [],
            'last_activity_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'progress' => $participant->progress,
        ]);
    }

    /**
     * Complete tour.
     */
    public function complete(VirtualPropertyTour $tour, $participantId)
    {
        $this->authorize('participate', $tour);

        $participant = $tour->participants()->findOrFail($participantId);

        $participant->update([
            'completed_at' => now(),
            'duration' => now()->diffInSeconds($participant->joined_at),
            'final_progress' => $participant->progress,
        ]);

        return response()->json([
            'success' => true,
            'completed_at' => $participant->completed_at,
            'duration' => $participant->duration,
        ]);
    }

    /**
     * Get tour analytics.
     */
    public function analytics(VirtualPropertyTour $tour)
    {
        $this->authorize('view', $tour);

        $analytics = [
            'booking_trends' => $tour->sessions()
                ->selectRaw('DATE(booked_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'participation_stats' => [
                'total_participants' => $tour->participants()->count(),
                'completion_rate' => $this->calculateCompletionRate($tour),
                'average_duration' => $this->calculateAverageDuration($tour),
                'drop_off_points' => $this->getDropOffPoints($tour),
            ],
            
            'engagement_metrics' => [
                'interaction_frequency' => $this->calculateInteractionFrequency($tour),
                'popular_points' => $this->getPopularPoints($tour),
                'time_per_point' => $this->getTimePerPoint($tour),
            ],
            
            'revenue_metrics' => [
                'total_revenue' => $tour->sessions()->sum('total_price'),
                'revenue_by_month' => $tour->sessions()
                    ->selectRaw('YEAR_MONTH(booked_at) as month, SUM(total_price) as revenue')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get(),
            ],
        ];

        return response()->json($analytics);
    }

    /**
     * Check if time slot is available.
     */
    private function isTimeSlotAvailable(VirtualPropertyTour $tour, $scheduledTime): bool
    {
        $conflictingSessions = $tour->sessions()
            ->where('scheduled_time', $scheduledTime)
            ->where('status', '!=', 'cancelled')
            ->count();

        return $conflictingSessions === 0;
    }

    /**
     * Process tour payment.
     */
    private function processTourPayment(VirtualPropertyTour $tour, float $amount): array
    {
        // This would integrate with actual payment gateway
        // For now, simulate payment processing
        
        try {
            // Simulate payment processing
            if ($tour->currency === 'ETH') {
                $result = $this->processCryptoPayment($tour, $amount);
            } else {
                $result = $this->processTraditionalPayment($tour, $amount);
            }

            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process cryptocurrency payment.
     */
    private function processCryptoPayment(VirtualPropertyTour $tour, float $amount): array
    {
        // Simulate crypto payment processing
        return [
            'success' => true,
            'message' => 'Payment processed successfully',
        ];
    }

    /**
     * Process traditional payment.
     */
    private function processTraditionalPayment(VirtualPropertyTour $tour, float $amount): array
    {
        // Simulate traditional payment processing
        return [
            'success' => true,
            'message' => 'Payment processed successfully',
        ];
    }

    /**
     * Get available time slots.
     */
    private function getAvailableTimeSlots(VirtualPropertyTour $tour): array
    {
        $availableTimes = $tour->schedule_settings['available_times'] ?? [];
        $bookedTimes = $tour->sessions()
            ->where('scheduled_time', '>', now())
            ->where('status', '!=', 'cancelled')
            ->pluck('scheduled_time')
            ->map(function ($time) {
                return $time->format('Y-m-d H:i');
            })
            ->toArray();

        return array_diff($availableTimes, $bookedTimes);
    }

    /**
     * Calculate completion rate.
     */
    private function calculateCompletionRate(VirtualPropertyTour $tour): float
    {
        $totalParticipants = $tour->participants()->count();
        $completedParticipants = $tour->participants()->whereNotNull('completed_at')->count();
        
        return $totalParticipants > 0 ? ($completedParticipants / $totalParticipants) * 100 : 0;
    }

    /**
     * Calculate average duration.
     */
    private function calculateAverageDuration(VirtualPropertyTour $tour): float
    {
        return $tour->participants()
            ->whereNotNull('duration')
            ->avg('duration') ?? 0;
    }

    /**
     * Get drop off points.
     */
    private function getDropOffPoints(VirtualPropertyTour $tour): array
    {
        return $tour->participants()
            ->selectRaw('current_point, COUNT(*) as count')
            ->whereNull('completed_at')
            ->groupBy('current_point')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Calculate interaction frequency.
     */
    private function calculateInteractionFrequency(VirtualPropertyTour $tour): float
    {
        $totalInteractions = $tour->participants()
            ->whereNotNull('interaction_data')
            ->count();
        
        $totalParticipants = $tour->participants()->count();
        
        return $totalParticipants > 0 ? $totalInteractions / $totalParticipants : 0;
    }

    /**
     * Get popular points.
     */
    private function getPopularPoints(VirtualPropertyTour $tour): array
    {
        return $tour->participants()
            ->selectRaw('current_point, COUNT(*) as count')
            ->groupBy('current_point')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Get time per point.
     */
    private function getTimePerPoint(VirtualPropertyTour $tour): array
    {
        // This would require detailed tracking of time spent at each point
        // For now, return placeholder data
        return [];
    }
}

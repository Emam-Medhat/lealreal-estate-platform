<?php

namespace App\Http\Controllers\Neighborhood;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood\CommunityEvent;
use App\Models\Neighborhood\Community;
use App\Models\Neighborhood\Neighborhood;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CommunityEventController extends Controller
{
    /**
     * Display the community events dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['community_id', 'event_type', 'status', 'date_range']);
        
        // Get event statistics
        $stats = [
            'total_events' => CommunityEvent::count(),
            'upcoming_events' => CommunityEvent::where('start_date', '>=', now())->count(),
            'past_events' => CommunityEvent::where('start_date', '<', now())->count(),
            'active_events' => CommunityEvent::where('status', 'active')->count(),
            'featured_events' => $this->getFeaturedEvents(),
            'popular_types' => $this->getPopularTypes(),
            'monthly_events' => $this->getMonthlyEvents(),
        ];

        // Get events with filters
        $events = CommunityEvent::with(['community', 'community.neighborhood'])
            ->when($filters['community_id'], function ($query, $communityId) {
                return $query->where('community_id', $communityId);
            })
            ->when($filters['event_type'], function ($query, $eventType) {
                return $query->where('event_type', $eventType);
            })
            ->when($filters['status'], function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($filters['date_range'], function ($query, $dateRange) {
                if ($dateRange === 'today') {
                    return $query->whereDate('start_date', today());
                } elseif ($dateRange === 'week') {
                    return $query->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($dateRange === 'month') {
                    return $query->whereMonth('start_date', now()->month);
                } elseif ($dateRange === 'year') {
                    return $query->whereYear('start_date', now()->year);
                }
            })
            ->orderBy('start_date', 'asc')
            ->paginate(12);

        // Get communities and event types for filters
        $communities = Community::where('status', 'active')->with(['neighborhood'])->get(['id', 'name', 'neighborhood_id']);
        $eventTypes = ['social', 'educational', 'sports', 'cultural', 'religious', 'charity', 'business', 'entertainment', 'health', 'other'];
        $statuses = ['draft', 'published', 'cancelled', 'completed'];
        $dateRanges = ['today', 'week', 'month', 'year'];

        return Inertia::render('CommunityEvent/Index', [
            'stats' => $stats,
            'events' => $events,
            'communities' => $communities,
            'eventTypes' => $eventTypes,
            'statuses' => $statuses,
            'dateRanges' => $dateRanges,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new community event.
     */
    public function create(): \Inertia\Response
    {
        $communities = Community::where('status', 'active')->with(['neighborhood'])->get(['id', 'name', 'neighborhood_id']);
        $eventTypes = ['social', 'educational', 'sports', 'cultural', 'religious', 'charity', 'business', 'entertainment', 'health', 'other'];
        $statuses = ['draft', 'published', 'cancelled', 'completed'];

        return Inertia::render('CommunityEvent/Create', [
            'communities' => $communities,
            'eventTypes' => $eventTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Store a newly created community event.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'community_id' => 'required|exists:communities,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_type' => 'required|string',
            'status' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'organizer_name' => 'required|string|max:255',
            'organizer_email' => 'nullable|email|max:255',
            'organizer_phone' => 'nullable|string|max:20',
            'max_participants' => 'nullable|integer|min:1',
            'current_participants' => 'nullable|integer|min:0',
            'age_restriction' => 'nullable|string|max:50',
            'price_info' => 'nullable|array',
            'price_info.is_free' => 'nullable|boolean',
            'price_info.price' => 'nullable|numeric|min:0',
            'price_info.currency' => 'nullable|string|max:3',
            'price_info.payment_methods' => 'nullable|array',
            'schedule' => 'nullable|array',
            'schedule.sessions' => 'nullable|array',
            'schedule.sessions.*.start_time' => 'nullable|date_format:H:i',
            'schedule.sessions.*.end_time' => 'nullable|date_format:H:i',
            'schedule.sessions.*.title' => 'nullable|string|max:255',
            'schedule.sessions.*.description' => 'nullable|string',
            'requirements' => 'nullable|array',
            'requirements.items' => 'nullable|array',
            'facilities' => 'nullable|array',
            'facilities.available' => 'nullable|array',
            'facilities.provided' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'contact_info.email' => 'nullable|email|max:255',
            'contact_info.phone' => 'nullable|string|max:20',
            'contact_info.website' => 'nullable|url|max:255',
            'social_media' => 'nullable|array',
            'social_media.facebook' => 'nullable|url',
            'social_media.twitter' => 'nullable|url',
            'social_media.instagram' => 'nullable|url',
            'social_media.linkedin' => 'nullable|url',
            'images' => 'nullable|array',
            'cover_image' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'tags' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $event = CommunityEvent::create([
                'community_id' => $validated['community_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'event_type' => $validated['event_type'],
                'status' => $validated['status'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'location' => $validated['location'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'organizer_name' => $validated['organizer_name'],
                'organizer_email' => $validated['organizer_email'] ?? null,
                'organizer_phone' => $validated['organizer_phone'] ?? null,
                'max_participants' => $validated['max_participants'] ?? null,
                'current_participants' => $validated['current_participants'] ?? 0,
                'age_restriction' => $validated['age_restriction'] ?? null,
                'price_info' => $validated['price_info'] ?? [],
                'schedule' => $validated['schedule'] ?? [],
                'requirements' => $validated['requirements'] ?? [],
                'facilities' => $validated['facilities'] ?? [],
                'contact_info' => $validated['contact_info'] ?? [],
                'social_media' => $validated['social_media'] ?? [],
                'images' => $validated['images'] ?? [],
                'cover_image' => $validated['cover_image'] ?? null,
                'gallery' => $validated['gallery'] ?? [],
                'tags' => $validated['tags'] ?? [],
                'metadata' => $validated['metadata'] ?? [],
                'view_count' => 0,
                'rating' => 0,
                'review_count' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء فعالية المجتمع بنجاح',
                'event' => $event,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء فعالية المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified community event.
     */
    public function show(CommunityEvent $event): \Inertia\Response
    {
        // Increment view count
        $event->increment('view_count');

        $event->load(['community', 'community.neighborhood']);

        // Get related events
        $relatedEvents = CommunityEvent::where('community_id', $event->community_id)
            ->where('id', '!=', $event->id)
            ->where('status', 'published')
            ->where('start_date', '>=', now())
            ->take(3)
            ->get(['id', 'title', 'event_type', 'start_date', 'location']);

        // Get events in same community
        $sameCommunityEvents = CommunityEvent::where('community_id', $event->community_id)
            ->where('id', '!=', $event->id)
            ->where('status', 'published')
            ->take(5)
            ->get(['id', 'title', 'event_type', 'start_date', 'location']);

        return Inertia::render('CommunityEvent/Show', [
            'event' => $event,
            'relatedEvents' => $relatedEvents,
            'sameCommunityEvents' => $sameCommunityEvents,
        ]);
    }

    /**
     * Show the form for editing the specified community event.
     */
    public function edit(CommunityEvent $event): \Inertia\Response
    {
        $communities = Community::where('status', 'active')->with(['neighborhood'])->get(['id', 'name', 'neighborhood_id']);
        $eventTypes = ['social', 'educational', 'sports', 'cultural', 'religious', 'charity', 'business', 'entertainment', 'health', 'other'];
        $statuses = ['draft', 'published', 'cancelled', 'completed'];

        return Inertia::render('CommunityEvent/Edit', [
            'event' => $event,
            'communities' => $communities,
            'eventTypes' => $eventTypes,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified community event.
     */
    public function update(Request $request, CommunityEvent $event): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_type' => 'required|string',
            'status' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'organizer_name' => 'required|string|max:255',
            'organizer_email' => 'nullable|email|max:255',
            'organizer_phone' => 'nullable|string|max:20',
            'max_participants' => 'nullable|integer|min:1',
            'age_restriction' => 'nullable|string|max:50',
            'price_info' => 'nullable|array',
            'schedule' => 'nullable|array',
            'requirements' => 'nullable|array',
            'facilities' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'social_media' => 'nullable|array',
            'images' => 'nullable|array',
            'cover_image' => 'nullable|string|max:255',
            'gallery' => 'nullable|array',
            'tags' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        try {
            $event->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث فعالية المجتمع بنجاح',
                'event' => $event,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث فعالية المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified community event.
     */
    public function destroy(CommunityEvent $event): JsonResponse
    {
        try {
            $event->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف فعالية المجتمع بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف فعالية المجتمع: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Join an event.
     */
    public function join(Request $request, CommunityEvent $event): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'participant_name' => 'required|string|max:255',
            'participant_email' => 'required|email|max:255',
            'participant_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // Check if event has capacity
            if ($event->max_participants && $event->current_participants >= $event->max_participants) {
                return response()->json([
                    'success' => false,
                    'message' => 'الفعالية ممتلية بالكامل',
                ], 400);
            }

            // Mock implementation - in real app, this would create a participant record
            $event->increment('current_participants');

            return response()->json([
                'success' => true,
                'message' => 'تم الانضمام للفعالية بنجاح',
                'current_participants' => $event->current_participants,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الانضمام للفعالية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Leave an event.
     */
    public function leave(Request $request, CommunityEvent $event): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            // Mock implementation - in real app, this would delete the participant record
            $event->decrement('current_participants');

            return response()->json([
                'success' => true,
                'message' => 'تم مغادرة الفعالية بنجاح',
                'current_participants' => $event->current_participants,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مغادرة الفعالية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get event statistics.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $filters = $request->only(['community_id', 'event_type', 'status', 'date_range']);
        
        $query = CommunityEvent::query();
        
        if ($filters['community_id']) {
            $query->where('community_id', $filters['community_id']);
        }
        
        if ($filters['event_type']) {
            $query->where('event_type', $filters['event_type']);
        }
        
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        
        if ($filters['date_range']) {
            if ($filters['date_range'] === 'today') {
                $query->whereDate('start_date', today());
            } elseif ($filters['date_range'] === 'week') {
                $query->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($filters['date_range'] === 'month') {
                $query->whereMonth('start_date', now()->month);
            } elseif ($filters['date_range'] === 'year') {
                $query->whereYear('start_date', now()->year);
            }
        }

        $statistics = [
            'total_events' => $query->count(),
            'upcoming_events' => $query->where('start_date', '>=', now())->count(),
            'past_events' => $query->where('start_date', '<', now())->count(),
            'active_events' => $query->where('status', 'active')->count(),
            'total_participants' => $query->sum('current_participants'),
            'average_rating' => $query->avg('rating') ?? 0,
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get calendar events.
     */
    public function getCalendarEvents(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'community_id' => 'nullable|exists:communities,id',
            'event_types' => 'nullable|array',
            'event_types.*' => 'string',
        ]);

        try {
            $query = CommunityEvent::where('status', 'published')
                ->whereBetween('start_date', [$validated['start_date'], $validated['end_date']]);

            if ($validated['community_id']) {
                $query->where('community_id', $validated['community_id']);
            }

            if (!empty($validated['event_types'])) {
                $query->whereIn('event_type', $validated['event_types']);
            }

            $events = $query->with(['community'])
                ->get(['id', 'title', 'event_type', 'start_date', 'end_date', 'location', 'community_id']);

            $calendarEvents = $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'type' => $event->event_type,
                    'start' => $event->start_date->format('Y-m-d H:i:s'),
                    'end' => $event->end_date->format('Y-m-d H:i:s'),
                    'location' => $event->location,
                    'community' => $event->community?->name ?? 'غير معروف',
                    'color' => $this->getEventColor($event->event_type),
                ];
            });

            return response()->json([
                'success' => true,
                'events' => $calendarEvents,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب أحداث التقويم: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Rate an event.
     */
    public function rate(Request $request, CommunityEvent $event): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'user_name' => 'required|string|max:255',
        ]);

        try {
            // Mock implementation - in real app, this would create a rating record
            // Update event rating (mock calculation)
            $newRating = ($event->rating * $event->review_count + $validated['rating']) / ($event->review_count + 1);
            $event->update([
                'rating' => $newRating,
                'review_count' => $event->review_count + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تقييم الفعالية بنجاح',
                'rating' => $newRating,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تقييم الفعالية: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search events.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:255',
            'limit' => 'nullable|integer|min:1|max:50',
            'event_type' => 'nullable|string',
        ]);

        $limit = $validated['limit'] ?? 20;
        $query = $validated['query'];

        try {
            $events = CommunityEvent::where('status', 'published')
                ->when($validated['event_type'], function ($q, $eventType) {
                    return $q->where('event_type', $eventType);
                })
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('event_type', 'LIKE', "%{$query}%")
                      ->orWhere('location', 'LIKE', "%{$query}%")
                      ->orWhere('organizer_name', 'LIKE', "%{$query}%");
                })
                ->with(['community'])
                ->orderBy('start_date', 'asc')
                ->take($limit)
                ->get(['id', 'title', 'description', 'event_type', 'start_date', 'location', 'community_id', 'cover_image']);

            return response()->json([
                'success' => true,
                'events' => $events,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث في الفعاليات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export event data.
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'filters' => 'nullable|array',
            'include_participants' => 'nullable|boolean',
            'include_schedule' => 'nullable|boolean',
        ]);

        try {
            $exportData = $this->prepareEventExport($validated);
            $filename = $this->generateEventExportFilename($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز بيانات الفعاليات للتصدير',
                'filename' => $filename,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تصدير بيانات الفعاليات: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured events.
     */
    private function getFeaturedEvents(): array
    {
        return CommunityEvent::where('status', 'published')
            ->where('start_date', '>=', now())
            ->where('rating', '>=', 4.0)
            ->orderBy('rating', 'desc')
            ->take(5)
            ->with(['community'])
            ->get(['title', 'event_type', 'start_date', 'location', 'community_id', 'rating'])
            ->toArray();
    }

    /**
     * Get popular types.
     */
    private function getPopularTypes(): array
    {
        return CommunityEvent::select('event_type', DB::raw('count(*) as count'))
            ->where('status', 'published')
            ->groupBy('event_type')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get()
            ->toArray();
    }

    /**
     * Get monthly events.
     */
    private function getMonthlyEvents(): array
    {
        return CommunityEvent::select(
                DB::raw('YEAR(start_date) as year'),
                DB::raw('MONTH(start_date) as month'),
                DB::raw('count(*) as count')
            )
            ->where('status', 'published')
            ->where('start_date', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get event color for calendar.
     */
    private function getEventColor(string $eventType): string
    {
        $colors = [
            'social' => '#3B82F6',
            'educational' => '#10B981',
            'sports' => '#F59E0B',
            'cultural' => '#8B5CF6',
            'religious' => '#EF4444',
            'charity' => '#EC4899',
            'business' => '#6366F1',
            'entertainment' => '#F97316',
            'health' => '#14B8A6',
            'other' => '#6B7280',
        ];

        return $colors[$eventType] ?? '#6B7280';
    }

    /**
     * Prepare event export data.
     */
    private function prepareEventExport(array $options): array
    {
        $filters = $options['filters'] ?? [];
        $includeParticipants = $options['include_participants'] ?? false;
        $includeSchedule = $options['include_schedule'] ?? false;

        $query = CommunityEvent::with(['community']);
        
        if (isset($filters['community_id'])) {
            $query->where('community_id', $filters['community_id']);
        }
        
        if (isset($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $events = $query->get();

        $data = $events->map(function ($event) use ($includeParticipants, $includeSchedule) {
            $item = [
                'id' => $event->id,
                'title' => $event->title,
                'community' => $event->community?->name ?? 'غير معروف',
                'event_type' => $event->event_type,
                'description' => $event->description,
                'start_date' => $event->start_date->format('Y-m-d H:i:s'),
                'end_date' => $event->end_date->format('Y-m-d H:i:s'),
                'location' => $event->location,
                'organizer_name' => $event->organizer_name,
                'status' => $event->status,
                'max_participants' => $event->max_participants,
                'current_participants' => $event->current_participants,
                'rating' => $event->rating,
                'review_count' => $event->review_count,
                'view_count' => $event->view_count,
                'created_at' => $event->created_at->format('Y-m-d H:i:s'),
            ];

            if ($includeParticipants) {
                $item['participant_info'] = [
                    'max_participants' => $event->max_participants,
                    'current_participants' => $event->current_participants,
                    'available_spots' => $event->max_participants ? $event->max_participants - $event->current_participants : null,
                ];
            }

            if ($includeSchedule) {
                $item['schedule'] = $event->schedule;
            }

            return $item;
        });

        return [
            'headers' => ['ID', 'Title', 'Community', 'Event Type', 'Status', 'Start Date', 'End Date', 'Location', 'Organizer', 'Rating', 'Created At'],
            'rows' => $data->toArray(),
        ];
    }

    /**
     * Generate event export filename.
     */
    private function generateEventExportFilename(array $options): string
    {
        $format = $options['format'];
        $timestamp = now()->format('Y-m-d_H-i-s');

        return "community_events_export_{$timestamp}.{$format}";
    }
}

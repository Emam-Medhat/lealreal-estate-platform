<?php

namespace App\Http\Controllers\Metaverse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Metaverse\HostVirtualEventRequest;
use App\Models\Metaverse\VirtualPropertyEvent;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\Metaverse\MetaverseShowroom;
use App\Models\Metaverse\VirtualWorld;
use App\Models\Metaverse\MetaverseAvatar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class VirtualPropertyEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of virtual property events.
     */
    public function index(Request $request)
    {
        $query = VirtualPropertyEvent::with(['virtualWorld', 'host', 'property', 'showroom'])
            ->withCount(['attendees', 'registrations']);

        // Filters
        if ($request->filled('world_id')) {
            $query->where('virtual_world_id', $request->world_id);
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('start_time', today());
                    break;
                case 'week':
                    $query->whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('start_time', now()->month);
                    break;
                case 'upcoming':
                    $query->where('start_time', '>', now());
                    break;
                case 'past':
                    $query->where('end_time', '<', now());
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'start_time');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $events = $query->paginate(12);
        $virtualWorlds = VirtualWorld::where('is_active', true)->get();

        return Inertia::render('Metaverse/Events/Index', [
            'events' => $events,
            'virtualWorlds' => $virtualWorlds,
            'filters' => $request->only(['world_id', 'event_type', 'status', 'date_filter', 'search', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Show the form for creating a new virtual property event.
     */
    public function create()
    {
        $virtualWorlds = VirtualWorld::where('is_active', true)->get();
        $properties = MetaverseProperty::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->with(['virtualWorld'])
            ->get();
        $showrooms = MetaverseShowroom::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->where('is_active', true)
            ->with(['virtualWorld'])
            ->get();

        return Inertia::render('Metaverse/Events/Create', [
            'virtualWorlds' => $virtualWorlds,
            'properties' => $properties,
            'showrooms' => $showrooms,
        ]);
    }

    /**
     * Store a newly created virtual property event.
     */
    public function store(HostVirtualEventRequest $request)
    {
        $event = VirtualPropertyEvent::create([
            'title' => $request->title,
            'description' => $request->description,
            'virtual_world_id' => $request->virtual_world_id,
            'event_type' => $request->event_type,
            'metaverse_property_id' => $request->metaverse_property_id,
            'metaverse_showroom_id' => $request->metaverse_showroom_id,
            'location' => $request->location,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'timezone' => $request->timezone ?? 'UTC',
            'max_attendees' => $request->max_attees,
            'registration_required' => $request->boolean('registration_required', true),
            'registration_deadline' => $request->registration_deadline,
            'entry_fee' => $request->entry_fee,
            'currency' => $request->currency ?? 'ETH',
            'access_level' => $request->access_level ?? 'public',
            'event_settings' => [
                'allow_chat' => $request->boolean('allow_chat', true),
                'allow_voice' => $request->boolean('allow_voice', true),
                'allow_video' => $request->boolean('allow_video', false),
                'record_event' => $request->boolean('record_event', false),
                'enable_qa' => $request->boolean('enable_qa', true),
                'enable_polls' => $request->boolean('enable_polls', false),
                'enable_networking' => $request->boolean('enable_networking', true),
            ],
            'agenda' => $request->agenda ?? [],
            'speakers' => $request->speakers ?? [],
            'sponsors' => $request->sponsors ?? [],
            'tags' => $request->tags ?? [],
            'status' => 'scheduled',
            'host_id' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        // Handle event images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('metaverse/events/' . $event->id, 'public');
                $event->images()->create(['path' => $path]);
            }
        }

        // Handle event resources
        if ($request->hasFile('resources')) {
            foreach ($request->file('resources') as $resource) {
                $path = $resource->store('metaverse/event-resources/' . $event->id, 'public');
                $event->resources()->create([
                    'path' => $path,
                    'file_name' => $resource->getClientOriginalName(),
                    'file_type' => $resource->getClientOriginalExtension(),
                    'file_size' => $resource->getSize(),
                ]);
            }
        }

        return redirect()->route('metaverse.events.show', $event)
            ->with('success', 'تم إنشاء الفعالية الافتراضية بنجاح');
    }

    /**
     * Display the specified virtual property event.
     */
    public function show(VirtualPropertyEvent $event)
    {
        $event->load([
            'virtualWorld',
            'host',
            'property',
            'showroom',
            'images',
            'resources',
            'speakers',
            'sponsors',
            'attendees' => function ($query) {
                $query->where('status', 'confirmed')->with('user.avatar');
            },
            'registrations' => function ($query) {
                $query->where('status', 'confirmed');
            },
        ]);

        // Get event statistics
        $stats = [
            'total_attendees' => $event->attendees_count,
            'total_registrations' => $event->registrations_count,
            'available_spots' => $event->max_attendees - $event->attendees_count,
            'registration_rate' => $this->calculateRegistrationRate($event),
            'attendance_rate' => $this->calculateAttendanceRate($event),
        ];

        // Check if user is registered
        $userRegistration = null;
        if (auth()->check()) {
            $userRegistration = $event->registrations()
                ->where('user_id', auth()->id())
                ->first();
        }

        // Get similar events
        $similarEvents = VirtualPropertyEvent::where('event_type', $event->event_type)
            ->where('id', '!=', $event->id)
            ->where('start_time', '>', now())
            ->where('status', 'scheduled')
            ->with(['virtualWorld', 'host'])
            ->limit(6)
            ->get();

        return Inertia::render('Metaverse/Events/Show', [
            'event' => $event,
            'stats' => $stats,
            'userRegistration' => $userRegistration,
            'similarEvents' => $similarEvents,
        ]);
    }

    /**
     * Show the form for editing the specified virtual property event.
     */
    public function edit(VirtualPropertyEvent $event)
    {
        $this->authorize('update', $event);

        $event->load(['images', 'resources', 'speakers', 'sponsors']);
        $virtualWorlds = VirtualWorld::where('is_active', true)->get();
        $properties = MetaverseProperty::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->with(['virtualWorld'])
            ->get();
        $showrooms = MetaverseShowroom::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->where('is_active', true)
            ->with(['virtualWorld'])
            ->get();

        return Inertia::render('Metaverse/Events/Edit', [
            'event' => $event,
            'virtualWorlds' => $virtualWorlds,
            'properties' => $properties,
            'showrooms' => $showrooms,
        ]);
    }

    /**
     * Update the specified virtual property event.
     */
    public function update(HostVirtualEventRequest $request, VirtualPropertyEvent $event)
    {
        $this->authorize('update', $event);

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'virtual_world_id' => $request->virtual_world_id,
            'event_type' => $request->event_type,
            'metaverse_property_id' => $request->metaverse_property_id,
            'metaverse_showroom_id' => $request->metaverse_showroom_id,
            'location' => $request->location,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'timezone' => $request->timezone ?? 'UTC',
            'max_attendees' => $request->max_attendees,
            'registration_required' => $request->boolean('registration_required'),
            'registration_deadline' => $request->registration_deadline,
            'entry_fee' => $request->entry_fee,
            'currency' => $request->currency ?? 'ETH',
            'access_level' => $request->access_level,
            'event_settings' => [
                'allow_chat' => $request->boolean('allow_chat', true),
                'allow_voice' => $request->boolean('allow_voice', true),
                'allow_video' => $request->boolean('allow_video', false),
                'record_event' => $request->boolean('record_event', false),
                'enable_qa' => $request->boolean('enable_qa', true),
                'enable_polls' => $request->boolean('enable_polls', false),
                'enable_networking' => $request->boolean('enable_networking', true),
            ],
            'agenda' => $request->agenda ?? [],
            'speakers' => $request->speakers ?? [],
            'sponsors' => $request->sponsors ?? [],
            'tags' => $request->tags ?? [],
            'updated_by' => auth()->id(),
        ]);

        // Handle new images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('metaverse/events/' . $event->id, 'public');
                $event->images()->create(['path' => $path]);
            }
        }

        // Handle new resources
        if ($request->hasFile('new_resources')) {
            foreach ($request->file('new_resources') as $resource) {
                $path = $resource->store('metaverse/event-resources/' . $event->id, 'public');
                $event->resources()->create([
                    'path' => $path,
                    'file_name' => $resource->getClientOriginalName(),
                    'file_type' => $resource->getClientOriginalExtension(),
                    'file_size' => $resource->getSize(),
                ]);
            }
        }

        return redirect()->route('metaverse.events.show', $event)
            ->with('success', 'تم تحديث الفعالية الافتراضية بنجاح');
    }

    /**
     * Remove the specified virtual property event.
     */
    public function destroy(VirtualPropertyEvent $event)
    {
        $this->authorize('delete', $event);

        // Delete associated files
        foreach ($event->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        foreach ($event->resources as $resource) {
            Storage::disk('public')->delete($resource->path);
        }

        $event->delete();

        return redirect()->route('metaverse.events.index')
            ->with('success', 'تم حذف الفعالية الافتراضية بنجاح');
    }

    /**
     * Register for event.
     */
    public function register(Request $request, VirtualPropertyEvent $event)
    {
        $this->authorize('register', $event);

        // Check if registration is still open
        if ($event->registration_deadline && now()->isAfter($event->registration_deadline)) {
            return back()->with('error', 'انتهت فترة التسجيل');
        }

        // Check if event is full
        if ($event->attendees_count >= $event->max_attendees) {
            return back()->with('error', 'الفعالية ممتلئة');
        }

        // Check if already registered
        if ($event->registrations()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'أنت مسجل بالفعل في هذه الفعالية');
        }

        // Process payment if required
        if ($event->entry_fee > 0) {
            $paymentResult = $this->processEventPayment($event);
            if (!$paymentResult['success']) {
                return back()->with('error', 'فشلت عملية الدفع: ' . $paymentResult['message']);
            }
        }

        // Create registration
        $registration = $event->registrations()->create([
            'user_id' => auth()->id(),
            'status' => 'confirmed',
            'payment_status' => $event->entry_fee > 0 ? 'paid' : 'free',
            'payment_amount' => $event->entry_fee,
            'payment_currency' => $event->currency,
            'registered_at' => now(),
        ]);

        return back()->with('success', 'تم التسجيل في الفعالية بنجاح');
    }

    /**
     * Cancel registration.
     */
    public function cancelRegistration(VirtualPropertyEvent $event)
    {
        $registration = $event->registrations()
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Check cancellation policy
        $hoursUntilEvent = now()->diffInHours($event->start_time);
        if ($hoursUntilEvent < 24) {
            return back()->with('error', 'لا يمكن إلغاء التسجيل قبل أقل من 24 ساعة من الفعالية');
        }

        $registration->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'تم إلغاء التسجيل بنجاح');
    }

    /**
     * Join event (enter virtual space).
     */
    public function join(VirtualPropertyEvent $event)
    {
        $this->authorize('join', $event);

        // Check if event is live
        if (!$event->isLive()) {
            return back()->with('error', 'الفعالية لم تبدأ بعد');
        }

        // Check if registered (if required)
        if ($event->registration_required) {
            $registration = $event->registrations()
                ->where('user_id', auth()->id())
                ->where('status', 'confirmed')
                ->first();
            
            if (!$registration) {
                return back()->with('error', 'يجب التسجيل في الفعالية أولاً');
            }
        }

        // Create attendance record
        $attendance = $event->attendees()->create([
            'user_id' => auth()->id(),
            'joined_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Inertia::render('Metaverse/Events/VirtualSpace', [
            'event' => $event->load(['property', 'showroom', 'host']),
            'attendance' => $attendance,
            'userAvatar' => auth()->user()->metaverseAvatar,
        ]);
    }

    /**
     * Leave event.
     */
    public function leave(VirtualPropertyEvent $event)
    {
        $attendance = $event->attendees()
            ->where('user_id', auth()->id())
            ->whereNull('left_at')
            ->latest()
            ->first();

        if ($attendance) {
            $attendance->update([
                'left_at' => now(),
                'duration' => now()->diffInSeconds($attendance->joined_at),
            ]);
        }

        return redirect()->route('metaverse.events.show', $event);
    }

    /**
     * Get event analytics.
     */
    public function analytics(VirtualPropertyEvent $event)
    {
        $this->authorize('view', $event);

        $analytics = [
            'registration_trend' => $event->registrations()
                ->selectRaw('DATE(registered_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'attendance_timeline' => $event->attendees()
                ->selectRaw('DATE(joined_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'engagement_metrics' => [
                'avg_attendance_duration' => $event->attendees()->avg('duration') ?? 0,
                'peak_attendance' => $this->calculatePeakAttendance($event),
                'interaction_rate' => $this->calculateInteractionRate($event),
            ],
            
            'demographics' => [
                'by_country' => $event->registrations()
                    ->join('users', 'event_registrations.user_id', '=', 'users.id')
                    ->selectRaw('users.country, COUNT(*) as count')
                    ->groupBy('users.country')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
            ],
            
            'revenue' => [
                'total_revenue' => $event->registrations()->sum('payment_amount'),
                'revenue_by_currency' => $event->registrations()
                    ->selectRaw('payment_currency, SUM(payment_amount) as total')
                    ->groupBy('payment_currency')
                    ->get(),
            ],
        ];

        return response()->json($analytics);
    }

    /**
     * Process event payment.
     */
    private function processEventPayment(VirtualPropertyEvent $event): array
    {
        // This would integrate with actual payment gateway
        // For now, simulate payment processing
        
        try {
            // Simulate payment processing
            if ($event->currency === 'ETH') {
                $result = $this->processCryptoPayment($event);
            } else {
                $result = $this->processTraditionalPayment($event);
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
    private function processCryptoPayment(VirtualPropertyEvent $event): array
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
    private function processTraditionalPayment(VirtualPropertyEvent $event): array
    {
        // Simulate traditional payment processing
        return [
            'success' => true,
            'message' => 'Payment processed successfully',
        ];
    }

    /**
     * Calculate registration rate.
     */
    private function calculateRegistrationRate(VirtualPropertyEvent $event): float
    {
        if (!$event->registration_required) {
            return 100;
        }

        $potentialAttendees = $event->max_attendees;
        $actualRegistrations = $event->registrations_count;
        
        return $potentialAttendees > 0 ? ($actualRegistrations / $potentialAttendees) * 100 : 0;
    }

    /**
     * Calculate attendance rate.
     */
    private function calculateAttendanceRate(VirtualPropertyEvent $event): float
    {
        $totalRegistrations = $event->registrations_count;
        $actualAttendees = $event->attendees_count;
        
        return $totalRegistrations > 0 ? ($actualAttendees / $totalRegistrations) * 100 : 0;
    }

    /**
     * Calculate peak attendance.
     */
    private function calculatePeakAttendance(VirtualPropertyEvent $event): int
    {
        // This would require tracking concurrent attendees
        // For now, return total attendees
        return $event->attendees_count;
    }

    /**
     * Calculate interaction rate.
     */
    private function calculateInteractionRate(VirtualPropertyEvent $event): float
    {
        // This would require tracking interactions (chat, polls, etc.)
        // For now, return a placeholder
        return 75.5;
    }
}

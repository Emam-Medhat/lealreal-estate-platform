<?php

namespace App\Http\Controllers;

use App\Models\VirtualOpenHouse;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VirtualOpenHouseController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_open_houses' => VirtualOpenHouse::count(),
            'active_open_houses' => VirtualOpenHouse::where('status', 'active')->count(),
            'scheduled_open_houses' => VirtualOpenHouse::where('status', 'scheduled')->count(),
            'completed_open_houses' => VirtualOpenHouse::where('status', 'completed')->count(),
            'total_attendees' => $this->getTotalAttendees(),
            'average_attendance' => $this->getAverageAttendance(),
        ];

        $recentOpenHouses = VirtualOpenHouse::with(['property', 'host'])
            ->latest()
            ->take(10)
            ->get();

        $openHouseTrends = $this->getOpenHouseTrends();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('vr.open-house.dashboard', compact(
            'stats', 
            'recentOpenHouses', 
            'openHouseTrends', 
            'performanceMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = VirtualOpenHouse::with(['property', 'host']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_range')) {
            $dates = explode(' - ', $request->date_range);
            $query->whereBetween('scheduled_at', [Carbon::parse($dates[0]), Carbon::parse($dates[1])]);
        }

        if ($request->filled('host_id')) {
            $query->where('host_id', $request->host_id);
        }

        $openHouses = $query->latest()->paginate(12);

        $properties = Property::where('status', 'active')->get();
        $statuses = ['scheduled', 'active', 'completed', 'cancelled'];
        $hosts = User::where('role', 'agent')->orWhere('role', 'broker')->get();

        return view('vr.open-house.index', compact(
            'openHouses', 
            'properties', 
            'statuses', 
            'hosts'
        ));
    }

    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        $hosts = User::where('role', 'agent')->orWhere('role', 'broker')->get();
        $presentationTypes = ['guided_tour', 'self_guided', 'live_presentation', 'recorded_tour'];
        $interactionModes = ['text_chat', 'voice_chat', 'video_call', 'q&a_session'];
        $accessLevels = ['public', 'registered_only', 'invitation_only', 'premium'];

        return view('vr.open-house.create', compact(
            'properties', 
            'hosts', 
            'presentationTypes', 
            'interactionModes', 
            'accessLevels'
        ));
    }

    public function store(ScheduleVirtualOpenHouseRequest $request)
    {
        DB::beginTransaction();
        try {
            $openHouseData = $request->validated();
            $openHouseData['host_id'] = auth()->id();
            $openHouseData['status'] = 'scheduled';
            $openHouseData['created_by'] = auth()->id();

            // Process presentation files
            if ($request->hasFile('presentation_files')) {
                $openHouseData['presentation_files'] = $this->processPresentationFiles($request->file('presentation_files'));
            }

            // Generate open house metadata
            $openHouseData['open_house_metadata'] = $this->generateOpenHouseMetadata($request);

            $openHouse = VirtualOpenHouse::create($openHouseData);

            // Set up presentation content
            if ($request->has('presentation_content')) {
                $this->setupPresentationContent($openHouse, $request->presentation_content);
            }

            // Configure interaction settings
            if ($request->has('interaction_settings')) {
                $this->configureInteractionSettings($openHouse, $request->interaction_settings);
            }

            // Set up access control
            if ($request->has('access_control')) {
                $this->setupAccessControl($openHouse, $request->access_control);
            }

            // Send notifications to registered users
            $this->sendOpenHouseNotifications($openHouse);

            DB::commit();

            return redirect()
                ->route('vr.open-house.show', $openHouse)
                ->with('success', 'تم جدولة البيت المفتوح الافتراضي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء جدولة البيت المفتوح الافتراضي: ' . $e->getMessage());
        }
    }

    public function show(VirtualOpenHouse $openHouse)
    {
        $openHouse->load(['property', 'host', 'presentationContent', 'interactionSettings', 'attendees']);
        $openHouseAnalytics = $this->getOpenHouseAnalytics($openHouse);
        $relatedOpenHouses = $this->getRelatedOpenHouses($openHouse);

        return view('vr.open-house.show', compact(
            'openHouse', 
            'openHouseAnalytics', 
            'relatedOpenHouses'
        ));
    }

    public function edit(VirtualOpenHouse $openHouse)
    {
        $properties = Property::where('status', 'active')->get();
        $hosts = User::where('role', 'agent')->orWhere('role', 'broker')->get();
        $presentationTypes = ['guided_tour', 'self_guided', 'live_presentation', 'recorded_tour'];
        $interactionModes = ['text_chat', 'voice_chat', 'video_call', 'q&a_session'];
        $accessLevels = ['public', 'registered_only', 'invitation_only', 'premium'];

        return view('vr.open-house.edit', compact(
            'openHouse', 
            'properties', 
            'hosts', 
            'presentationTypes', 
            'interactionModes', 
            'accessLevels'
        ));
    }

    public function update(ScheduleVirtualOpenHouseRequest $request, VirtualOpenHouse $openHouse)
    {
        DB::beginTransaction();
        try {
            $openHouseData = $request->validated();
            $openHouseData['updated_by'] = auth()->id();

            // Process updated presentation files
            if ($request->hasFile('presentation_files')) {
                $openHouseData['presentation_files'] = $this->processPresentationFiles($request->file('presentation_files'));
            }

            // Update open house metadata
            $openHouseData['open_house_metadata'] = $this->generateOpenHouseMetadata($request);

            $openHouse->update($openHouseData);

            // Update presentation content
            if ($request->has('presentation_content')) {
                $this->setupPresentationContent($openHouse, $request->presentation_content);
            }

            // Update interaction settings
            if ($request->has('interaction_settings')) {
                $this->configureInteractionSettings($openHouse, $request->interaction_settings);
            }

            // Update access control
            if ($request->has('access_control')) {
                $this->setupAccessControl($openHouse, $request->access_control);
            }

            DB::commit();

            return redirect()
                ->route('vr.open-house.show', $openHouse)
                ->with('success', 'تم تحديث البيت المفتوح الافتراضي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث البيت المفتوح الافتراضي: ' . $e->getMessage());
        }
    }

    public function destroy(VirtualOpenHouse $openHouse)
    {
        try {
            // Cancel open house and notify attendees
            $this->cancelOpenHouse($openHouse);

            // Delete open house
            $openHouse->delete();

            return redirect()
                ->route('vr.open-house.index')
                ->with('success', 'تم حذف البيت المفتوح الافتراضي بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف البيت المفتوح الافتراضي: ' . $e->getMessage());
        }
    }

    public function joinOpenHouse(VirtualOpenHouse $openHouse)
    {
        try {
            // Check if user can join
            if (!$this->canJoinOpenHouse($openHouse)) {
                return back()
                    ->with('error', 'لا يمكنك الانضمام إلى هذا البيت المفتوح');
            }

            // Initialize virtual session
            $session = $this->initializeVirtualSession($openHouse);

            // Register attendee
            $this->registerAttendee($openHouse, auth()->user());

            // Update attendance statistics
            $openHouse->increment('current_attendees');

            return view('vr.open-house.session', compact('openHouse', 'session'));
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء الانضمام إلى البيت المفتوح: ' . $e->getMessage());
        }
    }

    public function startOpenHouse(VirtualOpenHouse $openHouse)
    {
        try {
            // Verify host permissions
            if ($openHouse->host_id !== auth()->id()) {
                return back()
                    ->with('error', 'فقط المضيف يمكنه بدء البيت المفتوح');
            }

            // Start the open house
            $openHouse->update([
                'status' => 'active',
                'started_at' => now(),
            ]);

            // Notify registered attendees
            $this->notifyOpenHouseStart($openHouse);

            return redirect()
                ->route('vr.open-house.host', $openHouse)
                ->with('success', 'تم بدء البيت المفتوح بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء بدء البيت المفتوح: ' . $e->getMessage());
        }
    }

    public function endOpenHouse(VirtualOpenHouse $openHouse)
    {
        try {
            // Verify host permissions
            if ($openHouse->host_id !== auth()->id()) {
                return back()
                    ->with('error', 'فقط المضيف يمكنه إنهاء البيت المفتوح');
            }

            // End the open house
            $openHouse->update([
                'status' => 'completed',
                'ended_at' => now(),
            ]);

            // Generate attendance report
            $this->generateAttendanceReport($openHouse);

            return redirect()
                ->route('vr.open-house.show', $openHouse)
                ->with('success', 'تم إنهاء البيت المفتوح بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء إنهاء البيت المفتوح: ' . $e->getMessage());
        }
    }

    public function hostSession(VirtualOpenHouse $openHouse)
    {
        try {
            // Verify host permissions
            if ($openHouse->host_id !== auth()->id()) {
                return back()
                    ->with('error', 'فقط المضيف يمكنه الوصول إلى لوحة التحكم');
            }

            // Initialize host session
            $session = $this->initializeHostSession($openHouse);

            return view('vr.open-house.host', compact('openHouse', 'session'));
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء الوصول إلى لوحة التحكم: ' . $e->getMessage());
        }
    }

    public function sendMessage(Request $request, VirtualOpenHouse $openHouse)
    {
        try {
            $messageData = [
                'open_house_id' => $openHouse->id,
                'user_id' => auth()->id(),
                'message' => $request->message,
                'message_type' => $request->message_type ?? 'text',
                'timestamp' => now(),
            ];

            // Store message
            $message = $this->storeMessage($messageData);

            // Broadcast to other attendees
            $this->broadcastMessage($openHouse, $message);

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function recordInteraction(Request $request, VirtualOpenHouse $openHouse)
    {
        try {
            $interactionData = [
                'open_house_id' => $openHouse->id,
                'user_id' => auth()->id(),
                'interaction_type' => $request->interaction_type,
                'content_id' => $request->content_id,
                'duration' => $request->duration,
                'timestamp' => now(),
            ];

            // Record interaction
            $this->recordOpenHouseInteraction($interactionData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function analytics(VirtualOpenHouse $openHouse)
    {
        $analytics = $this->getDetailedOpenHouseAnalytics($openHouse);
        $attendeeBehavior = $this->getAttendeeBehaviorAnalytics($openHouse);
        $engagementMetrics = $this->getEngagementMetrics($openHouse);

        return view('vr.open-house.analytics', compact(
            'analytics', 
            'attendeeBehavior', 
            'engagementMetrics'
        ));
    }

    public function exportReport(VirtualOpenHouse $openHouse, Request $request)
    {
        try {
            $reportFormat = $request->format ?? 'pdf';
            $reportData = $this->prepareOpenHouseReport($openHouse, $reportFormat);

            return response()->download($reportData['file'], $reportData['filename']);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء تصدير التقرير: ' . $e->getMessage());
        }
    }

    private function processPresentationFiles($files)
    {
        $filePaths = [];
        
        foreach ($files as $file) {
            $path = $file->store('open-house-presentations', 'public');
            $filePaths[] = [
                'path' => $path,
                'type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
            ];
        }

        return $filePaths;
    }

    private function generateOpenHouseMetadata($request)
    {
        return [
            'presentation_duration' => $request->presentation_duration ?? 60,
            'max_attendees' => $request->max_attendees ?? 100,
            'recording_enabled' => $request->recording_enabled ?? false,
            'chat_enabled' => $request->chat_enabled ?? true,
            'qa_enabled' => $request->qa_enabled ?? true,
            'screen_sharing' => $request->screen_sharing ?? false,
            'virtual_tour' => $request->virtual_tour ?? false,
            'language' => $request->language ?? 'ar',
            'timezone' => $request->timezone ?? 'Asia/Riyadh',
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function setupPresentationContent($openHouse, $contentItems)
    {
        foreach ($contentItems as $contentData) {
            $openHouse->presentationContent()->create([
                'title' => $contentData['title'],
                'type' => $contentData['type'],
                'content' => $contentData['content'],
                'order' => $contentData['order'],
                'duration' => $contentData['duration'] ?? 5,
                'is_interactive' => $contentData['is_interactive'] ?? false,
                'content_metadata' => $contentData['metadata'] ?? [],
            ]);
        }
    }

    private function configureInteractionSettings($openHouse, $settings)
    {
        foreach ($settings as $settingData) {
            $openHouse->interactionSettings()->create([
                'setting_type' => $settingData['type'],
                'is_enabled' => $settingData['enabled'],
                'configuration' => $settingData['configuration'] ?? [],
                'restrictions' => $settingData['restrictions'] ?? [],
            ]);
        }
    }

    private function setupAccessControl($openHouse, $accessControl)
    {
        $openHouse->accessControl()->create([
            'access_level' => $accessControl['level'],
            'registration_required' => $accessControl['registration_required'] ?? false,
            'approval_required' => $accessControl['approval_required'] ?? false,
            'invitation_codes' => $accessControl['invitation_codes'] ?? [],
            'allowed_users' => $accessControl['allowed_users'] ?? [],
            'blocked_users' => $accessControl['blocked_users'] ?? [],
        ]);
    }

    private function sendOpenHouseNotifications($openHouse)
    {
        // Send notifications to registered users about the upcoming open house
        // This would integrate with a notification system
    }

    private function canJoinOpenHouse($openHouse)
    {
        // Check if user can join based on access control, timing, and capacity
        if ($openHouse->status !== 'active') {
            return false;
        }

        if ($openHouse->current_attendees >= $openHouse->open_house_metadata['max_attendees']) {
            return false;
        }

        return true;
    }

    private function initializeVirtualSession($openHouse)
    {
        return [
            'session_id' => uniqid('oh_'),
            'join_time' => now(),
            'user_id' => auth()->id(),
            'open_house_id' => $openHouse->id,
            'device_info' => $this->getDeviceInfo(),
            'permissions' => $this->getUserPermissions($openHouse, auth()->user()),
        ];
    }

    private function initializeHostSession($openHouse)
    {
        return [
            'session_id' => uniqid('host_'),
            'start_time' => now(),
            'host_id' => auth()->id(),
            'open_house_id' => $openHouse->id,
            'host_controls' => $this->getHostControls(),
            'attendee_management' => $this->getAttendeeManagementTools(),
        ];
    }

    private function registerAttendee($openHouse, $user)
    {
        $openHouse->attendees()->create([
            'user_id' => $user->id,
            'join_time' => now(),
            'status' => 'active',
            'device_info' => $this->getDeviceInfo(),
        ]);
    }

    private function cancelOpenHouse($openHouse)
    {
        // Update status and notify attendees
        $openHouse->update(['status' => 'cancelled']);
        $this->notifyOpenHouseCancellation($openHouse);
    }

    private function notifyOpenHouseStart($openHouse)
    {
        // Notify registered attendees that the open house has started
    }

    private function notifyOpenHouseCancellation($openHouse)
    {
        // Notify registered attendees about the cancellation
    }

    private function generateAttendanceReport($openHouse)
    {
        // Generate comprehensive attendance report
    }

    private function storeMessage($messageData)
    {
        // Store message in database
        // Return message object
    }

    private function broadcastMessage($openHouse, $message)
    {
        // Broadcast message to all attendees using WebSocket
    }

    private function recordOpenHouseInteraction($interactionData)
    {
        // Store interaction data for analytics
    }

    private function getDeviceInfo()
    {
        return [
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'screen_resolution' => request()->header('Screen-Resolution'),
            'device_type' => $this->detectDeviceType(),
        ];
    }

    private function detectDeviceType()
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/Mobile|Android|iPhone/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    private function getUserPermissions($openHouse, $user)
    {
        $permissions = ['view', 'listen'];

        if ($openHouse->open_house_metadata['chat_enabled']) {
            $permissions[] = 'chat';
        }

        if ($openHouse->open_house_metadata['qa_enabled']) {
            $permissions[] = 'qa';
        }

        return $permissions;
    }

    private function getHostControls()
    {
        return [
            'start_stop' => true,
            'mute_unmute' => true,
            'screen_share' => true,
            'manage_attendees' => true,
            'record_session' => true,
        ];
    }

    private function getAttendeeManagementTools()
    {
        return [
            'remove_attendee' => true,
            'mute_attendee' => true,
            'block_attendee' => true,
            'promote_speaker' => true,
        ];
    }

    private function getTotalAttendees()
    {
        return VirtualOpenHouse::sum('current_attendees') ?? 0;
    }

    private function getAverageAttendance()
    {
        return VirtualOpenHouse::avg('current_attendees') ?? 0;
    }

    private function getOpenHouseTrends()
    {
        return [
            'daily_open_houses' => VirtualOpenHouse::selectRaw('DATE(scheduled_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->take(30)
                ->get(),
            'attendance_trends' => $this->getAttendanceTrends(),
        ];
    }

    private function getPerformanceMetrics()
    {
        return [
            'attendance_rate' => 78.5,
            'engagement_score' => 8.2,
            'satisfaction_rate' => 4.1,
            'conversion_rate' => 12.3,
        ];
    }

    private function getOpenHouseAnalytics($openHouse)
    {
        return [
            'total_attendees' => $openHouse->current_attendees,
            'peak_attendance' => $openHouse->peak_attendees,
            'average_duration' => $openHouse->average_attendance_duration,
            'engagement_score' => $openHouse->engagement_score,
            'satisfaction_rating' => $openHouse->satisfaction_rating,
        ];
    }

    private function getRelatedOpenHouses($openHouse)
    {
        return VirtualOpenHouse::where('property_id', $openHouse->property_id)
            ->where('id', '!=', $openHouse->id)
            ->with('property')
            ->take(5)
            ->get();
    }

    private function getDetailedOpenHouseAnalytics($openHouse)
    {
        return [
            'attendance_analytics' => $this->getOpenHouseAnalytics($openHouse),
            'engagement_analytics' => $this->getEngagementAnalytics($openHouse),
            'interaction_analytics' => $this->getInteractionAnalytics($openHouse),
            'conversion_analytics' => $this->getConversionAnalytics($openHouse),
        ];
    }

    private function getAttendeeBehaviorAnalytics($openHouse)
    {
        return [
            'join_patterns' => $this->getJoinPatterns($openHouse),
            'participation_levels' => $this->getParticipationLevels($openHouse),
            'question_frequency' => $this->getQuestionFrequency($openHouse),
            'drop_off_points' => $this->getDropOffPoints($openHouse),
        ];
    }

    private function getEngagementMetrics($openHouse)
    {
        return [
            'message_count' => $openHouse->messages()->count(),
            'question_count' => $openHouse->questions()->count(),
            'interaction_rate' => $this->getInteractionRate($openHouse),
            'attention_span' => $this->getAttentionSpan($openHouse),
        ];
    }

    private function prepareOpenHouseReport($openHouse, $format)
    {
        $data = [
            'open_house' => $openHouse->toArray(),
            'attendees' => $openHouse->attendees->toArray(),
            'messages' => $openHouse->messages->toArray(),
            'analytics' => $this->getOpenHouseAnalytics($openHouse),
        ];

        if ($format === 'pdf') {
            $filename = 'open_house_report_' . $openHouse->id . '.pdf';
            $content = $this->generatePDFReport($data);
        } else {
            $filename = 'open_house_report_' . $openHouse->id . '.json';
            $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        file_put_contents($tempFile, $content);

        return [
            'file' => $tempFile,
            'filename' => $filename,
        ];
    }

    // Additional helper methods would be implemented here...
}

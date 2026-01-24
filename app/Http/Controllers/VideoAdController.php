<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\AdPlacement;
use App\Models\AdClick;
use App\Models\AdImpression;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class VideoAdController extends Controller
{
    public function index()
    {
        $videoAds = Advertisement::with(['campaign', 'placements'])
            ->where('type', 'video')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('ads.video-ads', compact('videoAds'));
    }

    public function create()
    {
        $campaigns = Auth::user()->adCampaigns()->where('status', 'active')->get();
        $placements = AdPlacement::where('type', 'popup')
            ->orWhere('type', 'content')
            ->where('is_active', true)
            ->get();

        return view('ads.create-video-ad', compact('campaigns', 'placements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:ad_campaigns,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'video_file' => 'required|mimes:mp4,avi,mov,wmv|max:51200',
            'video_url' => 'nullable|url|max:500',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'target_url' => 'required|url|max:500',
            'placements' => 'required|array',
            'placements.*' => 'exists:ad_placements,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'daily_budget' => 'required|numeric|min:1',
            'video_duration' => 'required|integer|min:5|max:300',
            'autoplay' => 'boolean',
            'muted' => 'boolean',
            'controls' => 'boolean',
            'loop' => 'boolean',
            'playback_position' => 'nullable|integer|min:0|max:100',
            'skip_after' => 'nullable|integer|min:5|max:60'
        ]);

        // Handle video file upload
        $videoPath = null;
        if ($request->hasFile('video_file')) {
            $video = $request->file('video_file');
            $videoName = time() . '_' . $video->getClientOriginalName();
            $videoPath = $video->storeAs('video-ads', $videoName, 'public');
        }

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = time() . '_thumb_' . $thumbnail->getClientOriginalName();
            $thumbnailPath = $thumbnail->storeAs('video-thumbnails', $thumbnailName, 'public');
        }

        $videoAd = Advertisement::create([
            'user_id' => Auth::id(),
            'campaign_id' => $request->campaign_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => 'video',
            'video_url' => $videoPath ?: $request->video_url,
            'thumbnail_url' => $thumbnailPath,
            'target_url' => $request->target_url,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'daily_budget' => $request->daily_budget,
            'status' => 'pending',
            'approval_status' => 'pending',
            'video_duration' => $request->video_duration,
            'autoplay' => $request->autoplay ?? false,
            'muted' => $request->muted ?? true,
            'controls' => $request->controls ?? true,
            'loop' => $request->loop ?? false,
            'playback_position' => $request->playback_position,
            'skip_after' => $request->skip_after
        ]);

        // Attach placements
        $videoAd->placements()->attach($request->placements);

        return redirect()->route('video-ads.show', $videoAd->id)
            ->with('success', 'تم إنشاء إعلان الفيديو بنجاح');
    }

    public function show(Advertisement $videoAd)
    {
        if ($videoAd->user_id !== Auth::id() && !Auth::user()->role === 'admin') {
            abort(403);
        }

        if ($videoAd->type !== 'video') {
            abort(404);
        }

        $videoAd->load(['campaign', 'placements', 'clicks', 'impressions']);

        // Get performance metrics
        $metrics = [
            'impressions' => $videoAd->impressions_count,
            'clicks' => $videoAd->clicks_count,
            'video_plays' => $this->getVideoPlays($videoAd),
            'video_completions' => $this->getVideoCompletions($videoAd),
            'average_watch_time' => $this->getAverageWatchTime($videoAd),
            'completion_rate' => $this->getCompletionRate($videoAd),
            'ctr' => $this->calculateCTR($videoAd),
            'cpc' => $this->calculateCPC($videoAd),
            'cpv' => $this->calculateCPV($videoAd),
            'total_spent' => $videoAd->total_spent
        ];

        // Get daily performance
        $dailyPerformance = $this->getDailyPerformance($videoAd, 30);

        // Get engagement metrics
        $engagement = $this->getVideoEngagement($videoAd);

        return view('ads.show-video-ad', compact('videoAd', 'metrics', 'dailyPerformance', 'engagement'));
    }

    public function edit(Advertisement $videoAd)
    {
        if ($videoAd->user_id !== Auth::id()) {
            abort(403);
        }

        if ($videoAd->type !== 'video') {
            abort(404);
        }

        $campaigns = Auth::user()->adCampaigns()->where('status', 'active')->get();
        $placements = AdPlacement::where('is_active', true)->get();

        return view('ads.edit-video-ad', compact('videoAd', 'campaigns', 'placements'));
    }

    public function update(Request $request, Advertisement $videoAd)
    {
        if ($videoAd->user_id !== Auth::id()) {
            abort(403);
        }

        if ($videoAd->type !== 'video') {
            abort(404);
        }

        $request->validate([
            'campaign_id' => 'required|exists:ad_campaigns,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'video_file' => 'nullable|mimes:mp4,avi,mov,wmv|max:51200',
            'video_url' => 'nullable|url|max:500',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'target_url' => 'required|url|max:500',
            'placements' => 'required|array',
            'placements.*' => 'exists:ad_placements,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'daily_budget' => 'required|numeric|min:1',
            'autoplay' => 'boolean',
            'muted' => 'boolean',
            'controls' => 'boolean',
            'loop' => 'boolean'
        ]);

        // Handle video file upload
        $videoPath = $videoAd->video_url;
        if ($request->hasFile('video_file')) {
            // Delete old video
            if ($videoAd->video_url && !str_contains($videoAd->video_url, 'http')) {
                Storage::disk('public')->delete($videoAd->video_url);
            }
            
            $video = $request->file('video_file');
            $videoName = time() . '_' . $video->getClientOriginalName();
            $videoPath = $video->storeAs('video-ads', $videoName, 'public');
        }

        // Handle thumbnail upload
        $thumbnailPath = $videoAd->thumbnail_url;
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($videoAd->thumbnail_url) {
                Storage::disk('public')->delete($videoAd->thumbnail_url);
            }
            
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = time() . '_thumb_' . $thumbnail->getClientOriginalName();
            $thumbnailPath = $thumbnail->storeAs('video-thumbnails', $thumbnailName, 'public');
        }

        $videoAd->update([
            'campaign_id' => $request->campaign_id,
            'title' => $request->title,
            'description' => $request->description,
            'video_url' => $videoPath ?: $request->video_url,
            'thumbnail_url' => $thumbnailPath,
            'target_url' => $request->target_url,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'daily_budget' => $request->daily_budget,
            'autoplay' => $request->autoplay ?? false,
            'muted' => $request->muted ?? true,
            'controls' => $request->controls ?? true,
            'loop' => $request->loop ?? false
        ]);

        // Update placements
        $videoAd->placements()->sync($request->placements);

        return redirect()->route('video-ads.show', $videoAd->id)
            ->with('success', 'تم تحديث إعلان الفيديو بنجاح');
    }

    public function destroy(Advertisement $videoAd)
    {
        if ($videoAd->user_id !== Auth::id()) {
            abort(403);
        }

        if ($videoAd->type !== 'video') {
            abort(404);
        }

        // Delete video file
        if ($videoAd->video_url && !str_contains($videoAd->video_url, 'http')) {
            Storage::disk('public')->delete($videoAd->video_url);
        }

        // Delete thumbnail
        if ($videoAd->thumbnail_url) {
            Storage::disk('public')->delete($videoAd->thumbnail_url);
        }

        $videoAd->delete();

        return redirect()->route('video-ads.index')
            ->with('success', 'تم حذف إعلان الفيديو بنجاح');
    }

    public function preview(Advertisement $videoAd)
    {
        if ($videoAd->user_id !== Auth::id()) {
            abort(403);
        }

        if ($videoAd->type !== 'video') {
            abort(404);
        }

        return view('ads.preview-video-ad', compact('videoAd'));
    }

    public function getVideoCode(Advertisement $videoAd)
    {
        if ($videoAd->user_id !== Auth::id()) {
            abort(403);
        }

        if ($videoAd->type !== 'video') {
            abort(404);
        }

        $embedCode = $this->generateVideoEmbedCode($videoAd);

        return view('ads.video-code', compact('videoAd', 'embedCode'));
    }

    public function trackVideoPlay(Request $request, Advertisement $videoAd)
    {
        if ($videoAd->type !== 'video') {
            return response()->json(['success' => false]);
        }

        // Track video play
        DB::table('video_plays')->insert([
            'advertisement_id' => $videoAd->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'played_at' => now(),
            'user_id' => Auth::id() ?? null
        ]);

        return response()->json(['success' => true]);
    }

    public function trackVideoCompletion(Request $request, Advertisement $videoAd)
    {
        if ($videoAd->type !== 'video') {
            return response()->json(['success' => false]);
        }

        // Track video completion
        DB::table('video_completions')->insert([
            'advertisement_id' => $videoAd->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'completed_at' => now(),
            'watch_time' => $request->watch_time ?? $videoAd->video_duration,
            'user_id' => Auth::id() ?? null
        ]);

        return response()->json(['success' => true]);
    }

    public function trackVideoEngagement(Request $request, Advertisement $videoAd)
    {
        if ($videoAd->type !== 'video') {
            return response()->json(['success' => false]);
        }

        // Track video engagement events
        DB::table('video_engagements')->insert([
            'advertisement_id' => $videoAd->id,
            'event_type' => $request->event_type,
            'event_data' => json_encode($request->event_data ?? []),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
            'user_id' => Auth::id() ?? null
        ]);

        return response()->json(['success' => true]);
    }

    private function getVideoPlays($videoAd)
    {
        return DB::table('video_plays')
            ->where('advertisement_id', $videoAd->id)
            ->count();
    }

    private function getVideoCompletions($videoAd)
    {
        return DB::table('video_completions')
            ->where('advertisement_id', $videoAd->id)
            ->count();
    }

    private function getAverageWatchTime($videoAd)
    {
        return DB::table('video_completions')
            ->where('advertisement_id', $videoAd->id)
            ->avg('watch_time') ?? 0;
    }

    private function getCompletionRate($videoAd)
    {
        $plays = $this->getVideoPlays($videoAd);
        $completions = $this->getVideoCompletions($videoAd);
        
        return $plays > 0 ? ($completions / $plays) * 100 : 0;
    }

    private function calculateCTR($videoAd)
    {
        $impressions = $videoAd->impressions_count;
        $clicks = $videoAd->clicks_count;
        
        return $impressions > 0 ? ($clicks / $impressions) * 100 : 0;
    }

    private function calculateCPC($videoAd)
    {
        $clicks = $videoAd->clicks_count;
        $spent = $videoAd->total_spent;
        
        return $clicks > 0 ? $spent / $clicks : 0;
    }

    private function calculateCPV($videoAd)
    {
        $views = $this->getVideoPlays($videoAd);
        $spent = $videoAd->total_spent;
        
        return $views > 0 ? $spent / $views : 0;
    }

    private function getDailyPerformance($videoAd, $days)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return $videoAd->impressions()
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as impressions')
            ->where('viewed_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getVideoEngagement($videoAd)
    {
        return [
            'quartile_25' => $this->getQuartileCompletion($videoAd, 25),
            'quartile_50' => $this->getQuartileCompletion($videoAd, 50),
            'quartile_75' => $this->getQuartileCompletion($videoAd, 75),
            'skips' => $this->getVideoSkips($videoAd),
            'pauses' => $this->getVideoPauses($videoAd),
            'replays' => $this->getVideoReplays($videoAd)
        ];
    }

    private function getQuartileCompletion($videoAd, $quartile)
    {
        return DB::table('video_completions')
            ->where('advertisement_id', $videoAd->id)
            ->whereRaw('(watch_time / video_duration) * 100 >= ?', [$quartile])
            ->count();
    }

    private function getVideoSkips($videoAd)
    {
        return DB::table('video_engagements')
            ->where('advertisement_id', $videoAd->id)
            ->where('event_type', 'skip')
            ->count();
    }

    private function getVideoPauses($videoAd)
    {
        return DB::table('video_engagements')
            ->where('advertisement_id', $videoAd->id)
            ->where('event_type', 'pause')
            ->count();
    }

    private function getVideoReplays($videoAd)
    {
        return DB::table('video_engagements')
            ->where('advertisement_id', $videoAd->id)
            ->where('event_type', 'replay')
            ->count();
    }

    private function generateVideoEmbedCode($videoAd)
    {
        $baseUrl = url('/');
        $videoUrl = $videoAd->video_url;
        $thumbnailUrl = Storage::url($videoAd->thumbnail_url);
        $clickUrl = route('video-ads.click', $videoAd->id);
        $trackUrl = route('video-ads.impression', $videoAd->id);

        return "
<div id='video-ad-{$videoAd->id}' style='width: 100%; max-width: 640px; height: auto;'>
    <video id='video-player-{$videoAd->id}' 
           poster='{$thumbnailUrl}' 
           " . ($videoAd->autoplay ? 'autoplay' : '') . " 
           " . ($videoAd->muted ? 'muted' : '') . " 
           " . ($videoAd->controls ? 'controls' : '') . " 
           " . ($videoAd->loop ? 'loop' : '') . "
           style='width: 100%; height: auto;'>
        <source src='{$videoUrl}' type='video/mp4'>
        Your browser does not support the video tag.
    </video>
    <div id='video-overlay-{$videoAd->id}' style='position: absolute; top: 0; left: 0; width: 100%; height: 100%; cursor: pointer;'>
        <a href='{$clickUrl}' target='_blank' style='display: block; width: 100%; height: 100%; text-decoration: none;'></a>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('video-player-{$videoAd->id}');
    const overlay = document.getElementById('video-overlay-{$videoAd->id}');
    
    // Track video play
    video.addEventListener('play', function() {
        trackVideoPlay({$videoAd->id});
    });
    
    // Track video completion
    video.addEventListener('ended', function() {
        trackVideoCompletion({$videoAd->id}, video.duration);
    });
    
    // Track quartile completions
    video.addEventListener('timeupdate', function() {
        const percent = (video.currentTime / video.duration) * 100;
        if (percent >= 25 && !video.dataset.quartile25) {
            video.dataset.quartile25 = 'true';
            trackVideoEngagement({$videoAd->id}, 'quartile_25', {percent: 25});
        }
        if (percent >= 50 && !video.dataset.quartile50) {
            video.dataset.quartile50 = 'true';
            trackVideoEngagement({$videoAd->id}, 'quartile_50', {percent: 50});
        }
        if (percent >= 75 && !video.dataset.quartile75) {
            video.dataset.quartile75 = 'true';
            trackVideoEngagement({$videoAd->id}, 'quartile_75', {percent: 75});
        }
    });
    
    // Track clicks
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            // Track click server-side
        }
    });
});

function trackVideoPlay(adId) {
    fetch('" . route('video-ads.track-play', ['video_ad' => ':adId']) . "'.replace(':adId', adId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
        }
    });
}

function trackVideoCompletion(adId, watchTime) {
    fetch('" . route('video-ads.track-completion', ['video_ad' => ':adId']) . "'.replace(':adId', adId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
        },
        body: JSON.stringify({watch_time: watchTime})
    });
}

function trackVideoEngagement(adId, eventType, eventData) {
    fetch('" . route('video-ads.track-engagement', ['video_ad' => ':adId']) . "'.replace(':adId', adId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
        },
        body: JSON.stringify({event_type: eventType, event_data: eventData})
    });
}
</script>";
    }
}

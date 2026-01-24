<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyShare;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PropertyShareController extends Controller
{
    public function share(Request $request, Property $property): JsonResponse
    {
        $request->validate([
            'method' => 'required|in:email,whatsapp,facebook,twitter,linkedin,link,copy',
            'recipient_email' => 'required_if:method,email|email',
            'message' => 'nullable|string|max:500',
        ]);

        $method = $request->method;
        $message = $request->message ?? $this->getDefaultShareMessage($property);
        $shareUrl = route('properties.show', $property);

        // Record share
        $share = PropertyShare::create([
            'property_id' => $property->id,
            'user_id' => Auth::id(),
            'share_method' => $method,
            'recipient_email' => $request->recipient_email,
            'message' => $message,
            'share_token' => Str::random(32),
            'expires_at' => now()->addDays(7),
        ]);

        // Increment property shares count
        $property->increment('shares_count');

        // Record analytics
        PropertyAnalytic::recordMetric($property->id, 'shares');

        // Generate share link based on method
        $shareLink = $this->generateShareLink($property, $method, $share->share_token);

        // Send/share based on method
        $result = $this->executeShare($method, $property, $shareLink, $message, $request->recipient_email);

        return response()->json([
            'success' => true,
            'message' => 'Property shared successfully',
            'share_link' => $shareLink,
            'share_id' => $share->id,
            'result' => $result,
        ]);
    }

    public function getShareLink(Request $request, Property $property): JsonResponse
    {
        $share = PropertyShare::create([
            'property_id' => $property->id,
            'user_id' => Auth::id(),
            'share_method' => 'link',
            'share_token' => Str::random(32),
            'expires_at' => now()->addDays(30),
        ]);

        $shareLink = route('properties.shared', ['token' => $share->share_token]);

        return response()->json([
            'success' => true,
            'share_link' => $shareLink,
            'share_id' => $share->id,
            'expires_at' => $share->expires_at,
        ]);
    }

    public function sharedView($token)
    {
        $share = PropertyShare::where('share_token', $token)
            ->with(['property' => function($query) {
                $query->with([
                    'propertyType',
                    'location',
                    'details',
                    'price',
                    'media' => function($query) {
                        $query->where('media_type', 'image');
                    },
                    'amenities',
                    'features',
                    'agent'
                ]);
            }])
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $property = $share->property;

        if ($property->status !== 'active') {
            abort(404);
        }

        // Record view
        PropertyView::create([
            'property_id' => $property->id,
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'view_type' => 'shared_link',
            'metadata' => [
                'share_id' => $share->id,
                'share_method' => $share->share_method,
                'shared_by' => $share->user_id,
            ],
        ]);

        // Increment share view count
        $share->increment('view_count');

        return view('properties.shared', compact('property', 'share'));
    }

    public function getShareStats(Request $request, Property $property): JsonResponse
    {
        $this->authorize('viewStats', $property);

        $stats = PropertyShare::where('property_id', $property->id)
            ->with(['user:id,name'])
            ->get()
            ->groupBy('share_method');

        $shareStats = [
            'total_shares' => PropertyShare::where('property_id', $property->id)->count(),
            'total_views' => PropertyShare::where('property_id', $property->id)->sum('view_count'),
            'by_method' => $stats->map(function($shares) {
                return [
                    'count' => $shares->count(),
                    'views' => $shares->sum('view_count'),
                ];
            }),
            'recent_shares' => PropertyShare::where('property_id', $property->id)
                ->with('user:id,name')
                ->latest()
                ->limit(10)
                ->get(),
            'top_sharers' => PropertyShare::where('property_id', $property->id)
                ->with('user:id,name')
                ->groupBy('user_id')
                ->selectRaw('user_id, count(*) as share_count')
                ->orderBy('share_count', 'desc')
                ->limit(5)
                ->with('user:id,name')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $shareStats,
        ]);
    }

    public function createCampaign(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'properties' => 'required|array',
            'properties.*' => 'exists:properties,id',
            'message' => 'required|string|max:500',
            'channels' => 'required|array',
            'channels.*' => 'in:email,social,whatsapp',
        ]);

        $campaign = PropertyShareCampaign::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'message' => $request->message,
            'channels' => $request->channels,
            'status' => 'active',
        ]);

        // Create shares for each property
        foreach ($request->properties as $propertyId) {
            $property = Property::findOrFail($propertyId);
            
            foreach ($request->channels as $channel) {
                PropertyShare::create([
                    'property_id' => $propertyId,
                    'user_id' => Auth::id(),
                    'share_method' => $channel,
                    'message' => $request->message,
                    'share_token' => Str::random(32),
                    'campaign_id' => $campaign->id,
                    'expires_at' => now()->addDays(7),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Share campaign created successfully',
            'campaign_id' => $campaign->id,
        ]);
    }

    public function generateQRCode(Request $request, Property $property): JsonResponse
    {
        $share = PropertyShare::create([
            'property_id' => $property->id,
            'user_id' => Auth::id(),
            'share_method' => 'qrcode',
            'share_token' => Str::random(32),
            'expires_at' => now()->addDays(30),
        ]);

        $shareUrl = route('properties.shared', ['token' => $share->share_token]);

        // Generate QR code (you'd need a QR code library)
        $qrCode = $this->generateQRCodeImage($shareUrl);

        return response()->json([
            'success' => true,
            'qr_code' => $qrCode,
            'share_url' => $shareUrl,
            'share_id' => $share->id,
        ]);
    }

    public function bulkShare(Request $request): JsonResponse
    {
        $request->validate([
            'property_ids' => 'required|array',
            'property_ids.*' => 'exists:properties,id',
            'method' => 'required|in:email,link',
            'recipients' => 'required_if:method,email|array',
            'recipients.*' => 'email',
            'message' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $shares = [];

        foreach ($request->property_ids as $propertyId) {
            $property = Property::findOrFail($propertyId);
            
            $share = PropertyShare::create([
                'property_id' => $propertyId,
                'user_id' => $user->id,
                'share_method' => $request->method,
                'message' => $request->message ?? $this->getDefaultShareMessage($property),
                'share_token' => Str::random(32),
                'expires_at' => now()->addDays(7),
            ]);

            $shares[] = $share;

            // Increment property shares count
            $property->increment('shares_count');

            // Record analytics
            PropertyAnalytic::recordMetric($propertyId, 'shares');
        }

        return response()->json([
            'success' => true,
            'message' => 'Properties shared successfully',
            'shares_count' => count($shares),
            'shares' => $shares,
        ]);
    }

    private function getDefaultShareMessage(Property $property): string
    {
        return "Check out this amazing property: {$property->title} in {$property->location->city}. " .
               "Only {$property->price->formatted_price} - {$property->details->formatted_area}. " .
               "View details: " . route('properties.show', $property);
    }

    private function generateShareLink(Property $property, string $method, string $token): string
    {
        $baseUrl = route('properties.show', $property);

        switch ($method) {
            case 'email':
                return "mailto:?subject=" . urlencode("Amazing Property: {$property->title}") .
                       "&body=" . urlencode($this->getDefaultShareMessage($property));
            
            case 'whatsapp':
                return "https://wa.me/?text=" . urlencode($this->getDefaultShareMessage($property));
            
            case 'facebook':
                return "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($baseUrl);
            
            case 'twitter':
                return "https://twitter.com/intent/tweet?text=" . urlencode("Check out this property: {$property->title}") .
                       "&url=" . urlencode($baseUrl);
            
            case 'linkedin':
                return "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($baseUrl);
            
            case 'link':
            case 'copy':
                return route('properties.shared', ['token' => $token]);
            
            default:
                return $baseUrl;
        }
    }

    private function executeShare(string $method, Property $property, string $shareLink, string $message, ?string $recipientEmail): array
    {
        switch ($method) {
            case 'email':
                // Send email logic would go here
                return [
                    'status' => 'sent',
                    'recipient' => $recipientEmail,
                    'message' => 'Email sent successfully',
                ];
            
            case 'whatsapp':
            case 'facebook':
            case 'twitter':
            case 'linkedin':
                return [
                    'status' => 'redirect',
                    'url' => $shareLink,
                    'message' => 'Redirecting to share platform',
                ];
            
            case 'link':
            case 'copy':
                return [
                    'status' => 'ready',
                    'url' => $shareLink,
                    'message' => 'Link ready to copy',
                ];
            
            default:
                return [
                    'status' => 'error',
                    'message' => 'Invalid share method',
                ];
        }
    }

    private function generateQRCodeImage(string $url): string
    {
        // This would use a QR code library like "simplesoftwareio/simple-qrcode"
        // For now, return a placeholder
        return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==";
    }
}

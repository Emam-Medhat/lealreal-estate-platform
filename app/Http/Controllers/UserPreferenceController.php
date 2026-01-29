<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $preferences = $user->preference ?? new UserPreference(['user_id' => $user->id]);
        
        return view('user.preferences', compact('preferences'));
    }

    public function update(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'email_notifications' => 'sometimes|boolean',
            'sms_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
            'marketing_emails' => 'sometimes|boolean',
            'newsletter_subscription' => 'sometimes|boolean',
            'property_alerts' => 'sometimes|boolean',
            'price_drop_alerts' => 'sometimes|boolean',
            'new_listing_alerts' => 'sometimes|boolean',
            'preferred_contact' => 'sometimes|string|in:email,phone,sms,whatsapp',
            'communication_frequency' => 'sometimes|string|in:immediately,daily,weekly,monthly',
            'language' => 'sometimes|string|in:en,ar,fr',
            'timezone' => 'sometimes|string',
            'currency' => 'sometimes|string|in:USD,EUR,GBP,SAR',
            'profile_visibility' => 'sometimes|string|in:public,private,friends',
            'show_contact_info' => 'sometimes|boolean',
            'allow_friend_requests' => 'sometimes|boolean',
            'two_factor_auth' => 'sometimes|boolean',
            'session_timeout' => 'sometimes|integer|min:5|max:1440',
            'auto_save_searches' => 'sometimes|boolean',
            'search_results_per_page' => 'sometimes|integer|min:10|max:100',
            'map_default_view' => 'sometimes|string|in:map,satellite,hybrid',
            'preferred_property_types' => 'sometimes|array',
            'price_range_min' => 'sometimes|numeric|min:0',
            'price_range_max' => 'sometimes|numeric|min:0',
            'preferred_locations' => 'sometimes|array',
        ]);

        $user = Auth::user();
        
        $preferenceData = [
            'email_notifications' => $request->boolean('email_notifications', true),
            'sms_notifications' => $request->boolean('sms_notifications', false),
            'push_notifications' => $request->boolean('push_notifications', true),
            'marketing_emails' => $request->boolean('marketing_emails', false),
            'newsletter_subscription' => $request->boolean('newsletter_subscription', false),
            'property_alerts' => $request->boolean('property_alerts', true),
            'price_drop_alerts' => $request->boolean('price_drop_alerts', true),
            'new_listing_alerts' => $request->boolean('new_listing_alerts', false),
            'preferred_contact' => $request->get('preferred_contact', 'email'),
            'communication_frequency' => $request->get('communication_frequency', 'immediately'),
            'language' => $request->get('language', 'en'),
            'timezone' => $request->get('timezone', 'UTC'),
            'currency' => $request->get('currency', 'USD'),
            'profile_visibility' => $request->get('profile_visibility', 'public'),
            'show_contact_info' => $request->boolean('show_contact_info', true),
            'allow_friend_requests' => $request->boolean('allow_friend_requests', true),
            'two_factor_auth' => $request->boolean('two_factor_auth', false),
            'session_timeout' => $request->get('session_timeout', 30),
            'auto_save_searches' => $request->boolean('auto_save_searches', true),
            'search_results_per_page' => $request->get('search_results_per_page', 20),
            'map_default_view' => $request->get('map_default_view', 'map'),
            'preferred_property_types' => $request->get('preferred_property_types', []),
            'price_range_min' => $request->get('price_range_min'),
            'price_range_max' => $request->get('price_range_max'),
            'preferred_locations' => $request->get('preferred_locations', []),
        ];

        $user->preference()->updateOrCreate(['user_id' => $user->id], $preferenceData);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'updated_preferences',
            'details' => 'Updated user preferences and settings',
            'ip_address' => $request->ip(),
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث التفضيلات بنجاح'
            ]);
        }

        return redirect()->route('user.preferences.index')
            ->with('success', 'تم تحديث التفضيلات بنجاح');
    }

    public function updateNotificationSettings(Request $request): JsonResponse
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
            'newsletter_subscription' => 'boolean',
            'property_alerts' => 'boolean',
            'price_drop_alerts' => 'boolean',
            'new_listing_alerts' => 'boolean',
        ]);

        $user = Auth::user();
        
        $user->preference()->updateOrCreate(['user_id' => $user->id], [
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'push_notifications' => $request->boolean('push_notifications'),
            'marketing_emails' => $request->boolean('marketing_emails'),
            'newsletter_subscription' => $request->boolean('newsletter_subscription'),
            'property_alerts' => $request->boolean('property_alerts'),
            'price_drop_alerts' => $request->boolean('price_drop_alerts'),
            'new_listing_alerts' => $request->boolean('new_listing_alerts'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated successfully'
        ]);
    }

    public function updatePrivacySettings(Request $request): JsonResponse
    {
        $request->validate([
            'profile_visibility' => 'required|in:public,friends,private',
            'show_contact_info' => 'boolean',
            'allow_friend_requests' => 'boolean',
            'two_factor_auth' => 'boolean',
        ]);

        $user = Auth::user();
        
        $user->preference()->updateOrCreate(['user_id' => $user->id], [
            'profile_visibility' => $request->profile_visibility,
            'show_contact_info' => $request->boolean('show_contact_info'),
            'allow_friend_requests' => $request->boolean('allow_friend_requests'),
            'two_factor_auth' => $request->boolean('two_factor_auth'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Privacy settings updated successfully'
        ]);
    }

    public function updateDisplaySettings(Request $request): JsonResponse
    {
        $request->validate([
            'language' => 'required|string|max:10',
            'timezone' => 'required|string|max:50',
            'currency' => 'required|string|max:3',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:10',
            'theme' => 'required|in:light,dark,auto',
            'search_results_per_page' => 'required|integer|min:10|max:100',
        ]);

        $user = Auth::user();
        
        $user->preference()->updateOrCreate(['user_id' => $user->id], [
            'language' => $request->language,
            'timezone' => $request->timezone,
            'currency' => $request->currency,
            'date_format' => $request->date_format,
            'time_format' => $request->time_format,
            'theme' => $request->theme,
            'search_results_per_page' => $request->search_results_per_page,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Display settings updated successfully'
        ]);
    }

    public function resetToDefaults(): JsonResponse
    {
        $user = Auth::user();
        
        $defaultPreferences = [
            'language' => 'en',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'date_format' => 'Y-m-d',
            'time_format' => '24h',
            'theme' => 'light',
            'email_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => true,
            'marketing_emails' => false,
            'newsletter_subscription' => true,
            'property_alerts' => true,
            'price_drop_alerts' => true,
            'new_listing_alerts' => true,
            'profile_visibility' => 'public',
            'show_contact_info' => false,
            'allow_friend_requests' => true,
            'two_factor_auth' => false,
            'session_timeout' => 120,
            'auto_save_searches' => true,
            'search_results_per_page' => 20,
            'map_default_view' => 'map',
        ];

        $user->preference()->updateOrCreate(['user_id' => $user->id], $defaultPreferences);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'reset_preferences',
            'details' => 'Reset preferences to default values',
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Preferences reset to defaults successfully'
        ]);
    }

    public function exportPreferences(): JsonResponse
    {
        $user = Auth::user();
        $preferences = $user->preference;

        return response()->json([
            'success' => true,
            'preferences' => $preferences ? $preferences->toArray() : []
        ]);
    }

    public function importPreferences(Request $request): JsonResponse
    {
        $request->validate([
            'preferences' => 'required|array',
        ]);

        $user = Auth::user();
        $preferences = $request->preferences;

        // Filter only valid preference keys
        $validKeys = [
            'language', 'timezone', 'currency', 'date_format', 'time_format', 'theme',
            'email_notifications', 'sms_notifications', 'push_notifications',
            'marketing_emails', 'newsletter_subscription', 'property_alerts',
            'price_drop_alerts', 'new_listing_alerts', 'profile_visibility',
            'show_contact_info', 'allow_friend_requests', 'two_factor_auth',
            'session_timeout', 'auto_save_searches', 'search_results_per_page',
            'map_default_view', 'preferred_property_types', 'price_range_min',
            'price_range_max', 'preferred_locations'
        ];

        $filteredPreferences = array_intersect_key($preferences, array_flip($validKeys));

        $user->preference()->updateOrCreate(['user_id' => $user->id], $filteredPreferences);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'imported_preferences',
            'details' => 'Imported user preferences from file',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Preferences imported successfully'
        ]);
    }
}

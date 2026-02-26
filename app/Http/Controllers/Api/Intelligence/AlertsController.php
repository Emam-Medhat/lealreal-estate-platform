<?php

namespace App\Http\Controllers\Api\Intelligence;

use App\Http\Controllers\Controller;
use App\Models\IntelligenceAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertsController extends Controller
{
    public function index(Request $request)
    {
        $query = IntelligenceAlert::where('user_id', Auth::id())
            ->latest();
        $unread = (clone $query)->whereNull('read_at')->limit(50)->get();
        $recent = (clone $query)->limit(100)->get();

        return response()->json([
            'unread' => $unread,
            'recent' => $recent,
        ]);
    }

    public function markRead(Request $request, int $id)
    {
        $alert = IntelligenceAlert::where('user_id', Auth::id())->findOrFail($id);
        $alert->read_at = now();
        $alert->save();
        return response()->json(['ok' => true]);
    }

    public function savePreferences(Request $request)
    {
        $data = $request->validate([
            'new_properties' => 'boolean',
            'price_changes' => 'boolean',
            'fraud_alerts' => 'boolean',
            'preferred_cities' => 'array',
            'preferred_cities.*' => 'string',
        ]);
        $user = Auth::user();
        $prefs = (array) ($user->notification_preferences ?? []);
        $prefs['new_properties'] = (bool) ($data['new_properties'] ?? ($prefs['new_properties'] ?? true));
        $prefs['price_changes'] = (bool) ($data['price_changes'] ?? ($prefs['price_changes'] ?? true));
        $prefs['fraud_alerts'] = (bool) ($data['fraud_alerts'] ?? ($prefs['fraud_alerts'] ?? true));
        if (isset($data['preferred_cities'])) {
            $prefs['preferred_cities'] = array_values($data['preferred_cities']);
        }
        $user->notification_preferences = $prefs;
        $user->save();
        return response()->json(['ok' => true, 'preferences' => $prefs]);
    }
}

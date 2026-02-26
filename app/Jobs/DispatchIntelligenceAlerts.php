<?php

namespace App\Jobs;

use App\Models\IntelligenceAlert;
use App\Models\Property;
use App\Models\User;
use App\Events\IntelligenceAlertBroadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchIntelligenceAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $propertyIds;
    public string $type;

    public function __construct(array $propertyIds, string $type = 'update')
    {
        $this->propertyIds = $propertyIds;
        $this->type = $type;
        $this->onQueue('intelligence');
    }

    public function handle(): void
    {
        $props = Property::whereIn('id', $this->propertyIds)->get();
        foreach ($props as $p) {
            $users = User::query()->get(['id', 'notification_preferences']);
            foreach ($users as $u) {
                $prefs = (array) ($u->notification_preferences ?? []);
                if ($this->type === 'fraud' && !($prefs['fraud_alerts'] ?? true)) {
                    continue;
                }
                if ($this->type === 'new_property' && isset($prefs['preferred_cities']) && is_array($prefs['preferred_cities'])) {
                    if ($p->city && !in_array($p->city, $prefs['preferred_cities'])) {
                        continue;
                    }
                }
                $alert = IntelligenceAlert::create([
                    'user_id' => $u->id,
                    'type' => $this->type,
                    'property_id' => $p->id,
                    'data' => [
                        'title' => $p->title,
                        'city' => $p->city,
                        'price' => (float) $p->price,
                        'overpriced' => (bool) $p->overpriced_flag,
                        'fraud_score' => (int) ($p->fraud_score ?? 0),
                    ],
                ]);
                if ((bool) config('intelligence.realtime.enabled')) {
                    event(new IntelligenceAlertBroadcast($alert));
                }
            }
        }
    }
}

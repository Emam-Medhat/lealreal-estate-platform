<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PropertyLeaderboard;
use App\Models\Gamification\UserLevel;
use App\Models\Gamification\UserBadge;
use App\Models\Gamification\Badge;
use Illuminate\Support\Facades\DB;

class GamificationSeeder extends Seeder
{
    public function run()
    {
        // Create some badges if they don't exist
        $badges = [
            ['name' => 'First Listing', 'description' => 'Create your first property listing', 'icon' => 'fa-home', 'category' => 'listing'],
            ['name' => 'Top Seller', 'description' => 'Sell 10 properties', 'icon' => 'fa-star', 'category' => 'sales'],
            ['name' => 'Community Leader', 'description' => 'Engage with the community', 'icon' => 'fa-users', 'category' => 'social'],
            ['name' => 'Investor', 'description' => 'Make your first investment', 'icon' => 'fa-chart-line', 'category' => 'investment'],
            ['name' => 'Elite Agent', 'description' => 'Reach level 10', 'icon' => 'fa-crown', 'category' => 'general'],
        ];

        foreach ($badges as $badgeData) {
            Badge::firstOrCreate(['name' => $badgeData['name']], $badgeData);
        }

        $users = User::all();
        $allBadges = Badge::all();

        foreach ($users as $user) {
            // Seed User Level
            $level = rand(1, 10);
            $points = $level * 1000 + rand(0, 999);
            
            UserLevel::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'level' => $level,
                    'total_points' => $points,
                    'current_points' => $points,
                    'leveled_up_at' => now()->subDays(rand(1, 30))
                ]
            );

            // Seed Badges
            $userBadgesCount = rand(0, 3);
            if ($userBadgesCount > 0 && $allBadges->count() > 0) {
                $randomBadges = $allBadges->random(min($userBadgesCount, $allBadges->count()));
                foreach ($randomBadges as $badge) {
                    UserBadge::firstOrCreate(
                        ['user_id' => $user->id, 'badge_id' => $badge->id],
                        ['awarded_at' => now()->subDays(rand(1, 60))]
                    );
                }
            }

            // Seed Leaderboard Entry
            PropertyLeaderboard::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => 'global',
                    'period' => 'all_time',
                    'category' => 'general'
                ],
                [
                    'score' => $points,
                    'rank' => 0, // Will update later
                    'previous_rank' => rand(1, 20),
                    'change' => rand(-5, 5),
                    'metadata' => json_encode([
                        'level' => $level,
                        'badges_count' => $userBadgesCount
                    ]),
                    'calculated_at' => now()
                ]
            );
        }

        // Update Ranks
        $leaderboardEntries = PropertyLeaderboard::where('type', 'global')
            ->orderBy('score', 'desc')
            ->get();

        foreach ($leaderboardEntries as $index => $entry) {
            $entry->update(['rank' => $index + 1]);
        }
    }
}

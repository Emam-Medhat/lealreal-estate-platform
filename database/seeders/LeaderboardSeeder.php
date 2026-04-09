<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyLeaderboard;
use App\Models\User;

class LeaderboardSeeder extends Seeder
{
    public function run()
    {
        // Get some users to create leaderboard entries
        $users = User::limit(10)->get();
        
        $leaderboardEntries = [];
        
        foreach ($users as $index => $user) {
            $leaderboardEntries[] = [
                'user_id' => $user->id,
                'type' => 'global',
                'period' => 'all_time',
                'category' => 'overall',
                'score' => rand(100, 5000) - ($index * 100), // Decreasing scores
                'rank' => $index + 1,
                'previous_rank' => $index + 2,
                'change' => $index > 0 ? 1 : 0, // Moved up in ranking
                'metadata' => json_encode([
                    'achievements_count' => rand(1, 20),
                    'properties_listed' => rand(0, 50),
                    'total_sales' => rand(0, 1000000),
                    'customer_rating' => rand(3.5, 5.0),
                ]),
                'calculated_at' => now()->subMinutes(rand(1, 60)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        // Insert the leaderboard entries
        PropertyLeaderboard::insert($leaderboardEntries);
    }
}

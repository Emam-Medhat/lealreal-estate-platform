<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Agent;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create an agent record for the first user if it doesn't exist
        $user = User::first();
        if ($user && !Agent::where('user_id', $user->id)->exists()) {
            Agent::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '000-000-0000',
                'status' => 'active',
                'commission_rate' => 3.5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the test agent record
        $user = User::first();
        if ($user) {
            Agent::where('user_id', $user->id)->delete();
        }
    }
};

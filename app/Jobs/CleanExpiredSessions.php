<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CleanExpiredSessions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Assuming database session driver or custom table
        DB::table('sessions')->where('last_activity', '<', now()->subDays(30)->timestamp)->delete();

        // Or if using user_sessions table
        DB::table('user_sessions')->where('expires_at', '<', now())->delete();
    }
}

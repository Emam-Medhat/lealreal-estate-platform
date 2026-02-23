<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create-super', function () {
    $existingCount = User::query()->where('user_type', 'super_admin')->count();

    if ($existingCount > 0) {
        $continue = $this->confirm('A super admin already exists. Create another one?', false);
        if (!$continue) {
            return 0;
        }
    }

    $firstName = (string) $this->ask('First name');
    $lastName = (string) $this->ask('Last name');
    $username = (string) $this->ask('Username');
    $email = (string) $this->ask('Email');
    $password = (string) $this->secret('Password');

    if ($firstName === '' || $lastName === '' || $username === '' || $email === '' || $password === '') {
        $this->error('All fields are required.');
        return 1;
    }

    if (User::query()->where('email', $email)->exists()) {
        $this->error('A user with this email already exists.');
        return 1;
    }

    if (User::query()->where('username', $username)->exists()) {
        $this->error('A user with this username already exists.');
        return 1;
    }

    User::query()->create([
        'uuid' => (string) Str::uuid(),
        'username' => $username,
        'email' => $email,
        'password' => Hash::make($password),
        'first_name' => $firstName,
        'last_name' => $lastName,
        'full_name' => trim($firstName . ' ' . $lastName),
        'user_type' => 'super_admin',
        'account_status' => 'active',
    ]);

    $this->info('Super admin created successfully.');
    return 0;
})->purpose('Create a super admin user safely from the CLI');

// ─── AI: Recalculate recommendation scores (scheduled hourly) ────────────
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\AI\RecalculatePropertyScoresJob;
use App\Jobs\AI\AiBudgetAlertJob;

Artisan::command('ai:recalc-scores', function () {
    $this->info('Dispatching RecalculatePropertyScoresJob...');
    RecalculatePropertyScoresJob::dispatch()->onQueue('default');
    $this->info('Job dispatched.');
})->purpose('Pre-warm recommendation score caches for active users');

// ─── AI: Budget alert check ───────────────────────────────────────────────
Artisan::command('ai:budget-alert', function () {
    $this->info('Running AI budget alert check...');
    (new AiBudgetAlertJob())->handle();
    $this->info('Budget check complete.');
})->purpose('Check AI monthly spend against budget and send alerts');

// ─── AI: Month-to-date cost report ───────────────────────────────────────
Artisan::command('ai:cost-report {--month= : Month number (default: current)}', function () {
    $month = (int) ($this->option('month') ?: now()->month);
    $year = now()->year;

    $rows = \Illuminate\Support\Facades\DB::table('ai_usage_logs')
        ->selectRaw('feature, model, COUNT(*) as calls, SUM(input_tokens) as input_tokens, SUM(output_tokens) as output_tokens, SUM(cost_usd) as cost_usd')
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->groupBy('feature', 'model')
        ->orderByDesc('cost_usd')
        ->get();

    $budget = config('ai.cost_tracking.monthly_budget_usd', 500);
    $total = $rows->sum('cost_usd');

    $this->newLine();
    $this->info("── AI Cost Report: {$year}-{$month} ──────────────────────");
    $this->table(
        ['Feature', 'Model', 'Calls', 'Input Tokens', 'Output Tokens', 'Cost (USD)'],
        $rows->map(fn($r) => [
            $r->feature,
            $r->model ?? '—',
            number_format($r->calls),
            number_format($r->input_tokens),
            number_format($r->output_tokens),
            '$' . number_format($r->cost_usd, 4),
        ])->toArray()
    );
    $this->newLine();
    $this->line("  Total spent : <fg=yellow>\${$total}</>");
    $this->line("  Monthly budget : <fg=cyan>\${$budget}</>");
    $percent = $budget > 0 ? round(($total / $budget) * 100, 1) : 0;
    $color = $percent >= 95 ? 'red' : ($percent >= 80 ? 'yellow' : 'green');
    $this->line("  Usage : <fg={$color}>{$percent}%</>");
    $this->newLine();
})->purpose('Print month-to-date AI API cost breakdown per feature');

// ─── Scheduler ───────────────────────────────────────────────────────────
app()->booted(function () {
    $schedule = app(Schedule::class);
    $schedule->command('ai:recalc-scores')->hourly();
    $schedule->command('ai:budget-alert')->hourly();
});

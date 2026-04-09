<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('report_schedules')) {
        Schema::create('report_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('report_type');
            $table->json('parameters')->nullable();
            $table->json('filters')->nullable();
            $table->string('frequency'); // daily, weekly, monthly, quarterly, yearly
            $table->json('schedule_config'); // cron expression, specific times, etc.
            $table->string('format')->default('pdf');
            $table->json('recipients'); // email addresses, user IDs, etc.
            $table->boolean('is_active')->default(true);
            $table->dateTime('next_run_at')->nullable();
            $table->dateTime('last_run_at')->nullable();
            $table->integer('run_count')->default(0);
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->index(['report_type', 'is_active']);
            $table->index('next_run_at');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_schedules');
    }
};

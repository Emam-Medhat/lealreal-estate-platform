<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ad_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('objective', ['awareness', 'traffic', 'conversions', 'engagement']);
            $table->enum('status', ['draft', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->datetime('launched_at')->nullable();
            $table->datetime('paused_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->decimal('total_budget', 12, 2)->nullable();
            $table->decimal('daily_budget', 10, 2)->nullable();
            $table->integer('target_audience_size')->nullable();
            $table->integer('estimated_reach')->nullable();
            $table->integer('actual_reach')->default(0);
            $table->integer('total_impressions')->default(0);
            $table->integer('total_clicks')->default(0);
            $table->integer('total_conversions')->default(0);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->decimal('average_ctr', 8, 2)->default(0);
            $table->decimal('average_cpc', 10, 2)->default(0);
            $table->decimal('average_cpa', 10, 2)->default(0);
            $table->decimal('conversion_rate', 8, 2)->default(0);
            $table->decimal('roi', 8, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['objective', 'status']);
            $table->index(['start_date', 'end_date']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_campaigns');
    }
};

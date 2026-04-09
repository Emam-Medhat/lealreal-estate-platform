<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ad_clicks')) {
        Schema::create('ad_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address');
            $table->text('user_agent');
            $table->text('referrer')->nullable();
            $table->text('page_url');
            $table->datetime('clicked_at');
            $table->datetime('conversion_time')->nullable();
            $table->enum('device_type', ['desktop', 'mobile', 'tablet']);
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('location')->nullable();
            $table->string('session_id')->nullable();
            $table->string('click_id')->unique();
            $table->boolean('is_unique')->default(true);
            $table->boolean('is_bot')->default(false);
            $table->boolean('is_fraud')->default(false);
            $table->decimal('fraud_score', 5, 2)->default(0);
            $table->decimal('click_value', 10, 2)->default(0);
            $table->decimal('conversion_value', 10, 2)->default(0);
            $table->enum('attribution_model', ['first_click', 'last_click', 'linear', 'time_decay', 'position_based'])->default('last_click');
            $table->integer('attribution_window')->default(30); // days
            $table->json('custom_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['advertisement_id', 'clicked_at']);
            $table->index(['user_id', 'clicked_at']);
            $table->index(['ip_address', 'clicked_at']);
            $table->index(['click_id']);
            $table->index(['is_unique']);
            $table->index(['is_bot']);
            $table->index(['is_fraud']);
            $table->index(['device_type']);
            $table->index(['created_at']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ad_clicks');
    }
};

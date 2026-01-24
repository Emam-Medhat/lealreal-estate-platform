<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ad_impressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address');
            $table->text('user_agent');
            $table->text('referrer')->nullable();
            $table->text('page_url');
            $table->datetime('viewed_at');
            $table->enum('device_type', ['desktop', 'mobile', 'tablet']);
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('location')->nullable();
            $table->string('session_id')->nullable();
            $table->string('impression_id')->unique();
            $table->boolean('is_unique')->default(true);
            $table->boolean('is_bot')->default(false);
            $table->boolean('is_fraud')->default(false);
            $table->decimal('fraud_score', 5, 2)->default(0);
            $table->integer('view_duration')->nullable(); // seconds
            $table->boolean('viewport_visible')->default(true);
            $table->string('ad_position')->nullable();
            $table->decimal('page_load_time', 8, 2)->nullable(); // seconds
            $table->json('custom_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['advertisement_id', 'viewed_at']);
            $table->index(['user_id', 'viewed_at']);
            $table->index(['ip_address', 'viewed_at']);
            $table->index(['impression_id']);
            $table->index(['is_unique']);
            $table->index(['is_bot']);
            $table->index(['is_fraud']);
            $table->index(['device_type']);
            $table->index(['viewport_visible']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_impressions');
    }
};

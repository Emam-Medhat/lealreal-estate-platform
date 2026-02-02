<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('marketing_campaigns')) {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['email', 'social_media', 'ppc', 'display', 'content', 'event']);
            $table->enum('status', ['draft', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->decimal('budget', 15, 2);
            $table->decimal('spent', 15, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->json('target_audience')->nullable();
            $table->string('objective')->nullable();
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('ctr', 5, 2)->default(0); // click-through rate
            $table->decimal('cpc', 10, 2)->default(0); // cost per click
            $table->decimal('cpa', 10, 2)->default(0); // cost per acquisition
            $table->json('assets')->nullable(); // images, videos, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('marketing_campaigns');
    }
};

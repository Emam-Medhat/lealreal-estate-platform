<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ad_placement_advertisement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_placement_id')->constrained()->onDelete('cascade');
            $table->foreignId('advertisement_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->datetime('activated_at')->nullable();
            $table->datetime('deactivated_at')->nullable();
            $table->decimal('bid_amount', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->integer('priority')->default(0);
            $table->json('placement_settings')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['ad_placement_id', 'advertisement_id'], 'ad_placement_adv_unique');
            $table->index(['ad_placement_id', 'is_active']);
            $table->index(['advertisement_id', 'is_active']);
            $table->index(['priority']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_placement_advertisement');
    }
};

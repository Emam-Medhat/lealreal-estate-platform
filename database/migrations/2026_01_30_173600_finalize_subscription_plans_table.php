<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('subscription_plans', 'slug')) {
                $table->string('slug')->unique();
            }
            // Check if index exists before adding
            $indexes = DB::select("SHOW INDEX FROM subscription_plans WHERE Key_name = 'subscription_plans_is_active_sort_order_index'");
            if (empty($indexes)) {
                $table->index(['is_active', 'sort_order']);
            }
        });

        // Create pivot table for plan features
        if (!Schema::hasTable('subscription_plan_features')) {
        Schema::create('subscription_plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_feature_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['subscription_plan_id', 'subscription_feature_id']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_features');
        
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropIndex(['is_active', 'sort_order']);
        });
    }
};

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
            if (!Schema::hasColumn('subscription_plans', 'tier_id')) {
                $table->foreignId('tier_id')->nullable()->constrained('subscription_tiers')->onDelete('set null');
            }
            if (!Schema::hasColumn('subscription_plans', 'slug')) {
                $table->string('slug')->unique()->after('name');
            }
            if (!Schema::hasColumn('subscription_plans', 'trial_days')) {
                $table->integer('trial_days')->default(0)->after('duration_days');
            }
            if (!Schema::hasColumn('subscription_plans', 'setup_fee')) {
                $table->decimal('setup_fee', 10, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('subscription_plans', 'max_users')) {
                $table->integer('max_users')->default(1)->after('setup_fee');
            }
            if (!Schema::hasColumn('subscription_plans', 'storage_limit')) {
                $table->integer('storage_limit')->default(0)->after('max_users');
            }
            if (!Schema::hasColumn('subscription_plans', 'bandwidth_limit')) {
                $table->integer('bandwidth_limit')->default(0)->after('storage_limit');
            }
            if (!Schema::hasColumn('subscription_plans', 'api_calls_limit')) {
                $table->integer('api_calls_limit')->default(0)->after('bandwidth_limit');
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
            
            $table->unique(['subscription_plan_id', 'subscription_feature_id'], 'sub_plan_feat_unique');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_features');
        
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropForeign(['tier_id']);
            $table->dropColumn(['tier_id', 'slug', 'trial_days', 'setup_fee', 'max_users', 'storage_limit', 'bandwidth_limit', 'api_calls_limit']);
            $table->dropIndex(['is_active', 'sort_order']);
        });
    }
};

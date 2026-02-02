<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->foreignId('tier_id')->nullable()->constrained('subscription_tiers')->onDelete('set null');
            $table->string('slug')->unique()->after('name');
            $table->integer('trial_days')->default(0)->after('duration_days');
            $table->decimal('setup_fee', 10, 2)->default(0)->after('price');
            $table->integer('max_users')->default(1)->after('setup_fee');
            $table->integer('storage_limit')->default(0)->after('max_users');
            $table->integer('bandwidth_limit')->default(0)->after('storage_limit');
            $table->integer('api_calls_limit')->default(0)->after('bandwidth_limit');
            $table->index(['is_active', 'sort_order']);
        });

        // Create pivot table for plan features
        Schema::create('subscription_plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_feature_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['subscription_plan_id', 'subscription_feature_id']);
        });
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

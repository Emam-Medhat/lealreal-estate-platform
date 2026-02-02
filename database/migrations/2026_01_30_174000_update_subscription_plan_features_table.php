<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plan_features', function (Blueprint $table) {
            $table->integer('limit')->nullable()->after('subscription_feature_id');
            $table->boolean('included')->default(true)->after('limit');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plan_features', function (Blueprint $table) {
            $table->dropColumn(['limit', 'included']);
        });
    }
};

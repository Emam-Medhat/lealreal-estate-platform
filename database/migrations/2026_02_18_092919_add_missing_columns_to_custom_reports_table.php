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
        Schema::table('custom_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('custom_reports', 'usage_count')) {
                $table->integer('usage_count')->default(0)->after('is_template');
            }
            if (!Schema::hasColumn('custom_reports', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('usage_count');
            }
            if (!Schema::hasColumn('custom_reports', 'active')) {
                $table->boolean('active')->default(true)->after('is_public');
            }
            if (!Schema::hasColumn('custom_reports', 'category')) {
                $table->string('category')->nullable()->after('active');
            }
            if (!Schema::hasColumn('custom_reports', 'tags')) {
                $table->json('tags')->nullable()->after('category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_reports', function (Blueprint $table) {
            $table->dropColumn(['usage_count', 'is_public', 'active', 'category', 'tags']);
        });
    }
};

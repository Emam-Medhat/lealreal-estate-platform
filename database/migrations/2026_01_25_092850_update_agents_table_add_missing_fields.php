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
        Schema::table('agents', function (Blueprint $table) {
            if (!Schema::hasColumn('agents', 'specialization')) {
                $table->string('specialization')->nullable()->after('license_number');
            }
            if (!Schema::hasColumn('agents', 'experience_years')) {
                $table->integer('experience_years')->default(0)->after('specialization');
            }
            if (!Schema::hasColumn('agents', 'rating')) {
                $table->decimal('rating', 3, 2)->default(0)->after('experience_years');
            }
            if (!Schema::hasColumn('agents', 'total_sales')) {
                $table->decimal('total_sales', 15, 2)->default(0)->after('rating');
            }
            if (!Schema::hasColumn('agents', 'total_properties')) {
                $table->integer('total_properties')->default(0)->after('total_sales');
            }
            if (!Schema::hasColumn('agents', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('status');
            }
            if (!Schema::hasColumn('agents', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_verified');
            }
            if (!Schema::hasColumn('agents', 'territory_id')) {
                $table->foreignId('territory_id')->nullable()->after('agency_id');
            }
            if (!Schema::hasColumn('agents', 'join_date')) {
                $table->date('join_date')->nullable()->after('hire_date');
            }
            if (!Schema::hasColumn('agents', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('join_date');
            }
            if (!Schema::hasColumn('agents', 'suspended_at')) {
                $table->timestamp('suspended_at')->nullable()->after('verified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn([
                'specialization',
                'experience_years',
                'rating',
                'total_sales',
                'total_properties',
                'is_verified',
                'is_active',
                'territory_id',
                'join_date',
                'verified_at',
                'suspended_at',
            ]);
        });
    }
};

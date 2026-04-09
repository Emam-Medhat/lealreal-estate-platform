<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('property_locations', function (Blueprint $table) {
            $table->index('city');
            $table->index('country');
            $table->index('state');
            $table->index('neighborhood');
            $table->index(['latitude', 'longitude']);
        });

        Schema::table('property_prices', function (Blueprint $table) {
            $table->index('price');
            $table->index('is_active');
            $table->index('price_type');
            $table->index(['property_id', 'is_active']);
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->index('status');
            }
            if (Schema::hasColumn('users', 'account_status')) {
                $table->index('account_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_locations', function (Blueprint $table) {
            $table->dropIndex(['city']);
            $table->dropIndex(['country']);
            $table->dropIndex(['state']);
            $table->dropIndex(['neighborhood']);
            $table->dropIndex(['latitude', 'longitude']);
        });

        Schema::table('property_prices', function (Blueprint $table) {
            $table->dropIndex(['price']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['price_type']);
            $table->dropIndex(['property_id', 'is_active']);
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropIndex(['status']);
            }
            if (Schema::hasColumn('users', 'account_status')) {
                $table->dropIndex(['account_status']);
            }
        });
    }
};

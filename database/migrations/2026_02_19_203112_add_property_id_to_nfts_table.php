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
        Schema::table('nfts', function (Blueprint $table) {
            if (!Schema::hasColumn('nfts', 'property_id')) {
                $table->foreignId('property_id')->nullable()->after('id')->constrained('properties')->nullOnDelete();
                $table->index('property_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nfts', function (Blueprint $table) {
            if (Schema::hasColumn('nfts', 'property_id')) {
                $table->dropForeign(['property_id']);
                $table->dropIndex(['property_id']);
                $table->dropColumn('property_id');
            }
        });
    }
};

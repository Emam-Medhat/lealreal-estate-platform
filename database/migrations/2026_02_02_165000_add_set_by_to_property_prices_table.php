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
        if (Schema::hasTable('property_prices')) {
            if (!Schema::hasColumn('property_prices', 'set_by')) {
                Schema::table('property_prices', function (Blueprint $table) {
                    $table->foreignId('set_by')->nullable()->constrained('users')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('property_prices') && Schema::hasColumn('property_prices', 'set_by')) {
            Schema::table('property_prices', function (Blueprint $table) {
                $table->dropForeign(['set_by']);
                $table->dropColumn('set_by');
            });
        }
    }
};

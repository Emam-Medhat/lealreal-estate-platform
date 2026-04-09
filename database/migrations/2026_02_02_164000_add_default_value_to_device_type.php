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
        if (Schema::hasTable('user_devices') && Schema::hasColumn('user_devices', 'device_type')) {
            Schema::table('user_devices', function (Blueprint $table) {
                // Modify the device_type column to have a default value
                $table->string('device_type')->default('desktop')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_devices') && Schema::hasColumn('user_devices', 'device_type')) {
            Schema::table('user_devices', function (Blueprint $table) {
                // Remove the default value
                $table->string('device_type')->default(null)->change();
            });
        }
    }
};

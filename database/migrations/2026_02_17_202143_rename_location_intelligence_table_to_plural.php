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
        if (Schema::hasTable('location_intelligence') && !Schema::hasTable('location_intelligences')) {
            Schema::rename('location_intelligence', 'location_intelligences');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('location_intelligences') && !Schema::hasTable('location_intelligence')) {
            Schema::rename('location_intelligences', 'location_intelligence');
        }
    }
};

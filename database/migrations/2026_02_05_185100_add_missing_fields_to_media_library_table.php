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
        Schema::table('media_library', function (Blueprint $table) {
            $table->string('path')->nullable()->after('file_path');
            $table->string('size')->nullable()->after('file_size');
            $table->string('caption')->nullable()->after('description');
            $table->string('category')->nullable()->after('caption');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_library', function (Blueprint $table) {
            $table->dropColumn([
                'path',
                'size',
                'caption',
                'category'
            ]);
        });
    }
};

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
        Schema::table('guides', function (Blueprint $table) {
            $table->integer('estimated_time')->nullable()->after('reading_time');
            $table->dateTime('published_at')->nullable()->after('status');
            $table->string('meta_title')->nullable()->after('seo_data');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('prerequisites')->nullable()->after('meta_description');
            $table->text('learning_objectives')->nullable()->after('prerequisites');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guides', function (Blueprint $table) {
            $table->dropColumn([
                'estimated_time',
                'published_at',
                'meta_title',
                'meta_description',
                'prerequisites',
                'learning_objectives'
            ]);
        });
    }
};

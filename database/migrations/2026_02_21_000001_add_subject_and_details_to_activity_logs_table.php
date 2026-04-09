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
        Schema::table('user_activity_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('user_activity_logs', 'subject_type')) {
                $table->string('subject_type')->nullable()->after('action');
            }
            if (!Schema::hasColumn('user_activity_logs', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');
            }
            if (!Schema::hasColumn('user_activity_logs', 'details')) {
                $table->json('details')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->dropColumn(['subject_type', 'subject_id', 'details']);
        });
    }
};

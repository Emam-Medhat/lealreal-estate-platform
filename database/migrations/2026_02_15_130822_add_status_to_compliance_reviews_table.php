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
        Schema::table('compliance_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('compliance_reviews', 'status')) {
                $table->string('status')->default('pending')->after('id');
                $table->index('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compliance_reviews', function (Blueprint $table) {
            if (Schema::hasColumn('compliance_reviews', 'status')) {
                $table->dropIndex(['status']);
                $table->dropColumn('status');
            }
        });
    }
};

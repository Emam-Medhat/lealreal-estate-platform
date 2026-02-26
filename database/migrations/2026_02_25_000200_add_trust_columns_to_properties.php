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
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'trust_index')) {
                $table->decimal('trust_index', 5, 2)->nullable()->after('fraud_score');
            }
            if (!Schema::hasColumn('properties', 'trust_level')) {
                $table->enum('trust_level', ['low', 'medium', 'high', 'critical'])->nullable()->after('trust_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'trust_level')) {
                $table->dropColumn('trust_level');
            }
            if (Schema::hasColumn('properties', 'trust_index')) {
                $table->dropColumn('trust_index');
            }
        });
    }
};

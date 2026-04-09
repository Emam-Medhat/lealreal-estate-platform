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
        Schema::table('tax_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('tax_payments', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('tax_payments', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_payments', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};

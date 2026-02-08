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
            // Only add columns that don't exist
            if (!Schema::hasColumn('tax_payments', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable()->after('refunded_at');
            }
            
            if (!Schema::hasColumn('tax_payments', 'refund_reference')) {
                $table->string('refund_reference')->nullable()->after('refund_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_payments', function (Blueprint $table) {
            $table->dropColumn(['refund_amount', 'refund_reference']);
        });
    }
};

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
        Schema::table('invoices', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('invoices', 'title')) {
                $table->string('title')->after('type');
            }
            
            if (!Schema::hasColumn('invoices', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            
            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('paid_date');
            }
            
            // Rename total_amount to total if needed
            if (Schema::hasColumn('invoices', 'total_amount') && !Schema::hasColumn('invoices', 'total')) {
                $table->renameColumn('total_amount', 'total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'payment_method']);
            
            // Rename total back to total_amount
            if (Schema::hasColumn('invoices', 'total') && !Schema::hasColumn('invoices', 'total_amount')) {
                $table->renameColumn('total', 'total_amount');
            }
        });
    }
};

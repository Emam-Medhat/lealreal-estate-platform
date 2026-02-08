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
            if (!Schema::hasColumn('invoices', 'property_id')) {
                $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            }
            if (!Schema::hasColumn('invoices', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete()->after('property_id');
            }
            if (!Schema::hasColumn('invoices', 'client_id')) {
                $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete()->after('company_id');
            }
            if (!Schema::hasColumn('invoices', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('total');
            }
            if (!Schema::hasColumn('invoices', 'amount')) {
                 $table->decimal('amount', 15, 2)->default(0)->after('items'); 
            }
            // Ensure status has default if not already set (this is tricky in migration if column exists, skipping for safety or using change() carefully)
             if (Schema::hasColumn('invoices', 'status')) {
                $table->string('status')->default('draft')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
             // Be careful dropping columns that might have existed before
            if (Schema::hasColumn('invoices', 'company_id')) {
                 $table->dropForeign(['company_id']);
                 $table->dropColumn('company_id');
            }
            if (Schema::hasColumn('invoices', 'client_id')) {
                 $table->dropForeign(['client_id']);
                 $table->dropColumn('client_id');
            }
            if (Schema::hasColumn('invoices', 'amount')) {
                $table->dropColumn('amount');
            }
        });
    }
};

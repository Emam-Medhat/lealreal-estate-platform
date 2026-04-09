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
        Schema::table('inventory_items', function (Blueprint $table) {
            // Add foreign key columns if they don't exist
            if (!Schema::hasColumn('inventory_items', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('inventory_categories')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('inventory_items', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('inventory_suppliers')->onDelete('set null');
            }
            
            // Add missing columns
            if (!Schema::hasColumn('inventory_items', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->nullable();
            }
            
            if (!Schema::hasColumn('inventory_items', 'reorder_level')) {
                $table->integer('reorder_level')->default(0);
            }
            
            if (!Schema::hasColumn('inventory_items', 'max_stock')) {
                $table->integer('max_stock')->nullable();
            }
            
            if (!Schema::hasColumn('inventory_items', 'unit_of_measure')) {
                $table->string('unit_of_measure')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            // Drop foreign key columns
            if (Schema::hasColumn('inventory_items', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
            
            if (Schema::hasColumn('inventory_items', 'supplier_id')) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            }
            
            // Drop other columns
            if (Schema::hasColumn('inventory_items', 'unit_price')) {
                $table->dropColumn('unit_price');
            }
            
            if (Schema::hasColumn('inventory_items', 'reorder_level')) {
                $table->dropColumn('reorder_level');
            }
            
            if (Schema::hasColumn('inventory_items', 'max_stock')) {
                $table->dropColumn('max_stock');
            }
            
            if (Schema::hasColumn('inventory_items', 'unit_of_measure')) {
                $table->dropColumn('unit_of_measure');
            }
        });
    }
};

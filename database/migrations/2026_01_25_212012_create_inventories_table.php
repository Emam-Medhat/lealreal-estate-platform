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
        if (!Schema::hasTable('inventories')) {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->foreignId('supplier_id')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->integer('reorder_level')->default(10);
            $table->integer('max_stock')->nullable();
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->string('unit_of_measure')->default('pcs');
            $table->string('location')->nullable(); // Warehouse location
            $table->json('attributes')->nullable(); // Additional attributes
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['sku']);
            $table->index(['category_id', 'status']);
            $table->index(['supplier_id']);
            $table->index(['quantity', 'reorder_level']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

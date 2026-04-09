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
        if (!Schema::hasTable('inventory_items')) {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->enum('category', ['tools', 'materials', 'equipment', 'supplies', 'safety', 'other'])->default('materials');
            $table->enum('status', ['active', 'inactive', 'discontinued', 'out_of_stock'])->default('active');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('sku')->nullable();
            $table->string('unit');
            $table->string('unit_ar')->nullable();
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('min_quantity')->default(0);
            $table->integer('max_quantity')->nullable();
            $table->integer('reorder_point')->default(0);
            $table->integer('reorder_quantity')->default(0);
            $table->string('supplier')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->date('last_purchase_date')->nullable();
            $table->date('next_purchase_date')->nullable();
            $table->string('location')->nullable();
            $table->string('location_ar')->nullable();
            $table->json('specifications')->nullable();
            $table->json('images')->nullable();
            $table->json('attachments')->nullable();
            $table->string('barcode')->nullable();
            $table->string('qr_code')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('requires_maintenance')->default(false);
            $table->text('maintenance_instructions')->nullable();
            $table->text('maintenance_instructions_ar')->nullable();
            $table->json('maintenance_schedule')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->text('safety_notes')->nullable();
            $table->text('safety_notes_ar')->nullable();
            $table->json('usage_history')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['item_code']);
            $table->index(['category']);
            $table->index(['status']);
            $table->index(['supplier']);
            $table->index(['quantity']);
            $table->index(['reorder_point']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};

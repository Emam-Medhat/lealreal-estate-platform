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
        if (!Schema::hasTable('order_items')) {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->morphs('itemable'); // For different item types (MetaverseProperty, etc.)
            $table->string('item_name');
            $table->text('item_description')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total', 15, 2);
            $table->json('item_data')->nullable(); // Store additional item data
            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'itemable_type', 'itemable_id'], 'order_items_order_itemable_index');
            $table->index(['itemable_type', 'itemable_id'], 'order_items_itemable_index');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

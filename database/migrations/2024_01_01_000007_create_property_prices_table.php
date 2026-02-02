<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('property_prices')) {
        Schema::create('property_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 12, 2);
            $table->string('currency', 3);
            $table->string('price_type')->default('sale'); // sale, rent, lease
            $table->decimal('price_per_sqm', 10, 2)->nullable();
            $table->boolean('is_negotiable')->default(false);
            $table->boolean('includes_vat')->default(false);
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('service_charges', 10, 2)->nullable();
            $table->decimal('maintenance_fees', 10, 2)->nullable();
            $table->string('payment_frequency')->nullable(); // monthly, quarterly, annually
            $table->json('payment_terms')->nullable();
            $table->date('effective_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('property_prices');
    }
};

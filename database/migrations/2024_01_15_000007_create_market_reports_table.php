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
        if (!Schema::hasTable('market_reports')) {
        Schema::create('market_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->string('market_area');
            $table->decimal('average_property_price', 15, 2)->default(0);
            $table->decimal('median_property_price', 15, 2)->default(0);
            $table->integer('total_listings')->default(0);
            $table->integer('total_sales')->default(0);
            $table->decimal('price_per_square_foot', 10, 2)->default(0);
            $table->decimal('average_days_on_market', 8, 2)->default(0);
            $table->decimal('inventory_level', 8, 2)->default(0); // months of inventory
            $table->json('price_trends')->nullable();
            $table->json('market_segments')->nullable();
            $table->json('neighborhood_data')->nullable();
            $table->json('market_indicators')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();

            $table->index(['market_area', 'period_start', 'period_end']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_reports');
    }
};

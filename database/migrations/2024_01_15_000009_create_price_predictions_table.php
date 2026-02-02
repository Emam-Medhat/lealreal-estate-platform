<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('price_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('current_price', 15, 2);
            $table->decimal('predicted_price', 15, 2);
            $table->decimal('confidence_score', 5, 2); // 0-100
            $table->integer('prediction_horizon'); // months into future
            $table->json('factors')->nullable(); // market factors used
            $table->json('historical_data')->nullable(); // price history
            $table->enum('model_version', ['v1.0', 'v2.0', 'v3.0'])->default('v1.0');
            $table->timestamp('prediction_date');
            $table->timestamp('target_date');
            $table->decimal('actual_price', 15, 2)->nullable(); // for accuracy tracking
            $table->decimal('accuracy_score', 5, 2)->nullable(); // calculated after actual price known
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('price_predictions');
    }
};

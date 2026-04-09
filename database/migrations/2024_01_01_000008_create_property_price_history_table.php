<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('property_price_history')) {
        Schema::create('property_price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->decimal('old_price', 12, 2);
            $table->decimal('new_price', 12, 2);
            $table->string('currency', 3);
            $table->string('change_reason')->nullable();
            $table->string('change_type'); // increase, decrease
            $table->decimal('change_percentage', 5, 2);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('property_price_history');
    }
};

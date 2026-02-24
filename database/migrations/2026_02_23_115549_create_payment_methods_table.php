<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Visa, Vodafone Cash, InstaPay, etc.
            $table->string('slug')->unique();
            $table->string('gateway'); // paymob, stripe, bank
            $table->json('credentials')->nullable(); // Optional gateway-specific creds if per method
            $table->boolean('is_active')->default(true);
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};

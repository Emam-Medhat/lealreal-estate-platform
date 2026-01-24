<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contract_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->integer('order')->default(0);
            $table->string('type')->default('general'); // general, payment, termination, liability
            $table->timestamps();
            
            $table->index(['contract_id']);
            $table->index(['type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contract_terms');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contract_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('role'); // buyer, seller, agent, witness
            $table->boolean('is_signatory')->default(true);
            $table->datetime('signed_at')->nullable();
            $table->text('signature_data')->nullable();
            $table->timestamps();
            
            $table->index(['contract_id']);
            $table->index(['email']);
            $table->index(['role']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contract_parties');
    }
};

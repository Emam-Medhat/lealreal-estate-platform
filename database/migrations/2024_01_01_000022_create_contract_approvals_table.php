<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contract_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->string('status'); // pending, approved, rejected
            $table->text('comments')->nullable();
            $table->datetime('reviewed_at')->nullable();
            $table->integer('order')->default(0); // approval workflow order
            $table->timestamps();
            
            $table->index(['contract_id']);
            $table->index(['user_id']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contract_approvals');
    }
};

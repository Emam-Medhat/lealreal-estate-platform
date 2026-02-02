<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('crowdfunding_investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('crowdfunding_campaigns')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->decimal('shares', 10, 2);
            $table->decimal('share_price', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'completed', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['campaign_id', 'user_id']);
            $table->index(['status']);
            $table->index(['user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('crowdfunding_investments');
    }
};

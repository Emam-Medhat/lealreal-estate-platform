<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('crowdfunding_campaigns')) {
        Schema::create('crowdfunding_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->decimal('target_amount', 15, 2);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->decimal('min_investment', 15, 2);
            $table->decimal('return_rate', 5, 2);
            $table->integer('duration_months');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['status', 'end_date']);
            $table->index(['property_id']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('crowdfunding_campaigns');
    }
};

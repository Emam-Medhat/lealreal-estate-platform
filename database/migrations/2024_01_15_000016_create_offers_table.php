<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('offer_amount', 15, 2);
            $table->decimal('property_price', 15, 2);
            $table->enum('type', ['purchase', 'rent', 'lease']);
            $table->enum('status', ['pending', 'accepted', 'rejected', 'countered', 'expired', 'withdrawn'])->default('pending');
            $table->text('terms')->nullable();
            $table->date('valid_until');
            $table->decimal('earnest_money', 15, 2)->nullable();
            $table->integer('financing_type')->nullable(); // 1=cash, 2=loan, 3=mixed
            $table->text('contingencies')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('offers');
    }
};

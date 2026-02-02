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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('billing_cycle'); // monthly, yearly, etc.
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('trial_days')->default(0);
            $table->string('stripe_plan_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'billing_cycle']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

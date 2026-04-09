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
        if (!Schema::hasTable('agent_reviews')) {
        Schema::create('agent_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->integer('rating');
            $table->string('title')->nullable();
            $table->text('review_text')->nullable();
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();
            $table->integer('service_quality')->nullable();
            $table->integer('communication')->nullable();
            $table->integer('professionalism')->nullable();
            $table->integer('market_knowledge')->nullable();
            $table->integer('negotiation_skills')->nullable();
            $table->integer('responsiveness')->nullable();
            $table->integer('problem_solving')->nullable();
            $table->integer('integrity')->nullable();
            $table->integer('availability')->nullable();
            $table->integer('documentation')->nullable();
            $table->integer('follow_up')->nullable();
            $table->integer('overall_experience')->nullable();
            $table->boolean('would_recommend')->nullable();
            $table->boolean('would_work_again')->nullable();
            $table->string('review_type')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('property_category')->nullable();
            $table->string('price_range_category')->nullable();
            $table->integer('location_satisfaction')->nullable();
            $table->integer('timeline_satisfaction')->nullable();
            $table->integer('process_satisfaction')->nullable();
            $table->integer('value_for_money')->nullable();
            $table->string('stress_level')->nullable();
            $table->text('unexpected_issues')->nullable();
            $table->text('issue_resolution')->nullable();
            $table->integer('agent_performance_rating')->nullable();
            $table->integer('company_performance_rating')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('set null');
            // $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null'); // transactions table doesn't exist yet

            $table->index(['agent_id', 'rating']);
            $table->index(['agent_id', 'created_at']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_reviews');
    }
};

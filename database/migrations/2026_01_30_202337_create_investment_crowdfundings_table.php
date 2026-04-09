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
        if (!Schema::hasTable('investment_crowdfundings')) {
        Schema::create('investment_crowdfundings', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name');
            $table->text('description');
            $table->string('category');
            $table->decimal('funding_goal', 15, 2);
            $table->decimal('total_raised', 15, 2)->default(0);
            $table->integer('investor_count')->default(0);
            $table->decimal('minimum_investment', 15, 2);
            $table->decimal('maximum_investment', 15, 2)->nullable();
            $table->decimal('equity_offered', 8, 4)->nullable();
            $table->decimal('projected_return_rate', 8, 4)->nullable();
            $table->string('risk_level')->default('medium');
            $table->string('status')->default('draft');
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->datetime('published_at')->nullable();
            $table->json('documents')->nullable();
            $table->json('images')->nullable();
            $table->json('updates')->nullable();
            $table->json('team_members')->nullable();
            $table->json('milestones')->nullable();
            $table->string('location')->nullable();
            $table->json('tags')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('category');
            $table->index('published_at');
            $table->index('created_by');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_crowdfundings');
    }
};

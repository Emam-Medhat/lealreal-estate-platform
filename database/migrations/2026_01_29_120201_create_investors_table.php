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
        if (!Schema::hasTable('investors')) {
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->enum('investor_type', ['individual', 'company', 'fund', 'bank', 'government', 'institution']);
            $table->enum('status', ['active', 'inactive', 'suspended', 'verified'])->default('active');
            $table->decimal('total_invested', 15, 2)->default(0);
            $table->decimal('total_returns', 15, 2)->default(0);
            $table->enum('risk_tolerance', ['conservative', 'moderate', 'aggressive', 'very_aggressive']);
            $table->json('investment_goals')->nullable();
            $table->json('preferred_sectors')->nullable();
            $table->integer('experience_years')->nullable();
            $table->boolean('accredited_investor')->default(false);
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->json('address')->nullable();
            $table->json('social_links')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_picture')->nullable();
            $table->json('watchlist')->nullable();
            $table->json('crowdfunding_watchlist')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['status']);
            $table->index(['verification_status']);
            $table->index(['investor_type']);
            $table->index(['risk_tolerance']);
            $table->index(['accredited_investor']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investors');
    }
};

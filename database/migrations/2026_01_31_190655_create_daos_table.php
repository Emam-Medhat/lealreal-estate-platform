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
        if (!Schema::hasTable('daos')) {
        Schema::create('daos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('purpose');
            $table->string('token_symbol');
            $table->decimal('total_supply', 20, 8)->default(0);
            $table->decimal('voting_power', 20, 8)->default(0);
            $table->decimal('quorum', 5, 2)->default(51.00);
            $table->integer('voting_period')->default(7);
            $table->string('contract_address')->nullable();
            $table->enum('status', ['active', 'inactive', 'proposed'])->default('proposed');
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['status', 'creator_id']);
            $table->index('contract_address');
        });
        }

        if (!Schema::hasTable('dao_members')) {
        Schema::create('dao_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dao_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('voting_power', 20, 8)->default(0);
            $table->decimal('tokens_held', 20, 8)->default(0);
            $table->enum('role', ['member', 'admin', 'treasurer'])->default('member');
            $table->timestamp('joined_at');
            $table->timestamps();
            
            $table->unique(['dao_id', 'user_id']);
            $table->index(['dao_id', 'role']);
        });
        }

        if (!Schema::hasTable('dao_proposals')) {
        Schema::create('dao_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dao_id')->constrained()->onDelete('cascade');
            $table->foreignId('proposer_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['funding', 'parameter_change', 'membership', 'other'])->default('other');
            $table->decimal('amount_requested', 20, 8)->nullable();
            $table->string('recipient_address')->nullable();
            $table->enum('status', ['active', 'passed', 'rejected', 'executed'])->default('active');
            $table->timestamp('voting_starts_at');
            $table->timestamp('voting_ends_at');
            $table->integer('votes_for')->default(0);
            $table->integer('votes_against')->default(0);
            $table->integer('votes_abstain')->default(0);
            $table->text('execution_result')->nullable();
            $table->timestamps();
            
            $table->index(['dao_id', 'status']);
            $table->index(['proposer_id', 'status']);
        });
        }

        if (!Schema::hasTable('dao_votes')) {
        Schema::create('dao_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('dao_proposals')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('vote', ['for', 'against', 'abstain']);
            $table->decimal('voting_power', 20, 8)->default(0);
            $table->string('reason')->nullable();
            $table->timestamps();
            
            $table->unique(['proposal_id', 'user_id']);
            $table->index(['proposal_id', 'vote']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dao_votes');
        Schema::dropIfExists('dao_proposals');
        Schema::dropIfExists('dao_members');
        Schema::dropIfExists('daos');
    }
};

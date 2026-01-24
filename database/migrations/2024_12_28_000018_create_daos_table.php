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
        Schema::create('daos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address', 42)->unique();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->string('token_address', 42)->nullable();
            $table->string('token_symbol')->nullable();
            $table->decimal('treasury_balance', 30, 18)->default(0);
            $table->integer('total_members')->default(0);
            $table->integer('active_members')->default(0);
            $table->decimal('voting_quorum', 5, 2)->default(50);
            $table->integer('voting_period')->default(7);
            $table->integer('execution_delay')->default(2);
            $table->enum('status', ['active', 'inactive', 'dissolved'])->default('active');
            $table->timestamp('created_at_block')->nullable();
            $table->json('governance_rules')->nullable();
            $table->json('permissions')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('address');
            $table->index('token_address');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daos');
    }
};

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
        if (!Schema::hasTable('smart_contracts')) {
        Schema::create('smart_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('address', 42)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('source_code');
            $table->json('abi');
            $table->string('bytecode');
            $table->string('compiler_version');
            $table->boolean('optimization')->default(false);
            $table->enum('type', ['erc20', 'erc721', 'erc1155', 'custom'])->default('custom');
            $table->enum('status', ['draft', 'deployed', 'verified', 'deprecated'])->default('draft');
            $table->timestamp('deployed_at')->nullable();
            $table->string('deployment_hash', 64)->nullable();
            $table->integer('gas_used')->nullable();
            $table->string('deployer_address', 42)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_message')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('address');
            $table->index('status');
            $table->index('type');
            $table->index('is_verified');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_contracts');
    }
};

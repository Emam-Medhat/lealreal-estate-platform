<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_amendments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->string('amendment_number');
            $table->text('title');
            $table->text('description');
            $table->enum('type', ['price', 'terms', 'contingencies', 'closing_date', 'possession_date', 'other'])->default('other');
            $table->enum('status', ['draft', 'proposed', 'accepted', 'rejected', 'withdrawn'])->default('draft');
            $table->json('original_terms')->nullable();
            $table->json('amended_terms');
            $table->text('reason_for_amendment')->nullable();
            $table->foreignId('proposed_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('proposed_at');
            $table->foreignId('accepted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('accepted_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('amendment_data')->nullable();
            $table->boolean('requires_all_signatures')->default(true);
            $table->json('signatures_required')->nullable();
            $table->json('signatures_received')->nullable();
            $table->timestamp('effective_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['contract_id', 'amendment_number']);
            $table->index(['contract_id', 'status']);
            $table->index(['proposed_by', 'proposed_at']);
            $table->index(['status', 'proposed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_amendments');
    }
};

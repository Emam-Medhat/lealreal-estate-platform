<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('eviction_number')->unique();
            $table->enum('reason', ['non_payment', 'lease_violation', 'property_damage', 'illegal_activity', 'other']);
            $table->text('description');
            $table->enum('status', ['pending', 'notice_served', 'court_filed', 'judgment', 'writ_issued', 'sheriff_scheduled', 'completed', 'cancelled'])->default('pending');
            $table->date('notice_date');
            $table->enum('notice_type', ['pay_or_quit', 'cure_or_quit', 'unconditional']);
            $table->date('court_date')->nullable();
            $table->string('court_order_number')->nullable();
            $table->date('eviction_date')->nullable();
            $table->date('actual_move_out_date')->nullable();
            $table->decimal('legal_fees', 10, 2)->default(0);
            $table->decimal('damages', 10, 2)->default(0);
            $table->decimal('recovery_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('documents')->nullable();
            $table->boolean('notice_served')->default(false);
            $table->datetime('notice_served_date')->nullable();
            $table->enum('notice_served_method', ['personal', 'certified_mail', 'posted', 'email'])->nullable();
            $table->date('court_filing_date')->nullable();
            $table->date('judgment_date')->nullable();
            $table->date('writ_date')->nullable();
            $table->date('sheriff_date')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['lease_id', 'status']);
            $table->index(['tenant_id', 'status']);
            $table->index(['property_id', 'status']);
            $table->index(['status', 'notice_date']);
            $table->index(['court_date']);
            $table->index(['eviction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evictions');
    }
};

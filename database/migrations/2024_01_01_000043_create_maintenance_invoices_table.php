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
        Schema::create('maintenance_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('maintenance_request_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('work_order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->enum('invoice_type', ['labor', 'materials', 'equipment', 'emergency', 'preventive', 'other'])->default('other');
            $table->enum('status', ['draft', 'sent', 'viewed', 'approved', 'paid', 'overdue', 'cancelled', 'disputed'])->default('draft');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency')->default('SAR');
            $table->enum('payment_method', ['cash', 'credit_card', 'bank_transfer', 'check', 'online', 'other'])->nullable();
            $table->string('payment_reference')->nullable();
            $table->json('line_items');
            $table->json('labor_items')->nullable();
            $table->json('material_items')->nullable();
            $table->json('equipment_items')->nullable();
            $table->json('tax_breakdown')->nullable();
            $table->json('attachments')->nullable();
            $table->string('billing_address');
            $table->string('billing_address_ar')->nullable();
            $table->string('billing_city');
            $table->string('billing_city_ar')->nullable();
            $table->string('billing_state');
            $table->string('billing_state_ar')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_country')->default('Saudi Arabia');
            $table->string('billing_country_ar')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('billing_email')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('terms_conditions_ar')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->string('pdf_file')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_frequency')->nullable();
            $table->date('next_invoice_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['invoice_number']);
            $table->index(['maintenance_request_id']);
            $table->index(['work_order_id']);
            $table->index(['property_id']);
            $table->index(['service_provider_id']);
            $table->index(['customer_id']);
            $table->index(['invoice_type']);
            $table->index(['status']);
            $table->index(['invoice_date']);
            $table->index(['due_date']);
            $table->index(['paid_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_invoices');
    }
};

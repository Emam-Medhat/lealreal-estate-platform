<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ad_conversions')) {
        Schema::create('ad_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained()->onDelete('cascade');
            $table->foreignId('click_id')->nullable()->constrained('ad_clicks')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('conversion_type', ['lead', 'sale', 'signup', 'download', 'inquiry', 'booking', 'call', 'form_submission', 'newsletter_signup', 'property_view', 'property_inquiry', 'property_booking']);
            $table->decimal('conversion_value', 10, 2)->default(0);
            $table->string('conversion_currency', 3)->default('SAR');
            $table->datetime('converted_at');
            $table->string('ip_address');
            $table->text('user_agent');
            $table->text('page_url');
            $table->json('conversion_data')->nullable();
            $table->enum('attribution_model', ['first_click', 'last_click', 'linear', 'time_decay', 'position_based', 'data_driven'])->default('last_click');
            $table->integer('attribution_window')->default(30); // days
            $table->decimal('attribution_score', 5, 2)->default(100);
            $table->boolean('is_verified')->default(false);
            $table->enum('verification_method', ['pixel', 'postback', 'api', 'manual', 'server_to_server'])->nullable();
            $table->datetime('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('revenue', 12, 2)->default(0);
            $table->decimal('cost', 10, 2)->default(0);
            $table->decimal('profit', 12, 2)->default(0);
            $table->json('custom_parameters')->nullable();
            $table->string('tracking_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('product_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('discount_code')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['advertisement_id', 'converted_at']);
            $table->index(['click_id']);
            $table->index(['user_id', 'converted_at']);
            $table->index(['conversion_type']);
            $table->index(['is_verified']);
            $table->index(['attribution_model']);
            $table->index(['tracking_id']);
            $table->index(['order_id']);
            $table->index(['created_at']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ad_conversions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enterprise_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_type');
            $table->string('industry');
            $table->string('size');
            $table->string('website')->nullable();
            $table->string('phone');
            $table->string('address');
            $table->string('city');
            $table->string('country');
            $table->string('postal_code')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('contact_person');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('payment_method');
            $table->string('subscription_plan');
            $table->string('status')->default('pending');
            $table->timestamp('upgraded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Add enterprise_id to subscriptions table if not exists
        if (Schema::hasTable('subscriptions') && !Schema::hasColumn('subscriptions', 'enterprise_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->foreignId('enterprise_id')->nullable()->constrained('enterprise_accounts')->onDelete('cascade');
                $table->string('plan')->nullable(); // To store plan name string like 'enterprise'
                $table->timestamp('next_billing_at')->nullable();
                $table->decimal('price', 10, 2)->nullable();
                $table->json('features')->nullable();
                $table->decimal('proration_amount', 10, 2)->nullable();
                $table->string('proration_reason')->nullable();
            });
        }
        
        // Add enterprise_id to users table if not exists
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'enterprise_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('enterprise_id')->nullable()->constrained('enterprise_accounts')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('enterprise_accounts');
        
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropForeign(['enterprise_id']);
                $table->dropColumn(['enterprise_id', 'plan', 'next_billing_at', 'price', 'features', 'proration_amount', 'proration_reason']);
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['enterprise_id']);
                $table->dropColumn(['enterprise_id']);
            });
        }
    }
};

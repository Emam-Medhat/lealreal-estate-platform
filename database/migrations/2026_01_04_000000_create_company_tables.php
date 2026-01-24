<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Companies Table
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique()->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('website')->nullable();
                $table->string('type')->default('agency'); // agency, developer, etc.
                $table->string('registration_number')->nullable();
                $table->string('tax_id')->nullable();

                $table->string('status')->default('pending'); // pending, active, suspended, rejected
                $table->foreignId('created_by')->constrained('users');
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('rejected_by')->nullable()->constrained('users');
                $table->timestamp('rejected_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->text('suspension_reason')->nullable();
                $table->timestamp('suspended_at')->nullable();

                $table->string('logo_url')->nullable();
                $table->string('cover_image_url')->nullable();
                $table->text('description')->nullable();
                $table->date('founded_date')->nullable();
                $table->integer('employee_count')->default(0);
                $table->decimal('annual_revenue', 15, 2)->nullable();

                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('postal_code')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();

                $table->boolean('is_featured')->default(false);
                $table->boolean('is_verified')->default(false);
                $table->integer('verification_level')->default(0);
                $table->decimal('rating', 3, 2)->default(0);
                $table->integer('total_reviews')->default(0);

                $table->string('subscription_plan')->default('basic');
                $table->timestamp('subscription_expires_at')->nullable();

                $table->string('api_key')->nullable();
                $table->string('webhook_url')->nullable();
                // $table->json('settings')->nullable(); // Moved to separate table
                $table->json('metadata')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Company Members Table
        if (!Schema::hasTable('company_members')) {
            Schema::create('company_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('role')->default('member'); // owner, admin, manager, agent, member
                $table->string('status')->default('active'); // active, inactive, invited
                $table->string('title')->nullable(); // Job title
                $table->json('permissions')->nullable();

                $table->timestamp('joined_at')->useCurrent();
                $table->foreignId('invited_by')->nullable()->constrained('users');
                $table->string('invitation_token')->nullable();
                $table->timestamp('invitation_accepted_at')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->unique(['company_id', 'user_id']);
            });
        }

        // Company Branches Table
        if (!Schema::hasTable('company_branches')) {
            Schema::create('company_branches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->foreignId('manager_id')->nullable()->constrained('users');
                $table->boolean('is_main')->default(false);
                $table->string('status')->default('active');
                $table->json('coordinates')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Company Settings Table (New)
        if (!Schema::hasTable('company_settings')) {
            Schema::create('company_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->string('key');
                $table->text('value')->nullable();
                $table->string('type')->default('string');
                $table->string('group')->default('general');
                $table->boolean('is_public')->default(false);
                $table->string('description')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users');

                $table->timestamps();

                $table->unique(['company_id', 'key']);
            });
        }

        // Company Analytics Table
        if (!Schema::hasTable('company_analytics')) {
            Schema::create('company_analytics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->string('type')->default('daily'); // daily, monthly, yearly
                $table->json('data');
                $table->timestamp('calculated_at');
                $table->timestamp('period_start')->nullable();
                $table->timestamp('period_end')->nullable();

                $table->timestamps();
            });
        }

        // Company Subscriptions Table
        if (!Schema::hasTable('company_subscriptions')) {
            Schema::create('company_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->string('plan_name');
                $table->string('status'); // active, cancelled
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->decimal('amount', 10, 2)->nullable();
                $table->string('currency')->default('USD');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_subscriptions');
        Schema::dropIfExists('company_analytics');
        Schema::dropIfExists('company_settings');
        Schema::dropIfExists('company_branches');
        Schema::dropIfExists('company_members');
        Schema::dropIfExists('companies');
    }
};

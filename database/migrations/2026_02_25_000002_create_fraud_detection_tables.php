<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create ip_history table for tracking IP-based fraud patterns
        Schema::create('ip_history', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('action')->default('login')
                ->comment('Action type: login, register, listing_create, etc.');
            $table->string('user_agent', 512)->nullable();
            $table->string('country_code', 3)->nullable();
            $table->string('city', 100)->nullable();
            $table->json('metadata')->nullable()
                ->comment('Additional context: browser, OS, etc.');
            $table->boolean('is_vpn')->default(false);
            $table->boolean('is_proxy')->default(false);
            $table->boolean('is_tor')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['ip_address', 'user_id', 'created_at']);
            $table->index(['ip_address', 'action', 'created_at']);
        });

        // Add risk score columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'risk_score')) {
                $table->unsignedTinyInteger('risk_score')->default(0)->after('account_status')
                    ->comment('User fraud risk score 0-100');
            }
            if (!Schema::hasColumn('users', 'risk_factors')) {
                $table->json('risk_factors')->nullable()->after('risk_score')
                    ->comment('JSON array of detected risk factors');
            }
            if (!Schema::hasColumn('users', 'is_restricted')) {
                $table->boolean('is_restricted')->default(false)->after('risk_factors')
                    ->comment('User is restricted from posting new listings');
            }
            if (!Schema::hasColumn('users', 'requires_manual_verification')) {
                $table->boolean('requires_manual_verification')->default(false)
                    ->after('is_restricted');
            }
            if (!Schema::hasColumn('users', 'restriction_reason')) {
                $table->string('restriction_reason')->nullable()->after('requires_manual_verification');
            }
            if (!Schema::hasColumn('users', 'restricted_at')) {
                $table->timestamp('restricted_at')->nullable()->after('restriction_reason');
            }
        });

        // Create property duplicates tracking table
        Schema::create('property_duplicates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('duplicate_property_id')->index();
            $table->unsignedTinyInteger('similarity_score')->default(0)
                ->comment('Overall similarity score 0-100');
            $table->unsignedTinyInteger('image_similarity')->default(0);
            $table->unsignedTinyInteger('location_similarity')->default(0);
            $table->unsignedTinyInteger('title_similarity')->default(0);
            $table->unsignedTinyInteger('price_similarity')->default(0);
            $table->json('matching_details')->nullable()
                ->comment('Detailed breakdown of how similarities were calculated');
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'merged'])
                ->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('duplicate_property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');

            // Prevent duplicate entries of same pair
            $table->unique(['property_id', 'duplicate_property_id']);
            $table->index(['similarity_score', 'status']);
        });

        // Create fraud_detection_logs table for comprehensive audit trail
        Schema::create('fraud_detection_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type')->index()
                ->comment('Type: property, user, property_media');
            $table->unsignedBigInteger('entity_id')->index();
            $table->string('detection_type')->index()
                ->comment('image_hash, address_validation, price_anomaly, fake_account, duplicate_detection');
            $table->unsignedTinyInteger('fraud_score')->default(0);
            $table->json('details')
                ->comment('Full detection result details');
            $table->string('action_taken')->nullable()
                ->comment('flagged, blocked, notified, none');
            $table->unsignedBigInteger('triggered_by')->nullable()
                ->comment('User ID or null for system-triggered');
            $table->timestamps();

            $table->foreign('triggered_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['entity_type', 'entity_id', 'created_at']);
            $table->index(['detection_type', 'fraud_score', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_detection_logs');
        Schema::dropIfExists('property_duplicates');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'risk_score',
                'risk_factors',
                'is_restricted',
                'requires_manual_verification',
                'restriction_reason',
                'restricted_at'
            ]);
        });

        Schema::dropIfExists('ip_history');
    }
};

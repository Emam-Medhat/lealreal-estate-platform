<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add fraud columns to property_media
        Schema::table('property_media', function (Blueprint $table) {
            if (!Schema::hasColumn('property_media', 'image_hash')) {
                $table->string('image_hash', 64)->nullable()->after('sort_order')
                    ->comment('Perceptual hash (pHash/dHash) of the image for duplicate detection');
            }
            if (!Schema::hasColumn('property_media', 'perceptual_hash')) {
                $table->string('perceptual_hash', 128)->nullable()->after('image_hash')
                    ->comment('Extended perceptual hash for more precise matching');
            }
            if (!Schema::hasColumn('property_media', 'fraud_score')) {
                $table->unsignedTinyInteger('fraud_score')->default(0)->after('perceptual_hash')
                    ->comment('Fraud score 0-100 for this image');
            }
            if (!Schema::hasColumn('property_media', 'is_duplicate')) {
                $table->boolean('is_duplicate')->default(false)->after('fraud_score');
            }
            if (!Schema::hasColumn('property_media', 'duplicate_of')) {
                $table->unsignedBigInteger('duplicate_of')->nullable()->after('is_duplicate')
                    ->comment('ID of the original image if this is a duplicate');
            }
        });

        // Add fraud columns to properties table
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'fraud_score')) {
                $table->unsignedTinyInteger('fraud_score')->default(0)->after('status')
                    ->comment('Overall fraud score 0-100');
            }
            if (!Schema::hasColumn('properties', 'fraud_flags')) {
                $table->json('fraud_flags')->nullable()->after('fraud_score')
                    ->comment('JSON object with detected fraud indicators');
            }
            if (!Schema::hasColumn('properties', 'anomaly_score')) {
                $table->unsignedTinyInteger('anomaly_score')->default(0)->after('fraud_flags')
                    ->comment('Price anomaly score 0-100');
            }
            if (!Schema::hasColumn('properties', 'fraud_status')) {
                $table->enum('fraud_status', ['clean', 'suspicious', 'blocked', 'reviewed', 'cleared'])
                    ->default('clean')->after('anomaly_score');
            }
            if (!Schema::hasColumn('properties', 'is_duplicate')) {
                $table->boolean('is_duplicate')->default(false)->after('fraud_status');
            }
            if (!Schema::hasColumn('properties', 'duplicate_of')) {
                $table->unsignedBigInteger('duplicate_of')->nullable()->after('is_duplicate');
            }
            if (!Schema::hasColumn('properties', 'geocode_verified')) {
                $table->boolean('geocode_verified')->default(false)->after('duplicate_of');
            }
            if (!Schema::hasColumn('properties', 'geocode_data')) {
                $table->json('geocode_data')->nullable()->after('geocode_verified')
                    ->comment('Raw geocoding API response data');
            }
            if (!Schema::hasColumn('properties', 'address_fraud_score')) {
                $table->unsignedTinyInteger('address_fraud_score')->default(0)->after('geocode_data');
            }
        });

        // Add indexes
        Schema::table('property_media', function (Blueprint $table) {
            $table->index('image_hash', 'idx_media_image_hash');
            $table->index('fraud_score', 'idx_media_fraud_score');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->index('fraud_score', 'idx_prop_fraud_score');
            $table->index('fraud_status', 'idx_prop_fraud_status');
            $table->index('anomaly_score', 'idx_prop_anomaly_score');
        });
    }

    public function down(): void
    {
        Schema::table('property_media', function (Blueprint $table) {
            $table->dropIndex('idx_media_image_hash');
            $table->dropIndex('idx_media_fraud_score');
            $table->dropColumn(['image_hash', 'perceptual_hash', 'fraud_score', 'is_duplicate', 'duplicate_of']);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex('idx_prop_fraud_score');
            $table->dropIndex('idx_prop_fraud_status');
            $table->dropIndex('idx_prop_anomaly_score');
            $table->dropColumn([
                'fraud_score',
                'fraud_flags',
                'anomaly_score',
                'fraud_status',
                'is_duplicate',
                'duplicate_of',
                'geocode_verified',
                'geocode_data',
                'address_fraud_score'
            ]);
        });
    }
};

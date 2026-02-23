<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Performance Indexes for 100k+ Properties
 *
 * Adds composite, covering, and functional indexes on the core property
 * tables to ensure sub-100ms query times under high concurrency.
 *
 * Run once after existing tables are populated:
 *   php artisan migrate --path=database/migrations/2026_02_22_000004_add_ai_performance_indexes.php
 *
 * All indexes use IF NOT EXISTS logic via Schema::hasIndex() to be
 * idempotent and safe on incremental deployments.
 */
return new class extends Migration {
    public function up(): void
    {
        // ── properties ─────────────────────────────────────────────────────
        if (Schema::hasTable('properties')) {
            Schema::table('properties', function (Blueprint $table) {
                // Composite for the most common filter: status + listing_type + featured
                if (!$this->hasIndex('properties', 'idx_props_status_listing_featured')) {
                    $table->index(['status', 'listing_type', 'featured'], 'idx_props_status_listing_featured');
                }
                // Compound key used by recommendation engine (excludes viewed/saved IDs)
                if (!$this->hasIndex('properties', 'idx_props_status_id')) {
                    $table->index(['status', 'id'], 'idx_props_status_id');
                }
            });
        }

        // ── property_prices ────────────────────────────────────────────────
        if (Schema::hasTable('property_prices')) {
            Schema::table('property_prices', function (Blueprint $table) {
                // SmartSearchService percentile query + min/max price filter
                if (!$this->hasIndex('property_prices', 'idx_pp_price_property')) {
                    $table->index(['price', 'property_id'], 'idx_pp_price_property');
                }
            });
        }

        // ── property_locations ─────────────────────────────────────────────
        if (Schema::hasTable('property_locations')) {
            Schema::table('property_locations', function (Blueprint $table) {
                // City/district lookups in SmartSearch & RecommendationService
                if (!$this->hasIndex('property_locations', 'idx_pl_city_district')) {
                    $table->index(['city', 'district'], 'idx_pl_city_district');
                }
                if (!$this->hasIndex('property_locations', 'idx_pl_city')) {
                    $table->index('city', 'idx_pl_city');
                }
            });
        }

        // ── property_details ───────────────────────────────────────────────
        if (Schema::hasTable('property_details')) {
            Schema::table('property_details', function (Blueprint $table) {
                // Bedroom match in recommendation + search
                if (!$this->hasIndex('property_details', 'idx_pd_bedrooms')) {
                    $table->index('bedrooms', 'idx_pd_bedrooms');
                }
            });
        }

        // ── property_views ─────────────────────────────────────────────────
        if (Schema::hasTable('property_views')) {
            Schema::table('property_views', function (Blueprint $table) {
                // RecalculatePropertyScoresJob: active users last 30 days
                if (!$this->hasIndex('property_views', 'idx_pv_user_created')) {
                    $table->index(['user_id', 'created_at'], 'idx_pv_user_created');
                }
            });
        }

        // ── ai_usage_logs ──────────────────────────────────────────────────
        if (Schema::hasTable('ai_usage_logs')) {
            Schema::table('ai_usage_logs', function (Blueprint $table) {
                // Monthly budget rollup: GROUP BY year+month
                if (!$this->hasIndex('ai_usage_logs', 'idx_aul_created_cost')) {
                    $table->index(['created_at', 'cost_usd'], 'idx_aul_created_cost');
                }
            });
        }
    }

    public function down(): void
    {
        $this->dropIndexIfExists('properties', 'idx_props_status_listing_featured');
        $this->dropIndexIfExists('properties', 'idx_props_status_id');
        $this->dropIndexIfExists('property_prices', 'idx_pp_price_property');
        $this->dropIndexIfExists('property_locations', 'idx_pl_city_district');
        $this->dropIndexIfExists('property_locations', 'idx_pl_city');
        $this->dropIndexIfExists('property_details', 'idx_pd_bedrooms');
        $this->dropIndexIfExists('property_views', 'idx_pv_user_created');
        $this->dropIndexIfExists('ai_usage_logs', 'idx_aul_created_cost');
    }

    // ─── Helpers ───────────────────────────────────────────────────────────

    private function hasIndex(string $table, string $indexName): bool
    {
        try {
            $indexes = \Illuminate\Support\Facades\DB::select(
                "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
                [$indexName]
            );
            return !empty($indexes);
        } catch (\Throwable) {
            return false;
        }
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (Schema::hasTable($table) && $this->hasIndex($table, $indexName)) {
            Schema::table($table, fn($t) => $t->dropIndex($indexName));
        }
    }
};

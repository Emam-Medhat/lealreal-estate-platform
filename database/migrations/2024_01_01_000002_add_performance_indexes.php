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
        $indexes = [
            'users' => [
                'idx_users_user_type' => ['user_type'],
                'idx_users_account_status' => ['account_status'],
                'idx_users_kyc_status' => ['kyc_status'],
                'idx_users_is_agent' => ['is_agent'],
                'idx_users_is_company' => ['is_company'],
                'idx_users_is_developer' => ['is_developer'],
                'idx_users_is_investor' => ['is_investor'],
                'idx_users_status_type' => ['account_status', 'user_type'],
                'idx_users_status_created' => ['account_status', 'created_at'],
                'idx_users_type_created' => ['user_type', 'created_at'],
                'idx_users_kyc_status_composite' => ['kyc_status', 'account_status'], // Renamed to avoid collision
                'idx_users_email' => ['email'],
                'idx_users_phone' => ['phone'],
                'idx_users_name' => ['first_name', 'last_name'],
                'idx_users_location' => ['country', 'city'],
                'idx_users_last_login' => ['last_login_at'],
                'idx_users_created' => ['created_at'],
                'idx_users_updated' => ['updated_at'],
            ],
            'properties' => [
                'idx_properties_type' => ['property_type_id'],
                'idx_properties_agent' => ['agent_id'],
                'idx_properties_listing_type' => ['listing_type'],
                'idx_properties_status' => ['status'],
                'idx_properties_featured' => ['featured'],
                'idx_properties_premium' => ['premium'],
                'idx_properties_status_featured' => ['status', 'featured'],
                'idx_properties_listing_status' => ['listing_type', 'status'],
                'idx_properties_type_listing' => ['property_type_id', 'listing_type'],
                'idx_properties_agent_status' => ['agent_id', 'status'],
                'idx_properties_featured_created' => ['featured', 'created_at'],
                'idx_properties_title' => ['title'],
                'idx_properties_price' => ['price'],
                'idx_properties_location' => ['city', 'state'],
                'idx_properties_rooms' => ['bedrooms', 'bathrooms'],
                'idx_properties_area' => ['area'],
                'idx_properties_views' => ['views_count'],
                'idx_properties_created' => ['created_at'],
                'idx_properties_updated' => ['updated_at'],
            ],
            'leads' => [
                'idx_leads_status' => ['lead_status'],
                'idx_leads_priority' => ['priority'],
                'idx_leads_source' => ['source_id'],
                'idx_leads_assigned' => ['assigned_to'],
                'idx_leads_created_by' => ['created_by'],
                'idx_leads_status_priority' => ['lead_status', 'priority'],
                'idx_leads_status_assigned' => ['lead_status', 'assigned_to'],
                'idx_leads_priority_created' => ['priority', 'created_at'],
                'idx_leads_source_created' => ['source_id', 'created_at'],
                'idx_leads_name' => ['first_name', 'last_name'],
                'idx_leads_email' => ['email'],
            ],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            if (Schema::hasTable($table)) {
                // Check outside the closure
                foreach ($tableIndexes as $indexName => $columns) {
                    if (!Schema::hasIndex($table, $indexName)) {
                        // Check if all columns exist
                        $allColumnsExist = true;
                        foreach ($columns as $column) {
                            if (!Schema::hasColumn($table, $column)) {
                                $allColumnsExist = false;
                                break;
                            }
                        }

                        if ($allColumnsExist) {
                            Schema::table($table, function (Blueprint $tableObj) use ($indexName, $columns) {
                                $tableObj->index($columns, $indexName);
                            });
                        }
                    }
                }
            }
        }

        // Full-text indexes
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            if (Schema::hasTable('users') && !Schema::hasIndex('users', 'users_fulltext_search')) {
                 if (Schema::hasColumn('users', 'first_name') && Schema::hasColumn('users', 'last_name') && Schema::hasColumn('users', 'full_name') && Schema::hasColumn('users', 'email')) {
                    Schema::table('users', function (Blueprint $table) {
                        $table->fullText(['first_name', 'last_name', 'full_name', 'email'], 'users_fulltext_search');
                    });
                 }
            }

            if (Schema::hasTable('properties') && !Schema::hasIndex('properties', 'properties_fulltext_search')) {
                if (Schema::hasColumn('properties', 'title') && Schema::hasColumn('properties', 'description')) {
                    Schema::table('properties', function (Blueprint $table) {
                        $table->fullText(['title', 'description'], 'properties_fulltext_search');
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

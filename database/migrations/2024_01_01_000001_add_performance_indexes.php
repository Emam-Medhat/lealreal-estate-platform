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
            'leads' => [
                'leads_status_index' => ['lead_status'],
                'leads_priority_index' => ['priority'],
                'leads_assigned_to_index' => ['assigned_to'],
                'leads_created_by_index' => ['created_by'],
                'leads_source_index' => ['lead_source'],
                'leads_status_priority_index' => ['lead_status', 'priority'],
                'leads_status_assigned_index' => ['lead_status', 'assigned_to'],
                'leads_priority_created_index' => ['priority', 'created_at'],
                'leads_assigned_created_index' => ['assigned_to', 'created_at'],
                'leads_name_index' => ['first_name', 'last_name'],
                'leads_email_index' => ['email'],
                'leads_phone_index' => ['phone'],
                'leads_company_index' => ['company'],
                'leads_created_at_index' => ['created_at'],
                'leads_updated_at_index' => ['updated_at'],
                'leads_last_contacted_index' => ['last_contacted_at'],
                'leads_follow_up_index' => ['next_follow_up_at'],
                'leads_estimated_value_index' => ['estimated_value'],
                'leads_conversion_index' => ['conversion_probability'],
                'leads_score_index' => ['score'],
                'leads_temperature_index' => ['temperature'],
            ],
            'users' => [
                'users_role_index' => ['role'],
                'users_type_index' => ['user_type'],
                'users_status_index' => ['account_status'],
                'users_agent_index' => ['is_agent'],
                'users_company_index' => ['is_company'],
                'users_created_at_index' => ['created_at'],
                'users_last_login_index' => ['last_login_at'],
                'users_email_index' => ['email'],
                'users_phone_index' => ['phone'],
            ],
            'properties' => [
                'properties_status_index' => ['status'],
                'properties_type_index' => ['type'],
                'properties_agent_index' => ['agent_id'],
                'properties_company_index' => ['company_id'],
                'properties_city_index' => ['city'],
                'properties_state_index' => ['state'],
                'properties_price_index' => ['price'],
                'properties_created_at_index' => ['created_at'],
                'properties_featured_index' => ['featured'],
                'properties_status_type_index' => ['status', 'type'],
                'properties_city_type_index' => ['city', 'type'],
                'properties_price_status_index' => ['price', 'status'],
            ],
            'lead_activities' => [
                'lead_activities_lead_index' => ['lead_id'],
                'lead_activities_user_index' => ['user_id'],
                'lead_activities_type_index' => ['type'],
                'lead_activities_created_index' => ['created_at'],
                'lead_activities_lead_created_index' => ['lead_id', 'created_at'],
                'lead_activities_user_created_index' => ['user_id', 'created_at'],
            ],
            'reports' => [
                'reports_type_index' => ['type'],
                'reports_status_index' => ['status'],
                'reports_generated_by_index' => ['generated_by'],
                'reports_created_at_index' => ['created_at'],
                'reports_type_status_index' => ['type', 'status'],
            ],
            'appointments' => [
                'appointments_agent_index' => ['agent_id'],
                'appointments_client_index' => ['client_id'],
                'appointments_property_index' => ['property_id'],
            ],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableObj) use ($table, $tableIndexes) {
                    foreach ($tableIndexes as $indexName => $columns) {
                        // We cannot use Schema::hasIndex inside the closure easily for the same table connection
                        // But we can catch the exception or use a raw check if needed.
                        // Ideally, we should check outside.
                    }
                });
                
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't remove indexes in down to avoid removing ones that existed before
        // or we can list them out. For now, leave empty or basic.
    }
};

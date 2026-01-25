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
        Schema::table('agent_profiles', function (Blueprint $table) {
            // Unify specialties/specializations to specializations
            if (Schema::hasColumn('agent_profiles', 'specialties')) {
                $table->renameColumn('specialties', 'specializations');
            } elseif (!Schema::hasColumn('agent_profiles', 'specializations')) {
                $table->json('specializations')->nullable();
            }

            // Add other missing fields from AgentProfile model
            $fields = [
                'photo' => 'string',
                'phone' => 'string',
                'email' => 'string',
                'address' => 'string',
                'city' => 'string',
                'state' => 'string',
                'country' => 'string',
                'postal_code' => 'string',
                'social_media' => 'json',
                'languages' => 'json',
                'achievements' => 'json',
                'education' => 'json',
                'experience' => 'json',
                'awards' => 'json',
                'professional_memberships' => 'json',
                'areas_of_expertise' => 'json',
                'personal_statement' => 'text',
                'company_name' => 'string',
                'company_logo' => 'string',
                'company_description' => 'text',
                'office_address' => 'string',
                'office_phone' => 'string',
                'office_email' => 'string',
                'working_hours' => 'json',
                'response_time' => 'string',
                'preferred_contact_method' => 'string',
                'additional_info' => 'text',
                'service_areas' => 'json',
            ];

            foreach ($fields as $column => $type) {
                if (!Schema::hasColumn('agent_profiles', $column)) {
                    if ($type === 'string') $table->string($column)->nullable();
                    elseif ($type === 'text') $table->text($column)->nullable();
                    elseif ($type === 'json') $table->json($column)->nullable();
                }
            }
            
            // Convert existing columns to json if they are not
            // Note: This might need manual data migration if there's existing data
            // $table->json('specializations')->change();
            // $table->json('languages')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_profiles', function (Blueprint $table) {
            $table->renameColumn('specializations', 'specialties');
            $table->dropColumn([
                'photo', 'phone', 'email', 'address', 'city', 'state', 'country', 'postal_code',
                'social_media', 'education', 'experience', 'awards',
                'professional_memberships', 'areas_of_expertise', 'personal_statement',
                'company_name', 'company_logo', 'company_description', 'office_email',
                'working_hours', 'response_time', 'preferred_contact_method', 'additional_info',
                'service_areas'
            ]);
        });
    }
};

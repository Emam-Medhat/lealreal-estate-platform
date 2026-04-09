<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Columns already exist in the database or table is too large to add more.
        // Skipping addition to avoid 'Row size too large' error.
    }

    public function down()
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn([
                'deleted_at', 'description', 'website', 'city', 'country', 'logo', 
                'established_year', 'number_of_agents', 'specializations', 'coverage_areas', 
                'commission_rates', 'is_verified', 'is_active', 'rating', 'total_sales', 
                'total_properties', 'social_media', 'contact_person', 'contact_person_title', 
                'contact_person_phone', 'contact_person_email', 'office_hours', 
                'languages_spoken', 'awards_certifications', 'extra_attributes'
            ]);
        });
    }
};

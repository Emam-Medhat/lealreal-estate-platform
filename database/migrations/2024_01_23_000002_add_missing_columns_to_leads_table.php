<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            // Add deleted_at column for soft deletes if it doesn't exist
            if (!Schema::hasColumn('leads', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable()->after('updated_at');
            }
            
            // Add missing columns that are in the Lead model fillable but not in the migration
            if (!Schema::hasColumn('leads', 'uuid')) {
                $table->string('uuid')->unique()->after('id');
            }
            
            if (!Schema::hasColumn('leads', 'full_name')) {
                $table->string('full_name')->nullable()->after('last_name');
            }
            
            if (!Schema::hasColumn('leads', 'whatsapp')) {
                $table->string('whatsapp')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('leads', 'job_title')) {
                $table->string('job_title')->nullable()->after('position');
            }
            
            if (!Schema::hasColumn('leads', 'lead_source')) {
                $table->string('lead_source')->nullable()->after('status_id');
            }
            
            if (!Schema::hasColumn('leads', 'lead_status')) {
                $table->string('lead_status')->default('new')->after('lead_source');
            }
            
            if (!Schema::hasColumn('leads', 'lead_type')) {
                $table->string('lead_type')->nullable()->after('lead_status');
            }
            
            if (!Schema::hasColumn('leads', 'budget_min')) {
                $table->decimal('budget_min', 15, 2)->nullable()->after('estimated_value');
            }
            
            if (!Schema::hasColumn('leads', 'budget_max')) {
                $table->decimal('budget_max', 15, 2)->nullable()->after('budget_min');
            }
            
            if (!Schema::hasColumn('leads', 'currency')) {
                $table->string('currency')->default('USD')->after('budget_max');
            }
            
            if (!Schema::hasColumn('leads', 'preferred_property_types')) {
                $table->json('preferred_property_types')->nullable()->after('currency');
            }
            
            if (!Schema::hasColumn('leads', 'preferred_locations')) {
                $table->json('preferred_locations')->nullable()->after('preferred_property_types');
            }
            
            if (!Schema::hasColumn('leads', 'preferred_bedrooms')) {
                $table->integer('preferred_bedrooms')->nullable()->after('preferred_locations');
            }
            
            if (!Schema::hasColumn('leads', 'preferred_bathrooms')) {
                $table->integer('preferred_bathrooms')->nullable()->after('preferred_bedrooms');
            }
            
            if (!Schema::hasColumn('leads', 'preferred_area_min')) {
                $table->decimal('preferred_area_min', 10, 2)->nullable()->after('preferred_bathrooms');
            }
            
            if (!Schema::hasColumn('leads', 'preferred_area_max')) {
                $table->decimal('preferred_area_max', 10, 2)->nullable()->after('preferred_area_min');
            }
            
            if (!Schema::hasColumn('leads', 'preferred_price_min')) {
                $table->decimal('preferred_price_min', 15, 2)->nullable()->after('preferred_area_max');
            }
            
            if (!Schema::hasColumn('leads', 'preferred_price_max')) {
                $table->decimal('preferred_price_max', 15, 2)->nullable()->after('preferred_price_min');
            }
            
            if (!Schema::hasColumn('leads', 'preferred_amenities')) {
                $table->json('preferred_amenities')->nullable()->after('preferred_price_max');
            }
            
            if (!Schema::hasColumn('leads', 'timeline')) {
                $table->string('timeline')->nullable()->after('preferred_amenities');
            }
            
            if (!Schema::hasColumn('leads', 'financing_status')) {
                $table->string('financing_status')->nullable()->after('timeline');
            }
            
            if (!Schema::hasColumn('leads', 'pre_approved')) {
                $table->boolean('pre_approved')->default(false)->after('financing_status');
            }
            
            if (!Schema::hasColumn('leads', 'property_purpose')) {
                $table->string('property_purpose')->nullable()->after('pre_approved');
            }
            
            if (!Schema::hasColumn('leads', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->after('is_active');
            }
            
            if (!Schema::hasColumn('leads', 'last_contacted_at')) {
                $table->timestamp('last_contacted_at')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('leads', 'next_follow_up_at')) {
                $table->timestamp('next_follow_up_at')->nullable()->after('last_contacted_at');
            }
            
            if (!Schema::hasColumn('leads', 'follow_up_count')) {
                $table->integer('follow_up_count')->default(0)->after('next_follow_up_at');
            }
            
            if (!Schema::hasColumn('leads', 'conversion_probability')) {
                $table->decimal('conversion_probability', 5, 2)->nullable()->after('follow_up_count');
            }
            
            if (!Schema::hasColumn('leads', 'estimated_value')) {
                $table->decimal('estimated_value', 15, 2)->nullable()->after('priority');
            }
            
            if (!Schema::hasColumn('leads', 'lost_reason')) {
                $table->text('lost_reason')->nullable()->after('conversion_probability');
            }
            
            if (!Schema::hasColumn('leads', 'won_reason')) {
                $table->text('won_reason')->nullable()->after('lost_reason');
            }
            
            if (!Schema::hasColumn('leads', 'converted_at')) {
                $table->timestamp('converted_at')->nullable()->after('won_reason');
            }
            
            if (!Schema::hasColumn('leads', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('converted_at');
            }
            
            if (!Schema::hasColumn('leads', 'score')) {
                $table->integer('score')->nullable()->after('archived_at');
            }
            
            if (!Schema::hasColumn('leads', 'temperature')) {
                $table->integer('temperature')->nullable()->after('score');
            }
            
            if (!Schema::hasColumn('leads', 'stage')) {
                $table->string('stage')->nullable()->after('temperature');
            }
            
            if (!Schema::hasColumn('leads', 'pipeline_position')) {
                $table->integer('pipeline_position')->nullable()->after('stage');
            }
            
            if (!Schema::hasColumn('leads', 'contact_frequency')) {
                $table->integer('contact_frequency')->nullable()->after('pipeline_position');
            }
            
            if (!Schema::hasColumn('leads', 'response_rate')) {
                $table->decimal('response_rate', 5, 2)->nullable()->after('contact_frequency');
            }
            
            if (!Schema::hasColumn('leads', 'engagement_score')) {
                $table->integer('engagement_score')->nullable()->after('response_rate');
            }
            
            if (!Schema::hasColumn('leads', 'activity_score')) {
                $table->integer('activity_score')->nullable()->after('engagement_score');
            }
            
            if (!Schema::hasColumn('leads', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('activity_score');
            }
            
            if (!Schema::hasColumn('leads', 'first_contact_at')) {
                $table->timestamp('first_contact_at')->nullable()->after('last_activity_at');
            }
            
            if (!Schema::hasColumn('leads', 'days_in_pipeline')) {
                $table->integer('days_in_pipeline')->nullable()->after('first_contact_at');
            }
            
            if (!Schema::hasColumn('leads', 'contact_attempts')) {
                $table->integer('contact_attempts')->default(0)->after('days_in_pipeline');
            }
            
            if (!Schema::hasColumn('leads', 'email_opens')) {
                $table->integer('email_opens')->default(0)->after('contact_attempts');
            }
            
            if (!Schema::hasColumn('leads', 'email_clicks')) {
                $table->integer('email_clicks')->default(0)->after('email_opens');
            }
            
            if (!Schema::hasColumn('leads', 'website_visits')) {
                $table->integer('website_visits')->default(0)->after('email_clicks');
            }
            
            if (!Schema::hasColumn('leads', 'form_submissions')) {
                $table->integer('form_submissions')->default(0)->after('website_visits');
            }
            
            if (!Schema::hasColumn('leads', 'call_count')) {
                $table->integer('call_count')->default(0)->after('form_submissions');
            }
            
            if (!Schema::hasColumn('leads', 'meeting_count')) {
                $table->integer('meeting_count')->default(0)->after('call_count');
            }
            
            if (!Schema::hasColumn('leads', 'property_viewings')) {
                $table->integer('property_viewings')->default(0)->after('meeting_count');
            }
            
            if (!Schema::hasColumn('leads', 'offer_count')) {
                $table->integer('offer_count')->default(0)->after('property_viewings');
            }
            
            if (!Schema::hasColumn('leads', 'referral_source')) {
                $table->string('referral_source')->nullable()->after('offer_count');
            }
            
            if (!Schema::hasColumn('leads', 'campaign_source')) {
                $table->string('campaign_source')->nullable()->after('referral_source');
            }
            
            if (!Schema::hasColumn('leads', 'medium')) {
                $table->string('medium')->nullable()->after('campaign_source');
            }
            
            if (!Schema::hasColumn('leads', 'content')) {
                $table->string('content')->nullable()->after('medium');
            }
            
            if (!Schema::hasColumn('leads', 'term')) {
                $table->string('term')->nullable()->after('content');
            }
            
            if (!Schema::hasColumn('leads', 'utm_source')) {
                $table->string('utm_source')->nullable()->after('term');
            }
            
            if (!Schema::hasColumn('leads', 'utm_medium')) {
                $table->string('utm_medium')->nullable()->after('utm_source');
            }
            
            if (!Schema::hasColumn('leads', 'utm_campaign')) {
                $table->string('utm_campaign')->nullable()->after('utm_medium');
            }
            
            if (!Schema::hasColumn('leads', 'utm_content')) {
                $table->string('utm_content')->nullable()->after('utm_campaign');
            }
            
            if (!Schema::hasColumn('leads', 'utm_term')) {
                $table->string('utm_term')->nullable()->after('utm_content');
            }
            
            if (!Schema::hasColumn('leads', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('utm_term');
            }
            
            if (!Schema::hasColumn('leads', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            
            if (!Schema::hasColumn('leads', 'device_type')) {
                $table->string('device_type')->nullable()->after('user_agent');
            }
            
            if (!Schema::hasColumn('leads', 'browser')) {
                $table->string('browser')->nullable()->after('device_type');
            }
            
            if (!Schema::hasColumn('leads', 'location')) {
                $table->string('location')->nullable()->after('browser');
            }
            
            if (!Schema::hasColumn('leads', 'language')) {
                $table->string('language')->nullable()->after('location');
            }
            
            if (!Schema::hasColumn('leads', 'timezone')) {
                $table->string('timezone')->nullable()->after('language');
            }
            
            if (!Schema::hasColumn('leads', 'gdpr_consent')) {
                $table->boolean('gdpr_consent')->default(false)->after('timezone');
            }
            
            if (!Schema::hasColumn('leads', 'marketing_consent')) {
                $table->boolean('marketing_consent')->default(false)->after('gdpr_consent');
            }
            
            if (!Schema::hasColumn('leads', 'data_privacy_accepted')) {
                $table->boolean('data_privacy_accepted')->default(false)->after('marketing_consent');
            }
            
            if (!Schema::hasColumn('leads', 'communication_preferences')) {
                $table->json('communication_preferences')->nullable()->after('data_privacy_accepted');
            }
            
            if (!Schema::hasColumn('leads', 'opt_out_date')) {
                $table->timestamp('opt_out_date')->nullable()->after('communication_preferences');
            }
            
            if (!Schema::hasColumn('leads', 'social_profiles')) {
                $table->json('social_profiles')->nullable()->after('opt_out_date');
            }
            
            if (!Schema::hasColumn('leads', 'company_info')) {
                $table->json('company_info')->nullable()->after('social_profiles');
            }
            
            if (!Schema::hasColumn('leads', 'decision_maker')) {
                $table->boolean('decision_maker')->default(false)->after('company_info');
            }
            
            if (!Schema::hasColumn('leads', 'influencers')) {
                $table->json('influencers')->nullable()->after('decision_maker');
            }
            
            if (!Schema::hasColumn('leads', 'competitors')) {
                $table->json('competitors')->nullable()->after('influencers');
            }
            
            if (!Schema::hasColumn('leads', 'pain_points')) {
                $table->json('pain_points')->nullable()->after('competitors');
            }
            
            if (!Schema::hasColumn('leads', 'objections')) {
                $table->json('objections')->nullable()->after('pain_points');
            }
            
            if (!Schema::hasColumn('leads', 'motivations')) {
                $table->json('motivations')->nullable()->after('objections');
            }
            
            if (!Schema::hasColumn('leads', 'goals')) {
                $table->json('goals')->nullable()->after('motivations');
            }
            
            if (!Schema::hasColumn('leads', 'requirements')) {
                $table->json('requirements')->nullable()->after('goals');
            }
            
            if (!Schema::hasColumn('leads', 'constraints')) {
                $table->json('constraints')->nullable()->after('requirements');
            }
            
            if (!Schema::hasColumn('leads', 'decision_criteria')) {
                $table->json('decision_criteria')->nullable()->after('constraints');
            }
            
            if (!Schema::hasColumn('leads', 'evaluation_process')) {
                $table->json('evaluation_process')->nullable()->after('decision_criteria');
            }
            
            if (!Schema::hasColumn('leads', 'timeline_details')) {
                $table->json('timeline_details')->nullable()->after('evaluation_process');
            }
            
            if (!Schema::hasColumn('leads', 'budget_details')) {
                $table->json('budget_details')->nullable()->after('timeline_details');
            }
            
            if (!Schema::hasColumn('leads', 'stakeholder_analysis')) {
                $table->json('stakeholder_analysis')->nullable()->after('budget_details');
            }
            
            if (!Schema::hasColumn('leads', 'risk_factors')) {
                $table->json('risk_factors')->nullable()->after('stakeholder_analysis');
            }
            
            if (!Schema::hasColumn('leads', 'opportunity_strength')) {
                $table->json('opportunity_strength')->nullable()->after('risk_factors');
            }
            
            if (!Schema::hasColumn('leads', 'relationship_strength')) {
                $table->json('relationship_strength')->nullable()->after('opportunity_strength');
            }
            
            if (!Schema::hasColumn('leads', 'trust_level')) {
                $table->json('trust_level')->nullable()->after('relationship_strength');
            }
            
            if (!Schema::hasColumn('leads', 'credibility_score')) {
                $table->json('credibility_score')->nullable()->after('trust_level');
            }
            
            if (!Schema::hasColumn('leads', 'custom_fields')) {
                $table->json('custom_fields')->nullable()->after('credibility_score');
            }
            
            if (!Schema::hasColumn('leads', 'metadata')) {
                $table->json('metadata')->nullable()->after('custom_fields');
            }
            
            if (!Schema::hasColumn('leads', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('metadata');
            }
            
            if (!Schema::hasColumn('leads', 'private_notes')) {
                $table->text('private_notes')->nullable()->after('internal_notes');
            }
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            // Drop all the columns that were added
            $columns = [
                'deleted_at', 'uuid', 'full_name', 'whatsapp', 'job_title', 'lead_source', 'lead_status', 'lead_type',
                'budget_min', 'budget_max', 'currency', 'preferred_property_types', 'preferred_locations', 'preferred_bedrooms',
                'preferred_bathrooms', 'preferred_area_min', 'preferred_area_max', 'preferred_price_min', 'preferred_price_max',
                'preferred_amenities', 'timeline', 'financing_status', 'pre_approved', 'property_purpose', 'created_by',
                'last_contacted_at', 'next_follow_up_at', 'follow_up_count', 'conversion_probability', 'estimated_value',
                'lost_reason', 'won_reason', 'converted_at', 'archived_at', 'score', 'temperature', 'stage',
                'pipeline_position', 'contact_frequency', 'response_rate', 'engagement_score', 'activity_score',
                'last_activity_at', 'first_contact_at', 'days_in_pipeline', 'contact_attempts', 'email_opens',
                'email_clicks', 'website_visits', 'form_submissions', 'call_count', 'meeting_count', 'property_viewings',
                'offer_count', 'referral_source', 'campaign_source', 'medium', 'content', 'term', 'utm_source',
                'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'ip_address', 'user_agent', 'device_type',
                'browser', 'location', 'language', 'timezone', 'gdpr_consent', 'marketing_consent', 'data_privacy_accepted',
                'communication_preferences', 'opt_out_date', 'social_profiles', 'company_info', 'decision_maker',
                'influencers', 'competitors', 'pain_points', 'objections', 'motivations', 'goals', 'requirements',
                'constraints', 'decision_criteria', 'evaluation_process', 'timeline_details', 'budget_details',
                'stakeholder_analysis', 'risk_factors', 'opportunity_strength', 'relationship_strength', 'trust_level',
                'credibility_score', 'custom_fields', 'metadata', 'internal_notes', 'private_notes'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('leads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeveloperProfile extends Model
{
    /** @use HasFactory<\Database\Factories\DeveloperProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'developer_id',
        'company_name_ar',
        'about_us',
        'about_us_ar',
        'vision',
        'vision_ar',
        'mission',
        'mission_ar',
        'values',
        'established_year',
        'employees_count',
        'engineers_count',
        'headquarters_address',
        'branches',
        'services',
        'expertise_areas',
        'awards',
        'partners',
        'banking_partners',
        'insurance_partners',
        'legal_partners',
        'media_gallery',
        'company_documents',
        'financial_statements',
        'project_portfolio',
        'certifications_details',
        'quality_standards',
        'sustainability_initiatives',
        'technology_stack',
        'design_philosophy',
        'construction_methods',
        'materials_preference',
        'cover_image',
        'video_presentation',
        'social_media_links',
        'contact_information',
        'working_hours',
        'languages_supported',
        'payment_methods',
        'warranty_policies',
        'after_sales_service',
        'maintenance_services',
        'show_contact_form',
        'enable_chat_support',
        'allow_online_booking',
        'seo_settings',
        'privacy_settings',
        'notification_preferences',
    ];

    protected $casts = [
        'headquarters_address' => 'array',
        'branches' => 'array',
        'services' => 'array',
        'expertise_areas' => 'array',
        'awards' => 'array',
        'partners' => 'array',
        'banking_partners' => 'array',
        'insurance_partners' => 'array',
        'legal_partners' => 'array',
        'media_gallery' => 'array',
        'company_documents' => 'array',
        'financial_statements' => 'array',
        'project_portfolio' => 'array',
        'certifications_details' => 'array',
        'quality_standards' => 'array',
        'sustainability_initiatives' => 'array',
        'technology_stack' => 'array',
        'design_philosophy' => 'array',
        'construction_methods' => 'array',
        'materials_preference' => 'array',
        'social_media_links' => 'array',
        'contact_information' => 'array',
        'working_hours' => 'array',
        'languages_supported' => 'array',
        'payment_methods' => 'array',
        'warranty_policies' => 'array',
        'after_sales_service' => 'array',
        'maintenance_services' => 'array',
        'seo_settings' => 'array',
        'privacy_settings' => 'array',
        'notification_preferences' => 'array',
        'show_contact_form' => 'boolean',
        'enable_chat_support' => 'boolean',
        'allow_online_booking' => 'boolean',
        'established_year' => 'integer',
        'employees_count' => 'integer',
        'engineers_count' => 'integer',
    ];

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }
}

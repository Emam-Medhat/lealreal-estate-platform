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
        Schema::create('users', function (Blueprint $table) {
            // المعرفات الأساسية
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            
            // بيانات الحساب الأساسية
            $table->string('username', 50)->unique();
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone', 20)->unique()->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password', 255);
            
            // المعلومات الشخصية
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('full_name', 200)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            
            // نوع المستخدم والصلاحيات
            $table->enum('user_type', ['user', 'agent', 'company', 'developer', 'investor', 'admin', 'super_admin'])->default('user');
            $table->enum('account_status', ['active', 'inactive', 'suspended', 'banned', 'pending_verification'])->default('pending_verification');
            
            // معلومات الموقع والعنوان
            $table->string('country_code', 2)->nullable(); // ISO 3166-1 alpha-2
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('timezone', 50)->default('UTC');
            
            // معلومات الاتصال الإضافية
            $table->string('whatsapp', 20)->nullable();
            $table->string('telegram', 100)->nullable();
            $table->string('website', 255)->nullable();
            
            // تفضيلات اللغة والعملة
            $table->string('language', 5)->default('en'); // en, ar, fr, etc
            $table->string('currency', 3)->default('USD'); // USD, EUR, EGP, etc
            
            // الصورة الشخصية
            $table->string('avatar', 255)->nullable();
            $table->string('avatar_thumbnail', 255)->nullable();
            
            // معلومات الهوية والوثائق
            $table->string('profile_image', 255)->nullable(); // صورة الملف الشخصي
            $table->string('cover_image', 255)->nullable(); // صورة الغلاف
            $table->string('id_document_front', 255)->nullable(); // صورة الوثاقة الأمامية
            $table->string('id_document_back', 255)->nullable(); // صورة الوثاقة الخلفية
            $table->string('passport_photo', 255)->nullable(); // صورة جواز السفر
            $table->string('selfie_with_id', 255)->nullable(); // صورة سيلفي مع الوثاقة
            $table->string('company_logo', 255)->nullable(); // شعار الشركة
            $table->string('commercial_register', 255)->nullable(); // صورة السجل التجاري
            $table->string('tax_card', 255)->nullable(); // صورة البطاقة الضريبية
            
            // الاشتراك والعضوية
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->enum('subscription_status', ['free', 'trial', 'active', 'expired', 'cancelled'])->default('free');
            $table->timestamp('subscription_start_date')->nullable();
            $table->timestamp('subscription_end_date')->nullable();
            
            // التحقق من الهوية (KYC)
            $table->enum('kyc_status', ['not_submitted', 'pending', 'verified', 'rejected'])->default('not_submitted');
            $table->timestamp('kyc_verified_at')->nullable();
            $table->enum('id_document_type', ['passport', 'national_id', 'driving_license'])->nullable();
            $table->string('id_document_number', 100)->nullable();
            
            // المحفظة المالية
            $table->decimal('wallet_balance', 15, 2)->default(0.00);
            $table->string('wallet_currency', 3)->default('USD');
            
            // تفضيلات العقارات (للبحث والتوصيات)
            $table->json('property_preferences')->nullable(); // {property_types: [], min_price: 0, max_price: 0, locations: [], bedrooms: 0, etc}
            $table->integer('saved_searches_count')->default(0);
            $table->integer('favorites_count')->default(0);
            
            // الأمان والمصادقة
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret', 255)->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->boolean('biometric_enabled')->default(false);
            
            // معلومات تسجيل الدخول
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->string('last_login_device', 255)->nullable();
            $table->integer('login_count')->default(0);
            
            // معلومات الجلسة
            $table->rememberToken();
            $table->string('api_token', 80)->unique()->nullable();
            
            // الإحصائيات
            $table->integer('properties_count')->default(0); // عدد العقارات المضافة
            $table->integer('properties_views_count')->default(0); // عدد مشاهدات عقاراته
            $table->integer('leads_count')->default(0); // عدد العملاء المحتملين
            $table->integer('transactions_count')->default(0); // عدد المعاملات
            $table->integer('reviews_count')->default(0); // عدد التقييمات
            $table->decimal('average_rating', 3, 2)->default(0.00); // متوسط التقييم (0.00 to 5.00)
            
            // معلومات الإحالة والتسويق
            $table->string('referral_code', 20)->unique()->nullable(); // كود الإحالة الخاص
            $table->unsignedBigInteger('referred_by_user_id')->nullable(); // من أحاله
            $table->integer('referral_count')->default(0); // عدد من أحالهم
            $table->decimal('referral_earnings', 15, 2)->default(0.00); // أرباح الإحالة
            
            // معلومات التسويق
            $table->boolean('marketing_consent')->default(false); // موافقة على التسويق
            $table->boolean('newsletter_subscribed')->default(false); // اشتراك في النشرة
            
            // معلومات الوكيل (إذا كان وكيل عقاري)
            $table->boolean('is_agent')->default(false);
            $table->string('agent_license_number', 100)->nullable();
            $table->date('agent_license_expiry')->nullable();
            $table->string('agent_company', 255)->nullable();
            $table->text('agent_bio')->nullable();
            $table->json('agent_specializations')->nullable(); // ['residential', 'commercial', 'luxury']
            $table->json('agent_service_areas')->nullable(); // ['Cairo', 'Alexandria']
            $table->decimal('agent_commission_rate', 5, 2)->nullable(); // نسبة العمولة
            $table->integer('properties_listed')->default(0); // عدد العقارات المعلنة
            $table->integer('properties_sold')->default(0); // عدد العقارات المباعة
            $table->integer('properties_rented')->default(0); // عدد العقارات المؤجرة
            $table->decimal('total_commission_earned', 15, 2)->default(0.00); // إجمالي العمولات المكتسبة
            $table->decimal('average_response_time', 5, 2)->nullable(); // متوسط وقت الاستجابة بالساعات
            $table->integer('client_count')->default(0); // عدد العملاء
            $table->decimal('client_satisfaction_rate', 3, 2)->default(0.00); // معدل رضا العملاء
            
            // معلومات الشركة (إذا كان شركة عقارية)
            $table->boolean('is_company')->default(false);
            $table->unsignedBigInteger('company_id')->nullable(); // ربط بجدول الشركات
            $table->string('company_role', 50)->nullable(); // owner, admin, manager, agent
            $table->string('company_registration_number', 100)->nullable(); // رقم تسجيل الشركة
            $table->date('company_established_date')->nullable(); // تاريخ تأسيس الشركة
            $table->integer('company_employees_count')->default(0); // عدد موظفي الشركة
            $table->string('company_headquarters', 255)->nullable(); // المقر الرئيسي للشركة
            $table->json('company_branches')->nullable(); // فروع الشركة
            $table->decimal('company_annual_revenue', 15, 2)->nullable(); // الإيرادات السنوية للشركة
            
            // معلومات المطور (إذا كان مطور عقاري)
            $table->boolean('is_developer')->default(false);
            $table->unsignedBigInteger('developer_id')->nullable();
            $table->string('developer_certification', 255)->nullable();
            $table->string('developer_license_number', 100)->nullable(); // رخصة المطور
            $table->date('developer_license_expiry')->nullable(); // انتهاء رخصة المطور
            $table->integer('projects_completed')->default(0); // عدد المشاريع المكتملة
            $table->integer('projects_ongoing')->default(0); // عدد المشاريع الجارية
            $table->decimal('total_units_built', 15, 2)->default(0.00); // إجمالي الوحدات المبنية
            $table->json('developer_specializations')->nullable(); // تخصصات المطور
            
            // معلومات المستثمر (إذا كان مستثمر)
            $table->boolean('is_investor')->default(false);
            $table->enum('investor_type', ['individual', 'institutional', 'fund'])->nullable();
            $table->decimal('investment_portfolio_value', 15, 2)->nullable();
            $table->decimal('minimum_investment_amount', 15, 2)->nullable(); // الحد الأدنى للاستثمار
            $table->decimal('maximum_investment_amount', 15, 2)->nullable(); // الحد الأقصى للاستثمار
            $table->json('investment_preferences')->nullable(); // تفضيلات الاستثمار
            $table->integer('properties_invested')->default(0); // عدد العقارات المستثمر فيها
            $table->decimal('total_investments', 15, 2)->default(0.00); // إجمالي الاستثمارات
            $table->decimal('investment_returns', 15, 2)->default(0.00); // عوائد الاستثمار
            
            // الوسائط الاجتماعية
            $table->string('facebook_url', 255)->nullable();
            $table->string('twitter_url', 255)->nullable();
            $table->string('linkedin_url', 255)->nullable();
            $table->string('instagram_url', 255)->nullable();
            $table->string('youtube_url', 255)->nullable();
            
            // تفضيلات الإشعارات
            $table->json('notifications_preferences')->nullable(); // {email: true, sms: true, push: true, property_alerts: true, etc}
            
            // البيانات الإضافية (مرنة)
            $table->json('metadata')->nullable(); // بيانات إضافية مرنة
            
            // معلومات الحظر والإيقاف
            $table->timestamp('banned_at')->nullable();
            $table->text('banned_reason')->nullable();
            $table->unsignedBigInteger('banned_by')->nullable();
            $table->timestamp('suspended_until')->nullable();
            $table->text('suspension_reason')->nullable();
            
            // معلومات إضافية لنظام العقارات
            $table->enum('preferred_property_type', ['apartment', 'villa', 'townhouse', 'penthouse', 'studio', 'duplex', 'land', 'commercial', 'office', 'retail', 'warehouse'])->nullable();
            $table->decimal('preferred_price_min', 15, 2)->nullable(); // الحد الأدنى للسعر المفضل
            $table->decimal('preferred_price_max', 15, 2)->nullable(); // الحد الأقصى للسعر المفضل
            $table->integer('preferred_bedrooms_min')->nullable(); // الحد الأدنى لعدد غرف النوم
            $table->integer('preferred_bedrooms_max')->nullable(); // الحد الأقصى لعدد غرف النوم
            $table->integer('preferred_bathrooms_min')->nullable(); // الحد الأدنى لعدد الحمامات
            $table->decimal('preferred_area_min', 8, 2)->nullable(); // الحد الأدنى للمساحة (م²)
            $table->decimal('preferred_area_max', 8, 2)->nullable(); // الحد الأقصى للمساحة (م²)
            $table->json('preferred_amenities')->nullable(); // المرافق المفضلة
            $table->json('preferred_locations')->nullable(); // المواقع المفضلة
            $table->boolean('is_first_time_buyer')->default(false); // هل هو مشتري لأول مرة
            $table->boolean('is_look_to_rent')->default(false); // هل يبحث عن إيجار
            $table->boolean('is_look_to_buy')->default(false); // هل يبحث عن شراء
            $table->enum('property_purpose', ['residential', 'commercial', 'investment', 'vacation'])->nullable(); // الغرض من العقار
            
            // التوقيعات الزمنية
            $table->timestamps();
            $table->softDeletes();
            
            // الفهارس (Indexes)
            $table->index(['email'], 'idx_email');
            $table->index(['phone'], 'idx_phone');
            $table->index(['user_type'], 'idx_user_type');
            $table->index(['account_status'], 'idx_account_status');
            $table->index(['country', 'city'], 'idx_country_city');
            $table->index(['subscription_plan_id', 'subscription_status'], 'idx_subscription');
            $table->index(['kyc_status'], 'idx_kyc_status');
            $table->index(['latitude', 'longitude'], 'idx_location');
            $table->index(['referral_code'], 'idx_referral');
            $table->index(['created_at'], 'idx_created_at');
            
            // المفاتيح الخارجية
            // $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('set null');
            // $table->foreign('referred_by_user_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            // $table->foreign('developer_id')->references('id')->on('developers')->onDelete('set null');
            // $table->foreign('banned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

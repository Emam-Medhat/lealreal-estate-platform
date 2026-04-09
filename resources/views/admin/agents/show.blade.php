@extends('admin.layouts.admin')
@section('title', 'تفاصيل الوكيل')

@section('content')
<style>
    .agent-details-page {
        background: #f8fafc;
        min-height: 100vh;
        padding: 2rem 0;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .page-header-content {
        position: relative;
        z-index: 1;
    }

    .page-title {
        color: white;
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .page-subtitle {
        color: rgba(255, 255, 255, 0.9);
        margin: 0.5rem 0 0 0;
        font-size: 0.95rem;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .btn-custom {
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-light {
        background: white;
        color: #667eea;
    }

    .btn-light:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        color: #667eea;
    }

    .btn-outline-light {
        background: transparent;
        border: 2px solid white;
        color: white;
    }

    .btn-outline-light:hover {
        background: white;
        color: #667eea;
        transform: translateY(-2px);
    }

    /* Agent Profile Card */
    .agent-profile-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
        text-align: center;
    }

    .agent-avatar-container {
        position: relative;
        display: inline-block;
        margin-bottom: 1.5rem;
    }

    .agent-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #f0f4ff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .agent-avatar-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        font-weight: 700;
        border: 5px solid #f0f4ff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .verified-badge {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: #10b981;
        color: white;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid white;
        box-shadow: 0 2px 10px rgba(16, 185, 129, 0.4);
    }

    .agent-name {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .agent-role {
        color: #64748b;
        font-size: 1rem;
        margin: 0.5rem 0 1rem 0;
    }

    .status-badges {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-verified {
        background: #dbeafe;
        color: #1e40af;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    }

    .stat-card.primary {
        border-left-color: #667eea;
    }

    .stat-card.success {
        border-left-color: #10b981;
    }

    .stat-card.warning {
        border-left-color: #f59e0b;
    }

    .stat-card.info {
        border-left-color: #3b82f6;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .stat-card.primary .stat-icon {
        background: #eef2ff;
        color: #667eea;
    }

    .stat-card.success .stat-icon {
        background: #d1fae5;
        color: #10b981;
    }

    .stat-card.warning .stat-icon {
        background: #fef3c7;
        color: #f59e0b;
    }

    .stat-card.info .stat-icon {
        background: #dbeafe;
        color: #3b82f6;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .stat-label {
        color: #64748b;
        font-size: 0.875rem;
        margin: 0.25rem 0 0 0;
    }

    /* Info Cards */
    .info-section {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.5rem;
    }

    .info-section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .info-section-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .info-section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .info-label {
        color: #64748b;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: #1e293b;
        font-size: 1rem;
        font-weight: 500;
    }

    .info-value a {
        color: #667eea;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .info-value a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    /* Properties Grid */
    .properties-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .property-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .property-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    }

    .property-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .property-image-placeholder {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
    }

    .property-content {
        padding: 1.25rem;
    }

    .property-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 0.5rem 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .property-location {
        color: #64748b;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .property-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
    }

    .property-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: #667eea;
    }

    .property-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.875rem;
        color: #64748b;
    }

    /* Reviews */
    .review-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 1rem;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .reviewer-info h4 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 0.5rem 0;
    }

    .rating-stars {
        display: flex;
        gap: 0.25rem;
        align-items: center;
    }

    .rating-stars i {
        color: #fbbf24;
        font-size: 1rem;
    }

    .rating-value {
        margin-left: 0.5rem;
        color: #64748b;
        font-size: 0.875rem;
    }

    .review-date {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    .review-comment {
        color: #475569;
        line-height: 1.6;
        margin: 0;
    }

    /* Social Links */
    .social-links {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .social-link {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        background: #f8fafc;
        color: #475569;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .social-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .social-link.facebook:hover {
        background: #1877f2;
        color: white;
    }

    .social-link.twitter:hover {
        background: #1da1f2;
        color: white;
    }

    .social-link.linkedin:hover {
        background: #0a66c2;
        color: white;
    }

    .social-link.instagram:hover {
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        color: white;
    }

    .social-link.youtube:hover {
        background: #ff0000;
        color: white;
    }

    .social-link.website:hover {
        background: #667eea;
        color: white;
    }

    .section-header-with-action {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .view-all-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .view-all-link:hover {
        color: #764ba2;
        transform: translateX(-5px);
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .properties-grid {
            grid-template-columns: 1fr;
        }

        .header-actions {
            flex-direction: column;
        }

        .btn-custom {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="agent-details-page">
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-content">
                <h1 class="page-title">
                    <i class="fas fa-user-tie"></i>
                    تفاصيل الوكيل
                </h1>
                <p class="page-subtitle">عرض معلومات الوكيل: {{ $agent->full_name ?? $agent->name }}</p>
                
                <div class="header-actions">
                    <a href="{{ route('admin.agents.index') }}" class="btn-custom btn-light">
                        <i class="fas fa-arrow-right"></i>
                        العودة للقائمة
                    </a>
                    <a href="{{ route('admin.agents.edit', $agent->id) }}" class="btn-custom btn-outline-light">
                        <i class="fas fa-edit"></i>
                        تعديل
                    </a>
                </div>
            </div>
        </div>

        <!-- Agent Profile Card -->
        <div class="agent-profile-card">
            <div class="agent-avatar-container">
                @if($agent->avatar)
                    <img src="{{ asset($agent->avatar) }}" alt="{{ $agent->full_name ?? $agent->name }}" class="agent-avatar">
                @else
                    <div class="agent-avatar-placeholder">
                        {{ strtoupper(substr($agent->full_name ?? $agent->name, 0, 1)) }}
                    </div>
                @endif
                
                @if($agent->kyc_status == 'verified')
                    <div class="verified-badge">
                        <i class="fas fa-check"></i>
                    </div>
                @endif
            </div>

            <h2 class="agent-name">{{ $agent->full_name ?? $agent->name }}</h2>
            <p class="agent-role">وكيل عقاري</p>

            <div class="status-badges">
                @if($agent->account_status == 'active')
                    <span class="badge-custom badge-success">
                        <i class="fas fa-circle"></i>
                        نشط
                    </span>
                @elseif($agent->account_status == 'inactive')
                    <span class="badge-custom badge-danger">
                        <i class="fas fa-circle"></i>
                        غير نشط
                    </span>
                @else
                    <span class="badge-custom badge-warning">
                        <i class="fas fa-clock"></i>
                        في انتظار
                    </span>
                @endif

                @if($agent->kyc_status == 'verified')
                    <span class="badge-custom badge-verified">
                        <i class="fas fa-shield-check"></i>
                        موثق
                    </span>
                @endif
            </div>
        </div>

        <!-- Performance Stats -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="stat-value">{{ $agent->properties_count ?? 0 }}</h3>
                <p class="stat-label">الخصائص المضافة</p>
            </div>

            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <h3 class="stat-value">{{ $agent->properties_views_count ?? 0 }}</h3>
                <p class="stat-label">مشاهدات الخصائص</p>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="stat-value">{{ $agent->leads_count ?? 0 }}</h3>
                <p class="stat-label">العملاء المحتملون</p>
            </div>

            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3 class="stat-value">{{ $agent->reviews_count ?? 0 }}</h3>
                <p class="stat-label">التقييمات</p>
            </div>

            @if($agent->is_agent)
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="stat-value">{{ $agent->properties_sold ?? 0 }}</h3>
                    <p class="stat-label">الخصائص المباعة</p>
                </div>

                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <h3 class="stat-value">{{ $agent->properties_rented ?? 0 }}</h3>
                    <p class="stat-label">الخصائص المؤجرة</p>
                </div>

                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3 class="stat-value">{{ $agent->total_commission_earned ?? 0 }}</h3>
                    <p class="stat-label">إجمالي العمولات</p>
                </div>

                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3 class="stat-value">{{ $agent->properties_listed ?? 0 }}</h3>
                    <p class="stat-label">الخصائص المعروضة</p>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-lg-6">
                <!-- Basic Information -->
                <div class="info-section">
                    <div class="info-section-header">
                        <div class="info-section-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="info-section-title">معلومات أساسية</h3>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">الاسم الكامل</span>
                            <span class="info-value">{{ $agent->full_name ?? $agent->name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">اسم المستخدم</span>
                            <span class="info-value">{{ $agent->username ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">البريد الإلكتروني</span>
                            <span class="info-value">
                                <a href="mailto:{{ $agent->email }}">{{ $agent->email }}</a>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">رقم الهاتف</span>
                            <span class="info-value">{{ $agent->phone ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">WhatsApp</span>
                            <span class="info-value">{{ $agent->whatsapp ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Telegram</span>
                            <span class="info-value">{{ $agent->telegram ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="info-section">
                    <div class="info-section-header">
                        <div class="info-section-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="info-section-title">معلومات الحساب</h3>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">نوع المستخدم</span>
                            <span class="info-value">{{ $agent->user_type ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">الدور</span>
                            <span class="info-value">{{ $agent->role ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">حالة الحساب</span>
                            <span class="info-value">
                                @if($agent->account_status == 'active')
                                    <span class="badge-custom badge-success">نشط</span>
                                @elseif($agent->account_status == 'inactive')
                                    <span class="badge-custom badge-danger">غير نشط</span>
                                @else
                                    <span class="badge-custom badge-warning">في انتظار</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">حالة KYC</span>
                            <span class="info-value">
                                @if($agent->kyc_status == 'verified')
                                    <span class="badge-custom badge-verified">موثق</span>
                                @elseif($agent->kyc_status == 'pending')
                                    <span class="badge-custom badge-warning">في انتظار</span>
                                @else
                                    <span class="badge-custom badge-danger">غير موثق</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">تاريخ التوثيق</span>
                            <span class="info-value">{{ $agent->kyc_verified_at ? $agent->kyc_verified_at->format('Y-m-d') : '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">تاريخ الإنشاء</span>
                            <span class="info-value">{{ $agent->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="info-section">
                    <div class="info-section-header">
                        <div class="info-section-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3 class="info-section-title">معلومات الموقع</h3>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">الدولة</span>
                            <span class="info-value">{{ $agent->country ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">المدينة</span>
                            <span class="info-value">{{ $agent->city ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">الولاية</span>
                            <span class="info-value">{{ $agent->state ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">الرمز البريدي</span>
                            <span class="info-value">{{ $agent->postal_code ?? '-' }}</span>
                        </div>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <span class="info-label">العنوان</span>
                            <span class="info-value">{{ $agent->address ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                @if($agent->is_agent)
                <!-- Agent Information -->
                <div class="info-section">
                    <div class="info-section-header">
                        <div class="info-section-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h3 class="info-section-title">معلومات الوكيل</h3>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">رقم الرخصة</span>
                            <span class="info-value">{{ $agent->agent_license_number ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">انتهاء الرخصة</span>
                            <span class="info-value">{{ $agent->agent_license_expiry ? $agent->agent_license_expiry->format('Y-m-d') : '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">الشركة</span>
                            <span class="info-value">{{ $agent->agent_company ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">نسبة العمولة</span>
                            <span class="info-value">{{ $agent->agent_commission_rate ?? '-' }}%</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">متوسط وقت الاستجابة</span>
                            <span class="info-value">{{ $agent->average_response_time ?? '-' }} دقيقة</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">معدل الرضا</span>
                            <span class="info-value">{{ $agent->client_satisfaction_rate ?? '-' }}%</span>
                        </div>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <span class="info-label">الاختصاصات</span>
                            <span class="info-value">{{ is_array($agent->agent_specializations) ? implode(', ', $agent->agent_specializations) : ($agent->agent_specializations ?? '-') }}</span>
                        </div>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <span class="info-label">مناطق الخدمة</span>
                            <span class="info-value">{{ is_array($agent->agent_service_areas) ? implode(', ', $agent->agent_service_areas) : ($agent->agent_service_areas ?? '-') }}</span>
                        </div>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <span class="info-label">السيرة الذاتية</span>
                            <span class="info-value">{{ $agent->agent_bio ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Financial Information -->
                <div class="info-section">
                    <div class="info-section-header">
                        <div class="info-section-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h3 class="info-section-title">معلومات مالية</h3>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">رصيد المحفظة</span>
                            <span class="info-value">{{ $agent->wallet_balance ?? 0 }} {{ $agent->wallet_currency ?? 'USD' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">عدد المعاملات</span>
                            <span class="info-value">{{ $agent->transactions_count ?? 0 }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">متوسط التقييم</span>
                            <span class="info-value">{{ $agent->average_rating ?? '-' }}/5</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">كود الإحالة</span>
                            <span class="info-value">{{ $agent->referral_code ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">عدد الإحالات</span>
                            <span class="info-value">{{ $agent->referral_count ?? 0 }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">أرباح الإحالة</span>
                            <span class="info-value">{{ $agent->referral_earnings ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Login Information -->
                <div class="info-section">
                    <div class="info-section-header">
                        <div class="info-section-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <h3 class="info-section-title">معلومات تسجيل الدخول</h3>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">آخر تسجيل دخول</span>
                            <span class="info-value">{{ $agent->last_login_at ? $agent->last_login_at->format('Y-m-d H:i') : '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">عدد مرات الدخول</span>
                            <span class="info-value">{{ $agent->login_count ?? 0 }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">آخر IP</span>
                            <span class="info-value">{{ $agent->last_login_ip ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">آخر جهاز</span>
                            <span class="info-value">{{ $agent->last_login_device ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">المصادقة الثنائية</span>
                            <span class="info-value">
                                @if($agent->two_factor_enabled)
                                    <span class="badge-custom badge-success">مفعلة</span>
                                @else
                                    <span class="badge-custom badge-danger">غير مفعلة</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">اللغة</span>
                            <span class="info-value">{{ $agent->language ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media -->
        <div class="info-section">
            <div class="info-section-header">
                <div class="info-section-icon">
                    <i class="fas fa-share-alt"></i>
                </div>
                <h3 class="info-section-title">وسائل التواصل الاجتماعي</h3>
            </div>
            
            <div class="social-links">
                @if($agent->website)
                    <a href="{{ $agent->website }}" target="_blank" class="social-link website">
                        <i class="fas fa-globe"></i>
                        الموقع الإلكتروني
                    </a>
                @endif

                @if($agent->facebook_url)
                    <a href="{{ $agent->facebook_url }}" target="_blank" class="social-link facebook">
                        <i class="fab fa-facebook"></i>
                        Facebook
                    </a>
                @endif

                @if($agent->twitter_url)
                    <a href="{{ $agent->twitter_url }}" target="_blank" class="social-link twitter">
                        <i class="fab fa-twitter"></i>
                        Twitter
                    </a>
                @endif

                @if($agent->linkedin_url)
                    <a href="{{ $agent->linkedin_url }}" target="_blank" class="social-link linkedin">
                        <i class="fab fa-linkedin"></i>
                        LinkedIn
                    </a>
                @endif

                @if($agent->instagram_url)
                    <a href="{{ $agent->instagram_url }}" target="_blank" class="social-link instagram">
                        <i class="fab fa-instagram"></i>
                        Instagram
                    </a>
                @endif

                @if($agent->youtube_url)
                    <a href="{{ $agent->youtube_url }}" target="_blank" class="social-link youtube">
                        <i class="fab fa-youtube"></i>
                        YouTube
                    </a>
                @endif
            </div>
        </div>

        @if($agent->is_agent && $agent->properties && $agent->properties->count() > 0)
        <!-- Properties Section -->
        <div class="info-section">
            <div class="section-header-with-action">
                <div class="info-section-header" style="margin-bottom: 0; padding-bottom: 0; border: none;">
                    <div class="info-section-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="info-section-title">آخر الخصائص</h3>
                </div>
                <a href="{{ route('admin.agents.properties', $agent->id) }}" class="view-all-link">
                    عرض كل الخصائص
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <div class="properties-grid">
                @foreach($agent->properties->take(6) as $property)
                <div class="property-card">
                    @if($property->featured_image)
                        <img src="{{ asset($property->featured_image) }}" alt="{{ $property->title }}" class="property-image">
                    @else
                        <div class="property-image-placeholder">
                            <i class="fas fa-home"></i>
                        </div>
                    @endif

                    <div class="property-content">
                        <h4 class="property-title">{{ $property->title ?? 'Untitled Property' }}</h4>
                        <p class="property-location">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $property->location ?? 'No location' }}
                        </p>

                        <div class="property-footer">
                            <div>
                                <div class="property-price">{{ $property->price ?? 0 }}</div>
                                <div class="property-meta">
                                    @if($property->property_type)
                                        <span><i class="fas fa-tag"></i> {{ $property->property_type }}</span>
                                    @endif
                                </div>
                            </div>
                            <div style="text-align: left;">
                                <div style="font-size: 0.75rem; color: #94a3b8;">{{ $property->created_at->format('M d, Y') }}</div>
                                <span class="badge-custom badge-{{ $property->status == 'active' ? 'success' : 'warning' }}">
                                    {{ $property->status ?? 'unknown' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($agent->reviews && $agent->reviews->count() > 0)
        <!-- Reviews Section -->
        <div class="info-section">
            <div class="section-header-with-action">
                <div class="info-section-header" style="margin-bottom: 0; padding-bottom: 0; border: none;">
                    <div class="info-section-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="info-section-title">آخر التقييمات</h3>
                </div>
                <a href="{{ route('admin.agents.reviews', $agent->id) }}" class="view-all-link">
                    عرض كل التقييمات
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            @foreach($agent->reviews->take(3) as $review)
            <div class="review-card">
                <div class="review-header">
                    <div class="reviewer-info">
                        <h4>{{ $review->reviewer_name ?? 'Anonymous' }}</h4>
                        <div class="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= ($review->rating ?? 0))
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                            <span class="rating-value">{{ $review->rating ?? 0 }}/5</span>
                        </div>
                    </div>
                    <div class="review-date">
                        {{ $review->created_at->format('M d, Y') }}
                    </div>
                </div>
                <p class="review-comment">{{ $review->comment ?? 'No comment provided' }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
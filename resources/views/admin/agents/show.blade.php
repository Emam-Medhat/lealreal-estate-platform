@extends('admin.layouts.admin')

@section('title', 'تفاصيل الوكيل')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">تفاصيل الوكيل</h1>
            <p class="text-muted mb-0">عرض معلومات الوكيل: {{ $agent->full_name ?? $agent->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
            </a>
            <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>تعديل
            </a>
        </div>
    </div>

    <!-- Agent Profile Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    @if($agent->avatar)
                        <img src="{{ asset('storage/' . $agent->avatar) }}" class="rounded-circle" width="120" height="120" alt="Avatar">
                    @else
                        <div class="avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center text-white mx-auto mb-3">
                            {{ strtoupper(substr($agent->full_name ?? $agent->name, 0, 1)) }}
                        </div>
                    @endif
                    <h5>{{ $agent->full_name ?? $agent->name }}</h5>
                    <p class="text-muted">وكيل عقاري</p>
                    <span class="badge bg-{{ $agent->account_status == 'active' ? 'success' : ($agent->account_status == 'inactive' ? 'danger' : 'warning') }}">
                        {{ $agent->account_status == 'active' ? 'نشط' : ($agent->account_status == 'inactive' ? 'غير نشط' : 'في انتظار') }}
                    </span>
                    @if($agent->kyc_status == 'verified')
                        <span class="badge bg-info ms-2">موثق</span>
                    @endif
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">معلومات أساسية</h6>
                            <p><strong>الاسم الكامل:</strong> {{ $agent->full_name ?? $agent->name }}</p>
                            <p><strong>الاسم الأول:</strong> {{ $agent->first_name ?? '-' }}</p>
                            <p><strong>الاسم الأخير:</strong> {{ $agent->last_name ?? '-' }}</p>
                            <p><strong>اسم المستخدم:</strong> {{ $agent->username ?? '-' }}</p>
                            <p><strong>البريد الإلكتروني:</strong> {{ $agent->email }}</p>
                            <p><strong>رقم الهاتف:</strong> {{ $agent->phone ?? '-' }}</p>
                            <p><strong>WhatsApp:</strong> {{ $agent->whatsapp ?? '-' }}</p>
                            <p><strong>Telegram:</strong> {{ $agent->telegram ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">معلومات الحساب</h6>
                            <p><strong>نوع المستخدم:</strong> {{ $agent->user_type ?? '-' }}</p>
                            <p><strong>الدور:</strong> {{ $agent->role ?? '-' }}</p>
                            <p><strong>حالة الحساب:</strong> 
                                <span class="badge bg-{{ $agent->account_status == 'active' ? 'success' : ($agent->account_status == 'inactive' ? 'danger' : 'warning') }}">
                                    {{ $agent->account_status == 'active' ? 'نشط' : ($agent->account_status == 'inactive' ? 'غير نشط' : 'في انتظار') }}
                                </span>
                            </p>
                            <p><strong>حالة KYC:</strong> 
                                <span class="badge bg-{{ $agent->kyc_status == 'verified' ? 'success' : ($agent->kyc_status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ $agent->kyc_status == 'verified' ? 'موثق' : ($agent->kyc_status == 'pending' ? 'في انتظار' : 'غير موثق') }}
                                </span>
                            </p>
                            <p><strong>تاريخ KYC:</strong> {{ $agent->kyc_verified_at ? $agent->kyc_verified_at->format('Y-m-d') : '-' }}</p>
                            <p><strong>تاريخ الإنشاء:</strong> {{ $agent->created_at->format('Y-m-d H:i') }}</p>
                            <p><strong>آخر تسجيل دخول:</strong> {{ $agent->last_login_at ? $agent->last_login_at->format('Y-m-d H:i') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Agent Specific Information -->
    @if($agent->is_agent)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">معلومات الوكيل</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>رقم رخصة الوكيل:</strong> {{ $agent->agent_license_number ?? '-' }}</p>
                    <p><strong>انتهاء الرخصة:</strong> {{ $agent->agent_license_expiry ? $agent->agent_license_expiry->format('Y-m-d') : '-' }}</p>
                    <p><strong>الشركة:</strong> {{ $agent->agent_company ?? '-' }}</p>
                    <p><strong>نسبة العمولة:</strong> {{ $agent->agent_commission_rate ?? '-' }}%</p>
                    <p><strong>متوسط وقت الاستجابة:</strong> {{ $agent->average_response_time ?? '-' }} دقيقة</p>
                </div>
                <div class="col-md-6">
                    <p><strong>الاختصاصات:</strong> {{ is_array($agent->agent_specializations) ? implode(', ', $agent->agent_specializations) : ($agent->agent_specializations ?? '-') }}</p>
                    <p><strong>مناطق الخدمة:</strong> {{ is_array($agent->agent_service_areas) ? implode(', ', $agent->agent_service_areas) : ($agent->agent_service_areas ?? '-') }}</p>
                    <p><strong>السيرة الذاتية:</strong> {{ $agent->agent_bio ?? '-' }}</p>
                    <p><strong>عدد العملاء:</strong> {{ $agent->client_count ?? 0 }}</p>
                    <p><strong>معدل رضا العملاء:</strong> {{ $agent->client_satisfaction_rate ?? '-' }}%</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Performance Statistics -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">إحصائيات الأداء</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-primary">{{ $agent->properties_count ?? 0 }}</h4>
                        <p class="text-muted">الخصائص المضافة</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-success">{{ $agent->properties_views_count ?? 0 }}</h4>
                        <p class="text-muted">مشاهدات الخصائص</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-info">{{ $agent->leads_count ?? 0 }}</h4>
                        <p class="text-muted">العملاء المحتملون</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-warning">{{ $agent->reviews_count ?? 0 }}</h4>
                        <p class="text-muted">التقييمات</p>
                    </div>
                </div>
            </div>
            @if($agent->is_agent)
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-primary">{{ $agent->properties_listed ?? 0 }}</h4>
                        <p class="text-muted">الخصائص المعروضة</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-success">{{ $agent->properties_sold ?? 0 }}</h4>
                        <p class="text-muted">الخصائص المباعة</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-info">{{ $agent->properties_rented ?? 0 }}</h4>
                        <p class="text-muted">الخصائص المؤجرة</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4 class="text-warning">{{ $agent->total_commission_earned ?? 0 }}</h4>
                        <p class="text-muted">إجمالي العمولات</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Location Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">معلومات الموقع</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>الدولة:</strong> {{ $agent->country ?? '-' }}</p>
                    <p><strong>المدينة:</strong> {{ $agent->city ?? '-' }}</p>
                    <p><strong>الولاية:</strong> {{ $agent->state ?? '-' }}</p>
                    <p><strong>الرمز البريدي:</strong> {{ $agent->postal_code ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>العنوان:</strong> {{ $agent->address ?? '-' }}</p>
                    <p><strong>خط العرض:</strong> {{ $agent->latitude ?? '-' }}</p>
                    <p><strong>خط الطول:</strong> {{ $agent->longitude ?? '-' }}</p>
                    <p><strong>المنطقة الزمنية:</strong> {{ $agent->timezone ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">معلومات مالية</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>رصيد المحفظة:</strong> {{ $agent->wallet_balance ?? 0 }} {{ $agent->wallet_currency ?? 'USD' }}</p>
                    <p><strong>عدد المعاملات:</strong> {{ $agent->transactions_count ?? 0 }}</p>
                    <p><strong>متوسط التقييم:</strong> {{ $agent->average_rating ?? '-' }}/5</p>
                </div>
                <div class="col-md-6">
                    <p><strong>كود الإحالة:</strong> {{ $agent->referral_code ?? '-' }}</p>
                    <p><strong>عدد الإحالات:</strong> {{ $agent->referral_count ?? 0 }}</p>
                    <p><strong>أرباح الإحالة:</strong> {{ $agent->referral_earnings ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Social Media -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">وسائل التواصل الاجتماعي</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>الموقع الإلكتروني:</strong> 
                        @if($agent->website)
                            <a href="{{ $agent->website }}" target="_blank">{{ $agent->website }}</a>
                        @else
                            -
                        @endif
                    </p>
                    <p><strong>Facebook:</strong> 
                        @if($agent->facebook_url)
                            <a href="{{ $agent->facebook_url }}" target="_blank">Profile</a>
                        @else
                            -
                        @endif
                    </p>
                    <p><strong>Twitter:</strong> 
                        @if($agent->twitter_url)
                            <a href="{{ $agent->twitter_url }}" target="_blank">Profile</a>
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>LinkedIn:</strong> 
                        @if($agent->linkedin_url)
                            <a href="{{ $agent->linkedin_url }}" target="_blank">Profile</a>
                        @else
                            -
                        @endif
                    </p>
                    <p><strong>Instagram:</strong> 
                        @if($agent->instagram_url)
                            <a href="{{ $agent->instagram_url }}" target="_blank">Profile</a>
                        @else
                            -
                        @endif
                    </p>
                    <p><strong>YouTube:</strong> 
                        @if($agent->youtube_url)
                            <a href="{{ $agent->youtube_url }}" target="_blank">Channel</a>
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Properties -->
    @if($agent->is_agent && $agent->properties && $agent->properties->count() > 0)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">آخر الخصائص</h5>
            <a href="{{ route('admin.properties.index', ['agent' => $agent->id]) }}" class="btn btn-sm btn-outline-primary">
                عرض كل الخصائص
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($agent->properties->take(6) as $property)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        @if($property->featured_image)
                            <img src="{{ asset('storage/' . $property->featured_image) }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="{{ $property->title }}">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                <i class="fas fa-home fa-2x text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h6 class="card-title">{{ $property->title ?? 'Untitled Property' }}</h6>
                            <p class="card-text text-muted">{{ $property->location ?? 'No location' }}</p>
                            <p class="card-text">
                                <strong>{{ $property->price ?? 0 }}</strong>
                                @if($property->property_type)
                                    <span class="badge bg-secondary ms-2">{{ $property->property_type }}</span>
                                @endif
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ $property->created_at->format('M d, Y') }}</small>
                                <span class="badge bg-{{ $property->status == 'active' ? 'success' : 'warning' }}">
                                    {{ $property->status ?? 'unknown' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Reviews -->
    @if($agent->reviews && $agent->reviews->count() > 0)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">آخر التقييمات</h5>
            <a href="#" class="btn btn-sm btn-outline-primary">
                عرض كل التقييمات
            </a>
        </div>
        <div class="card-body">
            @foreach($agent->reviews->take(3) as $review)
            <div class="border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">{{ $review->reviewer_name ?? 'Anonymous' }}</h6>
                        <div class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= ($review->rating ?? 0) ? '' : 'text-muted' }}"></i>
                            @endfor
                            <small class="text-muted ms-2">{{ $review->rating ?? 0 }}/5</small>
                        </div>
                    </div>
                    <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                </div>
                <p class="mb-0">{{ $review->comment ?? 'No comment provided' }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Login Information -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">معلومات تسجيل الدخول</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>عدد مرات تسجيل الدخول:</strong> {{ $agent->login_count ?? 0 }}</p>
                    <p><strong>آخر IP:</strong> {{ $agent->last_login_ip ?? '-' }}</p>
                    <p><strong>آخر جهاز:</strong> {{ $agent->last_login_device ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>المصادقة الثنائية:</strong> 
                        <span class="badge bg-{{ $agent->two_factor_enabled ? 'success' : 'secondary' }}">
                            {{ $agent->two_factor_enabled ? 'مفعلة' : 'غير مفعلة' }}
                        </span>
                    </p>
                    <p><strong>المصادقة البيومترية:</strong> 
                        <span class="badge bg-{{ $agent->biometric_enabled ? 'success' : 'secondary' }}">
                            {{ $agent->biometric_enabled ? 'مفعلة' : 'غير مفعلة' }}
                        </span>
                    </p>
                    <p><strong>اللغة:</strong> {{ $agent->language ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

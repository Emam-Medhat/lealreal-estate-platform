@extends('admin.layouts.admin')

@section('title', 'إضافة وكيل جديد')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">إضافة وكيل جديد</h1>
            <p class="text-muted mb-0">تسجيل وكيل عقاري جديد في المنصة</p>
        </div>
        <div>
            <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.agents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Basic Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">معلومات أساسية</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                            @error('first_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">الاسم الأخير <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required>
                            @error('last_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">اسم المستخدم</label>
                            <input type="text" name="username" class="form-control">
                            @error('username')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required>
                            @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">WhatsApp</label>
                            <input type="tel" name="whatsapp" class="form-control">
                            @error('whatsapp')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Telegram</label>
                            <input type="text" name="telegram" class="form-control" placeholder="@username">
                            @error('telegram')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                            @error('password')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">الصورة الرمزية</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                            @error('avatar')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">الموقع الإلكتروني</label>
                            <input type="url" name="website" class="form-control" placeholder="https://example.com">
                            @error('website')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agent Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">معلومات الوكيل</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">رقم رخصة الوكيل</label>
                            <input type="text" name="agent_license_number" class="form-control">
                            @error('agent_license_number')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">تاريخ انتهاء الرخصة</label>
                            <input type="date" name="agent_license_expiry" class="form-control">
                            @error('agent_license_expiry')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">الشركة</label>
                            <input type="text" name="agent_company" class="form-control">
                            @error('agent_company')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">نسبة العمولة (%)</label>
                            <input type="number" name="agent_commission_rate" class="form-control" step="0.01" min="0" max="100">
                            @error('agent_commission_rate')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">متوسط وقت الاستجابة (دقيقة)</label>
                            <input type="number" name="average_response_time" class="form-control" step="0.1" min="0">
                            @error('average_response_time')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">معدل رضا العملاء (%)</label>
                            <input type="number" name="client_satisfaction_rate" class="form-control" step="0.01" min="0" max="9.99">
                            @error('client_satisfaction_rate')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">الاختصاصات</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agent_specializations[]" value="بيع" id="spec_sale">
                                        <label class="form-check-label" for="spec_sale">بيع</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agent_specializations[]" value="شراء" id="spec_buy">
                                        <label class="form-check-label" for="spec_buy">شراء</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agent_specializations[]" value="إيجار" id="spec_rent">
                                        <label class="form-check-label" for="spec_rent">إيجار</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agent_specializations[]" value="إدارة" id="spec_manage">
                                        <label class="form-check-label" for="spec_manage">إدارة</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agent_specializations[]" value="استشارات" id="spec_consult">
                                        <label class="form-check-label" for="spec_consult">استشارات</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agent_specializations[]" value="تقييم" id="spec_valuation">
                                        <label class="form-check-label" for="spec_valuation">تقييم</label>
                                    </div>
                                </div>
                            </div>
                            @error('agent_specializations')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">مناطق الخدمة</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agent_service_areas[]" value="الرياض" id="area_riyadh">
                                        <label class="form-check-label" for="area_riyadh">الرياض</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agent_service_areas[]" value="جدة" id="area_jeddah">
                                        <label class="form-check-label" for="area_jeddah">جدة</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agent_service_areas[]" value="مكة" id="area_mecca">
                                        <label class="form-check-label" for="area_mecca">مكة</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agent_service_areas[]" value="المدينة" id="area_medinah">
                                        <label class="form-check-label" for="area_medinah">المدينة</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agent_service_areas[]" value="الدمام" id="area_dammam">
                                        <label class="form-check-label" for="area_dammam">الدمام</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="agent_service_areas[]" value="الخبر" id="area_khobar">
                                        <label class="form-check-label" for="area_khobar">الخبر</label>
                                    </div>
                                </div>
                            </div>
                            @error('agent_service_areas')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">السيرة الذاتية</label>
                            <textarea name="agent_bio" class="form-control" rows="4" placeholder="اكتب نبذة عن الوكيل وخبراته..."></textarea>
                            @error('agent_bio')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
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
                        <div class="mb-3">
                            <label class="form-label">الدولة</label>
                            <select name="country" class="form-select">
                                <option value="">اختر الدولة</option>
                                <option value="المملكة العربية السعودية" selected>المملكة العربية السعودية</option>
                                <option value="مصر">مصر</option>
                                <option value="الإمارات العربية المتحدة">الإمارات العربية المتحدة</option>
                                <option value="الكويت">الكويت</option>
                                <option value="قطر">قطر</option>
                                <option value="البحرين">البحرين</option>
                                <option value="عمان">عمان</option>
                            </select>
                            @error('country')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">المدينة</label>
                            <input type="text" name="city" class="form-control">
                            @error('city')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">الولاية</label>
                            <input type="text" name="state" class="form-control">
                            @error('state')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">الرمز البريدي</label>
                            <input type="text" name="postal_code" class="form-control">
                            @error('postal_code')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                            @error('address')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">خط العرض</label>
                            <input type="number" name="latitude" class="form-control" step="any">
                            @error('latitude')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">خط الطول</label>
                            <input type="number" name="longitude" class="form-control" step="any">
                            @error('longitude')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
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
                        <div class="mb-3">
                            <label class="form-label">Facebook</label>
                            <input type="url" name="facebook_url" class="form-control" placeholder="https://facebook.com/username">
                            @error('facebook_url')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Twitter</label>
                            <input type="url" name="twitter_url" class="form-control" placeholder="https://twitter.com/username">
                            @error('twitter_url')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">LinkedIn</label>
                            <input type="url" name="linkedin_url" class="form-control" placeholder="https://linkedin.com/in/username">
                            @error('linkedin_url')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Instagram</label>
                            <input type="url" name="instagram_url" class="form-control" placeholder="https://instagram.com/username">
                            @error('instagram_url')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">YouTube</label>
                            <input type="url" name="youtube_url" class="form-control" placeholder="https://youtube.com/c/channel">
                            @error('youtube_url')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">إعدادات الحساب</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">حالة الحساب</label>
                            <select name="account_status" class="form-select">
                                <option value="pending_verification">في انتظار التحقق</option>
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                                <option value="suspended">معلق</option>
                            </select>
                            @error('account_status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">حالة KYC</label>
                            <select name="kyc_status" class="form-select">
                                <option value="not_submitted">لم يتم التقديم</option>
                                <option value="pending">في انتظار</option>
                                <option value="verified">موثق</option>
                                <option value="rejected">مرفوض</option>
                            </select>
                            @error('kyc_status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">اللغة</label>
                            <select name="language" class="form-select">
                                <option value="ar">العربية</option>
                                <option value="en">English</option>
                            </select>
                            @error('language')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">العملة</label>
                            <select name="currency" class="form-select">
                                <option value="SAR">ريال سعودي</option>
                                <option value="USD">دولار أمريكي</option>
                                <option value="EUR">يورو</option>
                                <option value="EGP">جنيه مصري</option>
                            </select>
                            @error('currency')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">رصيد المحفظة</label>
                            <input type="number" name="wallet_balance" class="form-control" step="0.01" min="0" value="0">
                            @error('wallet_balance')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">كود الإحالة</label>
                            <input type="text" name="referral_code" class="form-control" maxlength="8">
                            @error('referral_code')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden Fields -->
        <input type="hidden" name="is_agent" value="1">
        <input type="hidden" name="user_type" value="agent">

        <!-- Form Actions -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ الوكيل
                        </button>
                        <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary">
                            إلغاء
                        </a>
                    </div>
                    <div>
                        <button type="reset" class="btn btn-outline-warning">
                            <i class="fas fa-undo me-2"></i>إعادة تعيين
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

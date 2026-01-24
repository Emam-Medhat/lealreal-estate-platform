@extends('layouts.app')

@section('title')
    إنشاء حملة تسويق عقاري
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">إنشاء حملة تسويق عقاري جديد</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('marketing.property-marketing.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">العقار <span class="text-danger">*</span></label>
                                <select class="form-select" name="property_id" required>
                                    <option value="">اختر العقار</option>
                                    @foreach($properties as $property)
                                        <option value="{{ $property->id }}">{{ $property->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">الوصف</label>
                                <textarea class="form-control" name="description" rows="4" placeholder="صف حملة التسويق بالتفصيل"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نوع الحملة</label>
                                <select class="form-select" name="campaign_type" required>
                                    <option value="brand_awareness">توعي بالعلامة التجارية</option>
                                    <option value="lead_generation">توليد العملاء المحتملين</option>
                                    <option value="property_promotion">ترويج العقار</option>
                                    <option value="neighborhood_showcase">عرض الحي</option>
                                    <option value="event_marketing">تسويق الفعاليات</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الحالة الأولية</label>
                                <select class="form-select" name="status">
                                    <option value="draft">مسودة</option>
                                    <option value="scheduled">مجدولة</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">تاريخ البدء</label>
                                <input type="date" class="form-control" name="start_date">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="الملف الميزانية</label>
                                <input type="text" class="form-control" name="budget" placeholder="0.00" step="0.01">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">العملة</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">العملة</label>
                                <input type="text" class="form-control" name="currency" value="SAR">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">الجمهور المستهدف</label>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">الفئة العمر</label>
                                        <select class="form-select" name="target_audience[age_range]">
                                            <option value="18-24">18-24 سنة</option>
                                            <option value="25-34">25-34 سنة</option>
                                            <option value="35-44">35-44 سنة</option>
                                            <option value="45-54">45-54 سنة</option>
                                            <option value="55+">55+ سنة</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">الجنس</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[genders][]" value="male" checked>
                                            <label class="form-check-label">ذكر</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[genders][]" value="female">
                                            <label class="form-check-label">أنثى</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">المناطق</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[locations][]" value="الرياض" checked>
                                            <label class="form-check-label">الرياض</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[locations][]" value="جدة">
                                                <label class="form-check-label">جدة</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[locations][]" value="الدمام">
                                                <label class="form-check-label">الدمام</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">قنوات التسويق</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="marketing_channels[]" value="social_media" checked>
                                            <label class="form-check-label">وسائل التواصل الاجتماعي</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="marketing_channels[]" value="email" checked>
                                            <label class="form-check-label">البريد الإلكتروني</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="marketing_channels[]" value="search">
                                                <label class="form-check-label">محركات البحث</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="marketing_channels[]" value="display">
                                                <label class="form-check-label">العرض</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">استراتيجية المحتوى</label>
                                <textarea class="form-control" name="content_strategy" rows="3" placeholder="صف استراتيجية المحتوى للحملة"></textarea>
                            </div>
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('marketing.property-marketing.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-1"></i>
                                عودة
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                حفظ الحملة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات إضافية</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>نصائح الحملات</h6>
                        <ul class="list-unstyled">
                            <li><strong>brand_awareness:</strong> زيادة الوعي بالعلامة التجارية</li>
                            <li><strong>lead_generation:</strong> التركيز على توليد العملاء المحتملين</li>
                            <li><strong>property_promotion:</strong> ترويج العقارات بشكل مميز</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <h6>نصائف الجمهور</h6>
                        <ul class="list-unstyled">
                            <li>العمرحلة: 18-24 سنة</li>
                            <li>الجنس: 25-34 سنة</li>
                            <li>الدخل: 35-44 سنة</li>
                            <li>45-54 سنة</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <h6>قنوات التسويق</h6>
                        <ul class="list-unstyled">
                            <li>وسائل التواصل الاجتماعي</li>
                            <li>البريد الإلكتروني</li>
                            <li>محركات البحث</li>
                            <li>العرض التقلي</li>
                        </ul>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>نصيحة:</strong>
                        اختر القنوات المناسبة لهدفك لتحقيق أفضل النتائج.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
